@extends('layouts.admin')

@section('page_title', 'Fidelización: Puntos & Recompensas')

@section('content')
<div class="space-y-10">

    <!-- Sección de Recompensas Activas y Creación -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Formulario Crear Recompensa -->
        <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-stone-900">Crear Nueva Recompensa</h2>
            <p class="text-xs text-stone-500">Define premios canjeables por puntos para incentivar la recompra</p>

            <form action="{{ route('admin.fidelizacion.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Nombre del Beneficio</label>
                    <input type="text" name="nombre" required placeholder="Ej: Bono $20.000 COP"
                           class="w-full px-3 py-2 text-xs border border-stone-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Descripción</label>
                    <input type="text" name="descripcion" placeholder="Aplica en compras superiores a..."
                           class="w-full px-3 py-2 text-xs border border-stone-300 rounded-lg">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-stone-700 mb-1">Puntos Requeridos</label>
                        <input type="number" name="puntos_requeridos" required min="1" placeholder="100"
                               class="w-full px-3 py-2 text-xs border border-stone-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-stone-700 mb-1">Valor Descuento</label>
                        <input type="number" name="descuento_valor" required min="0" placeholder="20000"
                               class="w-full px-3 py-2 text-xs border border-stone-300 rounded-lg">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Vigencia (Opcional)</label>
                    <input type="date" name="vigencia" class="w-full px-3 py-2 text-xs border border-stone-300 rounded-lg bg-white">
                </div>

                <button type="submit" class="btn-terracotta w-full py-2.5 rounded-lg font-bold text-xs shadow mt-2">
                    + Registrar Recompensa
                </button>
            </form>
        </div>

        <!-- Lista de Recompensas -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-stone-200 overflow-hidden shadow-sm">
            <div class="p-6 border-b border-stone-100">
                <h3 class="text-base font-bold text-stone-900">Catálogo de Beneficios Activos</h3>
            </div>

            <div class="divide-y divide-stone-100">
                @foreach($recompensas as $rec)
                    <div class="p-6 flex items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-stone-900 text-sm">{{ $rec->nombre }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-[#F7EBE4] text-[#A3542E]">
                                    {{ $rec->puntos_requeridos }} pts
                                </span>
                            </div>
                            <p class="text-xs text-stone-500 mt-1">{{ $rec->descripcion }}</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $rec->activa ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-200 text-stone-600' }}">
                                {{ $rec->activa ? 'Activa' : 'Pausada' }}
                            </span>
                            <form action="{{ route('admin.fidelizacion.toggle', $rec->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs text-stone-600 hover:text-stone-900 underline font-semibold">
                                    {{ $rec->activa ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Libreta Global de Puntos -->
    <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden shadow-sm">
        <div class="p-6 border-b border-stone-100">
            <h3 class="text-base font-bold text-stone-900">Bitácora Global de Movimientos de Fidelización</h3>
            <p class="text-xs text-stone-500">Auditoría de puntos generados, acreditados y canjeados en el sistema</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50 text-stone-500 uppercase tracking-wider text-[10px] border-b border-stone-200">
                    <tr>
                        <th class="p-4">Fecha</th>
                        <th class="p-4">Cliente</th>
                        <th class="p-4">Concepto</th>
                        <th class="p-4">Tipo</th>
                        <th class="p-4">Estado</th>
                        <th class="p-4 text-right">Puntos</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($historialPuntos as $hp)
                        <tr>
                            <td class="p-4 text-stone-500">{{ $hp->created_at->format('d/m/Y h:i A') }}</td>
                            <td class="p-4 font-bold text-stone-900">{{ $hp->user->name }}</td>
                            <td class="p-4 text-stone-600">{{ $hp->motivo }}</td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $hp->tipo === 'ganado' ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-100 text-stone-800' }}">
                                    {{ $hp->tipo }}
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $hp->estado === 'acreditado' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $hp->estado }}
                                </span>
                            </td>
                            <td class="p-4 text-right font-mono font-bold {{ $hp->puntos > 0 ? 'text-emerald-700' : 'text-stone-900' }}">
                                {{ $hp->puntos > 0 ? '+'.$hp->puntos : $hp->puntos }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-stone-100">
            {{ $historialPuntos->links() }}
        </div>
    </div>
</div>
@endsection
