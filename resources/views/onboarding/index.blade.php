@extends('layouts.app')

@section('title', 'Pilar 1: Conocerme · Perfil Sensorial y de Seguridad · SPARKLE')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-12">
    <!-- Header -->
    <div class="mb-10 text-center sm:text-left">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-900 text-xs font-bold tracking-wide uppercase mb-2">
            Pilar 1: Conocerme
        </div>
        <h1 class="text-3xl font-extrabold text-stone-900 tracking-tight">Mi Perfil Sensorial, Espacio y Criterios de Seguridad</h1>
        <p class="mt-2 text-stone-600 text-sm max-w-2xl leading-relaxed">
            Esta información calibra el motor algorítmico de SPARKLE. Cada aroma sugerido respetará estrictamente tus gustos olfativos y descartará automáticamente componentes con riesgo de alergias o contraindicaciones en el hogar.
        </p>
    </div>

    <form action="{{ route('onboarding.store') }}" method="POST" class="space-y-8">
        @csrf

        <!-- 1. Perfil Sensorial -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200 shadow-sm space-y-6">
            <div class="flex items-center gap-3 border-b border-stone-100 pb-4">
                <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-sm">
                    A
                </div>
                <div>
                    <h2 class="text-lg font-bold text-stone-900">1. Preferencias Olfativas (Perfil Sensorial)</h2>
                    <p class="text-xs text-stone-500">¿Qué familias aromáticas te revitalizan o deseas evitar?</p>
                </div>
            </div>

            <!-- Familias Preferidas -->
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">Familias Aromáticas Preferidas</label>
                @php $pref = old('familias_preferidas', $sensorial->familias_preferidas ?? ['Maderas']); @endphp
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    @foreach(['Maderas', 'Cítricos', 'Floral', 'Herbal'] as $fam)
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-stone-200 hover:border-sparkle-400 cursor-pointer bg-stone-50/50">
                            <input type="checkbox" name="familias_preferidas[]" value="{{ $fam }}" {{ in_array($fam, (array)$pref) ? 'checked' : '' }} class="rounded text-sparkle-600 focus:ring-sparkle-500">
                            <span class="font-medium text-stone-800">{{ $fam }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Familias Evitadas -->
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">Familias Aromáticas a Evitar o Reducir</label>
                @php $evit = old('familias_evitadas', $sensorial->familias_evitadas ?? []); @endphp
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    @foreach(['Maderas', 'Cítricos', 'Floral', 'Herbal'] as $fam)
                        <label class="flex items-center gap-2 p-3 rounded-xl border border-stone-200 hover:border-red-300 cursor-pointer bg-stone-50/50">
                            <input type="checkbox" name="familias_evitadas[]" value="{{ $fam }}" {{ in_array($fam, (array)$evit) ? 'checked' : '' }} class="rounded text-red-600 focus:ring-red-500">
                            <span class="font-medium text-stone-800">{{ $fam }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Intensidad Preferida -->
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">Intensidad Olfativa Predilecta</label>
                @php $inten = old('intensidad_preferida', $sensorial->intensidad_preferida ?? 'media'); @endphp
                <div class="grid grid-cols-3 gap-3 text-xs">
                    <label class="flex flex-col p-3 rounded-xl border border-stone-200 hover:border-sparkle-500 cursor-pointer bg-stone-50/50 text-center">
                        <input type="radio" name="intensidad_preferida" value="suave" {{ $inten === 'suave' ? 'checked' : '' }} class="mx-auto mb-1 text-sparkle-600">
                        <span class="font-bold text-stone-800">Suave</span>
                        <span class="text-[10px] text-stone-500">Sutil y ambiental (3-4 gotas)</span>
                    </label>
                    <label class="flex flex-col p-3 rounded-xl border border-stone-200 hover:border-sparkle-500 cursor-pointer bg-stone-50/50 text-center">
                        <input type="radio" name="intensidad_preferida" value="media" {{ $inten === 'media' ? 'checked' : '' }} class="mx-auto mb-1 text-sparkle-600">
                        <span class="font-bold text-stone-800">Media</span>
                        <span class="text-[10px] text-stone-500">Equilibrio envolvente (5-6 gotas)</span>
                    </label>
                    <label class="flex flex-col p-3 rounded-xl border border-stone-200 hover:border-sparkle-500 cursor-pointer bg-stone-50/50 text-center">
                        <input type="radio" name="intensidad_preferida" value="intensa" {{ $inten === 'intensa' ? 'checked' : '' }} class="mx-auto mb-1 text-sparkle-600">
                        <span class="font-bold text-stone-800">Intensa</span>
                        <span class="text-[10px] text-stone-500">Presencia activa (8-9 gotas)</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- 2. Perfil de Seguridad (Filtro Estricto de Descarte) -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200 shadow-sm space-y-6">
            <div class="flex items-center gap-3 border-b border-stone-100 pb-4">
                <div class="w-8 h-8 rounded-full bg-red-100 text-red-800 flex items-center justify-center font-bold text-sm">
                    B
                </div>
                <div>
                    <h2 class="text-lg font-bold text-stone-900">2. Criterios de Seguridad y Contraindicaciones</h2>
                    <p class="text-xs text-stone-500">Filtro de exclusión estricta para proteger tu salud y tu hogar</p>
                </div>
            </div>

            <!-- Alergias -->
            <div>
                <label for="alergias" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Alergias Conocidas a Esencias o Plantas (Opcional)</label>
                @php $alergiasStr = old('alergias', !empty($seguridad->alergias) ? implode(', ', $seguridad->alergias) : ''); @endphp
                <input type="text" name="alergias" id="alergias" value="{{ $alergiasStr }}" placeholder="Ej: citricos, bergamota, lavanda, canela (separar por coma)" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-sm focus:ring-2 focus:ring-sparkle-500">
                <span class="text-[11px] text-stone-500 mt-1 block">El motor descartará cualquier aroma que contenga alérgenos coincidentes.</span>
            </div>

            <!-- Mascotas y Niños -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div class="p-4 rounded-2xl bg-stone-50 border border-stone-200 space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="mascotas_en_casa" value="1" {{ old('mascotas_en_casa', $seguridad->mascotas_en_casa ?? false) ? 'checked' : '' }} class="rounded text-amber-600 focus:ring-amber-500">
                        <span class="text-xs font-bold text-stone-800">🐾 Mascotas en casa</span>
                    </label>
                    <input type="text" name="tipo_mascotas" value="{{ old('tipo_mascotas', !empty($seguridad->tipo_mascotas) ? implode(', ', $seguridad->tipo_mascotas) : 'gatos, perros') }}" placeholder="Ej: gatos, perros" class="w-full px-3 py-1.5 rounded-lg border border-stone-300 text-xs">
                    <span class="text-[10px] text-stone-500 block">Descarta esencias fotosensibilizantes o tóxicas para vías respiratorias de felinos y caninos.</span>
                </div>

                <div class="p-4 rounded-2xl bg-stone-50 border border-stone-200 space-y-2">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="ninos_en_casa" value="1" {{ old('ninos_en_casa', $seguridad->ninos_en_casa ?? false) ? 'checked' : '' }} class="rounded text-amber-600 focus:ring-amber-500">
                        <span class="text-xs font-bold text-stone-800">👶 Niños pequeños en el hogar</span>
                    </label>
                    <span class="text-[10px] text-stone-500 block mt-2">Ajusta la dosificación a concentraciones suaves para salvaguardar vías respiratorias sensibles.</span>
                </div>
            </div>

            <!-- Embarazo o Lactancia -->
            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">¿Te encuentras en periodo de embarazo o lactancia activa?</label>
                @php $emb = old('embarazo_o_lactancia', $seguridad->embarazo_o_lactancia ?? 'no'); @endphp
                <div class="grid grid-cols-3 gap-3 text-xs">
                    <label class="flex items-center justify-center gap-2 p-3 rounded-xl border border-stone-200 cursor-pointer bg-stone-50/50">
                        <input type="radio" name="embarazo_o_lactancia" value="si" {{ $emb === 'si' ? 'checked' : '' }} class="text-sparkle-600">
                        <span class="font-medium text-stone-800">Sí</span>
                    </label>
                    <label class="flex items-center justify-center gap-2 p-3 rounded-xl border border-stone-200 cursor-pointer bg-stone-50/50">
                        <input type="radio" name="embarazo_o_lactancia" value="no" {{ $emb === 'no' ? 'checked' : '' }} class="text-sparkle-600">
                        <span class="font-medium text-stone-800">No</span>
                    </label>
                    <label class="flex items-center justify-center gap-2 p-3 rounded-xl border border-stone-200 cursor-pointer bg-stone-50/50">
                        <input type="radio" name="embarazo_o_lactancia" value="prefiero_no_decir" {{ $emb === 'prefiero_no_decir' ? 'checked' : '' }} class="text-sparkle-600">
                        <span class="font-medium text-stone-800">Prefiero no decir</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- 3. Espacio de Calma -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200 shadow-sm space-y-6">
            <div class="flex items-center gap-3 border-b border-stone-100 pb-4">
                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-sm">
                    C
                </div>
                <div>
                    <h2 class="text-lg font-bold text-stone-900">3. Mi Espacio de Calma</h2>
                    <p class="text-xs text-stone-500">Donde realizarás tus sesiones y rutinas aromáticas</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="espacio_nombre" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Nombre del Espacio</label>
                    <input type="text" name="espacio_nombre" id="espacio_nombre" value="{{ old('espacio_nombre', $espacio->nombre ?? 'Mi Estudio Zen') }}" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-sm focus:ring-2 focus:ring-sparkle-500" placeholder="Ej: Estudio Principal">
                </div>

                <div>
                    <label for="espacio_tipo" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Tipo de Habitación</label>
                    @php $tipoEsp = old('espacio_tipo', $espacio->tipo ?? 'estudio'); @endphp
                    <select name="espacio_tipo" id="espacio_tipo" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-sm focus:ring-2 focus:ring-sparkle-500">
                        <option value="estudio" {{ $tipoEsp === 'estudio' ? 'selected' : '' }}>Estudio / Oficina</option>
                        <option value="dormitorio" {{ $tipoEsp === 'dormitorio' ? 'selected' : '' }}>Dormitorio</option>
                        <option value="sala" {{ $tipoEsp === 'sala' ? 'selected' : '' }}>Sala de estar</option>
                        <option value="bano" {{ $tipoEsp === 'bano' ? 'selected' : '' }}>Baño</option>
                        <option value="cocina" {{ $tipoEsp === 'cocina' ? 'selected' : '' }}>Cocina</option>
                        <option value="otro" {{ $tipoEsp === 'otro' ? 'selected' : '' }}>Otro espacio</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Tamaño de la Habitación</label>
                    @php $tamEsp = old('espacio_tamano', $espacio->tamano ?? 'medio'); @endphp
                    <select name="espacio_tamano" class="w-full px-4 py-2.5 rounded-xl border border-stone-300 text-sm focus:ring-2 focus:ring-sparkle-500">
                        <option value="pequeno" {{ $tamEsp === 'pequeno' ? 'selected' : '' }}>Pequeño (Menos de 10 m²)</option>
                        <option value="medio" {{ $tamEsp === 'medio' ? 'selected' : '' }}>Medio (10 a 25 m²)</option>
                        <option value="grande" {{ $tamEsp === 'grande' ? 'selected' : '' }}>Grande (Más de 25 m²)</option>
                    </select>
                </div>

                <div class="flex items-center pt-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="espacio_ventilado" value="1" {{ old('espacio_ventilado', $espacio->ventilado ?? true) ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500">
                        <span class="text-xs font-medium text-stone-700">Espacio con ventilación natural activa (ventanas / aire fresco)</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4 pt-4">
            <a href="{{ route('configurador.index') }}" class="px-5 py-3 rounded-xl text-xs font-bold text-stone-600 hover:bg-stone-200 transition">
                Omitir por ahora
            </a>
            <button type="submit" class="px-8 py-3.5 rounded-xl text-sm font-bold text-white bg-sparkle-600 hover:bg-sparkle-700 shadow-md transition">
                Guardar Perfil y Calibrar Fórmulas →
            </button>
        </div>
    </form>
</div>
@endsection
