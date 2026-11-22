<?php

namespace App\Services;

use App\Models\CarritoItem;
use App\Models\Producto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Obtiene el identificador de sesión para visitantes.
     */
    public function getSessionId(): string
    {
        if (! Session::has('cart_session_id')) {
            Session::put('cart_session_id', 'sess_'.bin2hex(random_bytes(12)));
        }

        return Session::get('cart_session_id');
    }

    /**
     * Obtiene los ítems del carrito según el usuario o sesión.
     */
    public function getItems(): Collection
    {
        $query = CarritoItem::with(['producto.inventario']);

        if (Auth::check()) {
            $query->where('user_id', Auth::id());
        } else {
            $query->where('session_id', $this->getSessionId());
        }

        return $query->get();
    }

    /**
     * Migra el carrito de invitado al usuario recién autenticado.
     */
    public function migrateGuestCartToUser(int $userId): void
    {
        $sessionId = Session::get('cart_session_id');
        if (! $sessionId) {
            return;
        }

        $guestItems = CarritoItem::where('session_id', $sessionId)->get();
        foreach ($guestItems as $guestItem) {
            $userItem = CarritoItem::where('user_id', $userId)
                ->where('producto_id', $guestItem->producto_id)
                ->first();

            if ($userItem) {
                $userItem->cantidad += $guestItem->cantidad;
                $userItem->save();
                $guestItem->delete();
            } else {
                $guestItem->user_id = $userId;
                $guestItem->session_id = null;
                $guestItem->save();
            }
        }
    }

    /**
     * Agrega un producto al carrito verificando stock.
     */
    public function addItem(int $productoId, int $cantidad = 1): array
    {
        $producto = Producto::with('inventario')->findOrFail($productoId);

        if (! $producto->activo) {
            return ['success' => false, 'message' => 'El producto no está disponible.'];
        }

        $stockDisponible = $producto->inventario ? $producto->inventario->stock_disponible : 0;
        if ($stockDisponible <= 0) {
            return ['success' => false, 'message' => 'Producto agotado temporalmente.', 'agotado' => true];
        }

        $itemQuery = CarritoItem::where('producto_id', $productoId);
        if (Auth::check()) {
            $itemQuery->where('user_id', Auth::id());
        } else {
            $itemQuery->where('session_id', $this->getSessionId());
        }

        $item = $itemQuery->first();
        $cantidadActual = $item ? $item->cantidad : 0;
        $nuevaCantidad = $cantidadActual + $cantidad;

        if ($nuevaCantidad > $stockDisponible) {
            return [
                'success' => false,
                'message' => "Solo quedan {$stockDisponible} unidades disponibles de {$producto->nombre}.",
                'disponible' => $stockDisponible,
            ];
        }

        if ($item) {
            $item->cantidad = $nuevaCantidad;
            $item->save();
        } else {
            CarritoItem::create([
                'user_id' => Auth::id(),
                'session_id' => Auth::check() ? null : $this->getSessionId(),
                'producto_id' => $productoId,
                'cantidad' => $cantidad,
            ]);
        }

        return ['success' => true, 'message' => 'Producto agregado al carrito con éxito.'];
    }

    /**
     * Actualiza la cantidad de un ítem en el carrito con validación de stock.
     */
    public function updateQuantity(int $itemId, int $nuevaCantidad): array
    {
        $item = CarritoItem::with('producto.inventario')->findOrFail($itemId);

        if ($nuevaCantidad <= 0) {
            $item->delete();

            return ['success' => true, 'message' => 'Producto eliminado del carrito.'];
        }

        $stockDisponible = $item->producto->inventario ? $item->producto->inventario->stock_disponible : 0;
        if ($nuevaCantidad > $stockDisponible) {
            return [
                'success' => false,
                'message' => "La disponibilidad cambió. Solo quedan {$stockDisponible} unidades de {$item->producto->nombre}.",
                'disponible' => $stockDisponible,
            ];
        }

        $item->cantidad = $nuevaCantidad;
        $item->save();

        return ['success' => true, 'message' => 'Carrito actualizado.'];
    }

    /**
     * Elimina un ítem del carrito.
     */
    public function removeItem(int $itemId): void
    {
        CarritoItem::where('id', $itemId)->delete();
    }

    /**
     * Vacía el carrito actual.
     */
    public function clearCart(): void
    {
        if (Auth::check()) {
            CarritoItem::where('user_id', Auth::id())->delete();
        } else {
            CarritoItem::where('session_id', $this->getSessionId())->delete();
        }
    }

    /**
     * Calcula los totales del carrito.
     */
    public function getTotals(): array
    {
        $items = $this->getItems();
        $subtotal = 0;
        $totalItems = 0;

        foreach ($items as $item) {
            $subtotal += ($item->producto->precio * $item->cantidad);
            $totalItems += $item->cantidad;
        }

        // Regla: Envío estándar $12.000 COP, gratis por compras superiores a $200.000 COP
        $costoEnvio = ($subtotal > 0 && $subtotal < 200000) ? 12000 : 0;
        $total = $subtotal + $costoEnvio;

        // Regla de puntos: 1 punto por cada $1.000 COP en la compra
        $puntosEstimados = (int) floor($subtotal / 1000);

        return [
            'subtotal' => $subtotal,
            'costo_envio' => $costoEnvio,
            'total' => $total,
            'total_items' => $totalItems,
            'puntos_estimados' => $puntosEstimados,
        ];
    }
}
