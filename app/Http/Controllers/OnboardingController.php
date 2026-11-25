<?php

namespace App\Http\Controllers;

use App\Models\Espacio;
use App\Models\PerfilSeguridad;
use App\Models\PerfilSensorial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $sensorial = $user->perfilSensorial;
        $seguridad = $user->perfilSeguridad;
        $espacio = $user->espacios()->first();

        return view('onboarding.index', compact('sensorial', 'seguridad', 'espacio'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'familias_preferidas' => ['nullable', 'array'],
            'familias_evitadas' => ['nullable', 'array'],
            'intensidad_preferida' => ['required', 'string', 'in:suave,media,intensa'],
            'alergias' => ['nullable', 'string'],
            'mascotas_en_casa' => ['nullable', 'boolean'],
            'tipo_mascotas' => ['nullable', 'string'],
            'ninos_en_casa' => ['nullable', 'boolean'],
            'embarazo_o_lactancia' => ['required', 'string', 'in:si,no,prefiero_no_decir'],
            'espacio_nombre' => ['required', 'string', 'max:100'],
            'espacio_tipo' => ['required', 'string', 'in:dormitorio,estudio,sala,bano,cocina,otro'],
            'espacio_tamano' => ['required', 'string', 'in:pequeno,medio,grande'],
            'espacio_ventilado' => ['nullable', 'boolean'],
        ]);

        // 1. Guardar o actualizar Perfil Sensorial
        PerfilSensorial::updateOrCreate(
            ['user_id' => $user->id],
            [
                'familias_preferidas' => $validated['familias_preferidas'] ?? [],
                'familias_evitadas' => $validated['familias_evitadas'] ?? [],
                'intensidad_preferida' => $validated['intensidad_preferida'],
            ]
        );

        // 2. Guardar o actualizar Perfil de Seguridad
        $alergiasArray = [];
        if (! empty($validated['alergias'])) {
            $alergiasArray = array_values(array_filter(array_map('trim', explode(',', $validated['alergias']))));
        }

        $mascotasArray = [];
        if (! empty($validated['tipo_mascotas'])) {
            $mascotasArray = array_values(array_filter(array_map('trim', explode(',', $validated['tipo_mascotas']))));
        }

        PerfilSeguridad::updateOrCreate(
            ['user_id' => $user->id],
            [
                'alergias' => $alergiasArray,
                'mascotas_en_casa' => $request->boolean('mascotas_en_casa'),
                'tipo_mascotas' => $mascotasArray,
                'ninos_en_casa' => $request->boolean('ninos_en_casa'),
                'embarazo_o_lactancia' => $validated['embarazo_o_lactancia'],
            ]
        );

        // 3. Crear o actualizar espacio inicial
        Espacio::updateOrCreate(
            ['user_id' => $user->id, 'nombre' => $validated['espacio_nombre']],
            [
                'tipo' => $validated['espacio_tipo'],
                'tamano' => $validated['espacio_tamano'],
                'ventilado' => $request->boolean('espacio_ventilado', true),
            ]
        );

        return redirect()->route('configurador.index')->with('success', '¡Perfil configurado con éxito! Ahora puedes calcular tu primera fórmula.');
    }
}
