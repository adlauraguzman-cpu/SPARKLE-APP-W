<?php

namespace App\Http\Controllers;

use App\Models\EntradaDiario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DiarioController extends Controller
{
    /**
     * Vista de diario con cálculo de racha, evolución y 4 estados de interfaz.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $entradas = $user->entradasDiario()->with(['formula.componentes.aroma', 'rutina'])->orderByDesc('fecha')->paginate(10);
        $formulas = $user->formulas()->with('componentes.aroma')->get();
        $rutinas = $user->rutinas()->where('activa', true)->get();

        // Cálculo de racha consecutiva de días
        $todasFechas = $user->entradasDiario()->orderByDesc('fecha')->pluck('fecha')->map(fn ($f) => $f->format('Y-m-d'))->unique()->values()->all();

        $racha = 0;
        $hoy = now()->format('Y-m-d');
        $ayer = now()->subDay()->format('Y-m-d');

        if (! empty($todasFechas)) {
            $currentDate = now();
            // Si no registró hoy pero sí ayer, la racha sigue viva
            if ($todasFechas[0] !== $hoy && $todasFechas[0] === $ayer) {
                $currentDate = now()->subDay();
            }

            foreach ($todasFechas as $fechaStr) {
                if ($fechaStr === $currentDate->format('Y-m-d')) {
                    $racha++;
                    $currentDate->subDay();
                } else {
                    break;
                }
            }
        }

        // Promedio de bienestar / evolución
        $promedioCalif = round($user->entradasDiario()->avg('calificacion') ?? 5, 1);
        $totalSesiones = $user->entradasDiario()->count();

        // Para demostración interactiva de los 4 estados
        $forceState = $request->query('estado'); // 'idle', 'loading', 'success', 'error'

        return view('diario.index', compact('entradas', 'formulas', 'rutinas', 'racha', 'promedioCalif', 'totalSesiones', 'forceState'));
    }

    /**
     * Guarda una nueva entrada en el diario (Estados 2: Loading -> 3: Success con comparativo de ánimo).
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'fecha' => ['required', 'date'],
            'formula_id' => ['nullable', 'exists:formulas,id'],
            'rutina_id' => ['nullable', 'exists:rutinas,id'],
            'emocion_antes' => ['required', 'string', 'in:estres,cansancio,calma,enfoque,energia'],
            'emocion_despues' => ['required', 'string', 'in:estres,cansancio,calma,enfoque,energia'],
            'energia_antes' => ['required', 'integer', 'between:1,5'],
            'energia_despues' => ['required', 'integer', 'between:1,5'],
            'calificacion' => ['required', 'integer', 'between:1,5'],
            'nota' => ['nullable', 'string', 'max:1000'],
            'etiquetas' => ['nullable', 'string'],
        ], [
            'emocion_antes.required' => 'Indica cómo te sentías antes de la sesión.',
            'emocion_despues.required' => 'Indica cómo te sientes al terminar la sesión.',
            'calificacion.required' => 'Por favor califica la experiencia del 1 al 5.',
        ]);

        $etiquetasArray = [];
        if (! empty($validated['etiquetas'])) {
            $etiquetasArray = array_values(array_filter(array_map('trim', explode(',', $validated['etiquetas']))));
        }

        $entrada = EntradaDiario::create([
            'user_id' => $user->id,
            'formula_id' => $validated['formula_id'] ?? null,
            'rutina_id' => $validated['rutina_id'] ?? null,
            'fecha' => $validated['fecha'],
            'emocion_antes' => $validated['emocion_antes'],
            'emocion_despues' => $validated['emocion_despues'],
            'energia_antes' => $validated['energia_antes'],
            'energia_despues' => $validated['energia_despues'],
            'calificacion' => $validated['calificacion'],
            'nota' => $validated['nota'] ?? null,
            'etiquetas' => $etiquetasArray,
        ]);

        $mejora = $validated['energia_despues'] - $validated['energia_antes'];
        $cambioMsg = $mejora > 0 ? "¡Tu nivel de energía subió +{$mejora} puntos!" : 'Sesión registrada satisfactoriamente.';

        return redirect()->route('diario.index', ['estado' => 'success'])
            ->with('success', "¡Entrada guardada! {$cambioMsg} Esta retroalimentación optimizará tus futuras fórmulas.")
            ->with('entrada_creada_id', $entrada->id);
    }
}
