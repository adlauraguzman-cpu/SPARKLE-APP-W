<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SPARKLE') · Aromas de Autor & Bienestar</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- Google Fonts: Cormorant Garamond & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --sparkle-bg: #FAF8F5;
            --sparkle-surface: #FFFFFF;
            --sparkle-surface-alt: #F4EFEA;
            --sparkle-text: #231F20;
            --sparkle-muted: #736B66;
            --sparkle-terracotta: #A3542E;
            --sparkle-terracotta-hover: #8B4321;
            --sparkle-terracotta-light: #F7EBE4;
            --sparkle-gold: #C5A059;
            --sparkle-border: #E8E2DA;
            --sparkle-success: #2E7D32;
            --sparkle-success-bg: #EBF5EF;
            --sparkle-error: #C62828;
            --sparkle-error-bg: #FDF2F2;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--sparkle-bg);
            color: var(--sparkle-text);
        }

        .font-serif {
            font-family: 'Cormorant Garamond', Georgia, serif;
        }

        .btn-terracotta {
            background-color: var(--sparkle-terracotta);
            color: #FFFFFF;
            transition: all 0.2s ease-in-out;
        }
        .btn-terracotta:hover {
            background-color: var(--sparkle-terracotta-hover);
        }
        .btn-terracotta:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-outline-terracotta {
            border: 1px solid var(--sparkle-terracotta);
            color: var(--sparkle-terracotta);
            background-color: transparent;
            transition: all 0.2s ease-in-out;
        }
        .btn-outline-terracotta:hover {
            background-color: var(--sparkle-terracotta-light);
        }

        .badge-aroma-alpha {
            background-color: #2D3748;
            color: #E2E8F0;
        }
        .badge-aroma-helios {
            background-color: #D69E2E;
            color: #FFFFFF;
        }
        .badge-aroma-jaz {
            background-color: #553C9A;
            color: #FAF5FF;
        }

        /* Regla de accesibilidad y foco */
        input:focus, select:focus, textarea:focus, button:focus {
            outline: 2px solid var(--sparkle-terracotta);
            outline-offset: 1px;
        }
    </style>
    @stack('styles')
