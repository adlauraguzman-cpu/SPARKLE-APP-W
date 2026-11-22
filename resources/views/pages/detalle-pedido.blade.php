@extends('layouts.app')

@section('title', 'Seguimiento de Pedido #' . $pedido->numero_pedido . ' · SPARKLE')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">

    <!-- Header y Volver -->
    <div class="flex items-center justify-between">
        <a href="{{ route('orders.index') }}" class="text-xs font-semibold text-stone-500 hover:text-stone-900 flex items-center gap-1">
            <span>‹</span> Volver a mis pedidos
        </a>
        <span class="text-xs text-stone-400">Detalle y Estados</span>
    </div>

    <!-- Tarjeta Principal con Stepper de Estados -->
    <div class="bg-white rounded-2xl border border-[#E8E2DA] p-6 sm:p-10 shadow-sm space-y-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-stone-100">
            <div>
                <span class="text-xs font-bold text-[#A3542E] uppercase tracking-wider">Seguimiento en Vivo</span>
                <h1 class="font-serif text-3xl font-bold text-stone-900 mt-1">Pedido #{{ $pedido->numero_pedido }}</h1>
                <p class="text-xs text-stone-500 mt-0.5">Fecha: {{ $pedido->created_at->format('d/m/Y h:i A') }} · Pago: {{ strtoupper($pedido->metodo_pago) }} (Aprobado)</p>
            </div>

            <span class="px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider
                {{ $pedido->estado === 'entregado' ? 'bg-emerald-100 text-emerald-800' :
                   ($pedido->estado === 'enviado' ? 'bg-blue-100 text-blue-800' :
                   ($pedido->estado === 'en_preparacion' ? 'bg-amber-100 text-amber-800' :
                   ($pedido->estado === 'cancelado' ? 'bg-red-100 text-red-800' : 'bg-stone-100 text-stone-800'))) }}">
                {{ str_replace('_', ' ', $pedido->estado) }}
            </span>
        </div>

        <!-- 5-State Visual Stepper (Flujo funcional de la compra - Página 4 & 6) -->
        @if($pedido->estado !== 'cancelado')
            @php
                $estadosOrden = ['pagado', 'en_preparacion', 'enviado', 'entregado'];
                $indiceActual = array_search($pedido->estado, $estadosOrden);
            @endphp
            <div>
                <div class="grid grid-cols-4 gap-2 text-center text-xs">
                    <!-- 1. Pagado -->
                    <div class="space-y-2">
                        <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center font-bold {{ $indiceActual >= 0 ? 'bg-[#A3542E] text-white' : 'bg-stone-200 text-stone-500' }}">
                            1
                        </div>
                        <span class="font-bold {{ $indiceActual >= 0 ? 'text-stone-900' : 'text-stone-400' }}">Pagado</span>
                    </div>

                    <!-- 2. En preparación -->
                    <div class="space-y-2">
                        <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center font-bold {{ $indiceActual >= 1 ? 'bg-[#A3542E] text-white' : 'bg-stone-200 text-stone-500' }}">
                            2
                        </div>
                        <span class="font-bold {{ $indiceActual >= 1 ? 'text-stone-900' : 'text-stone-400' }}">En Preparación</span>
                    </div>

                    <!-- 3. Enviado -->
                    <div class="space-y-2">
                        <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center font-bold {{ $indiceActual >= 2 ? 'bg-[#A3542E] text-white' : 'bg-stone-200 text-stone-500' }}">
                            3
                        </div>
                        <span class="font-bold {{ $indiceActual >= 2 ? 'text-stone-900' : 'text-stone-400' }}">Enviado</span>
                    </div>

                    <!-- 4. Entregado -->
                    <div class="space-y-2">
                        <div class="w-8 h-8 mx-auto rounded-full flex items-center justify-center font-bold {{ $indiceActual >= 3 ? 'bg-emerald-600 text-white' : 'bg-stone-200 text-stone-500' }}">
                            ✓
                        </div>
                        <span class="font-bold {{ $indiceActual >= 3 ? 'text-emerald-700' : 'text-stone-400' }}">Entregado</span>
                    </div>
                </div>
            </div>
        @else
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-xs font-semibold">
                Este pedido fue cancelado. Razón: {{ $pedido->solicitud_cambio_devolucion ?? 'Anulación de orden' }}
            </div>
        @endif

        <!-- Información de Envío y Guía de Transporte -->
        <div class="bg-[#FAF8F5] p-6 rounded-xl border border-[#E8E2DA] grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs text-stone-700">
            <div>
                <span class="font-bold text-stone-900 block mb-1">Dirección de Entrega:</span>
                <p>{{ $pedido->direccion_envio }}</p>
                <p>{{ $pedido->ciudad_envio }} · Tel: {{ $pedido->telefono_contacto }}</p>
            </div>
            <div>
                <span class="font-bold text-stone-900 block mb-1">Guía y Transportadora:</span>
                @if($pedido->guia_transporte)
                    <p class="font-bold text-stone-900">{{ $pedido->transportadora }}</p>
                    <p class="font-mono bg-white px-2 py-1 rounded border border-stone-200 inline-block mt-1">Guía: {{ $pedido->guia_transporte }}</p>
                @else
                    <p class="text-stone-500 italic">Tu guía será generada en cuanto el pedido salga de nuestro centro de preparación.</p>
                @endif
            </div>
        </div>

        <!-- Puntos del Pedido -->
        <div class="p-4 bg-white rounded-xl border border-stone-200 flex items-center justify-between text-xs">
            <div class="flex items-center gap-2">
                <span class="text-[#A3542E] text-base">✨</span>
                <span>Puntos acumulados con este pedido: <strong>+{{ $pedido->puntos_ganados }} pts</strong></span>
            </div>
            @if($pedido->estado === 'entregado')
                <span class="px-2.5 py-1 rounded bg-emerald-100 text-emerald-800 font-bold">Acreditados en tu cuenta ✓</span>
            @else
                <span class="px-2.5 py-1 rounded bg-amber-100 text-amber-800 font-bold">Pendientes (se acreditan al entregar)</span>
            @endif
        </div>

        <!-- Lista de Productos en el Pedido -->
        <div class="space-y-4">
            <h3 class="font-serif text-xl font-bold text-stone-900">Productos del Pedido</h3>
            <div class="divide-y divide-stone-100">
                @foreach($pedido->items as $item)
                    <div class="py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-center gap-4">
                            <img src="{{ $item->producto->foto }}" alt="{{ $item->producto->nombre }}" class="w-16 h-16 rounded-xl object-cover border border-stone-200">
                            <div>
                                <h4 class="font-serif text-base font-bold text-stone-900">{{ $item->producto->nombre }}</h4>
                                <span class="text-xs text-stone-500">Cantidad: {{ $item->cantidad }} × ${{ number_format($item->precio_unitario, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Si el pedido fue entregado: Permite dejar Reseña y Ofrecer Recarga (Punto 6) -->
                        <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                            @if($pedido->estado === 'entregado')
                                @php
                                    $yaResenado = $item->producto->resenas->isNotEmpty();
                                @endphp
                                @if(!$yaResenado)
                                    <button type="button" onclick="document.getElementById('modal-resena-{{ $item->producto_id }}').classList.remove('hidden')"
                                            class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-xs font-bold transition">
                                        ★ Escribir Reseña
                                    </button>
                                @else
                                    <span class="text-xs text-emerald-700 font-bold">✓ Reseña enviada</span>
                                @endif

                                <a href="{{ route('products.index', ['tipo' => 'Refill', 'aroma' => $item->producto->aroma]) }}"
                                   class="px-3 py-1.5 bg-[#FAF8F5] border border-[#A3542E] text-[#A3542E] hover:bg-[#F7EBE4] rounded-lg text-xs font-bold transition">
                                    Recomprar Recarga
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Modal para Escribir Reseña (Punto 2 & Punto 6) -->
                    <div id="modal-resena-{{ $item->producto_id }}" class="hidden fixed inset-0 bg-stone-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl space-y-4">
                            <div class="flex justify-between items-center">
                                <h4 class="font-serif text-lg font-bold text-stone-900">Reseñar {{ $item->producto->nombre }}</h4>
                                <button type="button" onclick="document.getElementById('modal-resena-{{ $item->producto_id }}').classList.add('hidden')" class="text-stone-400 hover:text-stone-700 font-bold">✕</button>
                            </div>

                            <form action="{{ route('reviews.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="producto_id" value="{{ $item->producto_id }}">
                                <input type="hidden" name="pedido_id" value="{{ $pedido->id }}">

                                <div>
                                    <label class="block text-xs font-semibold text-stone-700 mb-1">Calificación en Estrellas:</label>
                                    <select name="calificacion" required class="w-full px-3 py-2 border border-stone-300 rounded-lg text-sm bg-white">
                                        <option value="5">★★★★★ (5 estrellas - Excelente)</option>
                                        <option value="4">★★★★☆ (4 estrellas - Muy bueno)</option>
                                        <option value="3">★★★☆☆ (3 estrellas - Bueno)</option>
                                        <option value="2">★★☆☆☆ (2 estrellas - Regular)</option>
                                        <option value="1">★☆☆☆☆ (1 estrella - Malo)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-xs font-semibold text-stone-700 mb-1">Tu Experiencia:</label>
                                    <textarea name="comentario" rows="3" required placeholder="Cuéntanos sobre la intensidad del aroma, duración y presentación..."
                                              class="w-full px-3 py-2 border border-stone-300 rounded-lg text-xs focus:border-[#A3542E]"></textarea>
                                </div>

                                <div class="flex items-center gap-2">
                                    <input type="checkbox" name="autoriza_publicacion" value="1" checked id="aut_{{ $item->producto_id }}" class="rounded text-[#A3542E]">
                                    <label for="aut_{{ $item->producto_id }}" class="text-[11px] text-stone-500">Autorizo publicar mi nombre y opinión en la ficha del producto.</label>
                                </div>

                                <div class="flex gap-3 pt-2">
                                    <button type="submit" class="btn-terracotta flex-1 py-2.5 rounded-lg text-xs font-bold">
                                        Publicar Reseña
                                    </button>
                                    <button type="button" onclick="document.getElementById('modal-resena-{{ $item->producto_id }}').classList.add('hidden')" class="btn-outline-terracotta px-4 py-2.5 rounded-lg text-xs font-semibold">
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Solicitud de Cambio o Devolución (Punto 1 & Punto 8) -->
        <div class="pt-6 border-t border-stone-100">
            <details class="group bg-stone-50 rounded-xl p-4 border border-stone-200">
                <summary class="cursor-pointer text-xs font-bold text-stone-700 flex items-center justify-between">
                    <span>¿Necesitas un cambio de aroma, garantía o devolución para este pedido?</span>
                    <span class="group-open:rotate-180 transition">▼</span>
                </summary>
                <form action="{{ route('orders.cambio', $pedido->numero_pedido) }}" method="POST" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label for="motivo_cambio" class="block text-xs text-stone-600 mb-1">Explícanos el motivo de tu solicitud:</label>
                        <textarea name="motivo" id="motivo_cambio" rows="3" required
                                  placeholder="Ej: Deseo cambiar la recarga por aroma Helios o el frasco llegó con novedad..."
                                  class="w-full px-3 py-2 text-xs border border-stone-300 rounded-lg focus:border-[#A3542E] bg-white">{{ $pedido->solicitud_cambio_devolucion }}</textarea>
                    </div>
                    <button type="submit" class="btn-terracotta px-5 py-2 rounded-lg text-xs font-bold">
                        Enviar Solicitud de Cambio
                    </button>
                </form>
            </details>
        </div>
    </div>
</div>
@endsection
