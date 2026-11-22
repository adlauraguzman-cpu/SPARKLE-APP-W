@extends('layouts.admin')

@section('page_title', 'Catálogo de Productos & Precios')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-base font-bold text-stone-900">Productos Registrados</h2>
            <p class="text-xs text-stone-500">Gestión de formatos, aromas y precios de venta al público</p>
        </div>
        <a href="{{ route('admin.productos.create') }}" class="btn-terracotta px-4 py-2.5 rounded-xl font-bold text-xs tracking-wider shadow">
            + Nuevo Producto
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50 text-stone-500 uppercase tracking-wider text-[10px] border-b border-stone-200">
                    <tr>
                        <th class="p-4">Producto</th>
                        <th class="p-4">Formato / Tipo</th>
                        <th class="p-4">Aroma</th>
                        <th class="p-4">Precio (COP)</th>
                        <th class="p-4 text-center">Estado</th>
                        <th class="p-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($productos as $prod)
                        <tr class="hover:bg-stone-50 transition">
                            <td class="p-4 flex items-center gap-3">
                                <img src="{{ $prod->foto }}" alt="{{ $prod->nombre }}" class="w-10 h-10 rounded-lg object-cover border border-stone-200">
                                <div>
                                    <span class="font-bold text-stone-900 block">{{ $prod->nombre }}</span>
                                    <span class="text-stone-400 text-[11px]">slug: {{ $prod->slug }}</span>
                                </div>
                            </td>
                            <td class="p-4 font-medium">{{ $prod->tipo }}</td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                    {{ $prod->aroma === 'ALPHA' ? 'badge-aroma-alpha' : ($prod->aroma === 'HELIOS' ? 'badge-aroma-helios' : 'badge-aroma-jaz') }}">
                                    {{ $prod->aroma }}
                                </span>
                            </td>
                            <td class="p-4 font-bold text-stone-900 text-sm">
                                ${{ number_format($prod->precio, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $prod->activo ? 'bg-emerald-100 text-emerald-800' : 'bg-stone-200 text-stone-600' }}">
                                    {{ $prod->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <a href="{{ route('admin.productos.edit', $prod->id) }}" class="text-stone-700 hover:text-black font-bold">Editar</a>
                                <form action="{{ route('admin.productos.destroy', $prod->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro que deseas eliminar este producto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-stone-100">
            {{ $productos->links() }}
        </div>
    </div>
</div>
@endsection
