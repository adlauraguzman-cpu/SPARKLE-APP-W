@extends('layouts.app')

@section('title', 'SPARKLE · Mi Espacio Vitamina — Bienestar y Fórmulas Aromáticas')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden bg-gradient-to-b from-amber-50/50 via-stone-50 to-stone-50 pt-12 pb-20 border-b border-stone-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7 space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-100 text-amber-900 text-xs font-bold tracking-wide uppercase">
                    <span class="w-2 h-2 rounded-full bg-amber-600 animate-ping"></span>
                    Versión 2.0 · Arquitectura de Bienestar
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-stone-900 tracking-tight leading-[1.15]">
                    Tu bienestar calibrado a través del <span class="text-transparent bg-clip-text bg-gradient-to-r from-sparkle-600 via-amber-600 to-amber-500 font-serif italic">aroma consciente</span>.
                </h1>
                <p class="text-lg text-stone-600 leading-relaxed max-w-2xl">
                    SPARKLE no es una tienda ni un catálogo comercial: es tu espacio interactivo para autoconocimiento sensorial, cálculo algorítmico de fórmulas aromáticas seguras y seguimiento emocional diario.
                </p>

                <!-- Action CTAs -->
                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <a href="{{ route('configurador.index') }}" class="px-6 py-3.5 rounded-xl text-base font-bold text-white bg-sparkle-600 hover:bg-sparkle-700 shadow-lg shadow-sparkle-600/25 transition transform hover:-translate-y-0.5 flex items-center gap-2">
                        <span>✨ Calcular Mi Fórmula Ahora</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                    <a href="{{ route('aromas.index') }}" class="px-6 py-3.5 rounded-xl text-base font-semibold text-stone-700 bg-white hover:bg-stone-100 border border-stone-300 shadow-sm transition">
                        Explorar Tríada de Aromas
                    </a>
                </div>

                <!-- Three pillars pills -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-6 border-t border-stone-200 text-xs text-stone-600">
                    <div class="flex items-center gap-2">
                        <span class="text-sparkle-600 font-bold">1. Conocerme:</span>
                        <span>Perfil sensorial y de seguridad</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sparkle-600 font-bold">2. Mi Fórmula:</span>
                        <span>Motor de afinidad en 5 pasos</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-sparkle-600 font-bold">3. Mi Rutina:</span>
                        <span>Diario y rachas conscientes</span>
                    </div>
                </div>
            </div>

            <!-- Interactive Preview Card -->
            <div class="lg:col-span-5">
                <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-stone-200 relative">
                    <div class="absolute -top-3 -right-3 bg-amber-500 text-white text-[11px] font-bold px-3 py-1 rounded-full shadow-md uppercase tracking-wide">
                        Simulación en Vivo
                    </div>
                    <div class="flex items-center justify-between border-b border-stone-100 pb-4 mb-5">
                        <div>
                            <div class="text-xs font-bold text-stone-400 uppercase tracking-wider">Última calibración sugerida</div>
                            <h3 class="text-lg font-bold text-stone-900">Enfoque Profundo & Serenidad</h3>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            100% Segura
                        </span>
                    </div>

                    <!-- Formula Donut Preview -->
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-xs font-bold text-stone-700 mb-1">
                                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-amber-700"></span> ALPHA (Maderas · Cedro)</span>
                                <span>60%</span>
                            </div>
                            <div class="w-full bg-stone-100 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-amber-700 h-full rounded-full" style="width: 60%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs font-bold text-stone-700 mb-1">
                                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-amber-400"></span> HELIOS (Cítricos · Bergamota)</span>
                                <span>40%</span>
                            </div>
                            <div class="w-full bg-stone-100 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-amber-400 h-full rounded-full" style="width: 40%"></div>
                            </div>
                        </div>

                        <div class="bg-amber-50/70 rounded-2xl p-4 border border-amber-100 text-xs text-amber-900 mt-4 space-y-1">
                            <div class="font-bold">🌿 Ritual Sugerido (15 min)</div>
                            <p class="text-stone-600">6 gotas en difusor ultrasónico. Inhalación consciente 4-7-8 al inicio de la jornada de concentración.</p>
                        </div>

                        <div class="pt-3">
                            <a href="{{ route('configurador.index') }}" class="block text-center w-full py-2.5 px-4 rounded-xl text-xs font-bold text-sparkle-700 bg-sparkle-50 hover:bg-sparkle-100 border border-sparkle-200 transition">
                                Probar con tus propias respuestas →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- The Three Pillars Section -->
