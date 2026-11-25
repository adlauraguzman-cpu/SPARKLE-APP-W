<?php

namespace App\Http\Controllers;

use App\Models\Aroma;
use App\Models\ComponenteFormula;
use App\Models\Formula;
use App\Models\RegistroAnimo;
use App\Models\Sesion;
use App\Services\FormulaEngineService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ConfiguradorController extends Controller
{
    public function __construct(
        protected FormulaEngineService $engine
    ) {}

    public function index(Request $request): View
    {
        $user = Auth::user();
        $espacios = $user ? $user->espacios : collect();
        $aromas = Aroma::where('activo', true)->get();

        // Si se solicita forzar un estado específico para demostración de los 4 estados
        $forceState = $request->query('estado'); // 'idle', 'loading', 'success', 'error', 'sin_formula_segura'

        return view('configurador.index', compact('user', 'espacios', 'aromas', 'forceState'));
    }

    /**
     * Calcula la fórmula aplicando el motor de 5 pasos y gestiona los 4 estados.
     */
    public function calcular(Request $request): View|JsonResponse
    {
        $user = Auth::user();

        // 1. Detección de Estado de Error: "Faltan respuestas"
        $rules = [
            'objetivo' => ['required', 'string', 'in:concentrarme,desacelerar,hacer_una_pausa'],
            'momento' => ['required', 'string', 'in:manana,tarde,noche'],
            'intensidad' => ['required', 'string', 'in:suave,media,intensa'],
            'espacio_tipo' => ['nullable', 'string'],
            'emocion' => ['nullable', 'string', 'in:estres,cansancio,calma,enfoque,energia'],
            'energia' => ['nullable', 'integer', 'between:1,5'],
        ];

        // Validaciones condicionales para invitados
        if (! $user) {
            $rules['alergias'] = ['nullable', 'string'];
            $rules['mascotas'] = ['nullable', 'boolean'];
            $rules['embarazo'] = ['nullable', 'string', 'in:si,no,prefiero_no_decir'];
        }

        $validator = validator($request->all(), $rules);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'FALTAN_RESPUESTAS',
                    'message' => 'Por favor completa todas las preguntas obligatorias antes de formular.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            return back()->withErrors($validator)->withInput()->with('error_variant', 'faltan_respuestas');
        }

        $validated = $validator->validated();

        $context = [
            'objetivo' => $validated['objetivo'],
            'momento' => $validated['momento'],
            'intensidad' => $validated['intensidad'],
            'espacio_tipo' => $validated['espacio_tipo'] ?? 'estudio',
            'emocion' => $validated['emocion'] ?? 'calma',
            'energia' => (int) ($validated['energia'] ?? 3),
        ];

        $guestSafety = null;
        if (! $user) {
            $alergiasArray = ! empty($validated['alergias'])
                ? array_values(array_filter(array_map('trim', explode(',', $validated['alergias']))))
                : [];
            $guestSafety = [
                'alergias' => $alergiasArray,
                'mascotas' => $request->boolean('mascotas'),
                'embarazo' => $validated['embarazo'] ?? 'no',
            ];
        }

        // Ejecutar motor
        try {
            $resultado = $this->engine->calcular($context, $user, $guestSafety);
        } catch (Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'FALLA_CALCULO',
                    'message' => 'Ocurrió un error inesperado al calibrar la fórmula. Por favor intenta de nuevo.',
                ], 500);
            }

            return back()->with('error_variant', 'falla_calculo')->withInput();
        }

        // Caso de descarte por seguridad estricta: Estado 4 Variante "Sin fórmula segura"
        if (! $resultado['success']) {
            if ($request->wantsJson()) {
                return response()->json($resultado, 422);
            }

            return back()->with('error_variant', 'sin_formula_segura')
                ->with('motivos_seguridad', $resultado['motivos'] ?? [])
                ->withInput();
        }

        // Estado 3: Éxito (Success)
        // Si el usuario es invitado, guardamos el resultado temporalmente en la sesión web
        session([
            'ultima_formula_calculada' => array_merge($resultado, [
                'context' => $context,
                'es_invitado' => ! $user,
            ]),
        ]);

        if ($request->wantsJson()) {
            return response()->json($resultado);
        }

        return view('configurador.resultado', [
            'resultado' => $resultado,
            'context' => $context,
            'user' => $user,
        ]);
    }

    /**
     * Guarda la fórmula calculada en la cuenta del usuario.
     */
    public function guardar(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('register')->with('info', 'Crea una cuenta para guardar esta fórmula y sincronizarla en tus rutinas.');
        }

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'objetivo' => ['required', 'string'],
            'momento' => ['required', 'string'],
            'intensidad' => ['required', 'string'],
            'espacio_id' => ['nullable', 'exists:espacios,id'],
            'emocion' => ['nullable', 'string'],
            'energia' => ['nullable', 'integer'],
            'componentes' => ['required', 'array', 'min:1'],
            'componentes.*.aroma_id' => ['required', 'exists:aromas,id'],
            'componentes.*.porcentaje' => ['required', 'integer'],
            'explicacion' => ['required', 'string'],
            'ritual_pasos' => ['nullable', 'array'],
            'ritual_gotas' => ['nullable', 'integer'],
            'ritual_duracion' => ['nullable', 'integer'],
        ]);

        // Registrar registro de ánimo asociado
        $animo = null;
        if (! empty($validated['emocion'])) {
            $animo = RegistroAnimo::create([
                'user_id' => $user->id,
                'fecha_hora' => now(),
                'emocion' => $validated['emocion'],
                'energia' => $validated['energia'] ?? 3,
                'nota' => "Estado registrado al crear la fórmula {$validated['nombre']}",
            ]);
        }

        // Crear la sesión
        $sesion = Sesion::create([
            'user_id' => $user->id,
            'espacio_id' => $validated['espacio_id'] ?? null,
            'animo_id' => $animo?->id,
            'objetivo' => $validated['objetivo'],
            'momento' => $validated['momento'],
            'intensidad' => $validated['intensidad'],
            'es_invitado' => false,
        ]);

        // Crear la fórmula
        $formula = Formula::create([
            'sesion_id' => $sesion->id,
            'user_id' => $user->id,
            'nombre' => $validated['nombre'],
            'version_de_reglas' => 'v1.0',
            'explicacion' => $validated['explicacion'],
            'ritual_sugerido' => [
                'gotas' => $validated['ritual_gotas'] ?? 6,
                'duracion_min' => $validated['ritual_duracion'] ?? 15,
                'pasos' => $validated['ritual_pasos'] ?? [],
            ],
            'estado' => 'guardada',
        ]);

        foreach ($validated['componentes'] as $comp) {
            ComponenteFormula::create([
                'formula_id' => $formula->id,
                'aroma_id' => $comp['aroma_id'],
                'porcentaje' => $comp['porcentaje'],
            ]);
        }

        return redirect()->route('rutinas.index')->with('success', "¡Fórmula '{$formula->nombre}' guardada con éxito! Puedes asociarla a una rutina.");
    }
}
