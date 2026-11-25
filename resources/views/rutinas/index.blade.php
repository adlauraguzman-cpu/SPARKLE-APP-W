@extends('layouts.app')

@section('title', 'Pilar 3: Mis Rutinas Sensoriales · SPARKLE')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-900 text-xs font-bold tracking-wide uppercase mb-2">
                Pilar 3: Rutinas & Hábitos
            </div>
            <h1 class="text-3xl font-extrabold text-stone-900 tracking-tight">Mis Rutinas de Bienestar Olfativo</h1>
            <p class="mt-1 text-stone-600 text-sm">Organiza tus momentos diarios de conexión sensorial y recibe recordatorios conscientes.</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('rutinas.create') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-sparkle-600 hover:bg-sparkle-700 shadow-md transition flex items-center gap-1.5">
                <span>+ Nueva Rutina</span>
            </a>
        </div>
    </div>

    @if($rutinas->isEmpty())
        <div class="bg-white rounded-3xl p-12 text-center border border-stone-200 shadow-sm max-w-xl mx-auto">
            <div class="w-16 h-16 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center mx-auto mb-4 text-2xl">
                🌿
            </div>
            <h3 class="text-lg font-bold text-stone-900 mb-1">Aún no tienes rutinas programadas</h3>
            <p class="text-xs text-stone-500 mb-6 leading-relaxed">
                Vincula una de tus fórmulas calibradas a un horario y espacio específico para establecer un hábito saludable de bienestar olfativo.
            </p>
            <a href="{{ route('rutinas.create') }}" class="px-6 py-3 rounded-xl text-xs font-bold text-white bg-sparkle-600 hover:bg-sparkle-700 shadow-md transition inline-block">
                Crear Mi Primera Rutina
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($rutinas as $rutina)
                <div class="bg-white rounded-3xl p-6 border border-stone-200 shadow-sm flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <!-- Header Card -->
                        <div class="flex items-center justify-between mb-3">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $rutina->activa ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-stone-100 text-stone-500' }}">
                                {{ $rutina->activa ? '● Activa' : '○ Pausada' }}
                            </span>
                            <span class="text-xs font-bold text-stone-500 flex items-center gap-1">
                                <span>⏰</span> {{ $rutina->hora }}
                            </span>
                        </div>

                        <h3 class="text-lg font-bold text-stone-900 mb-1">{{ $rutina->nombre }}</h3>
                        <div class="text-xs text-sparkle-600 font-semibold mb-3">
                            Fórmula: {{ $rutina->formula->nombre }}
                        </div>

                        <!-- Días programados -->
                        <div class="flex gap-1 mb-4">
                            @foreach(['lun', 'mar', 'mie', 'jue', 'vie', 'sab', 'dom'] as $d)
                                <span class="w-6 h-6 rounded-md flex items-center justify-center text-[10px] font-bold {{ in_array($d, $rutina->dias ?? []) ? 'bg-amber-100 text-amber-900' : 'bg-stone-100 text-stone-400' }}">
                                    {{ ucfirst(substr($d, 0, 1)) }}
                                </span>
                            @endforeach
                        </div>

                        <!-- Espacio y Duración -->
                        <div class="bg-stone-50 rounded-2xl p-3 border border-stone-100 text-xs text-stone-600 space-y-1 mb-4">
                            <div>📍 <strong>Espacio:</strong> {{ $rutina->espacio?->nombre ?? 'Habitación asignada' }}</div>
                            <div>⏳ <strong>Duración:</strong> {{ $rutina->duracion_minutos }} minutos</div>
                            <div>🔔 <strong>Canal:</strong> {{ ucfirst($rutina->canal_recordatorio) }}</div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 border-t border-stone-100 space-y-2">
                        <div class="grid grid-cols-2 gap-2">
                            <!-- Iniciar Ritual Player -->
                            <a href="{{ route('rutinas.ritual', $rutina) }}" class="py-2 px-3 rounded-xl text-center text-xs font-bold text-white bg-sparkle-600 hover:bg-sparkle-700 transition">
                                ▶ Iniciar Ritual
                            </a>

                            <!-- Simular Recordatorio -->
                            <form action="{{ route('rutinas.simular', $rutina) }}" method="POST">
                                @csrf
                                <button type="submit" title="Envía un recordatorio push simulado" class="w-full py-2 px-3 rounded-xl text-center text-xs font-semibold text-stone-700 bg-stone-100 hover:bg-stone-200 transition">
                                    🔔 Simular Alerta
                                </button>
                            </form>
                        </div>

                        <!-- Toggle activa/pausada -->
                        <form action="{{ route('rutinas.toggle', $rutina) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-center text-[11px] font-medium text-stone-500 hover:text-stone-800 transition py-1">
                                {{ $rutina->activa ? 'Pausar esta rutina temporalmente' : 'Reactivar esta rutina' }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
