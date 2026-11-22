<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $productos = Producto::with('inventario')->latest()->paginate(15);

        return view('admin.productos.index', compact('productos'));
    }

    public function create(): View
    {
        return view('admin.productos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:120',
            'tipo' => 'required|in:Kit,Dúo,Gift,Refill',
            'aroma' => 'required|in:ALPHA,HELIOS,JAZ,MULTI',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric|min:0',
            'foto' => 'nullable|url',
            'stock_inicial' => 'required|integer|min:0',
            'umbral_minimo' => 'required|integer|min:0',
        ]);

        $slug = Str::slug($validated['nombre']);
        $originalSlug = $slug;
        $counter = 1;
        while (Producto::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        $producto = Producto::create([
            'nombre' => $validated['nombre'],
            'slug' => $slug,
            'tipo' => $validated['tipo'],
            'aroma' => $validated['aroma'],
            'descripcion' => $validated['descripcion'],
            'precio' => $validated['precio'],
            'foto' => $validated['foto'] ?: 'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?auto=format&fit=crop&w=800&q=80',
            'activo' => true,
        ]);

        Inventario::create([
            'producto_id' => $producto->id,
            'stock_disponible' => $validated['stock_inicial'],
            'stock_reservado' => 0,
            'umbral_minimo' => $validated['umbral_minimo'],
        ]);

        if ($validated['stock_inicial'] > 0) {
            MovimientoInventario::create([
                'producto_id' => $producto->id,
                'tipo' => 'entrada',
                'cantidad' => $validated['stock_inicial'],
                'motivo' => 'Creación de producto e inventario inicial',
                'user_id' => Auth::id(),
                'usuario_nombre' => Auth::user()->name,
            ]);
        }

        return redirect()->route('admin.productos.index')->with('success', "Producto '{$producto->nombre}' creado exitosamente.");
    }

    public function edit(int $id): View
    {
        $producto = Producto::with('inventario')->findOrFail($id);

        return view('admin.productos.edit', compact('producto'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $producto = Producto::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:120',
            'tipo' => 'required|in:Kit,Dúo,Gift,Refill',
            'aroma' => 'required|in:ALPHA,HELIOS,JAZ,MULTI',
            'descripcion' => 'required|string',
            'precio' => 'required|numeric|min:0',
            'foto' => 'nullable|url',
            'activo' => 'required|boolean',
        ]);

        $producto->update($validated);

        return redirect()->route('admin.productos.index')->with('success', "Producto '{$producto->nombre}' actualizado.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $producto = Producto::findOrFail($id);
        $nombre = $producto->nombre;
        $producto->delete();

        return redirect()->route('admin.productos.index')->with('success', "Producto '{$nombre}' eliminado.");
    }
}
