<?php

namespace App\Http\Controllers;

use App\Models\Recordatorio;
use App\Models\Rutina;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RutinaController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $rutinas = $user->rutinas()->with(['formula.componentes.aroma', 'espacio', 'recordatorios'])->latest()->get();
        $formulas = $user->formulas()->with('componentes.aroma')->get();
        $espacios = $user->espacios;

        return view('rutinas.index', compact('rutinas', 'formulas', 'espacios'));
    }

    public function create(): View
    {
        $user = Auth::user();
        $formulas = $user->formulas()->with('componentes.aroma')->get();
        $espacios = $user->espacios;

        return view('rutinas.create', compact('formulas', 'espacios'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'formula_id' => ['required', 'exists:formulas,id'],
            'espacio_id' => ['nullable', 'exists:espacios,id'],
            'nombre' => ['required', 'string', 'max:150'],
            'dias' => ['required', 'array', 'min:1'],
            'hora' => ['required', 'string'],
            'duracion_minutos' => ['required', 'integer', 'between:5,120'],
            'canal_recordatorio' => ['required', 'string', 'in:notificacion,correo,ninguno'],
        ]);

        $rutina = Rutina::create([
            'user_id' => $user->id,
            'formula_id' => $validated['formula_id'],
            'espacio_id' => $validated['espacio_id'] ?? null,
            'nombre' => $validated['nombre'],
            'dias' => $validated['dias'],
            'hora' => $validated['hora'],
            'duracion_minutos' => $validated['duracion_minutos'],
            'canal_recordatorio' => $validated['canal_recordatorio'],
            'activa' => true,
        ]);

        return redirect()->route('rutinas.index')->with('success', "Rutina '{$rutina->nombre}' programada con éxito.");
    }

    public function toggle(Rutina $rutina): RedirectResponse
    {
        $this->authorizeOwner($rutina);

        $rutina->activa = ! $rutina->activa;
        $rutina->save();

        $estado = $rutina->activa ? 'activada' : 'pausada';

        return back()->with('info', "La rutina '{$rutina->nombre}' ha sido {$estado}.");
    }

    public function simularRecordatorio(Rutina $rutina): RedirectResponse
    {
        $this->authorizeOwner($rutina);

        $recordatorio = Recordatorio::create([
            'rutina_id' => $rutina->id,
            'fecha_hora' => now(),
            'estado' => 'enviado',
        ]);

        return back()->with('recordatorio_simulado', [
            'rutina' => $rutina->nombre,
            'hora' => $rutina->hora,
            'canal' => $rutina->canal_recordatorio,
            'formula' => $rutina->formula->nombre,
            'espacio' => $rutina->espacio?->nombre ?? 'Tu espacio de calma',
        ]);
    }

    public function ritual(Rutina $rutina): View
    {
        $this->authorizeOwner($rutina);
        $rutina->load(['formula.componentes.aroma', 'espacio']);

        return view('rutinas.ritual', compact('rutina'));
    }

    protected function authorizeOwner(Rutina $rutina): void
    {
        if ($rutina->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para gestionar esta rutina.');
        }
    }
}
