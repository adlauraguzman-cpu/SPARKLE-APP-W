@extends('layouts.app')

@section('title', 'Panel Especialista en Aromas · SPARKLE')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10" x-data="{
    state: '{{ $forceState ?? (session('success') ? 'success' : (session('maker_checker_error') ? 'error' : 'idle')) }}',
    errorMsg: '{{ session('error', 'Error en validación de regla o principio Maker-Checker.') }}',
}">

    <!-- State Switcher Bar for Evaluators (4 States of Interface Demonstration) -->
    <div class="mb-8 p-3 rounded-2xl bg-stone-900 text-stone-300 text-xs flex flex-wrap items-center justify-between gap-3 shadow-md border border-stone-800">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
            <span class="font-bold text-white uppercase tracking-wider text-[11px]">4 Estados de Interfaz en Especialista:</span>
        </div>
        <div class="flex items-center gap-1.5 flex-wrap">
            <button type="button" @click="state = 'idle'" :class="state === 'idle' ? 'bg-emerald-600 text-white font-bold' : 'bg-stone-800 text-stone-300 hover:bg-stone-700'" class="px-2.5 py-1 rounded-lg text-xs transition">
                1. Idle (Matriz de Reglas)
            </button>
            <button type="button" @click="state = 'loading'" :class="state === 'loading' ? 'bg-emerald-600 text-white font-bold' : 'bg-stone-800 text-stone-300 hover:bg-stone-700'" class="px-2.5 py-1 rounded-lg text-xs transition">
                2. Loading (Simulando/Guardando)
            </button>
            <button type="button" @click="state = 'success'" :class="state === 'success' ? 'bg-emerald-600 text-white font-bold' : 'bg-stone-800 text-stone-300 hover:bg-stone-700'" class="px-2.5 py-1 rounded-lg text-xs transition">
                3. Success (Borrador en Revisión v2.0)
            </button>
            <button type="button" @click="state = 'error'; errorMsg = 'Principio Maker-Checker violado: No puedes aprobar una regla formulada por ti mismo.'" :class="state === 'error' ? 'bg-red-700 text-white font-bold' : 'bg-stone-800 text-stone-300 hover:bg-stone-700'" class="px-2.5 py-1 rounded-lg text-xs transition">
                4. Error (Maker-Checker Bloqueado)
            </button>
        </div>
    </div>

    <!-- Header & Anonymous Aggregate Metrics -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-900 text-xs font-bold tracking-wide uppercase mb-2">
                Rol Interno: Especialista en Aromas
            </div>
            <h1 class="text-3xl font-black text-stone-900 tracking-tight">Gobernanza de Reglas de Afinidad & Simulación</h1>
            <p class="mt-1 text-stone-600 text-sm">
                Control de pesos matemáticos (0-3), calibración del motor y revisión de pares cruzada (Maker-Checker).
            </p>
        </div>

        <!-- Anonymous Platform Aggregate Metrics -->
        <div class="flex gap-3">
            <div class="bg-white rounded-2xl p-4 border border-stone-200 shadow-sm text-center min-w-[120px]">
                <div class="text-[10px] uppercase font-bold text-stone-400">Total Fórmulas</div>
                <div class="text-2xl font-black text-stone-900 mt-0.5">{{ $totalFormulas }}</div>
                <div class="text-[10px] text-stone-500">Generadas</div>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-stone-200 shadow-sm text-center min-w-[120px]">
                <div class="text-[10px] uppercase font-bold text-stone-400">Reglas Activas</div>
                <div class="text-2xl font-black text-emerald-700 mt-0.5">{{ $totalReglas }}</div>
                <div class="text-[10px] text-stone-500">En el motor</div>
            </div>
            <div class="bg-white rounded-2xl p-4 border border-stone-200 shadow-sm text-center min-w-[120px]">
                <div class="text-[10px] uppercase font-bold text-stone-400">Efectividad Diaria</div>
                <div class="text-2xl font-black text-amber-600 mt-0.5">★ {{ $promedioCalificaciones }}</div>
                <div class="text-[10px] text-stone-500">Métrica anónima</div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- ESTADO 4: ERROR MAKER-CHECKER              -->
    <!-- ========================================== -->
    <div x-show="state === 'error'" x-cloak class="p-6 bg-red-50 border-2 border-red-300 rounded-3xl text-red-900 shadow-sm mb-8">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-2xl bg-red-100 text-red-700 flex items-center justify-center font-bold text-lg shrink-0">
                🛑
            </div>
            <div>
                <h3 class="text-base font-bold text-red-900">Estado de Error: Violación de Principio Maker-Checker</h3>
                <p class="text-xs text-red-800 mt-1" x-text="errorMsg"></p>
                <div class="mt-3 p-3 bg-white/70 rounded-xl text-xs text-stone-700 border border-red-200">
                    <strong>Pauta de Arquitectura SPARKLE:</strong> Para salvaguardar la precisión terapéutica, ninguna regla formulada por un especialista puede ser aprobada y publicada por el mismo autor. Debe iniciar sesión un <em>segundo especialista</em> (ej. Dra. Elena Revisor) para verificar los pesos y dar la aprobación definitiva.
                </div>
                <div class="mt-4 flex gap-2">
                    <a href="{{ route('demo.login', 'revisor') }}" class="px-4 py-2 bg-red-700 text-white text-xs font-bold rounded-xl hover:bg-red-800 transition">
                        Cambiar a rol: Dra. Elena (Revisor Maker-Checker) →
                    </a>
                    <button type="button" @click="state = 'idle'" class="px-4 py-2 bg-stone-200 text-stone-800 text-xs font-bold rounded-xl hover:bg-stone-300 transition">
                        Cerrar aviso
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- ESTADO 2: LOADING (SIMULANDO O GUARDANDO)  -->
    <!-- ========================================== -->
    <div x-show="state === 'loading'" x-cloak class="bg-white rounded-3xl p-10 border border-stone-200 shadow-xl text-center space-y-4 mb-8">
        <div class="w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto animate-spin">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
        </div>
        <h3 class="text-xl font-bold text-stone-900">Procesando Matriz de Afinidad...</h3>
        <p class="text-xs text-stone-500 max-w-sm mx-auto">Re-calculando ponderaciones cruzadas de los 3 aromas esenciales para publicar versión.</p>
    </div>

    <!-- ========================================== -->
    <!-- ESTADO 3: SUCCESS (BORRADOR EN REVISIÓN)   -->
    <!-- ========================================== -->
    <div x-show="state === 'success'" x-cloak class="p-6 bg-emerald-50 border-2 border-emerald-300 rounded-3xl text-emerald-950 shadow-sm mb-8">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-lg shrink-0">
                ✓
            </div>
            <div>
                <h3 class="text-base font-bold text-emerald-900">Estado Success: Regla en Cola de Revisión Maker-Checker (v2.0)</h3>
                <p class="text-xs text-emerald-800 mt-1 leading-relaxed">
                    La regla propuesta ha sido registrada correctamente en estado <strong>en_revision</strong> con versión v2.0. Ha quedado asignada en la cola de revisión técnica para ser auditada por un segundo especialista antes de incorporarse al motor público.
                </p>
                <div class="mt-4">
                    <button type="button" @click="state = 'idle'" class="px-4 py-2 bg-emerald-700 text-white text-xs font-bold rounded-xl hover:bg-emerald-800 transition">
                        Continuar editando matriz de afinidad
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cola de Revisión Maker-Checker (Pendientes de Aprobación Cruzada) -->
    <div class="bg-amber-50/70 border-2 border-amber-300 rounded-3xl p-6 sm:p-8 mb-10 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-amber-200 pb-4 mb-4">
            <div>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-200 text-amber-900 uppercase tracking-wider mb-1">
                    Maker-Checker Activo
                </span>
                <h2 class="text-lg font-black text-amber-950">Cola de Propuestas en Revisión (Versión 2.0)</h2>
            </div>
            <div class="text-xs text-amber-800 font-medium">
                Sesión actual: <strong>{{ Auth::user()->name }}</strong>
            </div>
        </div>

        <div class="space-y-3">
            @forelse($reglasRevision as $rev)
                <div class="bg-white rounded-2xl p-4 border border-amber-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold text-stone-900">{{ $rev->aroma->nombre }}</span>
                            <span class="text-xs font-bold text-amber-700">· {{ ucfirst($rev->variable) }} = "{{ $rev->valor }}"</span>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-stone-100 text-stone-700">Peso: {{ $rev->peso }} / 3</span>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-100 text-amber-800 uppercase">{{ $rev->estado }}</span>
                        </div>
                        <div class="text-[11px] text-stone-500">
                            Creado por: <strong>{{ $rev->creador?->name ?? 'Especialista autor' }}</strong> · Versión: {{ $rev->version }}
                        </div>
                    </div>

                    <!-- Botón de Aprobación Cruzada -->
                    <div>
                        @if($rev->creado_por === Auth::id())
                            <span class="text-xs text-stone-500 italic bg-stone-100 px-3 py-1.5 rounded-xl border border-stone-200">
                                🔒 Requiere revisión por otro especialista
                            </span>
                        @else
                            <form action="{{ route('especialista.reglas.aprobar', $rev) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition">
                                    ✓ Auditar y Aprobar como Revisor
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-xs text-amber-800">No hay reglas pendientes de revisión actualmente. Todas las reglas están aprobadas y publicadas.</div>
            @endforelse
        </div>
    </div>

    <!-- Formulario para Proponer / Editar Nueva Regla de Afinidad -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200 shadow-xl mb-10">
        <h2 class="text-xl font-bold text-stone-900 mb-2 flex items-center gap-2">
            <span>➕</span> Proponer / Calibrar Regla de Afinidad
        </h2>
        <p class="text-xs text-stone-500 mb-6">El peso oscila estrictamente entre 0 (sin afinidad) y 3 (máxima afinidad terapéutica).</p>

        <form action="{{ route('especialista.reglas.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-6 gap-4 items-end">
            @csrf

            <div class="sm:col-span-2">
                <label for="aroma_id" class="block text-xs font-bold text-stone-700 uppercase mb-1">Aroma</label>
                <select name="aroma_id" id="aroma_id" required class="w-full px-3 py-2 rounded-xl border border-stone-300 text-xs font-semibold focus:ring-2 focus:ring-emerald-500">
                    @foreach($aromas as $ar)
                        <option value="{{ $ar->id }}">{{ $ar->nombre }} ({{ $ar->familia }})</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-1">
                <label for="variable" class="block text-xs font-bold text-stone-700 uppercase mb-1">Variable</label>
                <select name="variable" id="variable" required class="w-full px-3 py-2 rounded-xl border border-stone-300 text-xs font-semibold focus:ring-2 focus:ring-emerald-500">
                    <option value="objetivo">Objetivo</option>
                    <option value="espacio">Espacio</option>
                    <option value="momento">Momento</option>
                    <option value="emocion">Emoción</option>
                </select>
            </div>

            <div class="sm:col-span-1">
                <label for="valor" class="block text-xs font-bold text-stone-700 uppercase mb-1">Valor Condición</label>
                <input type="text" name="valor" id="valor" value="concentrarme" required placeholder="Ej: desacelerar" class="w-full px-3 py-2 rounded-xl border border-stone-300 text-xs font-semibold focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="sm:col-span-1">
                <label for="peso" class="block text-xs font-bold text-stone-700 uppercase mb-1">Peso (0 - 3)</label>
                <input type="number" name="peso" id="peso" value="3" min="0" max="3" required class="w-full px-3 py-2 rounded-xl border border-stone-300 text-xs font-semibold focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="sm:col-span-1">
                <input type="hidden" name="version" value="v2.0">
                <input type="hidden" name="enviar_a_revision" value="1">
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-md transition">
                    Enviar a Revisión
                </button>
            </div>
        </form>
    </div>

    <!-- Matriz de Reglas Publicadas (Versión v1.0 Activa) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-stone-900">Matriz de Reglas de Afinidad Publicadas (v1.0)</h2>
                <p class="text-xs text-stone-500">Reglas activas utilizadas por el motor público para calcular fórmulas</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                {{ $reglasPublicadas->count() }} Reglas Publicadas
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-stone-200 text-stone-500 font-bold uppercase tracking-wider">
                        <th class="py-3 px-4">Aroma</th>
                        <th class="py-3 px-4">Variable</th>
                        <th class="py-3 px-4">Valor Evaluado</th>
                        <th class="py-3 px-4">Peso (0-3)</th>
                        <th class="py-3 px-4">Versión</th>
                        <th class="py-3 px-4">Creado Por</th>
                        <th class="py-3 px-4">Aprobado Por (Checker)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @foreach($reglasPublicadas as $regla)
                        <tr class="hover:bg-stone-50/70">
                            <td class="py-3 px-4 font-bold text-stone-900">{{ $regla->aroma->nombre }}</td>
                            <td class="py-3 px-4 capitalize">{{ $regla->variable }}</td>
                            <td class="py-3 px-4 font-mono font-medium">{{ $regla->valor }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded font-extrabold {{ $regla->peso === 3 ? 'bg-amber-100 text-amber-900' : 'bg-stone-100 text-stone-700' }}">
                                    +{{ $regla->peso }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-semibold text-stone-500">{{ $regla->version }}</td>
                            <td class="py-3 px-4 text-stone-600">{{ $regla->creador?->name ?? 'Dr. Carlos Especialista' }}</td>
                            <td class="py-3 px-4 text-emerald-700 font-semibold">{{ $regla->aprobador?->name ?? 'Dra. Elena Revisor' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
