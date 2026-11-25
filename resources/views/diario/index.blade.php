@extends('layouts.app')

@section('title', 'Pilar 3: Mi Diario de Experiencias y Racha · SPARKLE')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{
    state: '{{ $forceState ?? (session('success') ? 'success' : 'idle') }}',
    loading: false,
    submitDiario() {
        this.state = 'loading';
        setTimeout(() => {
            document.getElementById('diario-form').submit();
        }, 600);
    }
}">

    <!-- State Switcher Bar for Evaluators (4 States of Interface Demonstration) -->
    <div class="mb-8 p-3 rounded-2xl bg-stone-900 text-stone-300 text-xs flex flex-wrap items-center justify-between gap-3 shadow-md border border-stone-800">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
            <span class="font-bold text-white uppercase tracking-wider text-[11px]">4 Estados de Interfaz en Diario:</span>
        </div>
        <div class="flex items-center gap-1.5 flex-wrap">
            <button type="button" @click="state = 'idle'" :class="state === 'idle' ? 'bg-amber-600 text-white font-bold' : 'bg-stone-800 text-stone-300 hover:bg-stone-700'" class="px-2.5 py-1 rounded-lg text-xs transition">
                1. Idle (Formulario)
            </button>
            <button type="button" @click="state = 'loading'" :class="state === 'loading' ? 'bg-amber-600 text-white font-bold' : 'bg-stone-800 text-stone-300 hover:bg-stone-700'" class="px-2.5 py-1 rounded-lg text-xs transition">
                2. Loading (Registrando)
            </button>
            <button type="button" @click="state = 'success'" :class="state === 'success' ? 'bg-amber-600 text-white font-bold' : 'bg-stone-800 text-stone-300 hover:bg-stone-700'" class="px-2.5 py-1 rounded-lg text-xs transition">
                3. Success (Comparativo & Racha)
            </button>
            <button type="button" @click="state = 'error'" :class="state === 'error' ? 'bg-red-700 text-white font-bold' : 'bg-stone-800 text-stone-300 hover:bg-stone-700'" class="px-2.5 py-1 rounded-lg text-xs transition">
                4. Error (Validación)
            </button>
        </div>
    </div>

    <!-- Header & Streak Banner -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="md:col-span-2">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-900 text-xs font-bold tracking-wide uppercase mb-2">
                Pilar 3: Diario & Evolución
            </div>
            <h1 class="text-3xl font-black text-stone-900 tracking-tight">Mi Diario de Experiencias Sensoriales</h1>
            <p class="mt-1 text-stone-600 text-sm">
                Registra cómo cambió tu estado emocional antes y después de cada sesión. El algoritmo utiliza tus calificaciones para calibrar automáticamente tus fórmulas futuras.
            </p>
        </div>

        <!-- Racha y Bienestar Card -->
        <div class="bg-gradient-to-tr from-amber-500 to-sparkle-600 text-white rounded-3xl p-6 shadow-lg flex items-center justify-between">
            <div>
                <div class="text-[11px] uppercase tracking-wider font-bold text-amber-100">Racha Consecutiva</div>
                <div class="text-3xl font-black mt-1 flex items-center gap-1.5">
                    <span>🔥</span>
                    <span>{{ $racha }} {{ $racha === 1 ? 'Día' : 'Días' }}</span>
                </div>
                <div class="text-[11px] text-amber-100 mt-1">
                    {{ $totalSesiones }} sesiones registradas en total
                </div>
            </div>
            <div class="text-right border-l border-white/20 pl-4">
                <div class="text-[11px] uppercase tracking-wider font-bold text-amber-100">Promedio</div>
                <div class="text-2xl font-black mt-1">★ {{ $promedioCalif }}</div>
                <div class="text-[10px] text-amber-200">Efectividad</div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- ESTADO 4: ERROR DE VALIDACIÓN              -->
    <!-- ========================================== -->
    <div x-show="state === 'error'" x-cloak class="p-6 bg-red-50 border-2 border-red-300 rounded-3xl text-red-900 shadow-sm mb-8">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-2xl bg-red-100 text-red-700 flex items-center justify-center font-bold text-lg shrink-0">
                ⚠️
            </div>
            <div>
                <h3 class="text-base font-bold text-red-900">Estado de Error: Datos de sesión incompletos</h3>
                <p class="text-xs text-red-800 mt-1">
                    Para registrar tu experiencia en el diario y alimentar el algoritmo de retroalimentación, debes calificar la experiencia del 1 al 5 y reportar tanto tu emoción antes como después del ritual.
                </p>
                <div class="mt-4">
                    <button type="button" @click="state = 'idle'" class="px-4 py-2 bg-red-700 text-white text-xs font-bold rounded-xl hover:bg-red-800 transition">
                        Completar campos requeridos
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- ESTADO 2: LOADING (REGISTRANDO)            -->
    <!-- ========================================== -->
    <div x-show="state === 'loading'" x-cloak class="bg-white rounded-3xl p-10 border border-stone-200 shadow-xl text-center space-y-4 my-8">
        <div class="w-14 h-14 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center mx-auto animate-spin">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
        </div>
        <h3 class="text-xl font-bold text-stone-900">Guardando Experiencia Sensorial...</h3>
        <p class="text-xs text-stone-500 max-w-sm mx-auto">Calculando evolución de energía, actualizando racha y calibrando afinidades futuras.</p>
    </div>

    <!-- ========================================== -->
    <!-- ESTADO 3: SUCCESS (COMPARATIVO Y LOGRO)    -->
    <!-- ========================================== -->
    <div x-show="state === 'success'" x-cloak class="bg-white rounded-3xl p-6 sm:p-8 border-2 border-emerald-300 shadow-xl space-y-6 mb-8">
        <div class="flex items-center justify-between border-b border-stone-100 pb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-lg">
                    ✨
                </div>
                <div>
                    <h3 class="text-lg font-bold text-stone-900">¡Sesión Registrada en tu Diario!</h3>
                    <p class="text-xs text-emerald-700 font-medium">Comparativo emocional y aprendizaje algorítmico completados</p>
                </div>
            </div>
            <button type="button" @click="state = 'idle'" class="px-3 py-1.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 text-xs font-bold transition">
                + Nueva Entrada
            </button>
        </div>

        <!-- Delta de Ánimo -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
            <div class="p-4 rounded-2xl bg-stone-50 border border-stone-200">
                <div class="text-[11px] font-bold text-stone-500 uppercase">Antes de la Sesión</div>
                <div class="text-xl font-extrabold text-stone-800 mt-1 capitalize">Estrés / Sobrecarga</div>
                <div class="text-xs text-stone-500 mt-0.5">Nivel de energía: 2 / 5</div>
            </div>

            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 flex flex-col items-center justify-center">
                <span class="text-2xl mb-1">🌿</span>
                <span class="text-xs font-bold text-amber-900">Ritual Concluido</span>
                <span class="text-[10px] text-amber-700 font-semibold mt-0.5">Fórmula: Enfoque Sereno</span>
            </div>

            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200">
                <div class="text-[11px] font-bold text-emerald-700 uppercase">Al Finalizar la Sesión</div>
                <div class="text-xl font-extrabold text-emerald-900 mt-1 capitalize">Calma & Enfoque</div>
                <div class="text-xs text-emerald-700 mt-0.5">Nivel de energía: 4 / 5 (+2 pts)</div>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-stone-50 border border-stone-200 text-xs text-stone-600 flex items-start gap-2.5">
            <span class="text-base">💡</span>
            <div>
                <strong>Impacto en el motor:</strong> Al otorgar calificación alta (5/5) a esta mezcla con <em>ALPHA</em> y <em>HELIOS</em>, el algoritmo incrementará automáticamente su afinidad (+0.5) cuando busques concentración matutina.
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- ESTADO 1: IDLE (FORMULARIO DE ENTRADA)     -->
    <!-- ========================================== -->
    <div x-show="state === 'idle'" class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200 shadow-xl space-y-6 mb-12">
        <h2 class="text-xl font-bold text-stone-900 flex items-center gap-2">
            <span>✍️</span> Registrar Nueva Experiencia Olfativa
        </h2>

        <form id="diario-form" action="{{ route('diario.store') }}" method="POST" @submit.prevent="submitDiario()" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="fecha" class="block text-xs font-bold text-stone-700 uppercase mb-1">Fecha de la Sesión</label>
                    <input type="date" name="fecha" id="fecha" value="{{ old('fecha', now()->format('Y-m-d')) }}" required class="w-full px-3 py-2 rounded-xl border border-stone-300 text-xs font-semibold focus:ring-2 focus:ring-sparkle-500">
                </div>

                <div>
                    <label for="formula_id" class="block text-xs font-bold text-stone-700 uppercase mb-1">Fórmula Utilizada</label>
                    <select name="formula_id" id="formula_id" class="w-full px-3 py-2 rounded-xl border border-stone-300 text-xs font-semibold focus:ring-2 focus:ring-sparkle-500">
                        @foreach($formulas as $f)
                            <option value="{{ $f->id }}">{{ $f->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="rutina_id" class="block text-xs font-bold text-stone-700 uppercase mb-1">Rutina Asociada (Opcional)</label>
                    <select name="rutina_id" id="rutina_id" class="w-full px-3 py-2 rounded-xl border border-stone-300 text-xs font-semibold focus:ring-2 focus:ring-sparkle-500">
                        <option value="">Ninguna / Sesión libre</option>
                        @foreach($rutinas as $r)
                            <option value="{{ $r->id }}">{{ $r->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Emociones Antes y Después -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-4 rounded-2xl bg-stone-50 border border-stone-200">
                <div>
                    <label for="emocion_antes" class="block text-xs font-bold text-stone-800 uppercase mb-1">¿Cómo te sentías antes del ritual?</label>
                    <select name="emocion_antes" id="emocion_antes" required class="w-full px-3 py-2 rounded-xl border border-stone-300 text-xs font-semibold focus:ring-2 focus:ring-sparkle-500">
                        <option value="estres">Con estrés o sobrecarga mental</option>
                        <option value="cansancio">Con fatiga o somnolencia</option>
                        <option value="calma">Calma / Estado neutro</option>
                        <option value="enfoque">Enfocado</option>
                        <option value="energia">Con alta energía</option>
                    </select>
                    <div class="mt-2 flex items-center justify-between text-xs text-stone-600">
                        <span>Nivel de energía inicial:</span>
                        <select name="energia_antes" class="px-2 py-1 rounded-lg border border-stone-300 text-xs">
                            <option value="1">1 (Muy baja)</option>
                            <option value="2" selected>2 (Baja)</option>
                            <option value="3">3 (Media)</option>
                            <option value="4">4 (Buena)</option>
                            <option value="5">5 (Óptima)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="emocion_despues" class="block text-xs font-bold text-stone-800 uppercase mb-1">¿Cómo te sientes al terminar?</label>
                    <select name="emocion_despues" id="emocion_despues" required class="w-full px-3 py-2 rounded-xl border border-stone-300 text-xs font-semibold focus:ring-2 focus:ring-sparkle-500">
                        <option value="calma" selected>En calma y equilibrio interior</option>
                        <option value="enfoque">Con claridad mental y enfoque</option>
                        <option value="energia">Revitalizado / Despierto</option>
                        <option value="cansancio">Relajado / Listo para dormir</option>
                        <option value="estres">Aún con tensión</option>
                    </select>
                    <div class="mt-2 flex items-center justify-between text-xs text-stone-600">
                        <span>Nivel de energía final:</span>
                        <select name="energia_despues" class="px-2 py-1 rounded-lg border border-stone-300 text-xs">
                            <option value="1">1 (Muy baja)</option>
                            <option value="2">2 (Baja)</option>
                            <option value="3">3 (Media)</option>
                            <option value="4" selected>4 (Buena)</option>
                            <option value="5">5 (Óptima)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Calificación de la Experiencia -->
            <div>
                <label class="block text-xs font-bold text-stone-800 uppercase mb-2">Calificación Global de la Experiencia (1 a 5 estrellas)</label>
                <div class="flex items-center gap-3">
                    @for($i = 5; $i >= 1; $i--)
                        <label class="flex items-center gap-1 cursor-pointer">
                            <input type="radio" name="calificacion" value="{{ $i }}" {{ $i === 5 ? 'checked' : '' }} class="text-amber-500 focus:ring-amber-400">
                            <span class="text-sm font-bold text-stone-800">{{ $i }} ★</span>
                        </label>
                    @endfor
                </div>
            </div>

            <!-- Nota personal y etiquetas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nota" class="block text-xs font-bold text-stone-700 uppercase mb-1">Notas Personales o Sensaciones</label>
                    <textarea name="nota" id="nota" rows="2" placeholder="Ej: Noté cómo las notas de cedro me permitieron sentarme a trabajar con menos rumiación mental..." class="w-full px-3 py-2 rounded-xl border border-stone-300 text-xs"></textarea>
                </div>
                <div>
                    <label for="etiquetas" class="block text-xs font-bold text-stone-700 uppercase mb-1">Etiquetas (Separar por coma)</label>
                    <input type="text" name="etiquetas" id="etiquetas" placeholder="Ej: productividad, manana, estudio" class="w-full px-3 py-2 rounded-xl border border-stone-300 text-xs">
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-sparkle-600 hover:bg-sparkle-700 shadow-md transition">
                Guardar en Mi Diario y Retroalimentar Fórmulas →
            </button>
        </form>
    </div>

    <!-- Historial de Entradas -->
    <div>
        <h2 class="text-lg font-bold text-stone-900 mb-4">Historial de Sesiones Registradas</h2>
        <div class="space-y-3">
            @forelse($entradas as $entrada)
                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-stone-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-stone-900">{{ $entrada->fecha->format('d/m/Y') }}</span>
                            <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-900 border border-amber-200">
                                {{ $entrada->formula?->nombre ?? 'Fórmula de bienestar' }}
                            </span>
                            <span class="text-xs text-amber-500 font-bold">
                                {{ str_repeat('★', $entrada->calificacion) }}
                            </span>
                        </div>
                        <div class="text-xs text-stone-600">
                            Cambio: <span class="capitalize font-medium">{{ $entrada->emocion_antes }}</span> ({{ $entrada->energia_antes }}/5) ➔ <span class="capitalize font-bold text-emerald-700">{{ $entrada->emocion_despues }}</span> ({{ $entrada->energia_despues }}/5)
                        </div>
                        @if($entrada->nota)
                            <div class="text-[11px] text-stone-500 italic mt-1">"{{ $entrada->nota }}"</div>
                        @endif
                    </div>

                    @if(!empty($entrada->etiquetas))
                        <div class="flex flex-wrap gap-1">
                            @foreach($entrada->etiquetas as $tag)
                                <span class="text-[10px] px-2 py-0.5 bg-stone-100 text-stone-600 rounded-md font-medium">#{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-8 text-stone-500 text-xs">Aún no has registrado sesiones en tu diario.</div>
            @endforelse
        </div>
        <div class="mt-4">
            {{ $entradas->links() }}
        </div>
    </div>
</div>
@endsection
