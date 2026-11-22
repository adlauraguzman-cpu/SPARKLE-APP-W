<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('direcciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('direccion');
            $table->string('ciudad')->default('Bogotá');
            $table->string('telefono_contacto')->nullable();
            $table->boolean('es_principal')->default(false);
            $table->timestamps();
        });

        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->string('tipo'); // Kit, Dúo, Gift, Refill
            $table->string('aroma'); // ALPHA, HELIOS, JAZ, MULTI
            $table->text('descripcion');
            $table->decimal('precio', 10, 2);
            $table->string('foto')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('inventarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->unique()->constrained('productos')->cascadeOnDelete();
            $table->integer('stock_disponible')->default(0);
            $table->integer('stock_reservado')->default(0);
            $table->integer('umbral_minimo')->default(5);
            $table->timestamps();
        });

        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->string('tipo'); // entrada, venta, ajuste, merma, reserva_liberada
            $table->integer('cantidad');
            $table->string('motivo');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('usuario_nombre')->nullable();
            $table->timestamps();
        });

        Schema::create('carrito_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('session_id')->nullable()->index();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->integer('cantidad')->default(1);
            $table->timestamps();
        });

        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_pedido')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('direccion_id')->nullable()->constrained('direcciones')->nullOnDelete();
            $table->string('direccion_envio');
            $table->string('ciudad_envio')->default('Bogotá');
            $table->string('telefono_contacto');
            $table->string('estado')->default('pagado'); // pagado, en_preparacion, enviado, entregado, cancelado
            $table->decimal('subtotal', 10, 2);
            $table->decimal('costo_envio', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->integer('puntos_ganados')->default(0);
            $table->integer('puntos_canjeados')->default(0);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->string('metodo_pago')->default('tarjeta'); // tarjeta, pse, billetera
            $table->string('estado_pago')->default('aprobado'); // aprobado, rechazado, pendiente
            $table->string('referencia_pasarela')->nullable();
            $table->string('transportadora')->nullable();
            $table->string('guia_transporte')->nullable();
            $table->dateTime('fecha_estimada_entrega')->nullable();
            $table->boolean('acepta_terminos')->default(true);
            $table->text('solicitud_cambio_devolucion')->nullable();
            $table->timestamps();
        });

        Schema::create('pedido_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pedido_id')->constrained('pedidos')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->timestamps();
        });

        Schema::create('movimientos_puntos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->nullOnDelete();
            $table->string('tipo'); // ganado, canjeado, ajuste
            $table->integer('puntos');
            $table->string('estado')->default('pendiente'); // pendiente, acreditado
            $table->string('motivo')->nullable();
            $table->timestamps();
        });

        Schema::create('recompensas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('descripcion')->nullable();
            $table->integer('puntos_requeridos');
            $table->decimal('descuento_valor', 10, 2)->default(0);
            $table->date('vigencia')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });

        Schema::create('resenas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->nullOnDelete();
            $table->unsignedTinyInteger('calificacion'); // 1-5
            $table->text('comentario');
            $table->string('foto')->nullable();
            $table->boolean('autoriza_publicacion')->default(true);
            $table->string('estado')->default('publicada'); // pendiente, publicada
            $table->timestamps();
        });

        Schema::create('avisos_stock', function (Blueprint $table) {
            $table->id();
            $table->string('correo');
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->boolean('notificado')->default(false);
            $table->timestamps();
        });

        Schema::create('suscriptores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('correo')->unique();
            $table->boolean('autoriza_datos')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suscriptores');
        Schema::dropIfExists('avisos_stock');
        Schema::dropIfExists('resenas');
        Schema::dropIfExists('recompensas');
        Schema::dropIfExists('movimientos_puntos');
        Schema::dropIfExists('pedido_items');
        Schema::dropIfExists('pedidos');
        Schema::dropIfExists('carrito_items');
        Schema::dropIfExists('movimientos_inventario');
        Schema::dropIfExists('inventarios');
        Schema::dropIfExists('productos');
        Schema::dropIfExists('direcciones');
    }
};
