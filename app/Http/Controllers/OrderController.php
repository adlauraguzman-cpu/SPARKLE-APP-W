<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index(): View
    {
        $pedidos = Pedido::with(['items.producto'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('pages.mis-pedidos', compact('pedidos'));
    }

    public function show(string $numeroPedido): View
    {
        $pedido = Pedido::with(['items.producto.resenas' => function ($q) {
            $q->where('user_id', Auth::id());
        }])
            ->where('user_id', Auth::id())
            ->where('numero_pedido', $numeroPedido)
            ->firstOrFail();

        return view('pages.detalle-pedido', compact('pedido'));
    }

    public function solicitarCambio(Request $request, string $numeroPedido): RedirectResponse
    {
        $pedido = Pedido::where('user_id', Auth::id())
            ->where('numero_pedido', $numeroPedido)
            ->firstOrFail();

        $validated = $request->validate([
            'motivo' => 'required|string|min:10|max:500',
        ], [
            'motivo.required' => 'Por favor explica la razón de tu solicitud de cambio o devolución.',
            'motivo.min' => 'El motivo debe tener al menos 10 caracteres.',
        ]);

        $pedido->solicitud_cambio_devolucion = $validated['motivo'];
        $pedido->save();

        return back()->with('success', 'Tu solicitud de cambio o devolución ha sido registrada. Nuestro equipo de atención se comunicará contigo vía WhatsApp en menos de 24 horas.');
    }

    public function repetirPedido(string $numeroPedido): RedirectResponse
    {
        $pedido = Pedido::with('items')->where('user_id', Auth::id())
            ->where('numero_pedido', $numeroPedido)
            ->firstOrFail();

        foreach ($pedido->items as $item) {
            $this->cartService->addItem($item->producto_id, $item->cantidad);
        }

        return redirect()->route('cart.index')->with('cart_success', "Los productos del pedido {$numeroPedido} fueron agregados a tu carrito.");
    }
}
