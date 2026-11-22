@extends('layouts.admin')

@section('page_title', 'Crear Nuevo Producto')

@section('content')
<div class="max-w-2xl bg-white p-8 rounded-2xl border border-stone-200 shadow-sm space-y-6">
    <div class="flex items-center justify-between pb-4 border-b border-stone-100">
        <h2 class="text-base font-bold text-stone-900">Ficha de Producto y Precio</h2>
        <a href="{{ route('admin.productos.index') }}" class="text-xs text-stone-500 hover:text-stone-800">‹ Volver al catálogo</a>
    </div>

    <form action="{{ route('admin.productos.store') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label class="block text-xs font-semibold text-stone-700 mb-1">Nombre del Producto</label>
            <input type="text" name="nombre" required value="{{ old('nombre') }}" placeholder="Ej: Kit SPARKLE · ALPHA"
                   class="w-full px-3 py-2 text-sm border border-stone-300 rounded-lg">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Formato / Tipo</label>
                <select name="tipo" required class="w-full px-3 py-2 text-sm border border-stone-300 rounded-lg bg-white">
                    <option value="Kit">Kit Difusor</option>
                    <option value="Refill">Recarga (Refill)</option>
                    <option value="Dúo">Dúo Sensorial</option>
                    <option value="Gift">Gift Set</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Aroma Principal</label>
                <select name="aroma" required class="w-full px-3 py-2 text-sm border border-stone-300 rounded-lg bg-white">
                    <option value="ALPHA">ALPHA (Maderas)</option>
                    <option value="HELIOS">HELIOS (Cítrico)</option>
                    <option value="JAZ">JAZ (Floral)</option>
                    <option value="MULTI">MULTI (Trilogía / Mixto)</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-stone-700 mb-1">Descripción</label>
            <textarea name="descripcion" rows="3" required placeholder="Notas olfativas, ocasión de uso..."
                      class="w-full px-3 py-2 text-sm border border-stone-300 rounded-lg">{{ old('descripcion') }}</textarea>
        </div>

        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Precio (COP)</label>
                <input type="number" name="precio" required min="0" step="500" value="{{ old('precio', 120000) }}"
                       class="w-full px-3 py-2 text-sm border border-stone-300 rounded-lg">
            </div>
            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Stock Inicial</label>
                <input type="number" name="stock_inicial" required min="0" value="{{ old('stock_inicial', 10) }}"
                       class="w-full px-3 py-2 text-sm border border-stone-300 rounded-lg">
            </div>
            <div>
                <label class="block text-xs font-semibold text-stone-700 mb-1">Umbral Mínimo</label>
                <input type="number" name="umbral_minimo" required min="0" value="{{ old('umbral_minimo', 5) }}"
                       class="w-full px-3 py-2 text-sm border border-stone-300 rounded-lg">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-stone-700 mb-1">URL de Foto</label>
            <input type="url" name="foto" value="{{ old('foto') }}" placeholder="https://images.unsplash.com/..."
                   class="w-full px-3 py-2 text-sm border border-stone-300 rounded-lg">
        </div>

        <div class="pt-4">
            <button type="submit" class="btn-terracotta px-6 py-3 rounded-xl font-bold text-xs tracking-wider shadow">
                Guardar Producto en Catálogo
            </button>
        </div>
    </form>
</div>
@endsection
