@extends('layouts.admin')

@section('page_title', 'Gestión de Pedidos & Despachos')

@section('content')
<div class="space-y-6">

    <!-- Filtros de Estado -->
    <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-bold text-stone-500 uppercase tracking-wider mr-2">Estado:</span>
            <a href="{{ route('admin.pedidos.index') }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ !request('estado') ? 'bg-stone-900 text-white' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">
               Todos
            </a>
            <a href="{{ route('admin.pedidos.index', ['estado' => 'pagado']) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('estado') === 'pagado' ? 'bg-[#A3542E] text-white' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">
               Pagados
            </a>
            <a href="{{ route('admin.pedidos.index', ['estado' => 'en_preparacion']) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('estado') === 'en_preparacion' ? 'bg-amber-500 text-stone-900' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">
               En Preparación
            </a>
            <a href="{{ route('admin.pedidos.index', ['estado' => 'enviado']) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('estado') === 'enviado' ? 'bg-blue-600 text-white' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">
               Enviados
            </a>
            <a href="{{ route('admin.pedidos.index', ['estado' => 'entregado']) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('estado') === 'entregado' ? 'bg-emerald-600 text-white' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">
               Entregados
            </a>
            <a href="{{ route('admin.pedidos.index', ['estado' => 'cancelado']) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('estado') === 'cancelado' ? 'bg-red-600 text-white' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">
               Cancelados
            </a>
        </div>

        <form action="{{ route('admin.pedidos.index') }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
            @if(request('estado')) <input type="hidden" name="estado" value="{{ request('estado') }}"> @endif
            <input type="text" name="q" value="{{ request('q') }}" placeholder="N.º pedido, cliente o ciudad..."
                   class="px-3 py-1.5 text-xs border border-stone-300 rounded-lg bg-stone-50 w-full md:w-60 focus:border-[#A3542E]">
            <button type="submit" class="px-3.5 py-1.5 bg-stone-800 text-white text-xs font-semibold rounded-lg hover:bg-black">
                Buscar
            </button>
        </form>
    </div>

    <!-- Tabla de Pedidos -->
    <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden shadow-sm">
        @if($pedidos->isEmpty())
            <div class="p-12 text-center text-xs text-stone-500">
                No se encontraron pedidos con los filtros aplicados.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-700">
                    <thead class="bg-stone-50 text-stone-500 uppercase tracking-wider text-[10px] border-b border-stone-200">
                        <tr>
                            <th class="p-4">Pedido / Fecha</th>
                            <th class="p-4">Cliente (Entrega)</th>
                            <th class="p-4">Ciudad / Dirección</th>
                            <th class="p-4">Estado</th>
                            <th class="p-4">Transportadora & Guía</th>
                            @if(!$isColaborador)
                                <th class="p-4 text-right">Total</th>
                            @endif
                            <th class="p-4 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($pedidos as $pedido)
                            <tr class="hover:bg-stone-50 transition">
                                <td class="p-4">
                                    <span class="font-mono font-bold text-stone-900 block">#{{ $pedido->numero_pedido }}</span>
                                    <span class="text-stone-400 text-[11px]">{{ $pedido->created_at->format('d/m/Y h:i A') }}</span>
                                </td>
                                <td class="p-4">
                                    <span class="font-bold text-stone-900 block">{{ $pedido->user->name }}</span>
                                    <span class="text-stone-500 text-[11px]">Tel: {{ $pedido->telefono_contacto }}</span>
                                </td>
                                <td class="p-4">
                                    <span class="font-medium text-stone-800">{{ $pedido->ciudad_envio }}</span>
                                    <span class="text-stone-400 block text-[11px] truncate max-w-xs">{{ $pedido->direccion_envio }}</span>
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                        {{ $pedido->estado === 'entregado' ? 'bg-emerald-100 text-emerald-800' :
                                           ($pedido->estado === 'enviado' ? 'bg-blue-100 text-blue-800' :
                                           ($pedido->estado === 'en_preparacion' ? 'bg-amber-100 text-amber-800' :
                                           ($pedido->estado === 'cancelado' ? 'bg-red-100 text-red-800' : 'bg-stone-100 text-stone-800'))) }}">
                                        {{ str_replace('_', ' ', $pedido->estado) }}
                                    </span>
                                </td>
                                <td class="p-4">
                                    @if($pedido->guia_transporte)
                                        <span class="font-bold text-stone-900 block">{{ $pedido->transportadora }}</span>
                                        <span class="font-mono text-[11px] text-stone-500">Guía: {{ $pedido->guia_transporte }}</span>
                                    @else
                                        <span class="text-stone-400 italic text-[11px]">Sin guía registrada</span>
                                    @endif
                                </td>
                                @if(!$isColaborador)
                                    <td class="p-4 text-right font-bold text-stone-900 text-sm">
                                        ${{ number_format($pedido->total, 0, ',', '.') }}
                                    </td>
                                @endif
                                <td class="p-4 text-right">
                                    <a href="{{ route('admin.pedidos.show', $pedido->id) }}" class="px-3.5 py-1.5 bg-[#A3542E] text-white rounded-lg font-bold text-xs hover:bg-[#8B4321] transition">
                                        Gestionar →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-stone-100">
                {{ $pedidos->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
