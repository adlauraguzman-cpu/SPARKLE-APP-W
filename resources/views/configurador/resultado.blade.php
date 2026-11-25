@extends('layouts.app')

@section('title', 'Fórmula Calibrada con Éxito · SPARKLE')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-12">
    <!-- Breadcrumb -->
    <div class="mb-6">
        <a href="{{ route('configurador.index') }}" class="text-xs font-bold text-sparkle-600 hover:underline flex items-center gap-1">
            ← Volver al configurador
        </a>
    </div>

    <!-- Success Result Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-stone-200 shadow-xl space-y-8">
        <!-- Result Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-stone-100 pb-6">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 text-xs font-bold tracking-wide uppercase border border-emerald-200 mb-2">
                    <span>✓</span> Formulación Exitosa · Versión de Reglas {{ $resultado['version_reglas'] ?? 'v1.0' }}
                </div>
                <h1 class="text-3xl font-black text-stone-900 tracking-tight">Fórmula Calibrada para tu Sesión</h1>
                <p class="text-xs text-stone-500 mt-1">
                    Objetivo: <strong class="text-stone-800">{{ ucfirst($context['objetivo']) }}</strong> · Momento: <strong class="text-stone-800">{{ ucfirst($context['momento']) }}</strong> · Intensidad: <strong class="text-stone-800">{{ ucfirst($context['intensidad']) }}</strong>
                </p>
            </div>

            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-xl bg-amber-50 text-amber-900 font-bold text-xs border border-amber-200">
                    {{ $resultado['seguridad_analisis']['aromas_seguros'] ?? 3 }} Aromas Seguros Evaluados
                </span>
            </div>
        </div>

        <!-- Donut & Breakdown Visualization -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
            <!-- Graphical Component Bars -->
            <div class="md:col-span-7 space-y-4">
                <h3 class="text-xs font-bold text-stone-400 uppercase tracking-wider">Proporciones de la Mezcla</h3>

                @foreach($resultado['componentes'] as $comp)
                    @php
                        $colorClass = match($comp['aroma']->nombre) {
                            'ALPHA' => 'bg-amber-800',
                            'HELIOS' => 'bg-amber-400',
                            'JAZ' => 'bg-purple-600',
                            default => 'bg-sparkle-600'
                        };
                        $bgLight = match($comp['aroma']->nombre) {
                            'ALPHA' => 'bg-amber-50 border-amber-200',
                            'HELIOS' => 'bg-yellow-50 border-yellow-200',
                            'JAZ' => 'bg-purple-50 border-purple-200',
                            default => 'bg-stone-50 border-stone-200'
                        };
                    @endphp
                    <div class="p-4 rounded-2xl border {{ $bgLight }} space-y-2">
                        <div class="flex justify-between items-center text-sm font-bold text-stone-900">
                            <span class="flex items-center gap-2">
                                <span class="w-3.5 h-3.5 rounded-full {{ $colorClass }}"></span>
                                {{ $comp['aroma']->nombre }} <span class="text-xs font-normal text-stone-500">({{ $comp['aroma']->familia }})</span>
                            </span>
                            <span class="text-lg font-black text-stone-900">{{ $comp['porcentaje'] }}%</span>
                        </div>
                        <div class="w-full bg-stone-200 h-3 rounded-full overflow-hidden">
                            <div class="{{ $colorClass }} h-full rounded-full transition-all duration-700" style="width: {{ $comp['porcentaje'] }}%"></div>
                        </div>
                        <div class="text-[11px] text-stone-600 leading-snug">
                            {{ $comp['aroma']->descripcion }}
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Ritual and Explanation Box -->
            <div class="md:col-span-5 space-y-4">
                <div class="bg-stone-50 rounded-2xl p-5 border border-stone-200 text-xs leading-relaxed">
                    <div class="font-bold text-stone-900 text-sm mb-1">Fundamento de la Mezcla</div>
                    <p class="text-stone-600">{{ $resultado['explicacion'] }}</p>
                </div>

                <div class="bg-amber-50/80 rounded-2xl p-5 border border-amber-200 text-xs text-amber-950 space-y-3">
                    <div class="font-bold text-sm flex items-center justify-between">
                        <span>🌿 Ritual Sensorial Sugerido</span>
                        <span class="text-[11px] px-2 py-0.5 rounded-full bg-amber-200 text-amber-900 font-semibold">
                            {{ $resultado['ritual']['duracion_min'] ?? 15 }} min · {{ $resultado['ritual']['gotas'] ?? 6 }} gotas
                        </span>
                    </div>
                    <ul class="space-y-1.5 text-stone-600 text-xs">
                        @foreach($resultado['ritual']['pasos'] ?? [] as $paso)
                            <li>{{ $paso }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Save / Action form -->
        @auth
            <form action="{{ route('configurador.guardar') }}" method="POST" class="pt-6 border-t border-stone-200 space-y-4">
                @csrf
                <input type="hidden" name="objetivo" value="{{ $context['objetivo'] }}">
                <input type="hidden" name="momento" value="{{ $context['momento'] }}">
                <input type="hidden" name="intensidad" value="{{ $context['intensidad'] }}">
                <input type="hidden" name="emocion" value="{{ $context['emocion'] ?? 'calma' }}">
                <input type="hidden" name="energia" value="{{ $context['energia'] ?? 3 }}">
                <input type="hidden" name="explicacion" value="{{ $resultado['explicacion'] }}">
                <input type="hidden" name="ritual_gotas" value="{{ $resultado['ritual']['gotas'] ?? 6 }}">
                <input type="hidden" name="ritual_duracion" value="{{ $resultado['ritual']['duracion_min'] ?? 15 }}">

                @foreach($resultado['ritual']['pasos'] ?? [] as $i => $paso)
                    <input type="hidden" name="ritual_pasos[]" value="{{ $paso }}">
                @endforeach

                @foreach($resultado['componentes'] as $idx => $comp)
                    <input type="hidden" name="componentes[{{ $idx }}][aroma_id]" value="{{ $comp['aroma']->id }}">
                    <input type="hidden" name="componentes[{{ $idx }}][porcentaje]" value="{{ $comp['porcentaje'] }}">
                @endforeach

                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="w-full sm:flex-1">
                        <label class="block text-xs font-bold text-stone-700 uppercase mb-1">Nombre para esta fórmula</label>
                        <input type="text" name="nombre" value="Mi Fórmula: {{ ucfirst($context['objetivo']) }} en {{ ucfirst($context['momento']) }}" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-sm font-semibold focus:ring-2 focus:ring-sparkle-500">
                    </div>
                    <div class="w-full sm:w-auto pt-5">
                        <button type="submit" class="w-full py-3 px-6 rounded-xl text-sm font-bold text-white bg-sparkle-600 hover:bg-sparkle-700 shadow-md transition">
                            💾 Guardar y Vincular a Rutina
                        </button>
                    </div>
                </div>
            </form>
        @else
            <div class="pt-6 border-t border-stone-200 p-4 rounded-2xl bg-amber-50 border border-amber-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-amber-900">
                    <strong>¿Deseas guardar esta fórmula?</strong> Crea tu cuenta gratuita para guardarla en tu perfil, recibir recordatorios y registrar tu diario.
                </div>
                <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-sparkle-600 hover:bg-sparkle-700 whitespace-nowrap shadow-sm transition">
                    Crear Cuenta y Guardar
                </a>
            </div>
        @endauth
    </div>
</div>
@endsection
