@extends('layouts.app')

@section('title', 'Mi Carrito · SPARKLE')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="font-serif text-3xl sm:text-4xl font-bold text-[#231F20] mb-8">Mi Carrito de Compras</h1>

    <!-- Variante: Stock Insuficiente (Punto 3 - Página 10) -->
    @if(session('stock_insuficiente'))
        @php $stockInfo = session('stock_insuficiente'); @endphp
        <div class="mb-8 p-5 bg-amber-50 border-l-4 border-amber-500 rounded-r-xl">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <div>
                    <h3 class="text-sm font-bold text-amber-900">La disponibilidad de un producto cambió mientras comprabas</h3>
                    <p class="text-xs text-amber-800 mt-1">{{ $stockInfo['mensaje'] }}</p>
                    <p class="text-xs text-amber-700 mt-1 font-medium">Ajusta la cantidad o elige otro aroma. Tus demás productos siguen guardados en el carrito.</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('stock_warning'))
        <div class="mb-8 p-4 bg-amber-50 border border-amber-300 rounded-lg text-amber-800 text-xs font-semibold">
            {{ session('stock_warning') }}
        </div>
    @endif

    @if($items->isEmpty())
        <div class="bg-white rounded-2xl border border-[#E8E2DA] p-12 text-center max-w-lg mx-auto space-y-4">
            <div class="text-4xl">🛍️</div>
            <h2 class="font-serif text-2xl font-bold text-stone-900">Tu carrito está vacío</h2>
            <p class="text-xs text-stone-500">Aún no has agregado difusores ni recargas a tu carrito.</p>
            <div class="pt-4 flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('products.index') }}" class="btn-terracotta px-6 py-3 rounded-full text-xs font-bold">
                    Explorar Catálogo
                </a>
                <a href="{{ route('guia.index') }}" class="btn-outline-terracotta px-6 py-3 rounded-full text-xs font-bold">
                    Hacer Guía de Aromas
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
            <!-- Lista de Ítems -->
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white rounded-2xl border border-[#E8E2DA] overflow-hidden shadow-sm">
                    <div class="p-6 divide-y divide-stone-100">
                        @foreach($items as $item)
                            <div class="py-6 first:pt-0 last:pb-0 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <img src="{{ $item->producto->foto }}" alt="{{ $item->producto->nombre }}" class="w-20 h-20 rounded-xl object-cover border border-stone-200">
                                    <div>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#A3542E]">{{ $item->producto->aroma }} · {{ $item->producto->tipo }}</span>
                                        <h3 class="font-serif text-lg font-bold text-stone-900">
                                            <a href="{{ route('products.show', $item->producto->slug) }}" class="hover:underline">
                                                {{ $item->producto->nombre }}
                                            </a>
                                        </h3>
                                        <div class="text-xs font-bold text-stone-700 mt-1">
                                            ${{ number_format($item->producto->precio, 0, ',', '.') }} COP c/u
                                        </div>
                                        <div class="text-[11px] text-stone-400 mt-0.5">
                                            Stock actual: {{ $item->producto->stock_disponible }} unidades
                                        </div>
                                    </div>
                                </div>

                                <!-- Modificador de Cantidad y Subtotal -->
                                <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto">
                                    <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center border border-stone-300 rounded-lg overflow-hidden bg-stone-50">
                                        @csrf
                                        <button type="submit" name="cantidad" value="{{ $item->cantidad - 1 }}" 
                                                class="px-2.5 py-1 text-stone-600 hover:bg-stone-200 text-sm font-bold" title="Reducir">
                                            −
                                        </button>
                                        <span class="px-3 py-1 text-xs font-bold text-stone-900 bg-white">
                                            {{ $item->cantidad }}
                                        </span>
                                        <button type="submit" name="cantidad" value="{{ $item->cantidad + 1 }}" 
                                                class="px-2.5 py-1 text-stone-600 hover:bg-stone-200 text-sm font-bold" title="Aumentar"
                                                {{ $item->cantidad >= $item->producto->stock_disponible ? 'disabled' : '' }}>
                                            +
                                        </button>
                                    </form>

                                    <div class="text-right">
                                        <span class="text-sm font-bold text-stone-900 block">
                                            ${{ number_format($item->subtotal, 0, ',', '.') }} COP
                                        </span>
                                        <form action="{{ route('cart.destroy', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-[11px] text-red-500 hover:underline">Eliminar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-between items-center text-xs text-stone-500 px-2">
                    <a href="{{ route('products.index') }}" class="hover:text-stone-900 font-medium">← Seguir explorando catálogo</a>
                    <span>Precios en pesos colombianos (COP)</span>
                </div>
            </div>

            <!-- Resumen de Pedido -->
            <div class="bg-white rounded-2xl border border-[#E8E2DA] p-6 sm:p-8 shadow-sm space-y-6">
                <h2 class="font-serif text-2xl font-bold text-stone-900">Resumen del Pedido</h2>

                <div class="space-y-3 text-sm divide-y divide-stone-100">
                    <div class="flex justify-between text-stone-600 pb-2">
                        <span>Subtotal ({{ $totales['total_items'] }} productos)</span>
                        <span class="font-bold text-stone-900">${{ number_format($totales['subtotal'], 0, ',', '.') }} COP</span>
                    </div>

                    <div class="flex justify-between text-stone-600 pt-3 pb-2">
                        <span>Envío nacional</span>
                        <span>
                            @if($totales['costo_envio'] === 0)
                                <strong class="text-emerald-700 font-bold uppercase text-xs">Gratis</strong>
                            @else
                                <span class="font-bold text-stone-900">${{ number_format($totales['costo_envio'], 0, ',', '.') }} COP</span>
                            @endif
                        </span>
                    </div>

                    <div class="flex justify-between items-center pt-4 text-base font-bold text-stone-900">
                        <span>Total estimado</span>
                        <span class="text-xl text-[#A3542E]">${{ number_format($totales['total'], 0, ',', '.') }} COP</span>
                    </div>
                </div>

                <!-- Recompensa de Puntos Estimados -->
                <div class="p-3 bg-[#FAF8F5] border border-[#E8E2DA] rounded-xl text-xs flex items-center gap-2">
                    <span class="text-[#A3542E] text-base font-bold">✨</span>
                    <div>
                        <strong class="text-stone-900">+{{ $totales['puntos_estimados'] }} puntos con este pedido</strong>
                        <p class="text-stone-500 text-[11px]">Se acreditarán automáticamente cuando tu pedido sea entregado.</p>
                    </div>
                </div>

                <!-- Botón de Finalizar Compra -->
                <div class="space-y-3 pt-2">
                    @auth
                        <a href="{{ route('checkout.index') }}" class="btn-terracotta w-full py-4 rounded-full font-bold text-sm tracking-wide text-center block shadow">
                            Continuar al Checkout →
                        </a>
                    @else
                        <!-- Redirección con login o registro según flujo Página 3/4 -->
                        <div class="space-y-2">
                            <a href="{{ route('checkout.index') }}" class="btn-terracotta w-full py-4 rounded-full font-bold text-sm tracking-wide text-center block shadow">
                                Iniciar Sesión para Pagar →
                            </a>
                            <p class="text-[11px] text-stone-500 text-center">
                                ¿No tienes cuenta? <a href="{{ route('register', ['redirect' => 'checkout']) }}" class="text-[#A3542E] font-bold underline">Regístrate aquí</a> y recibe 50 puntos de bienvenida.
                            </p>
                        </div>
                    @endauth
                </div>

                <div class="text-[11px] text-stone-400 space-y-1 text-center pt-2">
                    <p>🔒 Pagos seguros y cifrados (Tarjeta, PSE, Billeteras digitales)</p>
                    <p>📦 Garantía de satisfacción y cambios fáciles</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
