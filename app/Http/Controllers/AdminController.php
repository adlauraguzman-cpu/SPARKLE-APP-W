<?php

namespace App\Http\Controllers;

use App\Models\Consentimiento;
use App\Models\Formula;
use App\Models\RegistroCambio;
use App\Models\ReglaAfinidad;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    /**
     * Panel de administración.
     * IMPORTANTE: No expone contenido privado de diarios ni perfiles médicos individuales.
     */
    public function index(): View
    {
        $metricas = [
            'usuarios_totales' => User::count(),
            'usuarios_activos' => User::where('activo', true)->count(),
            'formulas_generadas' => Formula::count(),
            'reglas_publicadas' => ReglaAfinidad::where('estado', 'publicada')->count(),
            'consentimientos_firmados' => Consentimiento::where('aceptado', true)->count(),
        ];

        $usuarios = User::latest()->paginate(15);
        $registrosAuditoria = RegistroCambio::with('user')->latest()->take(20)->get();

        return view('admin.index', compact('metricas', 'usuarios', 'registrosAuditoria'));
    }

    /**
     * Cambio de rol o estado de usuario.
     */
    public function updateUsuario(Request $request, User $usuario): RedirectResponse
    {
        $admin = Auth::user();

        $validated = $request->validate([
            'rol' => ['required', 'string', 'in:usuario,especialista,administrador'],
            'activo' => ['required', 'boolean'],
        ]);

        // Evitar que el admin se deshabilite a sí mismo
        if ($usuario->id === $admin->id && ! $validated['activo']) {
            return back()->with('error', 'No puedes deshabilitar tu propia cuenta administradora.');
        }

        $rolAnterior = $usuario->rol;
        $estadoAnterior = $usuario->activo;

        $usuario->rol = $validated['rol'];
        $usuario->activo = (bool) $validated['activo'];
        $usuario->save();

        RegistroCambio::create([
            'tipo_entidad' => 'User',
            'entidad_id' => $usuario->id,
            'user_id' => $admin->id,
            'accion' => 'modificar_usuario',
            'fecha' => now(),
            'comentario' => "Actualización de usuario {$usuario->email}: rol {$rolAnterior}->{$usuario->rol}, activo: {$usuario->activo}",
            'detalles' => ['anterior' => ['rol' => $rolAnterior, 'activo' => $estadoAnterior], 'nuevo' => $validated],
        ]);

        return back()->with('success', "Usuario {$usuario->name} actualizado correctamente.");
    }

    /**
     * Gestión de Consentimientos y Derechos ARCO / GDPR / Ley 1581.
     */
    public function consentimientos(): View
    {
        $consentimientos = Consentimiento::with('user')->latest()->paginate(20);

        return view('admin.consentimientos', compact('consentimientos'));
    }

    /**
     * Exportación de datos de usuario (Portabilidad de datos sin perfiles no autorizados).
     */
    public function exportarDatos(User $usuario): StreamedResponse
    {
        $admin = Auth::user();

        RegistroCambio::create([
            'tipo_entidad' => 'User',
            'entidad_id' => $usuario->id,
            'user_id' => $admin->id,
            'accion' => 'exportar_datos_arco',
            'fecha' => now(),
            'comentario' => "Exportación de expediente ARCO para {$usuario->email}",
        ]);

        $data = [
            'identificacion' => [
                'id' => $usuario->id,
                'nombre' => $usuario->name,
                'email' => $usuario->email,
                'fecha_registro' => $usuario->fecha_registro,
                'zona_horaria' => $usuario->zona_horaria,
            ],
            'consentimientos' => $usuario->consentimientos()->get(['tipo', 'aceptado', 'version_del_texto', 'fecha']),
            'espacios' => $usuario->espacios()->get(['nombre', 'tipo', 'tamano', 'ventilado']),
            'formulas_guardadas' => $usuario->formulas()->with('componentes.aroma')->get(['id', 'nombre', 'version_de_reglas', 'created_at']),
        ];

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return response()->streamDownload(function () use ($json) {
            echo $json;
        }, "sparkle_arco_export_{$usuario->id}.json", [
            'Content-Type' => 'application/json',
        ]);
    }
}
