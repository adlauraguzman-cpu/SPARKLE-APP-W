<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MovimientoPuntos;
use App\Models\Recompensa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoyaltyController extends Controller
{
    public function index(): View
    {
        $recompensas = Recompensa::latest()->get();
        $historialPuntos = MovimientoPuntos::with(['user', 'pedido'])->latest()->paginate(15);

        return view('admin.fidelizacion.index', compact('recompensas', 'historialPuntos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:120',
            'descripcion' => 'nullable|string',
            'puntos_requeridos' => 'required|integer|min:1',
            'descuento_valor' => 'required|numeric|min:0',
            'vigencia' => 'nullable|date',
        ]);

        Recompensa::create([
            'nombre' => $validated['nombre'],
            'descripcion' => $validated['descripcion'],
            'puntos_requeridos' => $validated['puntos_requeridos'],
            'descuento_valor' => $validated['descuento_valor'],
            'vigencia' => $validated['vigencia'],
            'activa' => true,
        ]);

        return back()->with('success', 'Recompensa creada exitosamente.');
    }

    public function toggle(int $id): RedirectResponse
    {
        $recompensa = Recompensa::findOrFail($id);
        $recompensa->activa = ! $recompensa->activa;
        $recompensa->save();

        return back()->with('success', 'Estado de recompensa actualizado.');
    }
}
