@extends('layouts.app')

@section('title', 'Pedido Confirmado · SPARKLE')

@section('content')
<div class="max-w-xl mx-auto px-4 sm:px-6 py-12">

    <!-- Alerta Verde con Check (Punto 3 - Estado 3 · Éxito / Success) -->
    <div class="mb-6 p-4 bg-[#EBF5EF] border border-[#A5D6A7] rounded-xl text-[#2E7D32] flex items-center gap-3 shadow-sm">
        <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        <span class="text-sm font-semibold">¡Tu solicitud fue enviada con éxito! Ya estamos preparando tu pedido.</span>
    </div>

    <!-- Tarjeta Principal de Pedido Confirmado -->
    <div class="bg-white rounded-2xl border border-[#E8E2DA] p-8 sm:p-10 shadow-sm text-center space-y-6">

        <!-- Checkmark Grande -->
        <div class="w-16 h-16 rounded-full bg-[#EBF5EF] text-[#2E7D32] mx-auto flex items-center justify-center text-3xl font-bold">
            ✓
        </div>

        <div>
            <span class="text-xs font-bold text-[#A3542E] uppercase tracking-wider">Compra Finalizada</span>
            <h1 class="font-serif text-3xl sm:text-4xl font-bold text-stone-900 mt-1">¡Gracias, {{ explode(' ', $pedido->user->name)[0] }}!</h1>
            <p class="text-sm text-stone-600 mt-1">Tu pedido está confirmado y en cola de empaque</p>
            <div class="mt-3 inline-block px-4 py-1.5 rounded-full bg-stone-100 border border-stone-200 text-xs font-mono font-bold text-stone-800">
                Pedido #{{ $pedido->numero_pedido }}
            </div>
        </div>

        <!-- Resumen de Contenido y Datos de Envío -->
        <div class="bg-[#FAF8F5] rounded-xl p-6 text-left border border-[#E8E2DA] space-y-3 text-xs text-stone-700">
            <div class="divide-y divide-stone-200">
                @foreach($pedido->items as $item)
                    <div class="py-2 first:pt-0 last:pb-0 flex justify-between items-center">
                        <span class="font-medium text-stone-900">{{ $item->producto->nombre }} ×{{ $item->cantidad }}</span>
                        <span class="font-bold">${{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            <div class="pt-3 border-t border-stone-200 flex justify-between font-bold text-sm text-stone-900">
                <span>Total pagado ({{ strtoupper($pedido->metodo_pago) }})</span>
                <span class="text-[#A3542E]">${{ number_format($pedido->total, 0, ',', '.') }} COP</span>
            </div>

            <div class="pt-3 border-t border-stone-200 space-y-1.5 text-stone-600">
                <div class="flex items-center gap-2">
                    <span>📍</span>
                    <span>Entrega en <strong>{{ $pedido->ciudad_envio }}</strong> · Estimado: 2 a 3 días hábiles</span>
                </div>
                <div class="flex items-center gap-2">
                    <span>💬</span>
                    <span>Confirmación y número de guía enviados por correo y WhatsApp ({{ $pedido->telefono_contacto }})</span>
                </div>
            </div>
        </div>

        <!-- Puntos Pendientes de Acreditación (Punto 2 & Punto 6) -->
        <div class="p-3 bg-[#FAF8F5] rounded-xl border border-[#E8E2DA] text-xs flex items-center justify-center gap-2 text-[#A3542E] font-bold">
            <span>✨</span>
            <span>+{{ $pedido->puntos_ganados }} puntos · se acreditan automáticamente al entregar</span>
        </div>

        <!-- Dos Salidas Claras (Punto 3 - Mockup Página 9) -->
        <div class="pt-4 space-y-3">
            <a href="{{ route('orders.show', $pedido->numero_pedido) }}" 
               class="btn-terracotta w-full py-4 rounded-full font-bold text-sm tracking-wide text-center block shadow">
                Ver mi pedido y seguimiento
            </a>

            <a href="{{ route('products.index') }}" 
               class="btn-outline-terracotta w-full py-3.5 rounded-full font-bold text-sm tracking-wide text-center block">
                Seguir explorando la tienda
            </a>
        </div>
    </div>
</div>
@endsection
