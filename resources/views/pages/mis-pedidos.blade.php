@extends('layouts.app')

@section('title', 'Mis Pedidos y Seguimiento · SPARKLE')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="font-serif text-3xl sm:text-4xl font-bold text-[#231F20]">Mis Pedidos y Seguimiento</h1>
            <p class="text-xs sm:text-sm text-stone-600 mt-1">Revisa el estado en tiempo real de tus envíos y solicita recargas o garantías.</p>
        </div>
        <a href="{{ route('loyalty.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#F7EBE4] text-[#A3542E] rounded-full text-xs font-bold hover:bg-[#ebd9ce] transition">
            <span>✨</span> Mis Puntos: {{ Auth::user()->puntos_disponibles }} pts
        </a>
    </div>

    @if($pedidos->isEmpty())
        <div class="bg-white rounded-2xl border border-[#E8E2DA] p-12 text-center max-w-md mx-auto space-y-4">
            <div class="text-3xl">📦</div>
            <h2 class="font-serif text-xl font-bold text-stone-900">Aún no tienes pedidos registrados</h2>
            <p class="text-xs text-stone-500">Cuando realices una compra podrás hacer seguimiento a la guía de transporte y acumular puntos aquí.</p>
            <a href="{{ route('products.index') }}" class="btn-terracotta inline-block px-6 py-3 rounded-full text-xs font-bold">
                Ir a la Tienda
            </a>
        </div>
    @else
        <div class="space-y-6">
            @foreach($pedidos as $pedido)
                <div class="bg-white rounded-2xl border border-[#E8E2DA] p-6 sm:p-8 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-stone-100">
                        <div>
                            <div class="flex items-center gap-3">
                                <span class="font-mono font-bold text-base text-stone-900">#{{ $pedido->numero_pedido }}</span>
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                                    {{ $pedido->estado === 'entregado' ? 'bg-emerald-100 text-emerald-800' :
                                       ($pedido->estado === 'enviado' ? 'bg-blue-100 text-blue-800' :
                                       ($pedido->estado === 'en_preparacion' ? 'bg-amber-100 text-amber-800' :
                                       ($pedido->estado === 'cancelado' ? 'bg-red-100 text-red-800' : 'bg-stone-100 text-stone-800'))) }}">
                                    {{ str_replace('_', ' ', $pedido->estado) }}
                                </span>
                            </div>
                            <span class="text-xs text-stone-400 block mt-1">Realizado el {{ $pedido->created_at->format('d/m/Y h:i A') }}</span>
                        </div>

                        <div class="text-left sm:text-right">
                            <span class="text-xs text-stone-500 block">Total pagado</span>
                            <span class="text-lg font-bold text-stone-900">${{ number_format($pedido->total, 0, ',', '.') }} COP</span>
                        </div>
                    </div>

                    <!-- Lista de ítems comprimida -->
                    <div class="space-y-2">
                        @foreach($pedido->items as $item)
                            <div class="flex items-center justify-between text-xs text-stone-700">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-stone-900">{{ $item->producto->nombre }}</span>
                                    <span class="text-stone-400">×{{ $item->cantidad }}</span>
                                </div>
                                <span class="font-bold">${{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Datos de Envío y Acciones -->
                    <div class="pt-4 border-t border-stone-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 text-xs">
                        <div class="text-stone-600">
                            @if($pedido->guia_transporte)
                                <span>🚚 <strong>{{ $pedido->transportadora }}:</strong> Guía #{{ $pedido->guia_transporte }}</span>
                            @else
                                <span>⏳ En preparación para despacho en taller SPARKLE</span>
                            @endif
                        </div>

                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <form action="{{ route('orders.repetir', $pedido->numero_pedido) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-outline-terracotta px-4 py-2 rounded-lg text-xs font-semibold">
                                    Repetir Pedido
                                </button>
                            </form>
                            <a href="{{ route('orders.show', $pedido->numero_pedido) }}" class="btn-terracotta px-4 py-2 rounded-lg text-xs font-semibold">
                                Ver Seguimiento Completo →
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            <div>
                {{ $pedidos->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
