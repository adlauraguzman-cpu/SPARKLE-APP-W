@extends('layouts.app')

@section('title', 'Mis Puntos y Recompensas · SPARKLE Club')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-10">

    <!-- Header y Saldo de Puntos -->
    <div class="bg-gradient-to-br from-[#231F20] to-[#3A3330] rounded-3xl p-8 sm:p-12 text-white shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-8">
        <div class="space-y-3">
            <span class="inline-block px-3 py-1 bg-amber-500/20 text-amber-300 border border-amber-400/30 text-xs font-bold uppercase tracking-widest rounded-full">
                SPARKLE Loyalty Club
            </span>
            <h1 class="font-serif text-3xl sm:text-5xl font-bold">Mis Puntos y Beneficios</h1>
            <p class="text-xs sm:text-sm text-stone-300 max-w-md leading-relaxed">
                Cada compra en SPARKLE te acerca a recargas gratuitas, envíos bonificados y experiencias de autor exclusivas.
            </p>
        </div>

        <div class="bg-white/10 backdrop-blur-md border border-white/20 p-6 sm:p-8 rounded-2xl text-center min-w-[220px]">
            <span class="text-xs text-stone-300 uppercase tracking-wider block font-semibold">Saldo Disponible</span>
            <span class="font-serif text-5xl sm:text-6xl font-bold text-amber-300 block my-1">
                {{ $user->puntos_disponibles }}
            </span>
            <span class="text-[11px] text-stone-400 block">Puntos acumulados listos para canjear</span>
        </div>
    </div>

    <!-- Catálogo de Recompensas Canjeables -->
    <div class="space-y-6">
        <div>
            <h2 class="font-serif text-2xl sm:text-3xl font-bold text-stone-900">Catálogo de Recompensas</h2>
            <p class="text-xs text-stone-500 mt-1">Elige tu beneficio y canjéalo con tus puntos acumulados</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($recompensas as $recompensa)
                <div class="bg-white rounded-2xl border border-[#E8E2DA] p-6 shadow-sm flex flex-col justify-between hover:border-[#A3542E] transition">
                    <div>
                        <div class="flex justify-between items-start">
                            <span class="px-3 py-1 bg-[#F7EBE4] text-[#A3542E] text-xs font-bold rounded-full">
                                {{ $recompensa->puntos_requeridos }} puntos
                            </span>
                            @if($user->puntos_disponibles >= $recompensa->puntos_requeridos)
                                <span class="text-[11px] text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded">¡Alcanzable!</span>
                            @endif
                        </div>

                        <h3 class="font-serif text-xl font-bold text-stone-900 mt-4">{{ $recompensa->nombre }}</h3>
                        <p class="text-xs text-stone-600 mt-2 leading-relaxed">{{ $recompensa->descripcion }}</p>
                    </div>

                    <div class="pt-6 border-t border-stone-100 mt-6">
                        @if($user->puntos_disponibles >= $recompensa->puntos_requeridos)
                            <form action="{{ route('loyalty.canjear', $recompensa->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn-terracotta w-full py-2.5 rounded-xl text-xs font-bold shadow">
                                    Canjear Ahora
                                </button>
                            </form>
                        @else
                            <button type="button" disabled class="w-full py-2.5 bg-stone-100 text-stone-400 rounded-xl text-xs font-semibold cursor-not-allowed">
                                Te faltan {{ $recompensa->puntos_requeridos - $user->puntos_disponibles }} pts
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Historial de Movimientos de Puntos (Ledger) -->
    <div class="space-y-4 pt-6 border-t border-[#E8E2DA]">
        <div>
            <h2 class="font-serif text-2xl font-bold text-stone-900">Historial de Movimientos</h2>
            <p class="text-xs text-stone-500">Registro de puntos ganados por pedidos entregados y canjes realizados</p>
        </div>

        <div class="bg-white rounded-2xl border border-[#E8E2DA] overflow-hidden shadow-sm">
            @if($movimientos->isEmpty())
                <div class="p-8 text-center text-xs text-stone-500">
                    Aún no registras movimientos en tu libreta de puntos.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-stone-700">
                        <thead class="bg-[#FAF8F5] text-stone-500 uppercase tracking-wider text-[10px] border-b border-stone-200">
                            <tr>
                                <th class="p-4">Fecha</th>
                                <th class="p-4">Motivo / Concepto</th>
                                <th class="p-4">Tipo</th>
                                <th class="p-4">Estado</th>
                                <th class="p-4 text-right">Puntos</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @foreach($movimientos as $mov)
                                <tr>
                                    <td class="p-4 text-stone-500">{{ $mov->created_at->format('d/m/Y') }}</td>
                                    <td class="p-4 font-medium text-stone-900">
                                        {{ $mov->motivo }}
                                        @if($mov->pedido)
                                            <a href="{{ route('orders.show', $mov->pedido->numero_pedido) }}" class="text-[#A3542E] underline ml-1">
                                                (Pedido #{{ $mov->pedido->numero_pedido }})
                                            </a>
                                        @endif
                                    </td>
                                    <td class="p-4 capitalize">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $mov->tipo === 'ganado' ? 'bg-emerald-50 text-emerald-800' : 'bg-stone-100 text-stone-800' }}">
                                            {{ $mov->tipo }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $mov->estado === 'acreditado' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ $mov->estado }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right font-bold text-sm {{ $mov->puntos > 0 ? 'text-emerald-700' : 'text-stone-800' }}">
                                        {{ $mov->puntos > 0 ? '+'.$mov->puntos : $mov->puntos }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-4 border-t border-stone-100">
                    {{ $movimientos->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
