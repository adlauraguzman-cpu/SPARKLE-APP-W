@extends('layouts.admin')

@section('page_title', 'Atender Pedido #' . $pedido->numero_pedido)

@section('content')
<div class="max-w-4xl space-y-8">

    <div class="flex items-center justify-between">
        <a href="{{ route('admin.pedidos.index') }}" class="text-xs font-semibold text-stone-500 hover:text-stone-900 flex items-center gap-1">
            <span>‹</span> Volver al listado de pedidos
        </a>
        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
            {{ $pedido->estado === 'entregado' ? 'bg-emerald-100 text-emerald-800' :
               ($pedido->estado === 'enviado' ? 'bg-blue-100 text-blue-800' :
               ($pedido->estado === 'en_preparacion' ? 'bg-amber-100 text-amber-800' :
               ($pedido->estado === 'cancelado' ? 'bg-red-100 text-red-800' : 'bg-stone-100 text-stone-800'))) }}">
            Estado: {{ str_replace('_', ' ', $pedido->estado) }}
        </span>
    </div>

    <!-- Tarjeta Principal de Atención de Pedido -->
    <div class="bg-white rounded-2xl border border-stone-200 p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-stone-100">
            <div>
                <span class="text-xs font-bold text-[#A3542E] uppercase tracking-wider">Orden de Despacho</span>
                <h2 class="text-2xl font-bold text-stone-900 mt-1">Pedido #{{ $pedido->numero_pedido }}</h2>
                <span class="text-xs text-stone-400">Creado el {{ $pedido->created_at->format('d/m/Y h:i A') }}</span>
            </div>

            @if(!$isColaborador)
                <div class="text-left sm:text-right">
                    <span class="text-xs text-stone-500 block">Total de la Orden</span>
                    <span class="text-2xl font-bold text-stone-900">${{ number_format($pedido->total, 0, ',', '.') }} COP</span>
                    <span class="text-[11px] text-emerald-700 block font-semibold">Pago {{ strtoupper($pedido->metodo_pago) }} (Aprobado)</span>
                </div>
            @else
                <div class="p-2.5 bg-stone-100 rounded-lg text-xs text-stone-500 italic">
                    Vista operativa: datos comerciales sensibles ocultos según política de roles.
                </div>
            @endif
        </div>

        <!-- Datos del Destinatario (Solo lo necesario para empaque y despacho) -->
        <div class="bg-[#FAF8F5] p-6 rounded-xl border border-[#E8E2DA] grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs text-stone-700">
            <div>
                <span class="font-bold text-stone-900 block text-sm mb-1">Destinatario / Cliente:</span>
                <p class="text-sm font-semibold text-stone-800">{{ $pedido->user->name }}</p>
                <p class="text-stone-600 mt-0.5">WhatsApp / Celular: <strong class="text-stone-900">{{ $pedido->telefono_contacto }}</strong></p>
                <p class="text-stone-500">{{ $pedido->user->email }}</p>
            </div>
            <div>
                <span class="font-bold text-stone-900 block text-sm mb-1">Dirección de Entrega:</span>
                <p class="text-stone-900 font-semibold">{{ $pedido->direccion_envio }}</p>
                <p class="text-stone-700 font-bold mt-0.5">{{ $pedido->ciudad_envio }}</p>
            </div>
        </div>

        <!-- Ítems para Preparar y Empacar -->
        <div class="space-y-4">
            <h3 class="text-sm font-bold text-stone-900 uppercase tracking-wider">Ítems a Preparar (Revisar Stock Reservado)</h3>
            <div class="divide-y divide-stone-100 border border-stone-200 rounded-xl overflow-hidden">
                @foreach($pedido->items as $item)
                    <div class="p-4 bg-white flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <img src="{{ $item->producto->foto }}" alt="{{ $item->producto->nombre }}" class="w-12 h-12 rounded-lg object-cover border border-stone-200">
                            <div>
                                <h4 class="font-bold text-stone-900 text-sm">{{ $item->producto->nombre }}</h4>
                                <span class="text-xs text-stone-500">Aroma: {{ $item->producto->aroma }} · Tipo: {{ $item->producto->tipo }}</span>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="px-3 py-1 bg-stone-100 text-stone-900 font-bold text-sm rounded-lg">
                                Cantidad: {{ $item->cantidad }}
                            </span>
                            @if(!$isColaborador)
                                <span class="block text-xs font-semibold text-stone-600 mt-1">
                                    ${{ number_format($item->subtotal, 0, ',', '.') }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- FLUJO DE ACCIONES SEGÚN EL ESTADO DEL PEDIDO (PUNTO 6) -->
        <div class="pt-6 border-t border-stone-100 space-y-6">

            <!-- 1. Registrar Envío (De Pagado / En preparación -> Enviado) -->
            @if(in_array($pedido->estado, ['pagado', 'en_preparacion']))
                <div class="p-6 bg-blue-50/60 border border-blue-200 rounded-xl space-y-4">
                    <div>
                        <h4 class="text-sm font-bold text-blue-900">Registrar Envío con Transportadora y Guía</h4>
                        <p class="text-xs text-blue-800">Al guardar, el estado pasará a «Enviado» y se notificará al cliente por WhatsApp y correo.</p>
                    </div>

                    <form action="{{ route('admin.pedidos.enviar', $pedido->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="transportadora" class="block text-xs font-semibold text-stone-700 mb-1">Empresa Transportadora</label>
                                <input type="text" name="transportadora" id="transportadora" required
                                       value="{{ old('transportadora', $pedido->transportadora ?? 'Coordinadora Mercantil') }}"
                                       class="w-full px-3 py-2 text-xs border border-stone-300 rounded-lg bg-white focus:border-[#A3542E]">
                            </div>

                            <div>
                                <label for="guia_transporte" class="block text-xs font-semibold text-stone-700 mb-1">Número de Guía</label>
                                <input type="text" name="guia_transporte" id="guia_transporte" required
                                       value="{{ old('guia_transporte', $pedido->guia_transporte) }}"
                                       placeholder="Ej: CRD-99882211"
                                       class="w-full px-3 py-2 text-xs border border-stone-300 rounded-lg bg-white focus:border-[#A3542E]">
                            </div>
                        </div>

                        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg transition shadow">
                            Cambiar Estado a «Enviado» y Notificar Guía
                        </button>
                    </form>
                </div>
            @endif

            <!-- 2. Marcar Entregado (Acredita Puntos y Dispara Fidelización) -->
            @if($pedido->estado === 'enviado')
                <div class="p-6 bg-emerald-50/60 border border-emerald-200 rounded-xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h4 class="text-sm font-bold text-emerald-900">Confirmación de Entrega al Cliente</h4>
                        <p class="text-xs text-emerald-800 mt-0.5">Al marcar como entregado, el sistema automáticamente acreditará los puntos acumulados (<strong>+{{ $pedido->puntos_ganados }} pts</strong>) al cliente y habilitará la solicitud de reseña y oferta de recarga.</p>
                    </div>

                    <form action="{{ route('admin.pedidos.entregar', $pedido->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow transition whitespace-nowrap">
                            ✓ Marcar como Entregado
                        </button>
                    </form>
                </div>
            @endif

            @if($pedido->estado === 'entregado')
                <div class="p-4 bg-emerald-100/60 border border-emerald-300 rounded-xl text-xs text-emerald-900 flex items-center gap-2">
                    <span class="text-base font-bold">✓</span>
                    <span>Pedido completado y entregado. Puntos de fidelización ({{ $pedido->puntos_ganados }} pts) acreditados exitosamente en la cuenta del cliente.</span>
                </div>
            @endif

            <!-- 3. Anular Pedido (Solo Administrador - Punto 1 & 2) -->
            @if(Auth::user()->isAdmin() && $pedido->estado !== 'cancelado')
                <div class="pt-4 border-t border-stone-100 flex justify-between items-center">
                    <span class="text-xs text-stone-500">Acción sensible de administración:</span>
                    <button type="button" onclick="document.getElementById('modal-anular').classList.remove('hidden')"
                            class="px-4 py-2 border border-red-300 text-red-600 hover:bg-red-50 font-bold text-xs rounded-lg transition">
                        Anular Pedido y Restituir Stock
                    </button>
                </div>

                <!-- Modal de Confirmación de Anulación -->
                <div id="modal-anular" class="hidden fixed inset-0 bg-stone-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl space-y-4">
                        <h4 class="text-base font-bold text-stone-900">¿Anular pedido #{{ $pedido->numero_pedido }}?</h4>
                        <p class="text-xs text-stone-600">Esta acción liberará las unidades reservadas y devolverá los ítems al inventario disponible con registro de auditoría.</p>

                        <form action="{{ route('admin.pedidos.anular', $pedido->id) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label for="motivo_anulacion" class="block text-xs font-semibold text-stone-700 mb-1">Motivo de Anulación (Auditoría):</label>
                                <textarea name="motivo_anulacion" id="motivo_anulacion" rows="2" required placeholder="Ej: Solicitud de cancelación del cliente antes de envío..."
                                          class="w-full px-3 py-2 text-xs border border-stone-300 rounded-lg focus:border-red-500"></textarea>
                            </div>

                            <div class="flex gap-3 pt-2">
                                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-bold flex-1">
                                    Confirmar Anulación
                                </button>
                                <button type="button" onclick="document.getElementById('modal-anular').classList.add('hidden')" class="px-4 py-2 border border-stone-300 text-stone-700 rounded-lg text-xs font-semibold">
                                    Volver
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
