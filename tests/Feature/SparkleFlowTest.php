<?php

namespace Tests\Feature;

use App\Models\AvisoStock;
use App\Models\MovimientoPuntos;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SparkleFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_visitor_can_access_catalog_and_aroma_guide(): void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('SPARKLE');

        $responseCatalog = $this->get(route('products.index'));
        $responseCatalog->assertStatus(200);
        $responseCatalog->assertSee('Kit SPARKLE · ALPHA');

        $responseGuide = $this->get(route('guia.index'));
        $responseGuide->assertStatus(200);
        $responseGuide->assertSee('Encuentra tu Aroma Signature');
    }

    public function test_visitor_can_add_item_to_cart_and_view_it(): void
    {
        $producto = Producto::where('slug', 'kit-sparkle-alpha')->first();

        $response = $this->post(route('cart.store'), [
            'producto_id' => $producto->id,
            'cantidad' => 1,
        ]);

        $response->assertRedirect(route('cart.index'));

        $cartResponse = $this->get(route('cart.index'));
        $cartResponse->assertStatus(200);
        $cartResponse->assertSee($producto->nombre);
    }

    public function test_visitor_cannot_access_checkout_without_account(): void
    {
        $response = $this->get(route('checkout.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_registered_client_can_complete_checkout_and_generate_order(): void
    {
        $cliente = User::where('email', 'cliente@sparkle.com')->first();
        $producto = Producto::where('slug', 'kit-sparkle-alpha')->first();
        $stockInicial = $producto->inventario->stock_disponible;

        // Login as client and add to cart
        $this->actingAs($cliente);
        $this->post(route('cart.store'), [
            'producto_id' => $producto->id,
            'cantidad' => 1,
        ]);

        // Process checkout
        $response = $this->post(route('checkout.process'), [
            'nombre' => 'María Torres',
            'celular' => '3105550142',
            'ciudad' => 'Bogotá',
            'direccion' => 'Cra 15 # 80-20, Apto 301',
            'metodo_pago' => 'tarjeta',
            'acepto_terminos' => '1',
        ]);

        $pedido = Pedido::where('user_id', $cliente->id)->latest('id')->first();
        $this->assertNotNull($pedido);
        $response->assertRedirect(route('checkout.confirmacion', $pedido->numero_pedido));

        // Verify inventory decreased
        $producto->refresh();
        $this->assertEquals($stockInicial - 1, $producto->inventario->stock_disponible);

        // Verify pending loyalty points recorded
        $puntosPendientes = MovimientoPuntos::where('pedido_id', $pedido->id)->first();
        $this->assertNotNull($puntosPendientes);
        $this->assertEquals('pendiente', $puntosPendientes->estado);
    }

    public function test_checkout_handles_rejected_payment_variant(): void
    {
        $cliente = User::where('email', 'cliente@sparkle.com')->first();
        $producto = Producto::where('slug', 'kit-sparkle-alpha')->first();

        $this->actingAs($cliente);
        $this->post(route('cart.store'), [
            'producto_id' => $producto->id,
            'cantidad' => 1,
        ]);

        $response = $this->post(route('checkout.process'), [
            'nombre' => 'María Torres',
            'celular' => '3105550142',
            'ciudad' => 'Bogotá',
            'direccion' => 'Cra 15 # 80-20, Apto 301',
            'metodo_pago' => 'tarjeta',
            'acepto_terminos' => '1',
            'simular_pago_fallido' => '1',
        ]);

        $response->assertSessionHas('pago_rechazado');
    }

    public function test_colaborador_cannot_access_admin_only_management(): void
    {
        $colaborador = User::where('email', 'operaciones@sparkle.com')->first();
        $this->actingAs($colaborador);

        // Can access dashboard and inventory
        $this->get(route('admin.dashboard'))->assertStatus(200);
        $this->get(route('admin.inventario.index'))->assertStatus(200);

        // Cannot access users or product price management
        $this->get(route('admin.usuarios.index'))->assertStatus(403);
        $this->get(route('admin.productos.index'))->assertStatus(403);
    }

    public function test_marking_order_delivered_credits_points_to_client(): void
    {
        $admin = User::where('email', 'admin@sparkle.com')->first();
        $cliente = User::where('email', 'cliente@sparkle.com')->first();
        $puntosAntes = $cliente->puntos_disponibles;

        // Pedido en preparacion
        $pedido = Pedido::where('user_id', $cliente->id)->where('estado', 'en_preparacion')->first();
        $this->assertNotNull($pedido);

        $this->actingAs($admin);
        // Despachar
        $this->post(route('admin.pedidos.enviar', $pedido->id), [
            'transportadora' => 'Coordinadora Mercantil',
            'guia_transporte' => 'CRD-11223344',
        ]);

        $pedido->refresh();
        $this->assertEquals('enviado', $pedido->estado);

        // Marcar entregado
        $this->post(route('admin.pedidos.entregar', $pedido->id));

        $pedido->refresh();
        $this->assertEquals('entregado', $pedido->estado);

        // Verificar que los puntos fueron acreditados al cliente
        $cliente->refresh();
        $this->assertGreaterThan($puntosAntes, $cliente->puntos_disponibles);
    }

    public function test_stock_adjustment_updates_inventory_and_notifies_waiting_subscribers(): void
    {
        $admin = User::where('email', 'admin@sparkle.com')->first();
        $productoAgotado = Producto::where('slug', 'sparkle-refill-jaz')->first(); // Stock inicial 0 en seeder

        // Suscribir aviso de stock para este producto
        AvisoStock::create([
            'correo' => 'esperando@gmail.com',
            'producto_id' => $productoAgotado->id,
            'notificado' => false,
        ]);

        $this->actingAs($admin);
        $response = $this->post(route('admin.inventario.ajustar'), [
            'producto_id' => $productoAgotado->id,
            'tipo' => 'entrada',
            'cantidad' => 20,
            'motivo' => 'Llegada de nuevo lote de recargas Jazmín',
        ]);

        $response->assertRedirect();

        $productoAgotado->refresh();
        $this->assertEquals(20, $productoAgotado->inventario->stock_disponible);

        // Verificar que el aviso de stock fue marcado como notificado
        $aviso = AvisoStock::where('correo', 'esperando@gmail.com')->first();
        $this->assertTrue((bool) $aviso->notificado);
    }
}
