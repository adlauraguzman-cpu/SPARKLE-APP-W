@extends('layouts.admin')

@section('page_title', 'Inventario por Producto & Ajuste de Stock')

@section('content')
<div class="space-y-10">

    <!-- Header con Selector de Producto para Ajustar (Mockup Página 11) -->
    @php
        $productoActivo = $productoSeleccionado ?? ($inventarios->first()?->producto);
        $invActivo = $productoActivo?->inventario;
    @endphp

    @if($productoActivo && $invActivo)
        <!-- TARJETA DEL SEGUNDO MÓDULO: AJUSTAR STOCK (PÁGINA 11) -->
        <div class="bg-white rounded-2xl border border-stone-200 shadow-sm p-6 sm:p-8 space-y-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-stone-100">
                <div class="flex items-center gap-2 text-xs font-mono text-stone-500">
                    <span>Inventario</span>
                    <span>›</span>
                    <strong class="text-stone-900 text-sm font-sans">{{ $productoActivo->nombre }}</strong>
                </div>
                <span class="px-2.5 py-1 bg-stone-100 text-stone-700 text-xs font-bold rounded-lg uppercase tracking-wider">
                    {{ Auth::user()->name }} ({{ Auth::user()->rol }})
                </span>
            </div>

            <!-- Alerta de Éxito Estado 3 (Página 11 Mockup) -->
            @if(session('stock_success'))
                <div class="p-4 bg-[#EBF5EF] border border-[#A5D6A7] rounded-xl text-[#2E7D32] flex items-center gap-3 text-sm font-semibold shadow-sm" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span>{{ session('stock_success') }}</span>
                </div>
            @endif

            <!-- Alerta de Error Estado 4 (Página 11 Mockup) -->
            @if($errors->any())
                <div class="p-4 bg-[#FDF2F2] border border-[#EF9A9A] rounded-xl text-[#C62828] flex items-center gap-3 text-sm font-semibold shadow-sm" role="alert">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>No se guardó el movimiento. Revisa los campos marcados; el stock no cambió.</span>
                </div>
            @endif

            <!-- Indicadores de Stock del Producto -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="p-4 bg-stone-50 rounded-xl border border-stone-200">
                    <span class="text-xs text-stone-500 font-medium block">Disponible</span>
                    <span class="text-3xl font-bold text-stone-900 mt-1 block" id="disp-count">{{ $invActivo->stock_disponible }}</span>
                </div>

                <div class="p-4 bg-stone-50 rounded-xl border border-stone-200">
                    <span class="text-xs text-stone-500 font-medium block">Reservado</span>
                    <span class="text-3xl font-bold text-stone-900 mt-1 block">{{ $invActivo->stock_reservado }}</span>
                </div>

                <div class="p-4 bg-stone-50 rounded-xl border border-stone-200">
                    <span class="text-xs text-stone-500 font-medium block">Umbral Mínimo</span>
                    <span class="text-3xl font-bold text-stone-900 mt-1 block">{{ $invActivo->umbral_minimo }}</span>
                </div>

                <div class="p-4 rounded-xl border flex flex-col justify-center items-start {{ $invActivo->esta_bajo_minimo ? 'bg-amber-50 border-amber-300 text-amber-800' : 'bg-emerald-50 border-emerald-300 text-emerald-800' }}">
                    <span class="text-[10px] uppercase tracking-wider font-bold block">Diagnóstico</span>
                    <span class="text-base font-bold mt-0.5">
                        {{ $invActivo->esta_bajo_minimo ? 'Stock bajo ⚠️' : 'Stock al día ✓' }}
                    </span>
                </div>
            </div>

            <!-- Formulario de Ajuste de Stock (Estado 1, 2, 4) -->
            <form id="stock-adjust-form" action="{{ route('admin.inventario.ajustar') }}" method="POST" class="space-y-4 pt-2">
                @csrf
                <input type="hidden" name="producto_id" value="{{ $productoActivo->id }}">

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Tipo de Movimiento -->
                    <div>
                        <label for="tipo" class="block text-xs font-semibold text-stone-700 mb-1">Tipo de movimiento</label>
                        <select name="tipo" id="tipo" required class="w-full px-3 py-2 text-sm border border-stone-300 rounded-lg bg-white focus:border-[#A3542E]">
                            <option value="entrada" {{ old('tipo') === 'entrada' ? 'selected' : '' }}>Entrada de mercancía (+)</option>
                            <option value="ajuste" {{ old('tipo') === 'ajuste' ? 'selected' : '' }}>Ajuste directo de inventario</option>
                            <option value="merma" {{ old('tipo') === 'merma' ? 'selected' : '' }}>Merma o daño (-)</option>
                        </select>
                    </div>

                    <!-- Cantidad -->
                    <div>
                        <label for="cantidad" class="block text-xs font-semibold text-stone-700 mb-1">Cantidad</label>
                        <input type="number" name="cantidad" id="cantidad" required min="1"
                               value="{{ old('cantidad', 0) }}"
                               class="w-full px-3 py-2 text-sm border {{ $errors->has('cantidad') ? 'border-red-500 bg-red-50' : 'border-stone-300' }} rounded-lg focus:border-[#A3542E]">
                        @error('cantidad')
                            <p class="text-xs text-red-600 mt-1 font-semibold flex items-center gap-1">
                                <span>ⓘ</span> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Motivo -->
                    <div>
                        <label for="motivo" class="block text-xs font-semibold text-stone-700 mb-1">Motivo</label>
                        <input type="text" name="motivo" id="motivo" required
                               value="{{ old('motivo') }}"
                               placeholder="Ej.: compra a proveedor o lote recibido"
                               class="w-full px-3 py-2 text-sm border {{ $errors->has('motivo') ? 'border-red-500 bg-red-50' : 'border-stone-300' }} rounded-lg focus:border-[#A3542E]">
                        @error('motivo')
                            <p class="text-xs text-red-600 mt-1 font-semibold flex items-center gap-1">
                                <span>ⓘ</span> {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Botón Guardar Movimiento con Spinner de Estado 2 (Espera / Loading) -->
                <div class="pt-2">
                    <button type="submit" id="btn-save-stock"
                            class="btn-terracotta px-6 py-3 rounded-xl font-bold text-xs tracking-wider shadow flex items-center gap-2">
                        <span id="btn-save-text">Guardar movimiento</span>
                        <span id="btn-save-spinner" class="hidden">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Guardando…
                        </span>
                    </button>
                </div>
            </form>

            @if(Auth::user()->isAdmin())
                <!-- Configuración de Umbral Mínimo (Exclusivo Administrador) -->
                <div class="pt-4 border-t border-stone-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold text-stone-800">Definir umbral de alerta mínima para {{ $productoActivo->nombre }}</span>
                        <p class="text-[11px] text-stone-500">El sistema dispara alertas operativas si el stock disponible cae a este número o menos.</p>
                    </div>
                    <form action="{{ route('admin.inventario.umbral', $productoActivo->id) }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        <input type="number" name="umbral_minimo" value="{{ $invActivo->umbral_minimo }}" min="0" max="999" class="w-20 px-2.5 py-1 text-xs border border-stone-300 rounded-lg">
                        <button type="submit" class="px-3 py-1 bg-stone-800 text-white rounded-lg text-xs font-semibold hover:bg-black">
                            Actualizar Umbral
                        </button>
                    </form>
                </div>
            @endif
        </div>
    @endif

    <!-- TABLA DE INVENTARIO GENERAL POR PRODUCTO -->
    <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden shadow-sm">
        <div class="p-6 border-b border-stone-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-base font-bold text-stone-900">Estado de Inventario por Producto</h2>
                <p class="text-xs text-stone-500">Monitoreo de existencias disponibles y umbrales de reabastecimiento</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.inventario.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ !request('filtro') ? 'bg-stone-800 text-white' : 'bg-stone-100 text-stone-700' }}">
                    Todos los productos
                </a>
                <a href="{{ route('admin.inventario.index', ['filtro' => 'bajo_stock']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('filtro') === 'bajo_stock' ? 'bg-amber-500 text-stone-900' : 'bg-stone-100 text-stone-700' }}">
                    Solo Bajo Stock Mínimo
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50 text-stone-500 uppercase tracking-wider text-[10px] border-b border-stone-200">
                    <tr>
                        <th class="p-4">Producto</th>
                        <th class="p-4">Aroma / Formato</th>
                        <th class="p-4 text-center">Disponible</th>
                        <th class="p-4 text-center">Reservado</th>
                        <th class="p-4 text-center">Mínimo</th>
                        <th class="p-4 text-center">Diagnóstico</th>
                        <th class="p-4 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($inventarios as $inv)
                        <tr class="hover:bg-stone-50 transition {{ $productoActivo?->id === $inv->producto_id ? 'bg-amber-50/30' : '' }}">
                            <td class="p-4 font-bold text-stone-900">{{ $inv->producto->nombre }}</td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold 
                                    {{ $inv->producto->aroma === 'ALPHA' ? 'badge-aroma-alpha' : ($inv->producto->aroma === 'HELIOS' ? 'badge-aroma-helios' : 'badge-aroma-jaz') }}">
                                    {{ $inv->producto->aroma }}
                                </span>
                                <span class="text-stone-400 ml-1">{{ $inv->producto->tipo }}</span>
                            </td>
                            <td class="p-4 text-center font-mono font-bold text-sm {{ $inv->stock_disponible <= $inv->umbral_minimo ? 'text-red-700' : 'text-stone-900' }}">
                                {{ $inv->stock_disponible }}
                            </td>
                            <td class="p-4 text-center font-mono text-stone-500">{{ $inv->stock_reservado }}</td>
                            <td class="p-4 text-center font-mono text-stone-500">{{ $inv->umbral_minimo }}</td>
                            <td class="p-4 text-center">
                                @if($inv->esta_bajo_minimo)
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-900 border border-amber-300 rounded-full font-bold text-[10px]">
                                        Stock bajo
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full font-bold text-[10px]">
                                        Stock al día
                                    </span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('admin.inventario.index', ['producto_id' => $inv->producto_id]) }}" 
                                   class="px-3 py-1.5 bg-[#A3542E] text-white hover:bg-[#8B4321] rounded-lg font-bold text-[11px] transition">
                                    Ajustar Stock →
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Historial de Movimientos de Inventario -->
    <div class="bg-white rounded-2xl border border-stone-200 overflow-hidden shadow-sm">
        <div class="p-6 border-b border-stone-100">
            <h3 class="text-base font-bold text-stone-900">Bitácora de Movimientos de Inventario</h3>
            <p class="text-xs text-stone-500">Auditoría completa de entradas, ventas, ajustes y liberaciones de reserva</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50 text-stone-500 uppercase tracking-wider text-[10px] border-b border-stone-200">
                    <tr>
                        <th class="p-4">Fecha</th>
                        <th class="p-4">Producto</th>
                        <th class="p-4">Tipo</th>
                        <th class="p-4 text-center">Cantidad</th>
                        <th class="p-4">Motivo</th>
                        <th class="p-4">Responsable</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($ultimosMovimientos as $mov)
                        <tr>
                            <td class="p-4 text-stone-500">{{ $mov->created_at->format('d/m/Y h:i A') }}</td>
                            <td class="p-4 font-medium text-stone-900">{{ $mov->producto->nombre }}</td>
                            <td class="p-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                    {{ $mov->tipo === 'entrada' ? 'bg-emerald-100 text-emerald-800' :
                                       ($mov->tipo === 'venta' ? 'bg-blue-100 text-blue-800' :
                                       ($mov->tipo === 'merma' ? 'bg-red-100 text-red-800' : 'bg-stone-100 text-stone-800')) }}">
                                    {{ $mov->tipo }}
                                </span>
                            </td>
                            <td class="p-4 text-center font-mono font-bold">{{ $mov->cantidad }}</td>
                            <td class="p-4 text-stone-600">{{ $mov->motivo }}</td>
                            <td class="p-4 text-stone-500">{{ $mov->usuario_nombre ?? 'Sistema' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-stone-100">
            {{ $ultimosMovimientos->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stockForm = document.getElementById('stock-adjust-form');
        const btnSave = document.getElementById('btn-save-stock');
        const btnText = document.getElementById('btn-save-text');
        const btnSpinner = document.getElementById('btn-save-spinner');

        if (stockForm) {
            stockForm.addEventListener('submit', function () {
                btnSave.disabled = true;
                btnText.classList.add('hidden');
                btnSpinner.classList.remove('hidden');
            });
        }
    });
</script>
@endpush
@endsection
