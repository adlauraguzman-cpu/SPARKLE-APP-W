@extends('layouts.app')

@section('title', 'Catálogo de Aromas Esenciales · SPARKLE')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-3xl mb-12">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sparkle-100 text-sparkle-900 text-xs font-bold tracking-wide uppercase mb-3">
            Tríada Maestra
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-stone-900 tracking-tight">Catálogo de Aromas y Fichas de Seguridad</h1>
        <p class="mt-3 text-stone-600 text-base leading-relaxed">
            Cada aroma en SPARKLE cumple una función terapéutica y emocional específica. Antes de cada formulación, el motor cruza los parámetros de seguridad para garantizar una experiencia 100% segura.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @foreach($aromas as $aroma)
            <div class="bg-white rounded-3xl p-8 border border-stone-200 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full {{ $aroma->nombre === 'ALPHA' ? 'bg-amber-100 text-amber-900' : ($aroma->nombre === 'HELIOS' ? 'bg-yellow-100 text-yellow-900' : 'bg-purple-100 text-purple-900') }}">
                            Familia {{ $aroma->familia }}
                        </span>
                        <span class="text-xs font-bold text-stone-400">ID #0{{ $aroma->id }}</span>
                    </div>

                    <h2 class="text-3xl font-black text-stone-900 tracking-tight mb-1">{{ $aroma->nombre }}</h2>
                    <div class="text-xs font-semibold text-sparkle-600 mb-4">{{ $aroma->notas }}</div>

                    <p class="text-sm text-stone-600 leading-relaxed mb-6">
                        {{ $aroma->descripcion }}
                    </p>

                    <!-- Advertencias de Seguridad -->
                    <div class="bg-stone-50 rounded-2xl p-4 border border-stone-200 text-xs space-y-2 mb-6">
                        <div class="font-bold text-stone-800 flex items-center gap-1.5">
                            <span>🛡️</span> Criterios de Seguridad Aromática:
                        </div>
                        <ul class="space-y-1 text-stone-600">
                            <li>
                                <strong>Alérgenos:</strong>
                                {{ !empty($aroma->advertencias_seguridad['alergias']) ? implode(', ', $aroma->advertencias_seguridad['alergias']) : 'Sin alérgenos comunes' }}
                            </li>
                            <li>
                                <strong>Mascotas en el hogar:</strong>
                                @if(isset($aroma->advertencias_seguridad['mascotas_seguro']) && $aroma->advertencias_seguridad['mascotas_seguro'])
                                    <span class="text-emerald-700 font-medium">✓ Seguro para difusión en presencia de animales</span>
                                @else
                                    <span class="text-red-700 font-medium">✗ Contraindicado si hay gatos o perros en la habitación</span>
                                @endif
                            </li>
                            <li>
                                <strong>Embarazo / Lactancia:</strong>
                                @if(isset($aroma->advertencias_seguridad['embarazo_seguro']) && $aroma->advertencias_seguridad['embarazo_seguro'])
                                    <span class="text-emerald-700 font-medium">✓ Seguro en difusión ambiental</span>
                                @else
                                    <span class="text-red-700 font-medium">✗ No recomendado en primer trimestre</span>
                                @endif
                            </li>
                        </ul>
                    </div>

                    <!-- Reglas de Afinidad Publicadas -->
                    <div>
                        <div class="text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">Afinidades Principales:</div>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($aroma->reglasAfinidad->where('estado', 'publicada')->take(5) as $regla)
                                <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-900 border border-amber-200">
                                    {{ ucfirst($regla->variable) }}: {{ $regla->valor }} (+{{ $regla->peso }})
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="pt-8 mt-6 border-t border-stone-100">
                    <a href="{{ route('configurador.index', ['aroma' => $aroma->id]) }}" class="block text-center w-full py-2.5 px-4 rounded-xl text-xs font-bold text-white bg-sparkle-600 hover:bg-sparkle-700 transition">
                        Incluir en Fórmula Personalizada →
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
