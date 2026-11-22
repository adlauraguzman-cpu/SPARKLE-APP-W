<?php

namespace Database\Seeders;

use App\Models\Direccion;
use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\MovimientoPuntos;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Producto;
use App\Models\Recompensa;
use App\Models\Resena;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Usuarios con roles (Visitante es público, no requiere usuario en bd salvo si se registra)
        $admin = User::firstOrCreate(
            ['email' => 'admin@sparkle.com'],
            [
                'name' => 'Laura Guzmán (Dueña)',
                'password' => Hash::make('password'),
                'telefono' => '3001234567',
                'rol' => 'administrador',
                'autoriza_datos' => true,
                'fecha_autoriza_datos' => now(),
                'puntos_disponibles' => 0,
                'activo' => true,
            ]
        );

        $colaborador = User::firstOrCreate(
            ['email' => 'operaciones@sparkle.com'],
            [
                'name' => 'Carlos Operaciones',
                'password' => Hash::make('password'),
                'telefono' => '3119876543',
                'rol' => 'colaborador',
                'autoriza_datos' => true,
                'fecha_autoriza_datos' => now(),
                'puntos_disponibles' => 0,
                'activo' => true,
            ]
        );

        $cliente = User::firstOrCreate(
            ['email' => 'cliente@sparkle.com'],
            [
                'name' => 'María Torres',
                'password' => Hash::make('password'),
                'telefono' => '3105550142',
                'rol' => 'cliente',
                'autoriza_datos' => true,
                'fecha_autoriza_datos' => now(),
                'puntos_disponibles' => 150,
                'activo' => true,
            ]
        );

        // Dirección principal del cliente
        $direccionCliente = Direccion::firstOrCreate(
            ['user_id' => $cliente->id, 'es_principal' => true],
            [
                'direccion' => 'Cra 15 # 80-20, Apto 301',
                'ciudad' => 'Bogotá',
                'telefono_contacto' => '3105550142',
            ]
        );

        // 2. Catálogo de Productos e Inventarios
        $productosData = [
            [
                'nombre' => 'Kit SPARKLE · ALPHA',
                'slug' => 'kit-sparkle-alpha',
                'tipo' => 'Kit',
                'aroma' => 'ALPHA',
                'descripcion' => 'Difusor artesanal con notas profundas de cedro, ámbar y especias cálidas. Diseñado para espacios con carácter, calma y elegancia.',
                'precio' => 120000,
                'foto' => 'https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?auto=format&fit=crop&w=800&q=80',
                'stock' => 12,
                'minimo' => 5,
            ],
            [
                'nombre' => 'Kit SPARKLE · HELIOS',
                'slug' => 'kit-sparkle-helios',
                'tipo' => 'Kit',
                'aroma' => 'HELIOS',
                'descripcion' => 'Inspirado en la luz matutina: destellos cítricos de bergamota mediterránea, flores de azahar y un fondo suave de té blanco.',
                'precio' => 120000,
                'foto' => 'https://images.unsplash.com/photo-1603006905003-be475563bc59?auto=format&fit=crop&w=800&q=80',
                'stock' => 18,
                'minimo' => 5,
            ],
            [
                'nombre' => 'Kit SPARKLE · JAZ',
                'slug' => 'kit-sparkle-jaz',
                'tipo' => 'Kit',
                'aroma' => 'JAZ',
                'descripcion' => 'Un bouquet floral sereno de jazmín nocturno silvestre y pétalos blancos con una brisa refrescante.',
                'precio' => 120000,
                'foto' => 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?auto=format&fit=crop&w=800&q=80',
                'stock' => 4, // Bajo umbral mínimo (umbral: 5) para probar alerta
                'minimo' => 5,
            ],
            [
                'nombre' => 'SPARKLE Refill · HELIOS',
                'slug' => 'sparkle-refill-helios',
                'tipo' => 'Refill',
                'aroma' => 'HELIOS',
                'descripcion' => 'Frasco de recarga ecológico de 250ml con esencia pura de Helios para prolongar la vida de tu difusor.',
                'precio' => 65000,
                'foto' => 'https://images.unsplash.com/photo-1616949755610-8c9bbc08f138?auto=format&fit=crop&w=800&q=80',
                'stock' => 25,
                'minimo' => 5,
            ],
            [
                'nombre' => 'SPARKLE Refill · ALPHA',
                'slug' => 'sparkle-refill-alpha',
                'tipo' => 'Refill',
                'aroma' => 'ALPHA',
                'descripcion' => 'Recarga de 250ml de esencia aromática amaderada para difusor de varillas.',
                'precio' => 65000,
                'foto' => 'https://images.unsplash.com/photo-1547887537-6158d64c35b3?auto=format&fit=crop&w=800&q=80',
                'stock' => 20,
                'minimo' => 5,
            ],
            [
                'nombre' => 'SPARKLE Refill · JAZ',
                'slug' => 'sparkle-refill-jaz',
                'tipo' => 'Refill',
                'aroma' => 'JAZ',
                'descripcion' => 'Recarga botánica de 250ml aroma Jazmín silvestre. (Actualmente agotado para probar solicitud de aviso).',
                'precio' => 65000,
                'foto' => 'https://images.unsplash.com/photo-1594035910387-fea47794261f?auto=format&fit=crop&w=800&q=80',
                'stock' => 0, // Agotado para probar vista agotado y aviso de stock
                'minimo' => 5,
            ],
            [
                'nombre' => 'Dúo Sensorial SPARKLE (ALPHA + HELIOS)',
                'slug' => 'duo-sensorial-sparkle',
                'tipo' => 'Dúo',
                'aroma' => 'MULTI',
                'descripcion' => 'La dualidad perfecta para hogar y oficina: la frescura vital de Helios de día y la calidez amaderada de Alpha al caer la tarde.',
                'precio' => 195000,
                'foto' => 'https://images.unsplash.com/photo-1602928321679-560bb453f190?auto=format&fit=crop&w=800&q=80',
                'stock' => 10,
                'minimo' => 3,
            ],
            [
                'nombre' => 'Gift Set SPARKLE Edición Colección',
                'slug' => 'gift-set-sparkle-edicion-coleccion',
                'tipo' => 'Gift',
                'aroma' => 'MULTI',
                'descripcion' => 'Caja de regalo de lujo con los tres aromas emblemáticos en frascos de diseño y juego de varitas de fibra de algodón natural.',
                'precio' => 260000,
                'foto' => 'https://images.unsplash.com/photo-1512496015851-a90fb38ba796?auto=format&fit=crop&w=800&q=80',
                'stock' => 8,
                'minimo' => 3,
            ],
        ];

        foreach ($productosData as $data) {
            $stock = $data['stock'];
            $minimo = $data['minimo'];
            unset($data['stock'], $data['minimo']);

            $producto = Producto::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );

            Inventario::updateOrCreate(
                ['producto_id' => $producto->id],
                [
                    'stock_disponible' => $stock,
                    'stock_reservado' => 0,
                    'umbral_minimo' => $minimo,
                ]
            );

            MovimientoInventario::firstOrCreate(
                [
                    'producto_id' => $producto->id,
                    'tipo' => 'entrada',
                    'cantidad' => $stock,
                ],
                [
                    'motivo' => 'Inventario inicial de apertura de tienda',
                    'user_id' => $admin->id,
                    'usuario_nombre' => $admin->name,
                ]
            );
        }

        // 3. Catálogo de Recompensas
        $recompensas = [
            [
                'nombre' => 'Bono de $15.000 COP en tu orden',
                'descripcion' => 'Descuento directo aplicable en cualquier compra superior a $60.000',
                'puntos_requeridos' => 100,
                'descuento_valor' => 15000,
                'vigencia' => now()->addMonths(6),
                'activa' => true,
            ],
            [
                'nombre' => 'Envío Gratis a nivel nacional',
                'descripcion' => 'Aplica para despacho en Bogotá o ciudades principales',
                'puntos_requeridos' => 50,
                'descuento_valor' => 12000,
                'vigencia' => now()->addMonths(6),
                'activa' => true,
            ],
            [
                'nombre' => 'Recarga SPARKLE Refill Gratis',
                'descripcion' => 'Canjea una recarga de 250ml de tu aroma favorito (Helios, Alpha o Jaz)',
                'puntos_requeridos' => 250,
                'descuento_valor' => 65000,
                'vigencia' => now()->addMonths(6),
                'activa' => true,
            ],
        ];

        foreach ($recompensas as $rec) {
            Recompensa::updateOrCreate(
                ['nombre' => $rec['nombre']],
                $rec
            );
        }

        // 4. Pedido histórico de María Torres (Entregado -> ya acreditó puntos y permite reseña)
        $kitAlpha = Producto::where('slug', 'kit-sparkle-alpha')->first();
        if ($kitAlpha) {
            $pedidoEntregado = Pedido::firstOrCreate(
                ['numero_pedido' => 'SPK-2026-001'],
                [
                    'user_id' => $cliente->id,
                    'direccion_id' => $direccionCliente->id,
                    'direccion_envio' => $direccionCliente->direccion,
                    'ciudad_envio' => $direccionCliente->ciudad,
                    'telefono_contacto' => $direccionCliente->telefono_contacto,
                    'estado' => 'entregado',
                    'subtotal' => 120000,
                    'costo_envio' => 12000,
                    'total' => 132000,
                    'puntos_ganados' => 132,
                    'puntos_canjeados' => 0,
                    'descuento' => 0,
                    'metodo_pago' => 'tarjeta',
                    'estado_pago' => 'aprobado',
                    'referencia_pasarela' => 'PAY-SPK-98124',
                    'transportadora' => 'Coordinadora',
                    'guia_transporte' => 'CRD-88992211',
                    'fecha_estimada_entrega' => now()->subDays(2),
                    'acepta_terminos' => true,
                ]
            );

            PedidoItem::firstOrCreate(
                [
                    'pedido_id' => $pedidoEntregado->id,
                    'producto_id' => $kitAlpha->id,
                ],
                [
                    'cantidad' => 1,
                    'precio_unitario' => 120000,
                ]
            );

            MovimientoPuntos::firstOrCreate(
                [
                    'user_id' => $cliente->id,
                    'pedido_id' => $pedidoEntregado->id,
                    'tipo' => 'ganado',
                ],
                [
                    'puntos' => 132,
                    'estado' => 'acreditado',
                    'motivo' => 'Puntos acumulados por pedido #SPK-2026-001 entregado con éxito',
                ]
            );

            // Reseña dejada por María
            Resena::firstOrCreate(
                [
                    'user_id' => $cliente->id,
                    'producto_id' => $kitAlpha->id,
                ],
                [
                    'pedido_id' => $pedidoEntregado->id,
                    'calificacion' => 5,
                    'comentario' => '¡Una maravilla! La presentación del kit es impecable y el aroma Alpha dura semanas completas en la sala. Es justo el toque de lujo que buscaba.',
                    'autoriza_publicacion' => true,
                    'estado' => 'publicada',
                ]
            );
        }

        // 5. Pedido en proceso para María (en_preparacion) para probar vistas operativas
        $kitHelios = Producto::where('slug', 'kit-sparkle-helios')->first();
        if ($kitHelios) {
            $pedidoPendiente = Pedido::firstOrCreate(
                ['numero_pedido' => 'SPK-2026-002'],
                [
                    'user_id' => $cliente->id,
                    'direccion_id' => $direccionCliente->id,
                    'direccion_envio' => $direccionCliente->direccion,
                    'ciudad_envio' => $direccionCliente->ciudad,
                    'telefono_contacto' => $direccionCliente->telefono_contacto,
                    'estado' => 'en_preparacion',
                    'subtotal' => 120000,
                    'costo_envio' => 12000,
                    'total' => 132000,
                    'puntos_ganados' => 132,
                    'puntos_canjeados' => 0,
                    'descuento' => 0,
                    'metodo_pago' => 'pse',
                    'estado_pago' => 'aprobado',
                    'referencia_pasarela' => 'PSE-SPK-44211',
                    'transportadora' => null,
                    'guia_transporte' => null,
                    'fecha_estimada_entrega' => now()->addDays(2),
                    'acepta_terminos' => true,
                ]
            );

            PedidoItem::firstOrCreate(
                [
                    'pedido_id' => $pedidoPendiente->id,
                    'producto_id' => $kitHelios->id,
                ],
                [
                    'cantidad' => 1,
                    'precio_unitario' => 120000,
                ]
            );

            MovimientoPuntos::firstOrCreate(
                [
                    'user_id' => $cliente->id,
                    'pedido_id' => $pedidoPendiente->id,
                    'tipo' => 'ganado',
                ],
                [
                    'puntos' => 132,
                    'estado' => 'pendiente',
                    'motivo' => 'Puntos pendientes de acreditación tras entrega de pedido #SPK-2026-002',
                ]
            );
        }
    }
}
