@extends('layouts.admin')

@section('page_title', 'Moderación de Reseñas de Clientes')

@section('content')
<div class="space-y-6">
    <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm">
        <h2 class="text-base font-bold text-stone-900">Control de Calidad y Testimonios</h2>
        <p class="text-xs text-stone-500">Modera opiniones de clientes que han recibido pedidos. Puedes publicarlas u ocultarlas de la ficha pública de producto.</p>
    </div>

    <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden shadow-sm">
        @if($resenas->isEmpty())
            <div class="p-12 text-center text-xs text-stone-500">
                No hay reseñas registradas aún.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-700">
                    <thead class="bg-stone-50 text-stone-500 uppercase tracking-wider text-[10px] border-b border-stone-200">
                        <tr>
                            <th class="p-4">Fecha</th>
                            <th class="p-4">Cliente</th>
                            <th class="p-4">Producto</th>
                            <th class="p-4">Calificación</th>
                            <th class="p-4">Comentario</th>
                            <th class="p-4 text-center">Estado</th>
                            <th class="p-4 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($resenas as $res)
                            <tr class="hover:bg-stone-50 transition">
                                <td class="p-4 text-stone-500">{{ $res->created_at->format('d/m/Y') }}</td>
                                <td class="p-4 font-bold text-stone-900">{{ $res->user->name }}</td>
                                <td class="p-4 font-semibold text-stone-800">{{ $res->producto->nombre }}</td>
                                <td class="p-4 text-amber-500 font-bold">
                                    {{ str_repeat('★', $res->calificacion) }}{{ str_repeat('☆', 5 - $res->calificacion) }}
                                </td>
                                <td class="p-4 text-stone-600 max-w-sm">"{{ $res->comentario }}"</td>
                                <td class="p-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $res->estado === 'publicada' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $res->estado }}
                                    </span>
                                </td>
                                <td class="p-4 text-right space-x-2">
                                    <form action="{{ route('admin.resenas.toggle', $res->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs text-stone-700 hover:text-black font-semibold underline">
                                            {{ $res->estado === 'publicada' ? 'Ocultar' : 'Aprobar' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.resenas.destroy', $res->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar esta reseña definitivamente?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 hover:text-red-800">
                                            Eliminar
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-stone-100">
                {{ $resenas->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
