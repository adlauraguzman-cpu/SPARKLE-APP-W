<?php

namespace App\Http\Controllers;

use App\Models\Direccion;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\MovimientoPuntos;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login', ['redirect' => 'checkout'])
                ->with('info', 'Para finalizar tu compra y acumular puntos con este pedido, por favor ingresa o crea tu cuenta.');
        }

        $items = $this->cartService->getItems();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('info', 'Tu carrito está vacío. Elige un aroma para comenzar.');
        }

        $totales = $this->cartService->getTotals();
        $user = Auth::user();
        $direccionPrincipal = $user->direcciones()->where('es_principal', true)->first()
            ?? $user->direcciones()->latest()->first();

        return view('pages.checkout', compact('items', 'totales', 'user', 'direccionPrincipal'));
    }

    public function process(Request $request): RedirectResponse
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $items = $this->cartService->getItems();
        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('info', 'Tu carrito está vacío.');
        }

        // Validación estricta con mensajes específicos y coherencia de datos (Punto 2 & Punto 3)
        $validated = $request->validate([
            'nombre' => 'required|string|max:120',
            'celular' => 'required|regex:/^[0-9]{10}$/',
            'ciudad' => 'required|string|max:80',
            'direccion' => 'required|string|min:5|max:200',
            'metodo_pago' => 'required|in:tarjeta,pse,billetera',
            'acepto_terminos' => 'accepted',
            'simular_pago_fallido' => 'nullable|boolean', // Switch para pruebas de la variante "Pago rechazado"
        ], [
            'nombre.required' => 'Indica tu nombre completo para la entrega.',
            'celular.required' => 'El número celular es obligatorio.',
            'celular.regex' => 'Faltan dígitos (10). Debe tener exactamente 10 dígitos numéricos.',
            'ciudad.required' => 'Indica la ciudad de destino.',
            'direccion.required' => 'Falta tu dirección de entrega.',
            'direccion.min' => 'Falta tu dirección completa (calle, número, apto).',
            'metodo_pago.required' => 'Selecciona un método de pago.',
            'acepto_terminos.accepted' => 'Debes aceptar los términos y condiciones de compra.',
        ]);

        $user = Auth::user();

        // 1. Verificar disponibilidad de stock para cada ítem en el carrito
        foreach ($items as $item) {
            $stockActual = $item->producto->inventario ? $item->producto->inventario->stock_disponible : 0;
            if ($item->cantidad > $stockActual) {
                return redirect()->route('cart.index')->with('stock_insuficiente', [
                    'producto' => $item->producto->nombre,
                    'disponible' => $stockActual,
                    'mensaje' => "La disponibilidad de {$item->producto->nombre} cambió mientras comprabas. Solo quedan {$stockActual} unidades.",
                ]);
            }
        }

        // 2. Variante: Simulación de fallo en pasarela de pago (Punto 3 - Variante Pago rechazado)
        if ($request->boolean('simular_pago_fallido')) {
            return back()->withInput()->with('pago_rechazado', 'Tu banco no aprobó el pago. No se te cobró nada. Prueba de nuevo o con otro medio.');
        }

        // 3. Procesar pedido con transacción en BD
        $totales = $this->cartService->getTotals();

        $pedido = DB::transaction(function () use ($user, $validated, $totales, $items) {
            // Guardar o actualizar dirección del usuario
            $direccion = Direccion::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'direccion' => $validated['direccion'],
                    'ciudad' => $validated['ciudad'],
                ],
                [
                    'telefono_contacto' => $validated['celular'],
                    'es_principal' => true,
                ]
            );

            // Actualizar teléfono del usuario si cambió
            if ($user->telefono !== $validated['celular']) {
                $user->telefono = $validated['celular'];
                $user->save();
            }

            // Generar número de pedido único SPK-YYYY-XXXX
            $numPedido = 'SPK-'.date('Y').'-'.str_pad((string) (Pedido::count() + 1), 4, '0', STR_PAD_LEFT);

            $pedido = Pedido::create([
                'numero_pedido' => $numPedido,
                'user_id' => $user->id,
                'direccion_id' => $direccion->id,
                'direccion_envio' => $validated['direccion'],
                'ciudad_envio' => $validated['ciudad'],
                'telefono_contacto' => $validated['celular'],
                'estado' => 'pagado',
                'subtotal' => $totales['subtotal'],
                'costo_envio' => $totales['costo_envio'],
                'total' => $totales['total'],
                'puntos_ganados' => $totales['puntos_estimados'],
                'puntos_canjeados' => 0,
                'descuento' => 0,
                'metodo_pago' => $validated['metodo_pago'],
                'estado_pago' => 'aprobado',
                'referencia_pasarela' => 'REF-'.strtoupper(bin2hex(random_bytes(5))),
                'transportadora' => 'Coordinadora Mercantil',
                'guia_transporte' => null,
                'fecha_estimada_entrega' => now()->addDays(3),
                'acepta_terminos' => true,
            ]);

            // Guardar ítems, descontar inventario y registrar movimientos
            foreach ($items as $item) {
                PedidoItem::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $item->producto_id,
                    'cantidad' => $item->cantidad,
                    'precio_unitario' => $item->producto->precio,
                ]);

                // Descontar inventario disponible
                $inventario = Inventario::where('producto_id', $item->producto_id)->lockForUpdate()->first();
                if ($inventario) {
                    $inventario->stock_disponible -= $item->cantidad;
                    $inventario->save();

                    MovimientoInventario::create([
                        'producto_id' => $item->producto_id,
                        'tipo' => 'venta',
                        'cantidad' => $item->cantidad,
                        'motivo' => "Venta aprobada en pedido {$pedido->numero_pedido}",
                        'user_id' => $user->id,
                        'usuario_nombre' => $user->name,
                    ]);
                }
            }

            // Registrar movimiento de puntos con estado PENDIENTE (se acredita al entregar)
            MovimientoPuntos::create([
                'user_id' => $user->id,
                'pedido_id' => $pedido->id,
                'tipo' => 'ganado',
                'puntos' => $totales['puntos_estimados'],
                'estado' => 'pendiente',
                'motivo' => "Puntos por pedido {$pedido->numero_pedido} (se acreditan al entregar)",
            ]);

            return $pedido;
        });

        // Limpiar carrito tras la compra exitosa
        $this->cartService->clearCart();

        return redirect()->route('checkout.confirmacion', $pedido->numero_pedido);
    }

    public function confirmacion(string $numeroPedido): View
    {
        $pedido = Pedido::with(['items.producto', 'user'])
            ->where('numero_pedido', $numeroPedido)
            ->firstOrFail();

        return view('pages.confirmacion', compact('pedido'));
    }
}