<section class="py-16 bg-white border-b border-stone-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-14">
            <h2 class="text-xs uppercase font-bold tracking-widest text-sparkle-600 mb-2">Fundamento Metodológico</h2>
            <h3 class="text-3xl font-extrabold text-stone-900 tracking-tight">Los 3 Pilares de Mi Espacio Vitamina</h3>
            <p class="mt-3 text-stone-600 text-sm">Diseñados conforme a las directrices de bienestar y sin dinámicas de compraventa.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Pilar 1 -->
            <div class="bg-stone-50 rounded-2xl p-7 border border-stone-200/80 hover:border-sparkle-300 transition hover:shadow-md">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-xl mb-5">
                    1
                </div>
                <h4 class="text-xl font-bold text-stone-900 mb-2">Conocerme</h4>
                <p class="text-stone-600 text-xs leading-relaxed mb-4">
                    Perfil Sensorial y de Seguridad. Mapea tus familias aromáticas preferidas y evitadas, tu intensidad ideal y tus filtros de seguridad: alergias, mascotas en casa, niños o etapa de embarazo.
                </p>
                <ul class="text-xs space-y-1.5 text-stone-500 font-medium">
                    <li class="flex items-center gap-1.5"><span class="text-emerald-600">✓</span> Descarte automático de contraindicaciones</li>
                    <li class="flex items-center gap-1.5"><span class="text-emerald-600">✓</span> Calibración de espacios ventilados</li>
                    <li class="flex items-center gap-1.5"><span class="text-emerald-600">✓</span> Privacidad garantizada (Ley 1581 / GDPR)</li>
                </ul>
            </div>

            <!-- Pilar 2 -->
            <div class="bg-stone-50 rounded-2xl p-7 border border-stone-200/80 hover:border-sparkle-300 transition hover:shadow-md">
                <div class="w-12 h-12 rounded-2xl bg-sparkle-100 text-sparkle-800 flex items-center justify-center font-bold text-xl mb-5">
                    2
                </div>
                <h4 class="text-xl font-bold text-stone-900 mb-2">Mi Fórmula</h4>
                <p class="text-stone-600 text-xs leading-relaxed mb-4">
                    Configurador inteligente que evalúa tu objetivo (concentrarme, desacelerar, hacer una pausa), tu momento y tu emoción. Calcula porcentajes exactos de ALPHA, HELIOS y JAZ explicando el porqué.
                </p>
                <ul class="text-xs space-y-1.5 text-stone-500 font-medium">
                    <li class="flex items-center gap-1.5"><span class="text-emerald-600">✓</span> Reglas de afinidad auditadas (v1.0 / v2.0)</li>
                    <li class="flex items-center gap-1.5"><span class="text-emerald-600">✓</span> Normalización con corte menor al 10%</li>
                    <li class="flex items-center gap-1.5"><span class="text-emerald-600">✓</span> Ritual sugerido paso a paso con dosificación</li>
                </ul>
            </div>

            <!-- Pilar 3 -->
            <div class="bg-stone-50 rounded-2xl p-7 border border-stone-200/80 hover:border-sparkle-300 transition hover:shadow-md">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xl mb-5">
                    3
                </div>
                <h4 class="text-xl font-bold text-stone-900 mb-2">Mi Rutina y Mi Diario</h4>
                <p class="text-stone-600 text-xs leading-relaxed mb-4">
                    Programa horarios conscientes y recibe recordatorios. Al finalizar, registra tu emoción antes/después en el diario: el algoritmo aprende de tus calificaciones (1-5) para calibrar tus fórmulas futuras.
                </p>
                <ul class="text-xs space-y-1.5 text-stone-500 font-medium">
                    <li class="flex items-center gap-1.5"><span class="text-emerald-600">✓</span> Contador de racha consecutiva de bienestar</li>
                    <li class="flex items-center gap-1.5"><span class="text-emerald-600">✓</span> Reproductor sensorial guiado con respiración 4-7-8</li>
                    <li class="flex items-center gap-1.5"><span class="text-emerald-600">✓</span> Aprendizaje de notas con alta efectividad</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- The Three Essences Showcase -->
