@extends('layouts.app')

@section('title', 'Finalizar Pedido · SPARKLE')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-10">

    <!-- Header con botón volver -->
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('cart.index') }}" class="text-xs font-semibold text-stone-500 hover:text-stone-900 flex items-center gap-1">
            <span>‹</span> Volver al carrito
        </a>
        <span class="text-xs text-stone-400">Paso 2 de 2 · Checkout</span>
    </div>

    <!-- Barra de Progreso de Carga (Estado 2 · Espera / Loading) -->
    <div id="loading-bar-container" class="hidden mb-6">
        <div class="w-full bg-stone-200 h-1.5 rounded-full overflow-hidden">
            <div class="bg-[#A3542E] h-full w-full animate-pulse"></div>
        </div>
        <p class="text-xs text-center text-stone-600 mt-2 font-medium" id="loading-timer-text">
            Procesando tu pago de forma segura... Por favor no cierres ni recargues esta ventana.
        </p>
    </div>

    <!-- Banner Estado 4: Error de Validación General (Punto 3 - Página 9) -->
    @if($errors->any())
        <div class="mb-6 p-4 bg-[#FDF2F2] border border-[#EF9A9A] rounded-xl text-[#C62828] flex items-start gap-3 shadow-sm" role="alert" aria-live="assertive">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <div>
                <h3 class="text-sm font-bold">No pudimos confirmar tu pedido</h3>
                <p class="text-xs mt-0.5">Revisa los campos marcados en rojo; tus demás datos han sido guardados.</p>
            </div>
        </div>
    @endif

    <!-- Banner Variante: Pago Rechazado (Punto 3 - Página 10) -->
    @if(session('pago_rechazado'))
        <div class="mb-6 p-5 bg-[#FDF2F2] border-l-4 border-[#C62828] rounded-r-xl shadow-sm space-y-3" role="alert" aria-live="assertive">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-[#C62828] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <h3 class="text-sm font-bold text-[#C62828]">{{ session('pago_rechazado') }}</h3>
                    <p class="text-xs text-stone-600 mt-1">El stock de tus productos sigue reservado temporalmente para ti.</p>
                </div>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="document.getElementById('checkout-form').submit();" class="btn-terracotta px-4 py-2 rounded-lg text-xs font-bold">
                    Reintentar pago
                </button>
                <a href="#metodo_pago" class="btn-outline-terracotta px-4 py-2 rounded-lg text-xs font-bold">
                    Usar otro medio de pago
                </a>
            </div>
        </div>
    @endif

    <!-- Tarjeta Principal de Checkout -->
    <div class="bg-white rounded-2xl border border-[#E8E2DA] p-6 sm:p-8 shadow-sm space-y-8">

        <!-- 1. Resumen de Ítems y Totales (Mockup Página 9) -->
        <div class="space-y-3 pb-6 border-b border-stone-100">
            <h2 class="font-serif text-2xl font-bold text-stone-900">Finalizar pedido</h2>
            
            <div class="divide-y divide-stone-100 text-sm">
                @foreach($items as $item)
                    <div class="py-2.5 flex justify-between items-center text-stone-700">
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-stone-900">{{ $item->producto->nombre }}</span>
                            <span class="text-xs text-stone-500 font-bold">×{{ $item->cantidad }}</span>
                        </div>
                        <span class="font-semibold text-stone-900">${{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            <div class="pt-2 space-y-1.5 text-xs text-stone-600">
                <div class="flex justify-between">
                    <span>Envío</span>
                    <span class="font-medium">
                        {{ $totales['costo_envio'] === 0 ? 'Gratis' : '$'.number_format($totales['costo_envio'], 0, ',', '.').' COP' }}
                    </span>
                </div>
                <div class="flex justify-between text-base font-bold text-stone-900 pt-2 border-t border-stone-100">
                    <span>Total a pagar</span>
                    <span class="text-[#A3542E]">${{ number_format($totales['total'], 0, ',', '.') }} COP</span>
                </div>
            </div>

            <!-- Puntos que acumula con este pedido -->
            <div class="pt-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#F7EBE4] text-[#A3542E] text-xs font-bold rounded-full">
                    <span>✨</span> +{{ $totales['puntos_estimados'] }} puntos con este pedido
                </span>
            </div>
        </div>

        <!-- 2. Formulario de Datos de Entrega y Pago -->
        <form id="checkout-form" action="{{ route('checkout.process') }}" method="POST" class="space-y-6">
            @csrf

            <!-- DATOS DE ENTREGA -->
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-[#A3542E] uppercase tracking-wider">Datos de Entrega</h3>

                <!-- Nombre Completo -->
                <div>
                    <label for="nombre" class="block text-xs font-semibold text-stone-700 mb-1">Nombre completo</label>
                    <input type="text" name="nombre" id="nombre" required
                           value="{{ old('nombre', $user->name) }}"
                           class="w-full px-3.5 py-2.5 text-sm border {{ $errors->has('nombre') ? 'border-red-500 bg-red-50' : 'border-stone-300' }} rounded-xl focus:border-[#A3542E]"
                           placeholder="María Torres">
                    @error('nombre')
                        <p class="text-xs text-red-600 mt-1 font-semibold flex items-center gap-1">
                            <span>ⓘ</span> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Celular y Ciudad en 2 columnas -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="celular" class="block text-xs font-semibold text-stone-700 mb-1">Celular (WhatsApp)</label>
                        <input type="tel" name="celular" id="celular" required maxlength="10"
                               value="{{ old('celular', $user->telefono) }}"
                               class="w-full px-3.5 py-2.5 text-sm border {{ $errors->has('celular') ? 'border-red-500 bg-red-50' : 'border-stone-300' }} rounded-xl focus:border-[#A3542E]"
                               placeholder="3105550142">
                        @error('celular')
                            <p class="text-xs text-red-600 mt-1 font-semibold flex items-center gap-1">
                                <span>ⓘ</span> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="ciudad" class="block text-xs font-semibold text-stone-700 mb-1">Ciudad</label>
                        <input type="text" name="ciudad" id="ciudad" required
                               value="{{ old('ciudad', $direccionPrincipal?->ciudad ?? 'Bogotá') }}"
                               class="w-full px-3.5 py-2.5 text-sm border {{ $errors->has('ciudad') ? 'border-red-500 bg-red-50' : 'border-stone-300' }} rounded-xl focus:border-[#A3542E]"
                               placeholder="Bogotá">
                        @error('ciudad')
                            <p class="text-xs text-red-600 mt-1 font-semibold flex items-center gap-1">
                                <span>ⓘ</span> {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Dirección de Entrega con autofoco si está vacía -->
                <div>
                    <label for="direccion" class="block text-xs font-semibold text-stone-700 mb-1">Dirección de entrega</label>
                    <input type="text" name="direccion" id="direccion" required
                           value="{{ old('direccion', $direccionPrincipal?->direccion) }}"
                           {{ empty($direccionPrincipal?->direccion) ? 'autofocus' : '' }}
                           class="w-full px-3.5 py-2.5 text-sm border {{ $errors->has('direccion') ? 'border-red-500 bg-red-50' : 'border-stone-300' }} rounded-xl focus:border-[#A3542E]"
                           placeholder="Calle, número, apto (ej: Cra 15 # 80-20, Apto 301)">
                    @error('direccion')
                        <p class="text-xs text-red-600 mt-1 font-semibold flex items-center gap-1">
                            <span>ⓘ</span> {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <!-- MÉTODO DE PAGO -->
            <div class="space-y-3 pt-4 border-t border-stone-100" id="metodo_pago">
                <h3 class="text-xs font-bold text-[#A3542E] uppercase tracking-wider">Método de Pago</h3>

                <div class="grid grid-cols-3 gap-3">
                    <label class="border-2 border-stone-200 rounded-xl p-3 text-center cursor-pointer hover:border-[#A3542E] transition has-[:checked]:border-[#A3542E] has-[:checked]:bg-[#FAF8F5]">
                        <input type="radio" name="metodo_pago" value="tarjeta" class="sr-only" {{ old('metodo_pago', 'tarjeta') === 'tarjeta' ? 'checked' : '' }}>
                        <span class="block text-xs font-bold text-stone-900">💳 Tarjeta</span>
                        <span class="block text-[10px] text-stone-500 mt-0.5">Crédito / Débito</span>
                    </label>

                    <label class="border-2 border-stone-200 rounded-xl p-3 text-center cursor-pointer hover:border-[#A3542E] transition has-[:checked]:border-[#A3542E] has-[:checked]:bg-[#FAF8F5]">
                        <input type="radio" name="metodo_pago" value="pse" class="sr-only" {{ old('metodo_pago') === 'pse' ? 'checked' : '' }}>
                        <span class="block text-xs font-bold text-stone-900">🏦 PSE</span>
                        <span class="block text-[10px] text-stone-500 mt-0.5">Débito bancario</span>
                    </label>

                    <label class="border-2 border-stone-200 rounded-xl p-3 text-center cursor-pointer hover:border-[#A3542E] transition has-[:checked]:border-[#A3542E] has-[:checked]:bg-[#FAF8F5]">
                        <input type="radio" name="metodo_pago" value="billetera" class="sr-only" {{ old('metodo_pago') === 'billetera' ? 'checked' : '' }}>
                        <span class="block text-xs font-bold text-stone-900">📱 Billetera</span>
                        <span class="block text-[10px] text-stone-500 mt-0.5">Nequi / Daviplata</span>
                    </label>
                </div>
                @error('metodo_pago')
                    <p class="text-xs text-red-600 mt-1 font-semibold flex items-center gap-1">
                        <span>ⓘ</span> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Casilla Términos y Condiciones -->
            <div class="pt-2">
                <div class="flex items-start gap-2.5">
                    <input type="checkbox" name="acepto_terminos" id="acepto_terminos" required value="1"
                           {{ old('acepto_terminos', true) ? 'checked' : '' }}
                           class="mt-1 rounded border-stone-300 text-[#A3542E] focus:ring-0">
                    <label for="acepto_terminos" class="text-xs text-stone-600 leading-snug">
                        Acepto los términos y condiciones de compra, tiempos de entrega y política de privacidad.
                    </label>
                </div>
                @error('acepto_terminos')
                    <p class="text-xs text-red-600 mt-1 font-semibold flex items-center gap-1">
                        <span>ⓘ</span> {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Opción para probar Variante Pago Rechazado (Demostración) -->
            <div class="p-3 bg-stone-50 border border-dashed border-stone-300 rounded-lg text-xs text-stone-600 flex items-center gap-2">
                <input type="checkbox" name="simular_pago_fallido" id="simular_pago_fallido" value="1" class="rounded text-red-600">
                <label for="simular_pago_fallido" class="cursor-pointer">
                    <strong class="text-stone-800">Probar Variante de Error:</strong> Simular rechazo de banco en pasarela de pago (Página 10).
                </label>
            </div>

            <!-- Botón Principal con Spinner / Estado Espera (Punto 3 - Página 9 & 10) -->
            <div class="pt-4 space-y-3">
                <button type="submit" id="submit-btn"
                        class="btn-terracotta w-full py-4 rounded-full font-bold text-sm tracking-wide text-center flex items-center justify-center gap-2 shadow-md">
                    <span id="btn-text">Confirmar y pagar</span>
                    <span id="btn-spinner" class="hidden">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Procesando tu pago… No cierres esta ventana
                    </span>
                </button>

                <p class="text-[11px] text-stone-400 text-center">
                    🔒 Pago seguro · tus datos están protegidos bajo cifrado SSL de 256 bits
                </p>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('checkout-form');
        const submitBtn = document.getElementById('submit-btn');
        const btnText = document.getElementById('btn-text');
        const btnSpinner = document.getElementById('btn-spinner');
        const loadingBar = document.getElementById('loading-bar-container');
        const timerText = document.getElementById('loading-timer-text');

        // Foco al primer error si existe (Punto 3 - Accesibilidad)
        const firstErrorInput = document.querySelector('input.border-red-500');
        if (firstErrorInput) {
            firstErrorInput.focus();
        }

        form.addEventListener('submit', function (e) {
            // Evitar doble envío (Página 10 - Un solo envío)
            if (submitBtn.disabled) {
                e.preventDefault();
                return;
            }

            // Cambiar a Estado 2 · Espera (Loading)
            submitBtn.disabled = true;
            btnText.classList.add('hidden');
            btnSpinner.classList.remove('hidden');
            loadingBar.classList.remove('hidden');

            // Regla de feedback: Pasados 30 segundos si sigue en proceso avisar
            setTimeout(function () {
                if (timerText) {
                    timerText.innerHTML = "Sigue en proceso; si tarda más de lo previsto, te confirmaremos la transacción por correo y WhatsApp.";
                }
            }, 30000);
        });
    });
</script>
@endpush
@endsection
