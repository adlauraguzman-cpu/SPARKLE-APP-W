@extends('layouts.app')

@section('title', 'Programar Nueva Rutina · SPARKLE')

@section('content')
<div class="max-w-xl mx-auto px-4 sm:px-6 py-12">
    <div class="bg-white rounded-3xl p-8 border border-stone-200 shadow-xl">
        <div class="mb-6">
            <a href="{{ route('rutinas.index') }}" class="text-xs font-bold text-sparkle-600 hover:underline">
                ← Volver a mis rutinas
            </a>
            <h1 class="text-2xl font-black text-stone-900 mt-2">Programar Rutina Sensorial</h1>
            <p class="text-xs text-stone-500 mt-1">Estructura un hábito aromático consciente</p>
        </div>

        <form action="{{ route('rutinas.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="nombre" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Nombre de la Rutina</label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', 'Pausa Restaurativa de la Tarde') }}" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-sm focus:ring-2 focus:ring-sparkle-500">
                @error('nombre')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="formula_id" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Fórmula Calibrada a Utilizar</label>
                <select name="formula_id" id="formula_id" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-sm focus:ring-2 focus:ring-sparkle-500">
                    @forelse($formulas as $f)
                        <option value="{{ $f->id }}">{{ $f->nombre }} ({{ $f->componentes->pluck('aroma.nombre')->implode(' + ') }})</option>
                    @empty
                        <option value="">No tienes fórmulas guardadas aún</option>
                    @endforelse
                </select>
                @if($formulas->isEmpty())
                    <a href="{{ route('configurador.index') }}" class="text-xs font-bold text-sparkle-600 hover:underline mt-1 block">
                        + Calibrar una fórmula primero
                    </a>
                @endif
            </div>

            <div>
                <label for="espacio_id" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Espacio de Calma</label>
                <select name="espacio_id" id="espacio_id" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-sm focus:ring-2 focus:ring-sparkle-500">
                    @foreach($espacios as $esp)
                        <option value="{{ $esp->id }}">{{ $esp->nombre }} ({{ ucfirst($esp->tipo) }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Días de la semana -->
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">Días de Repetición</label>
                <div class="grid grid-cols-7 gap-1.5 text-center text-xs">
                    @foreach(['lun' => 'L', 'mar' => 'M', 'mie' => 'X', 'jue' => 'J', 'vie' => 'V', 'sab' => 'S', 'dom' => 'D'] as $key => $label)
                        <label class="p-2 rounded-xl border border-stone-200 hover:border-sparkle-500 cursor-pointer bg-stone-50 has-[:checked]:bg-amber-500 has-[:checked]:text-white has-[:checked]:font-bold transition">
                            <input type="checkbox" name="dias[]" value="{{ $key }}" {{ in_array($key, ['lun', 'mie', 'vie']) ? 'checked' : '' }} class="hidden">
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Hora y Duración -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="hora" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Hora de Inicio</label>
                    <input type="time" name="hora" id="hora" value="{{ old('hora', '15:30') }}" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-sm focus:ring-2 focus:ring-sparkle-500">
                </div>

                <div>
                    <label for="duracion_minutos" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Duración (Minutos)</label>
                    <input type="number" name="duracion_minutos" id="duracion_minutos" value="{{ old('duracion_minutos', 15) }}" min="5" max="120" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-sm focus:ring-2 focus:ring-sparkle-500">
                </div>
            </div>

            <!-- Canal de Recordatorio -->
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">Canal de Notificación</label>
                <div class="grid grid-cols-3 gap-2 text-xs">
                    <label class="p-2.5 rounded-xl border border-stone-200 text-center cursor-pointer has-[:checked]:bg-amber-100 has-[:checked]:border-amber-500">
                        <input type="radio" name="canal_recordatorio" value="notificacion" checked class="hidden">
                        <span class="font-semibold text-stone-800">📱 Notificación</span>
                    </label>
                    <label class="p-2.5 rounded-xl border border-stone-200 text-center cursor-pointer has-[:checked]:bg-amber-100 has-[:checked]:border-amber-500">
                        <input type="radio" name="canal_recordatorio" value="correo" class="hidden">
                        <span class="font-semibold text-stone-800">✉️ Correo</span>
                    </label>
                    <label class="p-2.5 rounded-xl border border-stone-200 text-center cursor-pointer has-[:checked]:bg-amber-100 has-[:checked]:border-amber-500">
                        <input type="radio" name="canal_recordatorio" value="ninguno" class="hidden">
                        <span class="font-semibold text-stone-800">Silencioso</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-sparkle-600 hover:bg-sparkle-700 shadow-md transition">
                Guardar Rutina y Activar
            </button>
        </form>
    </div>
</div>
@endsection