<section class="py-16 bg-stone-50 border-b border-stone-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
                <h2 class="text-xs uppercase font-bold tracking-widest text-sparkle-600 mb-2">Tríada Aromática</h2>
                <h3 class="text-3xl font-extrabold text-stone-900 tracking-tight">Los Aromas Fundamentales</h3>
            </div>
            <a href="{{ route('aromas.index') }}" class="mt-4 md:mt-0 text-sm font-bold text-sparkle-600 hover:text-sparkle-700 flex items-center gap-1">
                Ver fichas técnicas y de seguridad completas →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($aromas as $aroma)
                <div class="bg-white rounded-3xl p-7 border border-stone-200 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold uppercase tracking-wider px-2.5 py-1 rounded-full {{ $aroma->nombre === 'ALPHA' ? 'bg-amber-100 text-amber-900' : ($aroma->nombre === 'HELIOS' ? 'bg-yellow-100 text-yellow-900' : 'bg-purple-100 text-purple-900') }}">
                            {{ $aroma->familia }}
                        </span>
                        <span class="text-xs font-semibold text-emerald-600 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Activo
                        </span>
                    </div>

                    <h4 class="text-2xl font-black text-stone-900 mb-1 tracking-tight">{{ $aroma->nombre }}</h4>
                    <p class="text-xs font-semibold text-sparkle-600 mb-3">{{ $aroma->notas }}</p>
                    <p class="text-stone-600 text-xs leading-relaxed mb-5">
                        {{ $aroma->descripcion }}
                    </p>

                    <div class="pt-4 border-t border-stone-100 text-[11px] text-stone-500 space-y-1">
                        <div class="font-bold text-stone-700">Filtro de seguridad:</div>
                        @if(!empty($aroma->advertencias_seguridad['alergias']))
                            <div>• Alérgenos: {{ implode(', ', $aroma->advertencias_seguridad['alergias']) }}</div>
                        @endif
                        @if(isset($aroma->advertencias_seguridad['mascotas_seguro']) && !$aroma->advertencias_seguridad['mascotas_seguro'])
                            <div class="text-amber-700 font-medium">⚠️ Precaución con mascotas en casa</div>
                        @endif
                        @if(isset($aroma->advertencias_seguridad['embarazo_seguro']) && !$aroma->advertencias_seguridad['embarazo_seguro'])
                            <div class="text-amber-700 font-medium">⚠️ Restringido en embarazo/lactancia</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Governance & Roles Matrix -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <h2 class="text-xs uppercase font-bold tracking-widest text-sparkle-600 mb-2">Gobernanza y Separación de Responsabilidades</h2>
            <h3 class="text-3xl font-extrabold text-stone-900 tracking-tight">4 Roles de la Plataforma</h3>
            <p class="mt-3 text-stone-600 text-sm">Cumplimiento estricto del principio de menor privilegio y privacidad de la salud.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="p-6 rounded-2xl bg-stone-50 border border-stone-200">
                <div class="text-2xl mb-2">🌱</div>
                <h4 class="font-bold text-stone-900 mb-1 text-sm">1. Invitado</h4>
                <div class="text-[11px] text-sparkle-600 font-semibold mb-2">Sin Registro</div>
                <p class="text-xs text-stone-600 leading-relaxed">
                    Acceso libre al configurador para experimentar sesiones de prueba sin almacenar datos personales.
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-stone-50 border border-stone-200">
                <div class="text-2xl mb-2">👤</div>
                <h4 class="font-bold text-stone-900 mb-1 text-sm">2. Persona Usuaria</h4>
                <div class="text-[11px] text-sparkle-600 font-semibold mb-2">Con Cuenta Privada</div>
                <p class="text-xs text-stone-600 leading-relaxed">
                    Perfil sensorial, perfil médico de seguridad, biblioteca de fórmulas, rutinas interactivas y diario íntimo con racha.
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-stone-50 border border-stone-200">
                <div class="text-2xl mb-2">🧪</div>
                <h4 class="font-bold text-stone-900 mb-1 text-sm">3. Especialista</h4>
                <div class="text-[11px] text-emerald-700 font-semibold mb-2">Rol Interno</div>
                <p class="text-xs text-stone-600 leading-relaxed">
                    Editor de reglas de afinidad (pesos 0-3), simulador y aprobación cruzada obligatoria (Maker-Checker por segundo especialista).
                </p>
            </div>

            <div class="p-6 rounded-2xl bg-stone-50 border border-stone-200">
                <div class="text-2xl mb-2">⚙️</div>
                <h4 class="font-bold text-stone-900 mb-1 text-sm">4. Administrador</h4>
                <div class="text-[11px] text-indigo-700 font-semibold mb-2">Gobernanza</div>
                <p class="text-xs text-stone-600 leading-relaxed">
                    Métricas globales, solicitudes ARCO/GDPR y auditoría. <strong>Bloqueo estricto: no puede ver diarios ni perfiles médicos de usuarios.</strong>
                </p>
            </div>
        </div>
    </div>
</section>
@endsection
