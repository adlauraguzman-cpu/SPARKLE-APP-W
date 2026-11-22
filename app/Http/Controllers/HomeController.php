<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Resena;
use App\Models\Suscriptor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $productosDestacados = Producto::with('inventario')
            ->where('activo', true)
            ->take(6)
            ->get();

        $resenasRecientes = Resena::with(['user', 'producto'])
            ->where('estado', 'publicada')
            ->latest()
            ->take(3)
            ->get();

        return view('pages.home', compact('productosDestacados', 'resenasRecientes'));
    }

    public function suscribirse(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:120',
            'correo' => 'required|email|max:180|unique:suscriptores,correo',
            'autoriza_datos' => 'accepted',
        ], [
            'nombre.required' => 'Por favor indica tu nombre completo.',
            'correo.required' => 'Ingresa tu correo electrónico para recibir beneficios.',
            'correo.unique' => 'Este correo ya hace parte de la Comunidad SPARKLE.',
            'autoriza_datos.accepted' => 'Debes autorizar el tratamiento de datos para suscribirte.',
        ]);

        Suscriptor::create([
            'nombre' => $validated['nombre'],
            'correo' => $validated['correo'],
            'autoriza_datos' => true,
        ]);

        return back()->with('subscription_success', '¡Bienvenida/o a la Comunidad SPARKLE! Te enviamos una sorpresa a tu correo.');
    }
}
