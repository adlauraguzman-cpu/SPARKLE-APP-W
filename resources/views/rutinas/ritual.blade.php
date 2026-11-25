@extends('layouts.app')

@section('title', 'Reproductor Sensorial Guiado · SPARKLE')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-12" x-data="{
    running: false,
    timeLeft: {{ $rutina->duracion_minutos * 60 }},
    initialTime: {{ $rutina->duracion_minutos * 60 }},
    breathePhase: 'Inhala suavemente...',
    breatheSec: 4,
    timer: null,
    breatheTimer: null,
    startRitual() {
        this.running = true;
        this.timer = setInterval(() => {
            if (this.timeLeft > 0) {
                this.timeLeft--;
            } else {
                clearInterval(this.timer);
                clearInterval(this.breatheTimer);
                this.running = false;
            }
        }, 1000);

        let cycle = 0;
        this.breatheTimer = setInterval(() => {
            cycle = (cycle + 1) % 3;
            if (cycle === 0) {
                this.breathePhase = 'Inhala profundamente el aroma... (4s)';
            } else if (cycle === 1) {
                this.breathePhase = 'Sostén el aire con calma... (4s)';
            } else {
                this.breathePhase = 'Exhala y libera toda tensión... (6s)';
            }
        }, 4000);
    },
    pauseRitual() {
        this.running = false;
        clearInterval(this.timer);
        clearInterval(this.breatheTimer);
        this.breathePhase = 'Ritual en pausa';
    },
    formatTime(sec) {
        let m = Math.floor(sec / 60);
        let s = sec % 60;
        return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
    }
}">

    <!-- Ritual Player Card -->
    <div class="bg-stone-900 text-stone-100 rounded-3xl p-8 sm:p-12 shadow-2xl border border-stone-800 text-center relative overflow-hidden">
        <div class="absolute -top-24 -left-24 w-60 h-60 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-60 h-60 bg-sparkle-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Ritual Header -->
        <div class="mb-8">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold uppercase tracking-wider border border-amber-500/30 mb-2">
                Reproductor de Ritual Sensorial
            </span>
            <h1 class="text-3xl font-black tracking-tight text-white">{{ $rutina->nombre }}</h1>
            <p class="text-xs text-stone-400 mt-1">
                Fórmula: <strong class="text-amber-400">{{ $rutina->formula->nombre }}</strong> en {{ $rutina->espacio?->nombre ?? 'tu espacio' }}
            </p>
        </div>

        <!-- Breathing Pulse Animation Circle -->
        <div class="my-10 relative flex items-center justify-center">
            <div class="w-48 h-48 rounded-full border-2 border-amber-500/30 flex items-center justify-center transition-transform duration-1000" :class="running ? 'scale-110 shadow-lg shadow-amber-500/20' : 'scale-100'">
                <div class="w-36 h-36 rounded-full bg-gradient-to-tr from-amber-600/30 to-sparkle-600/30 flex flex-col items-center justify-center">
                    <span class="text-3xl font-black font-mono tracking-wider text-white" x-text="formatTime(timeLeft)"></span>
                    <span class="text-[10px] uppercase font-bold text-amber-300 mt-0.5">Tiempo restante</span>
                </div>
            </div>
        </div>

        <!-- Guided Breathing Phase Prompt -->
        <div class="min-h-[50px] mb-8">
            <div class="text-base font-bold text-amber-200 transition" x-text="breathePhase"></div>
            <div class="text-xs text-stone-400 mt-1">Conecta con los acordes de la mezcla y mantén una respiración suave</div>
        </div>

        <!-- Player Controls -->
        <div class="flex items-center justify-center gap-4">
            <button type="button" @click="running ? pauseRitual() : startRitual()" class="px-8 py-3.5 rounded-2xl text-sm font-extrabold text-stone-900 bg-gradient-to-r from-amber-400 to-sparkle-400 hover:from-amber-300 hover:to-sparkle-300 shadow-lg shadow-amber-500/25 transition transform hover:scale-105 flex items-center gap-2">
                <span x-text="running ? '⏸ Pausar Ritual' : '▶ Iniciar Sesión Guiada'"></span>
            </button>

            <a href="{{ route('diario.index') }}" class="px-5 py-3.5 rounded-2xl text-xs font-bold text-stone-300 bg-stone-800 hover:bg-stone-700 transition">
                Finalizar y Registrar en Diario →
            </a>
        </div>
    </div>
</div>
@endsection
