<!DOCTYPE html>
<html lang="es" class="h-full bg-stone-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SPARKLE · Mi Espacio Vitamina')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sparkle: {
                            50: '#FAF8F5',
                            100: '#F5F0E8',
                            200: '#EBDDCB',
                            300: '#DFC5A7',
                            400: '#CFA87F',
                            500: '#B88B57', // Ámbar Vitamina
                            600: '#9B6F3E',
                            700: '#7A542D',
                            800: '#5C3E21',
                            900: '#3D2814',
                        },
                        sage: {
                            50: '#F4F7F4',
                            100: '#E4EBE5',
                            200: '#C9D8CB',
                            300: '#A7C0AB',
                            400: '#81A386',
                            500: '#5F8565',
                            600: '#4A694F',
                            700: '#38503C',
                        }
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }
        [x-cloak] { display: none !important; }
    </style>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="flex flex-col min-h-screen text-stone-800 antialiased bg-stone-50 selection:bg-sparkle-200">

    <!-- Top Demo Switcher Bar -->
    <div class="bg-stone-900 text-stone-300 text-xs py-1.5 px-4 border-b border-stone-800">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                    MODO DEMO
                </span>
                <span class="hidden sm:inline text-stone-400">Alternar rol rápido para evaluación:</span>
            </div>
            <div class="flex items-center gap-1.5 flex-wrap">
                <a href="{{ route('demo.login', 'usuario') }}" class="px-2 py-0.5 rounded transition {{ Auth::check() && Auth::user()->isUsuario() ? 'bg-amber-600 text-white font-medium' : 'bg-stone-800 hover:bg-stone-700 text-stone-200' }}">
                    👤 María (Usuario)
                </a>
                <a href="{{ route('demo.login', 'especialista') }}" class="px-2 py-0.5 rounded transition {{ Auth::check() && Auth::user()->isEspecialista() && Auth::user()->email === 'especialista1@sparkle.com' ? 'bg-amber-600 text-white font-medium' : 'bg-stone-800 hover:bg-stone-700 text-stone-200' }}">
                    🧪 Dr. Carlos (Especialista)
                </a>
                <a href="{{ route('demo.login', 'revisor') }}" class="px-2 py-0.5 rounded transition {{ Auth::check() && Auth::user()->isEspecialista() && Auth::user()->email === 'especialista2@sparkle.com' ? 'bg-amber-600 text-white font-medium' : 'bg-stone-800 hover:bg-stone-700 text-stone-200' }}">
                    🔍 Dra. Elena (Revisor Maker-Checker)
                </a>
                <a href="{{ route('demo.login', 'admin') }}" class="px-2 py-0.5 rounded transition {{ Auth::check() && Auth::user()->isAdministrador() ? 'bg-amber-600 text-white font-medium' : 'bg-stone-800 hover:bg-stone-700 text-stone-200' }}">
                    ⚙️ Laura (Admin)
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-stone-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center gap-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-sparkle-600 to-amber-400 flex items-center justify-center text-white shadow-sm shadow-sparkle-500/20 group-hover:scale-105 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-xl font-extrabold tracking-tight text-stone-900 group-hover:text-sparkle-600 transition">SPARKLE</span>
                            <span class="block text-[10px] uppercase font-bold tracking-widest text-sparkle-600 -mt-1">Mi Espacio Vitamina</span>
                        </div>
                    </a>

                    <!-- Navigation Links -->
                    <nav class="hidden md:flex items-center gap-1">
                        <a href="{{ route('home') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('home') ? 'text-sparkle-700 bg-sparkle-50 font-semibold' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-50' }}">
                            Inicio
                        </a>
                        <a href="{{ route('aromas.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('aromas.*') ? 'text-sparkle-700 bg-sparkle-50 font-semibold' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-50' }}">
                            Catálogo de Aromas
                        </a>
                        <a href="{{ route('configurador.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('configurador.*') ? 'text-sparkle-700 bg-sparkle-50 font-semibold' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-50' }}">
                            ✨ Mi Fórmula (Configurador)
                        </a>

                        @auth
                            <a href="{{ route('rutinas.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('rutinas.*') ? 'text-sparkle-700 bg-sparkle-50 font-semibold' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-50' }}">
                                🌿 Rutinas
                            </a>
                            <a href="{{ route('diario.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('diario.*') ? 'text-sparkle-700 bg-sparkle-50 font-semibold' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-50' }}">
                                📖 Mi Diario
                            </a>

                            @if(Auth::user()->isEspecialista())
                                <a href="{{ route('especialista.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-emerald-800 bg-emerald-50 hover:bg-emerald-100 font-semibold border border-emerald-200">
                                    🧪 Panel Especialista
                                </a>
                            @endif

                            @if(Auth::user()->isAdministrador())
                                <a href="{{ route('admin.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium text-indigo-800 bg-indigo-50 hover:bg-indigo-100 font-semibold border border-indigo-200">
                                    ⚙️ Panel Admin
                                </a>
                            @endif
                        @endauth
                    </nav>
                </div>

                <!-- Right Actions / Profile -->
                <div class="flex items-center gap-3">
                    @auth
                        <div class="flex items-center gap-2">
                            <a href="{{ route('onboarding.index') }}" title="Editar mi perfil sensorial" class="p-1.5 rounded-lg text-stone-500 hover:text-stone-800 hover:bg-stone-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </a>
                            <div class="text-right hidden sm:block">
                                <div class="text-xs font-bold text-stone-900 leading-tight">{{ Auth::user()->name }}</div>
                                <div class="text-[10px] uppercase font-semibold text-sparkle-600">{{ Auth::user()->rol }}</div>
                            </div>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-2.5 py-1 text-xs font-medium text-stone-600 hover:text-red-700 hover:bg-red-50 rounded-md transition">
                                    Salir
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('configurador.index') }}" class="text-xs font-semibold px-3 py-1.5 text-stone-700 bg-stone-100 hover:bg-stone-200 rounded-lg transition">
                            Probar sin cuenta
                        </a>
                        <a href="{{ route('login') }}" class="text-xs font-semibold px-3 py-1.5 text-sparkle-700 hover:bg-sparkle-50 rounded-lg transition">
                            Iniciar Sesión
                        </a>
                        <a href="{{ route('register') }}" class="text-xs font-bold px-3.5 py-1.5 text-white bg-sparkle-600 hover:bg-sparkle-700 shadow-sm rounded-lg transition">
                            Crear Cuenta
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Alerts -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-emerald-800 bg-emerald-50 rounded-xl border border-emerald-200 flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1 font-medium">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-4 text-sm text-red-800 bg-red-50 rounded-xl border border-red-200 flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1 font-medium">{{ session('error') }}</div>
            </div>
        @endif

        @if(session('info'))
            <div class="p-4 mb-4 text-sm text-sky-800 bg-sky-50 rounded-xl border border-sky-200 flex items-start gap-3 shadow-sm">
                <svg class="w-5 h-5 text-sky-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1 font-medium">{{ session('info') }}</div>
            </div>
        @endif

        @if(session('recordatorio_simulado'))
            @php $rec = session('recordatorio_simulado'); @endphp
            <div class="p-4 mb-4 bg-amber-50 border-2 border-amber-300 rounded-2xl shadow-md text-amber-950 flex items-start gap-3 animate-pulse">
                <span class="text-2xl">🔔</span>
                <div class="flex-1 text-sm">
                    <div class="font-bold text-amber-900">¡Notificación Simulada en Tiempo Real! (Canal: {{ ucfirst($rec['canal']) }})</div>
                    <div class="mt-0.5 text-amber-800">
                        Es hora de tu ritual <strong>{{ $rec['rutina'] }}</strong> a las <strong>{{ $rec['hora'] }}</strong> en <em>{{ $rec['espacio'] }}</em> con tu fórmula <strong>{{ $rec['formula'] }}</strong>.
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-stone-900 text-stone-400 text-sm mt-16 border-t border-stone-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-sparkle-600 to-amber-400 flex items-center justify-center text-white">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-white tracking-tight">SPARKLE · Mi Espacio Vitamina</span>
                    </div>
                    <p class="text-xs leading-relaxed text-stone-400 max-w-md">
                        Plataforma interactiva de bienestar olfativo y personalización sensorial. Diseñada para calibrar fórmulas aromáticas seguras, estructurar rutinas conscientes y acompañar tu evolución emocional diaria.
                    </p>
                    <div class="mt-3 text-[11px] text-stone-500">
                        Conforme a normatividad de privacidad de datos personales y bienestar (Ley 1581 / GDPR).
                    </div>
                </div>

                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-stone-200 mb-3">Pilares</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('onboarding.index') }}" class="hover:text-amber-400 transition">1. Conocerme (Perfil Sensorial & Seguridad)</a></li>
                        <li><a href="{{ route('configurador.index') }}" class="hover:text-amber-400 transition">2. Mi Fórmula (Configurador Inteligente)</a></li>
                        <li><a href="{{ route('rutinas.index') }}" class="hover:text-amber-400 transition">3. Mi Rutina y Mi Diario de Bienestar</a></li>
                        <li><a href="{{ route('aromas.index') }}" class="hover:text-amber-400 transition">Catálogo Esencial: ALPHA, HELIOS, JAZ</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-stone-200 mb-3">Gobernanza & Roles</h4>
                    <ul class="space-y-2 text-xs">
                        <li><span class="text-stone-400">Persona Usuaria</span> - Personalización y privacidad</li>
                        <li><span class="text-stone-400">Especialista en Aromas</span> - Reglas & Maker-Checker</li>
                        <li><span class="text-stone-400">Administrador</span> - Métricas & ARCO (Sin diarios)</li>
                        <li><span class="text-stone-400">Invitado</span> - Modo Demo sin registro</li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-stone-800 mt-8 pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-stone-500">
                <div>&copy; {{ date('Y') }} SPARKLE · Mi Espacio Vitamina. Versión 2.0 (Arquitectura de Bienestar).</div>
                <div class="flex gap-4 mt-2 sm:mt-0">
                    <span class="hover:text-stone-300">Aviso Legal</span>
                    <span class="hover:text-stone-300">Consentimiento Informado</span>
                    <span class="hover:text-stone-300">Seguridad Aromática</span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
