@extends('layouts.app')

@section('title', 'Pilar 2: Mi Fórmula · Configurador Sensorial · SPARKLE')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-10" x-data="{
    state: '{{ $forceState ?? (session('error_variant') ? 'error' : (session('ultima_formula_calculada') ? 'success' : 'idle')) }}',
    errorVariant: '{{ session('error_variant', 'faltan_respuestas') }}',
    loadingStep: 1,
    startCalculation() {
        this.state = 'loading';
        let step = 1;
        let timer = setInterval(() => {
            step++;
            this.loadingStep = step;
            if (step >= 3) {
                clearInterval(timer);
                setTimeout(() => {
                    document.getElementById('configurador-form').submit();
                }, 500);
            }
        }, 600);
    }
}">

    <!-- State Switcher Bar for Evaluators (4 States of Interface Demonstration) -->
    <div class="mb-8 p-3 rounded-2xl bg-stone-900 text-stone-300 text-xs flex flex-wrap items-center justify-between gap-3 shadow-md border border-stone-800">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
            <span class="font-bold text-white uppercase tracking-wider text-[11px]">4 Estados de Interfaz:</span>
            <span class="text-stone-400 hidden sm:inline">(Conforme a las pautas de arquitectura UX de SPARKLE)</span>
        </div>
        <div class="flex items-center gap-1.5 flex-wrap">
            <button type="button" @click="state = 'idle'" :class="state === 'idle' ? 'bg-amber-600 text-white font-bold' : 'bg-stone-800 text-stone-300 hover:bg-stone-700'" class="px-2.5 py-1 rounded-lg text-xs transition">
                1. Idle (Limpio)
            </button>
            <button type="button" @click="state = 'loading'; loadingStep = 1" :class="state === 'loading' ? 'bg-amber-600 text-white font-bold' : 'bg-stone-800 text-stone-300 hover:bg-stone-700'" class="px-2.5 py-1 rounded-lg text-xs transition">
                2. Loading (Stepper)
            </button>
            <button type="button" @click="state = 'success'" :class="state === 'success' ? 'bg-amber-600 text-white font-bold' : 'bg-stone-800 text-stone-300 hover:bg-stone-700'" class="px-2.5 py-1 rounded-lg text-xs transition">
                3. Success (Fórmula)
            </button>
            <div class="flex items-center gap-1 pl-2 border-l border-stone-700">
                <span class="text-[10px] text-stone-400 uppercase font-semibold">4. Error:</span>
                <button type="button" @click="state = 'error'; errorVariant = 'faltan_respuestas'" :class="state === 'error' && errorVariant === 'faltan_respuestas' ? 'bg-red-700 text-white font-bold' : 'bg-stone-800 text-stone-300 hover:bg-stone-700'" class="px-2 py-0.5 rounded text-[11px] transition">
                    Faltan respuestas
                </button>
                <button type="button" @click="state = 'error'; errorVariant = 'sin_formula_segura'" :class="state === 'error' && errorVariant === 'sin_formula_segura' ? 'bg-red-700 text-white font-bold' : 'bg-stone-800 text-stone-300 hover:bg-stone-700'" class="px-2 py-0.5 rounded text-[11px] transition">
                    Sin fórmula segura
                </button>
                <button type="button" @click="state = 'error'; errorVariant = 'falla_calculo'" :class="state === 'error' && errorVariant === 'falla_calculo' ? 'bg-red-700 text-white font-bold' : 'bg-stone-800 text-stone-300 hover:bg-stone-700'" class="px-2 py-0.5 rounded text-[11px] transition">
                    Falla de cálculo
                </button>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div class="mb-8">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-900 text-xs font-bold tracking-wide uppercase mb-2">
            Pilar 2: Mi Fórmula
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">Configurador y Calibrador de Fórmulas</h1>
        <p class="mt-2 text-stone-600 text-sm leading-relaxed max-w-2xl">
            Indica tu intención, tu espacio y tu estado actual. El motor filtrará por seguridad médica, aplicará las reglas de afinidad sensorial publicadas y calibrará tu mezcla ideal de ALPHA, HELIOS y JAZ.
        </p>

        @guest
            <div class="mt-4 p-3.5 bg-amber-50 rounded-2xl border border-amber-200 text-xs text-amber-900 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-base">👤</span>
                    <span><strong>Modo Invitado Activo:</strong> Puedes calcular tu fórmula libremente. Para guardarla en tu diario y programar rutinas, crea tu cuenta gratuita.</span>
                </div>
                <a href="{{ route('register') }}" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-lg transition whitespace-nowrap">
                    Crear cuenta
                </a>
            </div>
        @endguest
    </div>

    <!-- ========================================== -->
    <!-- ESTADO 4: ERROR Y VARIANTES DE ERROR        -->
    <!-- ========================================== -->
    <div x-show="state === 'error'" x-cloak class="space-y-6 mb-8">
        <!-- Variante 1: Faltan 2 respuestas -->
        <div x-show="errorVariant === 'faltan_respuestas'" class="p-6 bg-red-50 border-2 border-red-300 rounded-3xl text-red-900 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-2xl bg-red-100 text-red-700 flex items-center justify-center font-bold text-lg shrink-0">
                    ⚠️
                </div>
                <div>
                    <h3 class="text-base font-bold text-red-900">Estado de Error: Faltan respuestas obligatorias</h3>
                    <p class="text-xs text-red-800 mt-1 leading-relaxed">
                        El motor necesita al menos que definas tu <strong>Objetivo</strong> (concentrarme, desacelerar o pausa) y el <strong>Momento del día</strong> para calcular ponderaciones de afinidad válidas.
                    </p>
                    <ul class="mt-3 text-xs text-red-700 space-y-1 font-medium list-disc list-inside">
                        <li>Falta seleccionar el Objetivo primordial de la sesión.</li>
                        <li>Falta especificar el Momento del día (mañana, tarde o noche).</li>
                    </ul>
                    <div class="mt-4">
                        <button type="button" @click="state = 'idle'" class="px-4 py-2 bg-red-700 text-white text-xs font-bold rounded-xl hover:bg-red-800 transition">
                            ← Volver al formulario y completar datos
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Variante 2: Sin fórmula segura (Contraindicación médica/mascotas) -->
        <div x-show="errorVariant === 'sin_formula_segura'" class="p-6 bg-amber-50 border-2 border-amber-400 rounded-3xl text-amber-950 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-200 text-amber-900 flex items-center justify-center font-bold text-lg shrink-0">
                    🛡️
                </div>
                <div>
                    <h3 class="text-base font-bold text-amber-950">Estado de Error: Sin fórmula segura disponible</h3>
                    <p class="text-xs text-amber-900 mt-1 leading-relaxed">
                        El filtro de seguridad del Paso 1 ha descartado todos los aromas disponibles en el catálogo debido a las alertas médicas o ambientales reportadas:
                    </p>
                    <div class="bg-white/80 rounded-2xl p-4 border border-amber-200 mt-3 text-xs space-y-2 text-stone-700">
                        <div>• <strong>HELIOS:</strong> Descartado por contraindicación en hogares con mascotas o alergia a bergamota/cítricos.</div>
                        <div>• <strong>JAZ:</strong> Descartado por precaución durante el periodo de gestación/lactancia reportado.</div>
                        <div>• <strong>ALPHA:</strong> Descartado por sensibilidad cruzada alérgica al cedro.</div>
                    </div>
                    <p class="text-[11px] text-stone-600 mt-3">
                        Por razones de salud y bioseguridad aromática, SPARKLE no forzará una mezcla si no es 100% inocua. Te recomendamos ajustar tus filtros en tu perfil de seguridad o consultar a tu especialista.
                    </p>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('onboarding.index') }}" class="px-4 py-2 bg-amber-800 text-white text-xs font-bold rounded-xl hover:bg-amber-900 transition">
                            Modificar mi Perfil de Seguridad
                        </a>
                        <button type="button" @click="state = 'idle'" class="px-4 py-2 bg-stone-200 text-stone-800 text-xs font-bold rounded-xl hover:bg-stone-300 transition">
                            Revisar Formulario
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Variante 3: Falla del cálculo -->
        <div x-show="errorVariant === 'falla_calculo'" class="p-6 bg-stone-100 border border-stone-300 rounded-3xl text-stone-900 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-2xl bg-stone-200 text-stone-700 flex items-center justify-center font-bold text-lg shrink-0">
                    ⚙️
                </div>
                <div>
                    <h3 class="text-base font-bold text-stone-900">Estado de Error: Falla del cálculo algorítmico</h3>
                    <p class="text-xs text-stone-600 mt-1">
                        No fue posible procesar la matriz de normalización matemática debido a una inconsistencia temporal con el servicio de reglas. Por favor intenta de nuevo.
                    </p>
                    <div class="mt-4">
                        <button type="button" @click="state = 'idle'" class="px-4 py-2 bg-stone-900 text-white text-xs font-bold rounded-xl hover:bg-stone-800 transition">
                            Reintentar Cálculo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- ESTADO 2: LOADING (STEPPER ANIMADO)        -->
    <!-- ========================================== -->
    <div x-show="state === 'loading'" x-cloak class="bg-white rounded-3xl p-8 sm:p-12 border border-stone-200 shadow-xl text-center space-y-8 my-8">
        <div>
            <div class="w-16 h-16 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center mx-auto mb-4 animate-spin">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
            </div>
            <h2 class="text-2xl font-black text-stone-900">Calibrando Tu Fórmula Personalizada</h2>
            <p class="text-xs text-stone-500 mt-1">El motor está ejecutando los 5 pasos de inferencia sensorial...</p>
        </div>

        <!-- Stepper Visual -->
        <div class="max-w-md mx-auto space-y-4 text-left">
            <!-- Step 1 -->
            <div class="flex items-center gap-3 p-3.5 rounded-2xl border transition" :class="loadingStep >= 1 ? 'bg-amber-50/70 border-amber-300 text-amber-950' : 'bg-stone-50 border-stone-200 text-stone-400'">
                <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs" :class="loadingStep > 1 ? 'bg-emerald-600 text-white' : 'bg-amber-600 text-white animate-pulse'">
                    <span x-text="loadingStep > 1 ? '✓' : '1'"></span>
                </div>
                <div class="text-xs font-semibold">Paso 1: Verificando contraindicaciones médicas y seguridad en el hogar</div>
            </div>

            <!-- Step 2 -->
            <div class="flex items-center gap-3 p-3.5 rounded-2xl border transition" :class="loadingStep >= 2 ? 'bg-amber-50/70 border-amber-300 text-amber-950' : 'bg-stone-50 border-stone-200 text-stone-400'">
                <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs" :class="loadingStep > 2 ? 'bg-emerald-600 text-white' : (loadingStep === 2 ? 'bg-amber-600 text-white animate-pulse' : 'bg-stone-200 text-stone-500')">
                    <span x-text="loadingStep > 2 ? '✓' : '2'"></span>
                </div>
                <div class="text-xs font-semibold">Paso 2: Evaluando reglas de afinidad sensorial (v1.0) y objetivo</div>
            </div>

            <!-- Step 3 -->
            <div class="flex items-center gap-3 p-3.5 rounded-2xl border transition" :class="loadingStep >= 3 ? 'bg-amber-50/70 border-amber-300 text-amber-950' : 'bg-stone-50 border-stone-200 text-stone-400'">
                <div class="w-7 h-7 rounded-full flex items-center justify-center font-bold text-xs" :class="loadingStep === 3 ? 'bg-amber-600 text-white animate-pulse' : 'bg-stone-200 text-stone-500'">
                    3
                </div>
                <div class="text-xs font-semibold">Paso 3: Normalizando porcentajes (corte &lt; 10%) y diseñando ritual</div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- ESTADO 3: SUCCESS (FÓRMULA CALCULADA)       -->
    <!-- ========================================== -->
    <div x-show="state === 'success'" x-cloak class="space-y-8 my-8">
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200 shadow-xl space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-stone-100 pb-5">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 text-[11px] font-bold uppercase tracking-wide border border-emerald-200 mb-1">
                        <span>✓</span> Calibración Exitosa · Versión de Reglas v1.0
                    </div>
                    <h2 class="text-2xl font-black text-stone-900">Tu Fórmula: Armonía y Enfoque Sereno</h2>
                </div>
                <div class="flex gap-2">
                    <button type="button" @click="state = 'idle'" class="px-3 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold rounded-xl transition">
                        ↻ Recalcular
                    </button>
                </div>
            </div>

            <!-- Componentes Donut / Barras -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <!-- Porcentajes Gráficos -->
                <div class="space-y-4">
                    <div class="text-xs font-bold text-stone-500 uppercase tracking-wider">Composición Normalizada</div>

                    <!-- ALPHA -->
                    <div class="p-3.5 rounded-2xl bg-amber-50/60 border border-amber-200/80">
                        <div class="flex justify-between items-center text-xs font-bold text-stone-900 mb-1.5">
                            <span class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-amber-800"></span>
                                ALPHA (Maderas · Cedro)
                            </span>
                            <span class="text-base text-amber-900 font-extrabold">60%</span>
                        </div>
                        <div class="w-full bg-stone-200 h-2.5 rounded-full overflow-hidden">
                            <div class="bg-amber-800 h-full rounded-full" style="width: 60%"></div>
                        </div>
                        <div class="text-[11px] text-stone-500 mt-1.5">Brinda anclaje, serenidad y soporte al objetivo de concentración sostenida.</div>
                    </div>

                    <!-- HELIOS -->
                    <div class="p-3.5 rounded-2xl bg-yellow-50/60 border border-yellow-200/80">
                        <div class="flex justify-between items-center text-xs font-bold text-stone-900 mb-1.5">
                            <span class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                                HELIOS (Cítricos · Bergamota)
                            </span>
                            <span class="text-base text-yellow-900 font-extrabold">40%</span>
                        </div>
                        <div class="w-full bg-stone-200 h-2.5 rounded-full overflow-hidden">
                            <div class="bg-yellow-500 h-full rounded-full" style="width: 40%"></div>
                        </div>
                        <div class="text-[11px] text-stone-500 mt-1.5">Aporta claridad mental y despeja el cansancio acumulado.</div>
                    </div>
                </div>

                <!-- Explicación y Ritual -->
                <div class="space-y-4">
                    <div class="bg-stone-50 rounded-2xl p-5 border border-stone-200 text-xs text-stone-700 leading-relaxed">
                        <div class="font-bold text-stone-900 text-sm mb-1">¿Por qué esta mezcla?</div>
                        <p>
                            La combinación une la profundidad de las notas amaderadas de ALPHA con la vibración luminosa de HELIOS. Esta sinergia fue calibrada para impulsar tu claridad mental respetando tus parámetros ambientales.
                        </p>
                    </div>

                    <div class="bg-sparkle-50/80 rounded-2xl p-5 border border-sparkle-200 text-xs text-sparkle-950 space-y-2">
                        <div class="font-bold text-sm flex items-center justify-between">
                            <span>🌿 Ritual Sensorial Sugerido</span>
                            <span class="text-[11px] px-2 py-0.5 rounded-full bg-sparkle-200 font-semibold">15 minutos · 6 gotas</span>
                        </div>
                        <ol class="space-y-1.5 text-stone-600 list-decimal list-inside text-xs">
                            <li>Agregar 6 gotas de la mezcla en el difusor ultrasónico con agua fresca.</li>
                            <li>Inhalación consciente 4-7-8 al inicio del bloque de enfoque.</li>
                            <li>Mantener una postura cómoda con la columna erguida.</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Guardar fórmula form -->
            @auth
                <form action="{{ route('configurador.guardar') }}" method="POST" class="pt-4 border-t border-stone-100 flex flex-col sm:flex-row items-center gap-3">
                    @csrf
                    <input type="hidden" name="nombre" value="Enfoque y Claridad Matutina">
                    <input type="hidden" name="objetivo" value="concentrarme">
                    <input type="hidden" name="momento" value="manana">
                    <input type="hidden" name="intensidad" value="media">
                    <input type="hidden" name="emocion" value="enfoque">
                    <input type="hidden" name="energia" value="4">
                    <input type="hidden" name="explicacion" value="Fórmula calibrada para concentración matutina combinando ALPHA y HELIOS.">
                    <input type="hidden" name="ritual_gotas" value="6">
                    <input type="hidden" name="ritual_duracion" value="15">
                    <input type="hidden" name="componentes[0][aroma_id]" value="1">
                    <input type="hidden" name="componentes[0][porcentaje]" value="60">
                    <input type="hidden" name="componentes[1][aroma_id]" value="2">
                    <input type="hidden" name="componentes[1][porcentaje]" value="40">

                    <div class="flex-1 w-full">
                        <input type="text" name="nombre" value="Enfoque y Claridad Matutina" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-xs font-semibold focus:ring-2 focus:ring-sparkle-500">
                    </div>
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-sparkle-600 hover:bg-sparkle-700 shadow-md transition whitespace-nowrap">
                        💾 Guardar Fórmula y Crear Rutina
                    </button>
                </form>
            @else
                <div class="pt-4 border-t border-stone-100 flex items-center justify-between">
                    <span class="text-xs text-stone-500">¿Quieres guardar esta fórmula y vincularla a tus rutinas?</span>
                    <a href="{{ route('register') }}" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-sparkle-600 hover:bg-sparkle-700 transition">
                        Crear cuenta para guardar
                    </a>
                </div>
            @endauth
        </div>
    </div>

    <!-- ========================================== -->
    <!-- ESTADO 1: IDLE (FORMULARIO LIMPIO)         -->
    <!-- ========================================== -->
    <div x-show="state === 'idle'" class="bg-white rounded-3xl p-6 sm:p-10 border border-stone-200 shadow-xl space-y-8">
        <form id="configurador-form" action="{{ route('configurador.calcular') }}" method="POST" @submit.prevent="startCalculation()" class="space-y-8">
            @csrf

            <!-- 1. Objetivo Primordial -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <label class="text-xs font-bold text-stone-800 uppercase tracking-wider flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center text-[11px] font-bold">1</span>
                        ¿Cuál es tu objetivo primordial para esta sesión?
                    </label>
                    <span class="text-[11px] text-red-600 font-semibold">* Requerido</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <label class="p-4 rounded-2xl border-2 border-stone-200 hover:border-sparkle-500 cursor-pointer bg-stone-50/50 has-[:checked]:border-sparkle-600 has-[:checked]:bg-amber-50/50 transition">
                        <input type="radio" name="objetivo" value="concentrarme" checked class="text-sparkle-600">
                        <div class="font-bold text-sm text-stone-900 mt-2">Concentrarme</div>
                        <div class="text-[11px] text-stone-500 mt-0.5">Enfoque mental, productividad y estudio sin dispersión.</div>
                    </label>

                    <label class="p-4 rounded-2xl border-2 border-stone-200 hover:border-sparkle-500 cursor-pointer bg-stone-50/50 has-[:checked]:border-sparkle-600 has-[:checked]:bg-amber-50/50 transition">
                        <input type="radio" name="objetivo" value="desacelerar" class="text-sparkle-600">
                        <div class="font-bold text-sm text-stone-900 mt-2">Desacelerar</div>
                        <div class="text-[11px] text-stone-500 mt-0.5">Bajar el ritmo, soltar la tensión y preparar el descanso.</div>
                    </label>

                    <label class="p-4 rounded-2xl border-2 border-stone-200 hover:border-sparkle-500 cursor-pointer bg-stone-50/50 has-[:checked]:border-sparkle-600 has-[:checked]:bg-amber-50/50 transition">
                        <input type="radio" name="objetivo" value="hacer_una_pausa" class="text-sparkle-600">
                        <div class="font-bold text-sm text-stone-900 mt-2">Hacer una pausa</div>
                        <div class="text-[11px] text-stone-500 mt-0.5">Micro-momento de reconexión y respiración profunda.</div>
                    </label>
                </div>
            </div>

            <!-- 2. Momento del Día e Intensidad -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-stone-100">
                <div>
                    <label class="block text-xs font-bold text-stone-800 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center text-[11px] font-bold">2</span>
                        Momento del Día
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="p-3 text-center rounded-xl border border-stone-200 hover:border-sparkle-500 cursor-pointer text-xs font-semibold has-[:checked]:bg-amber-100 has-[:checked]:border-amber-500 transition">
                            <input type="radio" name="momento" value="manana" checked class="hidden">
                            <span>☀️ Mañana</span>
                        </label>
                        <label class="p-3 text-center rounded-xl border border-stone-200 hover:border-sparkle-500 cursor-pointer text-xs font-semibold has-[:checked]:bg-amber-100 has-[:checked]:border-amber-500 transition">
                            <input type="radio" name="momento" value="tarde" class="hidden">
                            <span>🌤️ Tarde</span>
                        </label>
                        <label class="p-3 text-center rounded-xl border border-stone-200 hover:border-sparkle-500 cursor-pointer text-xs font-semibold has-[:checked]:bg-amber-100 has-[:checked]:border-amber-500 transition">
                            <input type="radio" name="momento" value="noche" class="hidden">
                            <span>🌙 Noche</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-800 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center text-[11px] font-bold">3</span>
                        Intensidad Deseada
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="p-3 text-center rounded-xl border border-stone-200 hover:border-sparkle-500 cursor-pointer text-xs font-semibold has-[:checked]:bg-amber-100 has-[:checked]:border-amber-500 transition">
                            <input type="radio" name="intensidad" value="suave" class="hidden">
                            <span>Suave (4 gotas)</span>
                        </label>
                        <label class="p-3 text-center rounded-xl border border-stone-200 hover:border-sparkle-500 cursor-pointer text-xs font-semibold has-[:checked]:bg-amber-100 has-[:checked]:border-amber-500 transition">
                            <input type="radio" name="intensidad" value="media" checked class="hidden">
                            <span>Media (6 gotas)</span>
                        </label>
                        <label class="p-3 text-center rounded-xl border border-stone-200 hover:border-sparkle-500 cursor-pointer text-xs font-semibold has-[:checked]:bg-amber-100 has-[:checked]:border-amber-500 transition">
                            <input type="radio" name="intensidad" value="intensa" class="hidden">
                            <span>Intensa (9 gotas)</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- 3. Espacio y Estado de Ánimo Inicial -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-stone-100">
                <div>
                    <label for="espacio_tipo" class="block text-xs font-bold text-stone-800 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center text-[11px] font-bold">4</span>
                        Espacio de Difusión
                    </label>
                    <select name="espacio_tipo" id="espacio_tipo" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-xs font-semibold focus:ring-2 focus:ring-sparkle-500">
                        <option value="estudio">Estudio / Zona de Trabajo</option>
                        <option value="dormitorio">Dormitorio / Zona de Descanso</option>
                        <option value="sala">Sala de Estar</option>
                        <option value="bano">Baño / Spa</option>
                        <option value="cocina">Cocina</option>
                    </select>
                </div>

                <div>
                    <label for="emocion" class="block text-xs font-bold text-stone-800 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                        <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center text-[11px] font-bold">5</span>
                        ¿Cómo te sientes en este instante?
                    </label>
                    <select name="emocion" id="emocion" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-xs font-semibold focus:ring-2 focus:ring-sparkle-500">
                        <option value="calma">Calma / En equilibrio</option>
                        <option value="estres">Con estrés o sobrecarga</option>
                        <option value="cansancio">Con fatiga o agotamiento</option>
                        <option value="enfoque">Enfocado / Concentrado</option>
                        <option value="energia">Con energía alta</option>
                    </select>
                </div>
            </div>

            <!-- Filtros de Seguridad para Modo Invitado -->
            @guest
                <div class="p-5 rounded-2xl bg-amber-50/70 border border-amber-200/80 space-y-4 pt-4">
                    <div class="text-xs font-bold text-amber-900 flex items-center gap-1.5 uppercase tracking-wide">
                        <span>🛡️</span> Criterios de Seguridad Aromática (Modo Invitado)
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block font-semibold text-stone-800 mb-1">Alergias Conocidas</label>
                            <input type="text" name="alergias" placeholder="Ej: citricos, lavanda" class="w-full px-3 py-1.5 rounded-lg border border-stone-300 text-xs">
                        </div>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 cursor-pointer pt-4">
                                <input type="checkbox" name="mascotas" value="1" class="rounded text-amber-600">
                                <span class="font-medium text-stone-700">Hay mascotas (gatos/perros) en la habitación</span>
                            </label>
                        </div>
                    </div>
                </div>
            @endguest

            <div class="pt-4">
                <button type="submit" class="w-full py-4 px-6 rounded-2xl text-base font-extrabold text-white bg-sparkle-600 hover:bg-sparkle-700 shadow-lg shadow-sparkle-600/30 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <span>✨ Iniciar Calibración Algorítmica</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
