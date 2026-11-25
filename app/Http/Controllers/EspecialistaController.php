<?php

namespace App\Http\Controllers;

use App\Models\Aroma;
use App\Models\EntradaDiario;
use App\Models\Formula;
use App\Models\RegistroCambio;
use App\Models\ReglaAfinidad;
use App\Services\FormulaEngineService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EspecialistaController extends Controller
{
    public function __construct(
        protected FormulaEngineService $engine
    ) {}

    public function index(Request $request): View
    {
        $aromas = Aroma::with(['reglasAfinidad'])->get();
        $reglasPublicadas = ReglaAfinidad::with(['aroma', 'creador', 'aprobador'])
            ->where('estado', 'publicada')
            ->orderBy('aroma_id')
            ->get();

        $reglasRevision = ReglaAfinidad::with(['aroma', 'creador', 'aprobador'])
            ->whereIn('estado', ['borrador', 'en_revision'])
            ->latest()
            ->get();

        // Métricas agregadas anónimas (sin datos privados de usuarios)
        $totalFormulas = Formula::count();
        $promedioCalificaciones = round(EntradaDiario::avg('calificacion') ?? 4.8, 1);
        $totalReglas = ReglaAfinidad::count();

        $forceState = $request->query('estado'); // 'idle', 'loading', 'success', 'error'

        return view('especialista.index', compact(
            'aromas',
            'reglasPublicadas',
            'reglasRevision',
            'totalFormulas',
            'promedioCalificaciones',
            'totalReglas',
            'forceState'
        ));
    }

    /**
     * Crear o editar una regla de afinidad (crea en estado 'borrador' o 'en_revision').
     */
    public function storeRegla(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'aroma_id' => ['required', 'exists:aromas,id'],
            'variable' => ['required', 'string', 'in:objetivo,espacio,momento,emocion'],
            'valor' => ['required', 'string'],
            'peso' => ['required', 'integer', 'between:0,3'],
            'version' => ['required', 'string', 'max:10'],
            'enviar_a_revision' => ['nullable', 'boolean'],
        ], [
            'peso.between' => 'El peso de afinidad debe estar estrictamente entre 0 (sin afinidad) y 3 (máxima afinidad).',
        ]);

        $estado = $request->boolean('enviar_a_revision') ? 'en_revision' : 'borrador';

        $regla = ReglaAfinidad::create([
            'aroma_id' => $validated['aroma_id'],
            'variable' => $validated['variable'],
            'valor' => $validated['valor'],
            'peso' => $validated['peso'],
            'version' => $validated['version'],
            'estado' => $estado,
            'creado_por' => $user->id,
            'aprobado_por' => null,
        ]);

        RegistroCambio::create([
            'tipo_entidad' => 'ReglaAfinidad',
            'entidad_id' => $regla->id,
            'user_id' => $user->id,
            'accion' => $estado === 'en_revision' ? 'enviar_revision' : 'crear_borrador',
            'fecha' => now(),
            'comentario' => "Regla {$regla->variable}={$regla->valor} (peso {$regla->peso}) creada en versión {$regla->version}.",
            'detalles' => $regla->toArray(),
        ]);

        $msg = $estado === 'en_revision'
            ? 'Regla enviada a revisión. Debe ser aprobada por un segundo especialista (Maker-Checker).'
            : 'Regla guardada como borrador.';

        return redirect()->route('especialista.index', ['estado' => 'success'])->with('success', $msg);
    }

    /**
     * Aprobación Maker-Checker por un segundo especialista.
     */
    public function aprobarRegla(ReglaAfinidad $regla): RedirectResponse
    {
        $user = Auth::user();

        // Regla de Negocio MAKER-CHECKER estricta:
        // El creador no puede aprobar su propia regla.
        if ($regla->creado_por === $user->id) {
            return back()->with('error', 'Principio Maker-Checker violado: No puedes aprobar una regla que tú mismo formulaste. Debe ser verificada por otro especialista.')
                ->with('maker_checker_error', true);
        }

        $regla->estado = 'publicada';
        $regla->aprobado_por = $user->id;
        $regla->save();

        RegistroCambio::create([
            'tipo_entidad' => 'ReglaAfinidad',
            'entidad_id' => $regla->id,
            'user_id' => $user->id,
            'accion' => 'aprobar_y_publicar',
            'fecha' => now(),
            'comentario' => "Regla v{$regla->version} aprobada por {$user->name} e incorporada al motor público.",
            'detalles' => $regla->toArray(),
        ]);

        return redirect()->route('especialista.index', ['estado' => 'success'])
            ->with('success', "¡Regla v{$regla->version} aprobada y publicada con éxito en el motor!");
    }

    /**
     * Simulador de fórmulas en vivo para especialistas.
     */
    public function simular(Request $request): View
    {
        $validated = $request->validate([
            'objetivo' => ['required', 'string'],
            'momento' => ['required', 'string'],
            'intensidad' => ['required', 'string'],
            'espacio_tipo' => ['nullable', 'string'],
            'emocion' => ['nullable', 'string'],
        ]);

        $context = [
            'objetivo' => $validated['objetivo'],
            'momento' => $validated['momento'],
            'intensidad' => $validated['intensidad'],
            'espacio_tipo' => $validated['espacio_tipo'] ?? 'estudio',
            'emocion' => $validated['emocion'] ?? 'calma',
        ];

        $resultado = $this->engine->calcular($context);

        return view('especialista.simulador', compact('resultado', 'context'));
    }
}
