<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AvisoStock;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = Inventario::with('producto');

        if ($request->query('filtro') === 'bajo_stock') {
            $query->whereColumn('stock_disponible', '<=', 'umbral_minimo');
        }

        $inventarios = $query->get();
        $selectedProductoId = $request->query('producto_id');
        $productoSeleccionado = $selectedProductoId ? Producto::with('inventario')->find($selectedProductoId) : null;

        $ultimosMovimientos = MovimientoInventario::with('producto')
            ->latest()
            ->paginate(15);

        return view('admin.inventario', compact('inventarios', 'productoSeleccionado', 'ultimosMovimientos'));
    }

    public function ajustar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'tipo' => 'required|in:entrada,ajuste,merma',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|min:4|max:255',
        ], [
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.min' => 'Debe ser mayor que 0.',
            'motivo.required' => 'Indica el motivo del ajuste.',
            'motivo.min' => 'El motivo debe tener al menos 4 caracteres.',
        ]);

        $user = Auth::user();

        $resultado = DB::transaction(function () use ($validated, $user) {
            $producto = Producto::findOrFail($validated['producto_id']);
            $inventario = Inventario::firstOrCreate(
                ['producto_id' => $producto->id],
                ['stock_disponible' => 0, 'stock_reservado' => 0, 'umbral_minimo' => 5]
            );

            $stockAnterior = $inventario->stock_disponible;
            $cantidad = (int) $validated['cantidad'];

            if ($validated['tipo'] === 'entrada') {
                $inventario->stock_disponible += $cantidad;
            } elseif ($validated['tipo'] === 'merma') {
                $inventario->stock_disponible = max(0, $inventario->stock_disponible - $cantidad);
            } elseif ($validated['tipo'] === 'ajuste') {
                // Ajuste directo al nuevo valor o suma según valor
                $inventario->stock_disponible = $cantidad;
            }

            $inventario->save();
            $stockNuevo = $inventario->stock_disponible;

            MovimientoInventario::create([
                'producto_id' => $producto->id,
                'tipo' => $validated['tipo'],
                'cantidad' => $cantidad,
                'motivo' => $validated['motivo'],
                'user_id' => $user->id,
                'usuario_nombre' => $user->name,
            ]);

            // Si el producto estaba sin stock o bajo y ahora tiene stock, avisar a suscritos (Punto 2 & Punto 3)
            $avisosCount = 0;
            if ($stockAnterior <= 0 && $stockNuevo > 0) {
                $avisos = AvisoStock::where('producto_id', $producto->id)->where('notificado', false)->get();
                $avisosCount = $avisos->count();
                foreach ($avisos as $aviso) {
                    $aviso->notificado = true;
                    $aviso->save();
                }
            }

            return [
                'producto' => $producto->nombre,
                'stockAnterior' => $stockAnterior,
                'stockNuevo' => $stockNuevo,
                'avisosCount' => $avisosCount,
            ];
        });

        $msg = "Stock actualizado: {$resultado['producto']} pasó de {$resultado['stockAnterior']} a {$resultado['stockNuevo']} unidades.";
        if ($resultado['avisosCount'] > 0) {
            $msg .= " Se avisó a {$resultado['avisosCount']} clientes suscritos.";
        }

        return redirect()->route('admin.inventario.index', ['producto_id' => $validated['producto_id']])
            ->with('stock_success', $msg);
    }

    public function actualizarUmbral(Request $request, int $productoId): RedirectResponse
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Solo el Administrador puede definir umbrales mínimos de stock.');
        }

        $validated = $request->validate([
            'umbral_minimo' => 'required|integer|min:0|max:1000',
        ]);

        $inventario = Inventario::where('producto_id', $productoId)->firstOrFail();
        $inventario->umbral_minimo = (int) $validated['umbral_minimo'];
        $inventario->save();

        return back()->with('success', 'Umbral mínimo actualizado correctamente.');
    }
}
