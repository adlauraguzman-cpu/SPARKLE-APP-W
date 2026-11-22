@extends('layouts.admin')

@section('page_title', 'Editar Producto: ' . $producto->nombre)

@section('content')
<div class="max-w-2xl bg-white p-8 rounded-2xl border border-stone-200 shadow-sm space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-stone-100">
        <h2 class="text-base font-bold text-stone-900">Modificar Datos de Catálogo y Precios</h2>
        <a href="{{ route('admin.productos.index') }}" class="text-xs text-stone-500 hover:text-stone-800">‹ Volver al catálogo</a>
    </div>

    <form action="{{ route('admin.productos.update', $producto->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-xs font-semibold text-stone-700 mb-1">Nombre del Producto</label>
            <input type="text" name="nombre" required value="{{ old('nombre', $producto->nombre) }}"
                   class="w-full px-3 py-2 text-sm border border-stone-300 rounded-lg">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Formato / Tipo</label>
                <select name="tipo" required class="w-full px-3 py-2 text-sm border border-stone-300 rounded-lg bg-white">
                    <option value="Kit" {{ $producto->tipo === 'Kit' ? 'selected' : '' }}>Kit Difusor</option>
                    <option value="Refill" {{ $producto->tipo === 'Refill' ? 'selected' : '' }}>Recarga (Refill)</option>
                    <option value="Dúo" {{ $producto->tipo === 'Dúo' ? 'selected' : '' }}>Dúo Sensorial</option>
                    <option value="Gift" {{ $producto->tipo === 'Gift' ? 'selected' : '' }}>Gift Set</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Aroma Principal</label>
                <select name="aroma" required class="w-full px-3 py-2 text-sm border border-stone-300 rounded-lg bg-white">
                    <option value="ALPHA" {{ $producto->aroma === 'ALPHA' ? 'selected' : '' }}>ALPHA (Maderas)</option>
                    <option value="HELIOS" {{ $producto->aroma === 'HELIOS' ? 'selected' : '' }}>HELIOS (Cítrico)</option>
                    <option value="JAZ" {{ $producto->aroma === 'JAZ' ? 'selected' : '' }}>JAZ (Floral)</option>
                    <option value="MULTI" {{ $producto->aroma === 'MULTI' ? 'selected' : '' }}>MULTI (Trilogía / Mixto)</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-stone-700 mb-1">Descripción</label>
            <textarea name="descripcion" rows="3" required class="w-full px-3 py-2 text-sm border border-stone-300 rounded-lg">{{ old('descripcion', $producto->descripcion) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Precio (COP)</label>
                <input type="number" name="precio" required min="0" step="500" value="{{ old('precio', $producto->precio) }}"
                       class="w-full px-3 py-2 text-sm border border-stone-300 rounded-lg">
            </div>
            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Estado de Publicación</label>
                <select name="activo" required class="w-full px-3 py-2 text-sm border border-stone-300 rounded-lg bg-white">
                    <option value="1" {{ $producto->activo ? 'selected' : '' }}>Activo en Tienda</option>
                    <option value="0" {{ !$producto->activo ? 'selected' : '' }}>Oculto / Inactivo</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-stone-700 mb-1">URL de Foto</label>
            <input type="url" name="foto" value="{{ old('foto', $producto->foto) }}" class="w-full px-3 py-2 text-sm border border-stone-300 rounded-lg">
        </div>

        <div class="pt-4">
            <button type="submit" class="btn-terracotta px-6 py-3 rounded-xl font-bold text-xs tracking-wider shadow">
                Actualizar Cambios
            </button>
        </div>
    </form>
</div>
@endsection
