<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Control') · SPARKLE Interno</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8F9FA; color: #212529; }
        .sidebar-link-active { background-color: #A3542E; color: #FFFFFF !important; }
    </style>
    @stack('styles')
</head>
<body class="flex h-full">

    <!-- Sidebar Lateral -->
    <aside class="w-64 bg-[#1E1B18] text-[#E8E2DA] flex flex-col flex-shrink-0 min-h-screen">
        <div class="p-6 border-b border-stone-800">
            <a href="{{ route('admin.dashboard') }}" class="block">
                <span class="font-serif text-2xl font-bold tracking-widest text-white">SPARKLE</span>
                <span class="block text-xs uppercase tracking-wider text-amber-500 font-semibold mt-0.5">Gestión Interna</span>
            </a>
            <div class="mt-4 p-2.5 rounded bg-stone-900 border border-stone-800 text-xs">
                <div class="text-stone-400">Usuario activo:</div>
                <div class="font-bold text-white truncate">{{ Auth::user()->name }}</div>
                <div class="mt-1">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ Auth::user()->isAdmin() ? 'bg-amber-900/60 text-amber-200 border border-amber-700' : 'bg-blue-900/60 text-blue-200 border border-blue-700' }}">
                        {{ Auth::user()->isAdmin() ? 'Dueña (Administrador)' : 'Colaborador (Operación)' }}
                    </span>
                </div>
            </div>
        </div>

        <nav class="flex-1 p-4 space-y-1.5 text-sm overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-stone-300 hover:bg-stone-800 transition {{ request()->routeIs('admin.dashboard') ? 'sidebar-link-active' : '' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span>Panel de Control</span>
            </a>

            <!-- Inventario (Visible para Admin y Colaborador) -->
            @php
                $alertasCount = \App\Models\Inventario::whereColumn('stock_disponible', '<=', 'umbral_minimo')->count();
            @endphp
            <a href="{{ route('admin.inventario.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-stone-300 hover:bg-stone-800 transition {{ request()->routeIs('admin.inventario.*') ? 'sidebar-link-active' : '' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <span>Inventario</span>
                </div>
                @if($alertasCount > 0)
                    <span class="px-2 py-0.5 text-xs font-bold bg-amber-500 text-stone-900 rounded-full" title="{{ $alertasCount }} productos bajo stock mínimo">
                        {{ $alertasCount }}
                    </span>
                @endif
            </a>

            <!-- Pedidos (Visible para Admin y Colaborador) -->
            @php
                $pedidosNuevosCount = \App\Models\Pedido::whereIn('estado', ['pagado', 'en_preparacion'])->count();
            @endphp
            <a href="{{ route('admin.pedidos.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-lg text-stone-300 hover:bg-stone-800 transition {{ request()->routeIs('admin.pedidos.*') ? 'sidebar-link-active' : '' }}">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <span>Pedidos</span>
                </div>
                @if($pedidosNuevosCount > 0)
                    <span class="px-2 py-0.5 text-xs font-bold bg-[#A3542E] text-white rounded-full">
                        {{ $pedidosNuevosCount }}
                    </span>
                @endif
            </a>

            @if(Auth::user()->isAdmin())
                <div class="pt-4 pb-1">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-stone-500 px-3">Gestión de la Marca</span>
                </div>

                <!-- Catálogo y Precios (Solo Admin) -->
                <a href="{{ route('admin.productos.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-stone-300 hover:bg-stone-800 transition {{ request()->routeIs('admin.productos.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    <span>Catálogo y Precios</span>
                </a>

                <!-- Fidelización & Puntos (Solo Admin) -->
                <a href="{{ route('admin.fidelizacion.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-stone-300 hover:bg-stone-800 transition {{ request()->routeIs('admin.fidelizacion.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Fidelización & Cupones</span>
                </a>

                <!-- Reseñas (Solo Admin) -->
                <a href="{{ route('admin.resenas.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-stone-300 hover:bg-stone-800 transition {{ request()->routeIs('admin.resenas.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    <span>Moderar Reseñas</span>
                </a>

                <!-- Usuarios y Roles (Solo Admin) -->
                <a href="{{ route('admin.usuarios.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-stone-300 hover:bg-stone-800 transition {{ request()->routeIs('admin.usuarios.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span>Usuarios y Roles</span>
                </a>
            @endif
        </nav>

        <div class="p-4 border-t border-stone-800 space-y-2">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-xs text-stone-400 hover:text-white transition">
                <span>←</span> Ir a la Tienda Pública
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full text-left text-xs text-red-400 hover:text-red-300 transition">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </aside>

    <!-- Área de Trabajo Principal -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <header class="bg-white border-b border-stone-200 px-8 py-4 flex items-center justify-between">
            <h1 class="text-xl font-bold text-stone-900">@yield('page_title', 'Administración SPARKLE')</h1>
            <div class="flex items-center gap-4">
                <span class="text-xs text-stone-500">WSL Ubuntu · Entorno Lando</span>
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500" title="Sistema en línea"></span>
            </div>
        </header>

        <main class="p-8">
            <!-- Alertas Flash -->
            @if(session('success') || session('stock_success'))
                <div class="p-4 mb-6 rounded-lg bg-[#EBF5EF] border border-[#A5D6A7] text-[#2E7D32] flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    <div class="font-medium text-sm">{{ session('success') ?? session('stock_success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 mb-6 rounded-lg bg-[#FDF2F2] border border-[#EF9A9A] text-[#C62828] flex items-center gap-3">
                    <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                    <div class="font-medium text-sm">{{ session('error') }}</div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