</head>
<body class="flex flex-col min-h-screen">

    <!-- Barra de Notificación Superior -->
    <div class="bg-[#231F20] text-[#E8E2DA] text-xs py-2 px-4 text-center tracking-wider">
        <span>✨ Envío gratis en Colombia por compras superiores a $200.000 · Acumula puntos con cada pedido</span>
    </div>

    <!-- Barra de Selección Rápida de Roles (Para Pruebas del Evaluador) -->
    <div class="bg-[#F4EFEA] border-b border-[#E8E2DA] px-4 py-1.5 text-xs text-[#736B66] flex flex-wrap items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <span class="font-medium text-[#231F20]">Modo Demostración / Roles:</span>
            <span class="text-gray-500">Prueba los flujos iniciando como:</span>
            <a href="{{ route('demo.login', 'cliente') }}" class="px-2 py-0.5 rounded bg-white hover:bg-stone-200 border border-stone-300 text-stone-800 font-medium">Cliente (María Torres)</a>
            <a href="{{ route('demo.login', 'colaborador') }}" class="px-2 py-0.5 rounded bg-amber-100 hover:bg-amber-200 border border-amber-300 text-amber-900 font-medium">Colaborador Operativo</a>
            <a href="{{ route('demo.login', 'admin') }}" class="px-2 py-0.5 rounded bg-stone-800 hover:bg-black text-white font-medium">Administrador (Dueña)</a>
        </div>
        <div>
            @auth
                <span class="text-stone-600">Sesión actual: <strong class="text-stone-900">{{ Auth::user()->name }}</strong> (Rol: <span class="capitalize font-semibold text-amber-800">{{ Auth::user()->rol }}</span>)</span>
            @else
                <span class="text-stone-500 italic">Sesión: Visitante sin registro</span>
            @endauth
        </div>
    </div>

    <!-- Navegación Principal -->
    <header class="bg-white/90 backdrop-blur sticky top-0 z-40 border-b border-[#E8E2DA]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logotipo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <span class="font-serif text-3xl font-bold tracking-[0.2em] text-[#231F20]">SPARKLE</span>
                    </a>
                </div>

                <!-- Enlaces de Navegación -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-medium tracking-wide">
                    <a href="{{ route('home') }}" class="text-[#231F20] hover:text-[#A3542E] transition">Inicio</a>
                    <a href="{{ route('products.index') }}" class="text-[#231F20] hover:text-[#A3542E] transition">Catálogo</a>
                    <a href="{{ route('guia.index') }}" class="flex items-center gap-1.5 text-[#A3542E] hover:text-[#8B4321] font-semibold">
                        <span>✦</span> Guía de Aromas
                    </a>
                    <a href="{{ route('products.index', ['tipo' => 'Refill']) }}" class="text-[#231F20] hover:text-[#A3542E] transition">Recargas (Refill)</a>
                    <a href="#comunidad" class="text-[#736B66] hover:text-[#231F20] transition">Comunidad</a>
                </nav>

                <!-- Acciones / Carrito / Usuario -->
                <div class="flex items-center gap-4">
                    @auth
                        @if(Auth::user()->canManageInventory())
                            <a href="{{ route('admin.dashboard') }}" class="hidden sm:inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-full bg-stone-800 text-white hover:bg-black transition">
                                ⚙ Panel {{ Auth::user()->isAdmin() ? 'Administrador' : 'Operativo' }}
                            </a>
                        @endif

                        <!-- Menú Mi Cuenta -->
                        <div class="relative group">
                            <button class="flex items-center gap-1.5 text-sm font-medium text-[#231F20] hover:text-[#A3542E] py-2">
                                <span>{{ explode(' ', Auth::user()->name)[0] }}</span>
                                <span class="bg-[#F7EBE4] text-[#A3542E] text-xs px-2 py-0.5 rounded-full font-bold">
                                    {{ Auth::user()->puntos_disponibles }} pts
                                </span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div class="absolute right-0 w-52 mt-1 bg-white border border-[#E8E2DA] rounded-lg shadow-lg py-2 hidden group-hover:block z-50">
                                <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-stone-700 hover:bg-stone-50">Mis Pedidos y Seguimiento</a>
                                <a href="{{ route('loyalty.index') }}" class="block px-4 py-2 text-sm text-stone-700 hover:bg-stone-50">Mis Puntos & Recompensas</a>
                                <hr class="my-1 border-stone-200">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Cerrar Sesión</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-[#231F20] hover:text-[#A3542E] transition">Ingresar</a>
                        <a href="{{ route('register') }}" class="hidden sm:inline-block text-sm font-medium text-[#A3542E] hover:underline">Registrarse</a>
                    @endauth

                    <!-- Carrito Ícono con Contador -->
                    @php
                        $cartService = app(\App\Services\CartService::class);
                        $cartTotals = $cartService->getTotals();
                    @endphp
                    <a href="{{ route('cart.index') }}" class="relative p-2 text-[#231F20] hover:text-[#A3542E] transition" aria-label="Carrito de compras">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        @if($cartTotals['total_items'] > 0)
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-[#A3542E] rounded-full">
                                {{ $cartTotals['total_items'] }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Alertas y Notificaciones Globales -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        @if(session('success'))
            <div class="p-4 mb-4 rounded-lg bg-[#EBF5EF] border border-[#A5D6A7] text-[#2E7D32] flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-4 rounded-lg bg-[#FDF2F2] border border-[#EF9A9A] text-[#C62828] flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="p-4 mb-4 rounded-lg bg-[#EBF3FA] border border-[#90CAF9] text-[#1565C0] flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                    <span>{{ session('info') }}</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Contenido Principal -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer Elegante -->
    <footer class="bg-[#1E1B18] text-[#E8E2DA] mt-20 border-t border-[#3A3530]" id="comunidad">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <!-- Columna 1: Marca -->
                <div class="space-y-4">
                    <span class="font-serif text-3xl font-bold tracking-[0.2em] text-white">SPARKLE</span>
                    <p class="text-sm text-stone-400 leading-relaxed">
                        Aromaterapia de autor y ambientación sensorial para hogares contemporáneos. Kits sostenibles con recargas botánicas.
                    </p>
                    <div class="text-xs text-stone-500">
                        © {{ date('Y') }} SPARKLE S.A.S. Todos los derechos reservados.
                    </div>
                </div>

                <!-- Columna 2: Aromas Insignia -->
                <div>
                    <h3 class="font-serif text-lg text-white mb-4">Nuestros Aromas</h3>
                    <ul class="space-y-2 text-sm text-stone-400">
                        <li><a href="{{ route('products.index', ['aroma' => 'ALPHA']) }}" class="hover:text-white transition">ALPHA · Maderas Nobles & Ámbar</a></li>
                        <li><a href="{{ route('products.index', ['aroma' => 'HELIOS']) }}" class="hover:text-white transition">HELIOS · Cítrico Solar & Azahar</a></li>
                        <li><a href="{{ route('products.index', ['aroma' => 'JAZ']) }}" class="hover:text-white transition">JAZ · Jazmín Nocturno & Té Blanco</a></li>
                        <li><a href="{{ route('guia.index') }}" class="text-[#A3542E] hover:underline font-medium">Hacer Guía de Aromas →</a></li>
                    </ul>
                </div>

                <!-- Columna 3: Experiencia y Clientes -->
                <div>
                    <h3 class="font-serif text-lg text-white mb-4">Tu Cuenta</h3>
                    <ul class="space-y-2 text-sm text-stone-400">
                        <li><a href="{{ route('orders.index') }}" class="hover:text-white transition">Seguimiento de Pedidos</a></li>
                        <li><a href="{{ route('loyalty.index') }}" class="hover:text-white transition">Puntos y Recompensas</a></li>
                        <li><a href="{{ route('products.index', ['tipo' => 'Refill']) }}" class="hover:text-white transition">Recompra de Refills</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">Ingreso / Registro</a></li>
                    </ul>
                </div>

                <!-- Columna 4: Suscripción Comunidad SPARKLE (Punto 2 & 7) -->
                <div>
                    <h3 class="font-serif text-lg text-white mb-2">Comunidad SPARKLE</h3>
                    <p class="text-xs text-stone-400 mb-4">Suscríbete para recibir lanzamientos privados y acumular tus primeros puntos.</p>
                    
                    @if(session('subscription_success'))
                        <div class="p-3 bg-stone-800 text-amber-300 text-xs rounded border border-amber-600/40">
                            {{ session('subscription_success') }}
                        </div>
                    @else
                        <form action="{{ route('suscribirse') }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <input type="text" name="nombre" placeholder="Tu nombre" required
                                    class="w-full px-3 py-2 bg-stone-900 border border-stone-700 text-white rounded text-xs placeholder-stone-500 focus:border-[#A3542E]">
                            </div>
                            <div>
                                <input type="email" name="correo" placeholder="correo@ejemplo.com" required
                                    class="w-full px-3 py-2 bg-stone-900 border border-stone-700 text-white rounded text-xs placeholder-stone-500 focus:border-[#A3542E]">
                            </div>
                            <div class="flex items-start gap-2">
                                <input type="checkbox" name="autoriza_datos" id="autoriza_datos_footer" required
                                    class="mt-1 rounded bg-stone-900 border-stone-700 text-[#A3542E] focus:ring-0">
                                <label for="autoriza_datos_footer" class="text-[11px] text-stone-400 leading-tight">
                                    Autorizo el tratamiento de mis datos personales según la política de privacidad.
                                </label>
                            </div>
                            <button type="submit" class="w-full py-2 px-4 bg-[#A3542E] hover:bg-[#8B4321] text-white font-medium text-xs rounded transition">
                                Unirme a la Comunidad
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
