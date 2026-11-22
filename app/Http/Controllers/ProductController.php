<?php

namespace App\Http\Controllers;

use App\Models\AvisoStock;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Producto::with('inventario')->where('activo', true);

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->query('tipo'));
        }

        if ($request->filled('aroma')) {
            $query->where('aroma', $request->query('aroma'));
        }

        if ($request->filled('q')) {
            $term = $request->query('q');
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%")
                    ->orWhere('descripcion', 'like', "%{$term}%");
            });
        }

        $productos = $query->paginate(12)->withQueryString();

        $aromas = ['ALPHA', 'HELIOS', 'JAZ'];
        $tipos = ['Kit', 'Dúo', 'Gift', 'Refill'];

        return view('pages.catalogo', compact('productos', 'aromas', 'tipos'));
    }

    public function show(string $slug): View
    {
        $producto = Producto::with(['inventario', 'resenas' => function ($query) {
            $query->where('estado', 'publicada')->with('user')->latest();
        }])->where('slug', $slug)->firstOrFail();

        // Productos alternativos o complementarios recomendados
        $alternativas = Producto::with('inventario')
            ->where('activo', true)
            ->where('id', '!=', $producto->id)
            ->where(function ($q) use ($producto) {
                $q->where('aroma', $producto->aroma)
                    ->orWhere('tipo', $producto->tipo);
            })
            ->take(3)
            ->get();

        return view('pages.producto', compact('producto', 'alternativas'));
    }

    public function solicitarAvisoStock(Request $request, int $id): RedirectResponse
    {
        $producto = Producto::findOrFail($id);

        $validated = $request->validate([
            'correo' => 'required|email|max:180',
        ], [
            'correo.required' => 'Ingresa tu correo para avisarte cuando esté disponible.',
        ]);

        AvisoStock::create([
            'correo' => $validated['correo'],
            'producto_id' => $producto->id,
            'notificado' => false,
        ]);

        return back()->with('stock_alert_success', "¡Listo! Te avisaremos al correo {$validated['correo']} apenas tengamos unidades de {$producto->nombre}.");
    }
}
