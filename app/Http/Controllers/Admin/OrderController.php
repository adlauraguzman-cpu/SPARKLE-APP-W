<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\MovimientoPuntos;
use App\Models\Pedido;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Pedido::with(['user', 'items.producto'])->latest();

        if ($request->filled('estado')) {
            $query->where('estado', $request->query('estado'));
        }

        if ($request->filled('q')) {
            $term = $request->query('q');
            $query->where(function ($q) use ($term) {
                $q->where('numero_pedido', 'like', "%{$term}%")
                    ->orWhere('direccion_envio', 'like', "%{$term}%")
                    ->orWhereHas('user', function ($uq) use ($term) {
                        $uq->where('name', 'like', "%{$term}%")
                            ->orWhere('email', 'like', "%{$term}%");
                    });
            });
        }

        $pedidos = $query->paginate(15)->withQueryString();
        $isColaborador = Auth::user()->isColaborador();

        return view('admin.pedidos.index', compact('pedidos', 'isColaborador'));
    }

    public function show(int $id): View
    {
        $pedido = Pedido::with(['user', 'items.producto', 'movimientoPuntos'])->findOrFail($id);
        $isColaborador = Auth::user()->isColaborador();

        return view('admin.pedidos.show', compact('pedido', 'isColaborador'));
    }

    public function registrarEnvio(Request $request, int $id): RedirectResponse
    {
        $pedido = Pedido::findOrFail($id);

        $validated = $request->validate([
            'transportadora' => 'required|string|max:80',
            'guia_transporte' => 'required|string|max:80',
        ], [
            'transportadora.required' => 'Indica el nombre de la empresa transportadora.',
            'guia_transporte.required' => 'Ingresa el número de guía de envío.',
        ]);

        $pedido->transportadora = $validated['transportadora'];
        $pedido->guia_transporte = $validated['guia_transporte'];
        $pedido->estado = 'enviado';
        $pedido->save();

        return back()->with('success', "Envío registrado para el pedido {$pedido->numero_pedido}. Estado actualizado a «Enviado» y notificación enviada al cliente.");
    }

    public function marcarEntregado(int $id): RedirectResponse
    {
        $pedido = Pedido::with('user')->findOrFail($id);

        DB::transaction(function () use ($pedido) {
            $pedido->estado = 'entregado';
            $pedido->save();

            // Acreditar puntos al cliente tras entrega exitosa (Punto 2 & Punto 6)
            $movimientoPuntos = MovimientoPuntos::where('pedido_id', $pedido->id)
                ->where('tipo', 'ganado')
                ->where('estado', 'pendiente')
                ->first();

            if ($movimientoPuntos) {
                $movimientoPuntos->estado = 'acreditado';
                $movimientoPuntos->motivo = "Puntos acreditados por entrega del pedido {$pedido->numero_pedido}";
                $movimientoPuntos->save();

                // Sumar al saldo disponible del cliente
                $cliente = $pedido->user;
                $cliente->puntos_disponibles += $movimientoPuntos->puntos;
                $cliente->save();
            }
        });

        return back()->with('success', "Pedido {$pedido->numero_pedido} marcado como ENTREGADO. Se acreditaron los puntos al cliente y se activó la solicitud de reseña y oferta de recarga.");
    }

    public function anular(Request $request, int $id): RedirectResponse
    {
        if (! Auth::user()->isAdmin()) {
            abort(403, 'Solo el Administrador puede anular pedidos.');
        }

        $pedido = Pedido::with('items')->findOrFail($id);

        $validated = $request->validate([
            'motivo_anulacion' => 'required|string|min:5|max:255',
        ], [
            'motivo_anulacion.required' => 'Indica el motivo de la anulación para el registro de auditoría.',
        ]);

        DB::transaction(function () use ($pedido, $validated) {
            $pedido->estado = 'cancelado';
            $pedido->solicitud_cambio_devolucion = 'ANULADO POR ADMIN: '.$validated['motivo_anulacion'];
            $pedido->save();

            // Devolver stock al inventario
            foreach ($pedido->items as $item) {
                $inventario = Inventario::where('producto_id', $item->producto_id)->first();
                if ($inventario) {
                    $inventario->stock_disponible += $item->cantidad;
                    $inventario->save();

                    MovimientoInventario::create([
                        'producto_id' => $item->producto_id,
                        'tipo' => 'reserva_liberada',
                        'cantidad' => $item->cantidad,
                        'motivo' => "Anulación de pedido {$pedido->numero_pedido}: {$validated['motivo_anulacion']}",
                        'user_id' => Auth::id(),
                        'usuario_nombre' => Auth::user()->name,
                    ]);
                }
            }

            // Cancelar puntos pendientes
            MovimientoPuntos::where('pedido_id', $pedido->id)->update([
                'estado' => 'cancelado',
            ]);
        });

        return back()->with('success', "El pedido {$pedido->numero_pedido} ha sido anulado y los ítems retornaron al inventario disponible.");
    }
}
