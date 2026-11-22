@extends('layouts.admin')

@section('page_title', 'Panel de Control e Indicadores')

@section('content')
<div class="space-y-8">

    <!-- Tarjetas de KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @if(!$isColaborador)
            <!-- Ventas Totales (Solo Administrador) -->
            <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider block">Ingresos por Ventas</span>
                <span class="text-2xl sm:text-3xl font-bold text-stone-900 mt-2 block">
                    ${{ number_format($totalVentas, 0, ',', '.') }} COP
                </span>
                <span class="text-xs text-emerald-700 font-medium mt-1 inline-flex items-center gap-1">
                    ✓ Pagos aprobados en pasarela
                </span>
            </div>
        @endif

        <!-- Pedidos Nuevos / En Preparación (Operativo) -->
        <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm">
            <span class="text-xs font-bold text-[#A3542E] uppercase tracking-wider block">Pedidos por Despachar</span>
            <span class="text-2xl sm:text-3xl font-bold text-stone-900 mt-2 block">
                {{ $pedidosNuevos }}
            </span>
            <span class="text-xs text-stone-500 mt-1 block">
                Estado: Pagado o En preparación
            </span>
        </div>

        <!-- Alertas de Stock Bajo (Operativo & Admin) -->
        <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm {{ $productosBajoStock > 0 ? 'ring-2 ring-amber-400 bg-amber-50/40' : '' }}">
            <span class="text-xs font-bold text-amber-700 uppercase tracking-wider block">Alertas de Stock Bajo</span>
            <span class="text-2xl sm:text-3xl font-bold {{ $productosBajoStock > 0 ? 'text-amber-800' : 'text-stone-900' }} mt-2 block">
                {{ $productosBajoStock }}
            </span>
            <a href="{{ route('admin.inventario.index', ['filtro' => 'bajo_stock']) }}" class="text-xs text-[#A3542E] hover:underline font-bold mt-1 inline-block">
                Revisar umbrales mínimos →
            </a>
        </div>

        <!-- Pedidos Entregados -->
        <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm">
            <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider block">Pedidos Entregados</span>
            <span class="text-2xl sm:text-3xl font-bold text-stone-900 mt-2 block">
                {{ $pedidosEntregados }}
            </span>
            <span class="text-xs text-stone-500 mt-1 block">
                Puntos acreditados a clientes
            </span>
        </div>
    </div>

    <!-- Alertas Activas de Stock Mínimo -->
    @if($alertasStock->isNotEmpty())
        <div class="bg-amber-50 border border-amber-300 rounded-2xl p-6 shadow-sm space-y-3">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <h3 class="text-sm font-bold text-amber-900">Productos por debajo del umbral mínimo de seguridad</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                @foreach($alertasStock as $alerta)
                    <div class="bg-white p-3.5 rounded-xl border border-amber-200 flex items-center justify-between text-xs">
                        <div>
                            <strong class="text-stone-900 block">{{ $alerta->producto->nombre }}</strong>
                            <span class="text-red-700 font-bold">Disponible: {{ $alerta->stock_disponible }}</span>
                            <span class="text-stone-400"> (Mínimo: {{ $alerta->umbral_minimo }})</span>
                        </div>
                        <a href="{{ route('admin.inventario.index', ['producto_id' => $alerta->producto_id]) }}" class="px-3 py-1.5 bg-[#A3542E] text-white rounded-lg font-bold text-[11px] hover:bg-[#8B4321]">
                            Reponer
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Tabla de Últimos Pedidos -->
    <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden shadow-sm">
        <div class="p-6 border-b border-stone-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-stone-900">Últimos Pedidos Registrados</h2>
                <p class="text-xs text-stone-500">Gestión de despacho y preparación de órdenes</p>
            </div>
            <a href="{{ route('admin.pedidos.index') }}" class="text-xs font-bold text-[#A3542E] hover:underline">
                Ver todos los pedidos →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50 text-stone-500 uppercase tracking-wider text-[10px] border-b border-stone-200">
                    <tr>
                        <th class="p-4">N.º Pedido</th>
                        <th class="p-4">Cliente</th>
                        <th class="p-4">Destino</th>
                        <th class="p-4">Estado</th>
                        @if(!$isColaborador)
                            <th class="p-4 text-right">Total</th>
                        @endif
                        <th class="p-4 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($ultimosPedidos as $ped)
                        <tr class="hover:bg-stone-50 transition">
                            <td class="p-4 font-mono font-bold text-stone-900">#{{ $ped->numero_pedido }}</td>
                            <td class="p-4">
                                <span class="font-semibold block text-stone-900">{{ $ped->user->name }}</span>
                                <span class="text-stone-400 text-[11px]">{{ $ped->telefono_contacto }}</span>
                            </td>
                            <td class="p-4">
                                <span>{{ $ped->ciudad_envio }}</span>
                                <span class="text-stone-400 block text-[11px] truncate max-w-xs">{{ $ped->direccion_envio }}</span>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                    {{ $ped->estado === 'entregado' ? 'bg-emerald-100 text-emerald-800' :
                                       ($ped->estado === 'enviado' ? 'bg-blue-100 text-blue-800' :
                                       ($ped->estado === 'en_preparacion' ? 'bg-amber-100 text-amber-800' :
                                       ($ped->estado === 'cancelado' ? 'bg-red-100 text-red-800' : 'bg-stone-100 text-stone-800'))) }}">
                                    {{ str_replace('_', ' ', $ped->estado) }}
                                </span>
                            </td>
                            @if(!$isColaborador)
                                <td class="p-4 text-right font-bold text-stone-900">${{ number_format($ped->total, 0, ',', '.') }}</td>
                            @endif
                            <td class="p-4 text-right">
                                <a href="{{ route('admin.pedidos.show', $ped->id) }}" class="px-3 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-800 rounded-lg font-bold text-xs">
                                    Atender →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
