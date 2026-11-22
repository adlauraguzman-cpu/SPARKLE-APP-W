<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index(): View
    {
        $items = $this->cartService->getItems();
        $totales = $this->cartService->getTotals();

        return view('pages.carrito', compact('items', 'totales'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'nullable|integer|min:1',
        ]);

        $cantidad = (int) ($validated['cantidad'] ?? 1);
        $result = $this->cartService->addItem((int) $validated['producto_id'], $cantidad);

        if (! $result['success']) {
            return back()->with('cart_error', $result['message']);
        }

        return redirect()->route('cart.index')->with('cart_success', $result['message']);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'cantidad' => 'required|integer|min:0|max:99',
        ]);

        $result = $this->cartService->updateQuantity($id, (int) $validated['cantidad']);

        if (! $result['success']) {
            return back()->with('stock_warning', $result['message']);
        }

        return redirect()->route('cart.index')->with('cart_success', $result['message']);
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->cartService->removeItem($id);

        return redirect()->route('cart.index')->with('cart_success', 'Producto retirado del carrito.');
    }
}
