@extends('layouts.app')

@section('title', $producto->nombre . ' · SPARKLE')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-stone-500 mb-8">
        <a href="{{ route('home') }}" class="hover:text-stone-900">Inicio</a>
        <span>/</span>
        <a href="{{ route('products.index') }}" class="hover:text-stone-900">Catálogo</a>
        <span>/</span>
        <span class="text-stone-900 font-semibold">{{ $producto->nombre }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-start">
        <!-- Imagen Principal -->
        <div class="space-y-4">
            <div class="aspect-square rounded-2xl overflow-hidden bg-stone-100 border border-[#E8E2DA] shadow-sm relative">
                <img src="{{ $producto->foto }}" alt="{{ $producto->nombre }}" class="w-full h-full object-cover">
                <div class="absolute top-4 left-4 flex gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-bold shadow
                        {{ $producto->aroma === 'ALPHA' ? 'badge-aroma-alpha' : ($producto->aroma === 'HELIOS' ? 'badge-aroma-helios' : 'badge-aroma-jaz') }}">
                        Aroma {{ $producto->aroma }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-white text-stone-800 shadow">
                        {{ $producto->tipo }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Información y Compra -->
        <div class="space-y-6">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-[#A3542E]">Difusión Botánica de Autor</span>
                <h1 class="font-serif text-3xl sm:text-4xl font-bold text-[#231F20] mt-1">{{ $producto->nombre }}</h1>
                <div class="mt-3 flex items-center gap-3">
                    <span class="text-3xl font-bold text-[#231F20]">${{ number_format($producto->precio, 0, ',', '.') }} COP</span>
                    <span class="text-xs text-stone-500">Impuestos incluidos</span>
                </div>
            </div>

            <!-- Disponibilidad de Inventario -->
            <div class="p-4 rounded-xl border {{ $producto->en_stock ? 'bg-[#EBF5EF] border-[#A5D6A7]' : 'bg-[#FDF2F2] border-[#EF9A9A]' }}">
                @if($producto->en_stock)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-sm text-[#2E7D32] font-semibold">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#2E7D32]"></span>
                            <span>En Stock · Listo para despacho inmediato</span>
                        </div>
                        <span class="text-xs text-stone-600 font-medium">({{ $producto->stock_disponible }} unidades disponibles)</span>
                    </div>
                @else
                    <div class="flex items-center gap-2 text-sm text-[#C62828] font-bold">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#C62828]"></span>
                        <span>Producto Agotado Temporalmente</span>
                    </div>
                    <p class="text-xs text-stone-600 mt-1">Estamos produciendo un nuevo lote artesanal en taller. Déjanos tu correo y te notificaremos primero apenas esté disponible.</p>
                @endif
            </div>

            <!-- Formulario de Compra O Aviso de Stock -->
            @if($producto->en_stock)
                <form action="{{ route('cart.store') }}" method="POST" class="space-y-4 pt-2">
                    @csrf
                    <input type="hidden" name="producto_id" value="{{ $producto->id }}">

                    <div class="flex items-center gap-4">
                        <label for="cantidad" class="text-sm font-semibold text-stone-800">Cantidad:</label>
                        <select name="cantidad" id="cantidad" class="px-4 py-2 border border-stone-300 rounded-lg text-sm bg-white focus:border-[#A3542E]">
                            @for($i = 1; $i <= min(10, $producto->stock_disponible); $i++)
                                <option value="{{ $i }}">{{ $i }} {{ $i === 1 ? 'unidad' : 'unidades' }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 pt-2">
                        <button type="submit" class="btn-terracotta flex-1 py-4 px-8 rounded-full font-bold text-sm tracking-wide shadow flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            <span>Agregar al Carrito</span>
                        </button>
                    </div>
                    <p class="text-xs text-stone-500 text-center">✨ Acumulas aproximadamente <strong>+{{ floor($producto->precio / 1000) }} puntos</strong> con esta compra al entregar.</p>
                </form>
            @else
                <!-- Flujo Producto Agotado: Notificación (Punto 2 & 3) -->
                <div class="bg-white p-6 rounded-xl border border-stone-200 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-stone-900">Avísame cuando haya stock</h3>
                    @if(session('stock_alert_success'))
                        <div class="p-3 bg-emerald-50 border border-emerald-300 text-emerald-800 rounded text-xs">
                            {{ session('stock_alert_success') }}
                        </div>
                    @else
                        <form action="{{ route('products.aviso-stock', $producto->id) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label for="correo_aviso" class="block text-xs font-medium text-stone-600 mb-1">Tu Correo Electrónico:</label>
                                <input type="email" name="correo" id="correo_aviso" required
                                       value="{{ Auth::check() ? Auth::user()->email : old('correo') }}"
                                       placeholder="tu@correo.com"
                                       class="w-full px-3 py-2 text-sm border border-stone-300 rounded-lg focus:border-[#A3542E]">
                            </div>
                            <button type="submit" class="w-full py-2.5 px-4 bg-stone-800 hover:bg-black text-white text-xs font-bold rounded-lg transition">
                                Notificarme disponibilidad
                            </button>
                        </form>
                    @endif
                </div>
            @endif

            <!-- Descripción y Detalles -->
            <div class="pt-6 border-t border-[#E8E2DA] space-y-4 text-sm text-stone-700 leading-relaxed">
                <h3 class="font-serif text-lg font-bold text-[#231F20]">Descripción Sensorial</h3>
                <p>{{ $producto->descripcion }}</p>
                
                <div class="grid grid-cols-2 gap-4 pt-4 text-xs">
                    <div class="p-3 bg-[#FAF8F5] rounded-lg border border-[#E8E2DA]">
                        <span class="font-bold text-stone-900 block">Duración estimada</span>
                        <span class="text-stone-600">60 a 90 días de difusión continua</span>
                    </div>
                    <div class="p-3 bg-[#FAF8F5] rounded-lg border border-[#E8E2DA]">
                        <span class="font-bold text-stone-900 block">Ingredientes</span>
                        <span class="text-stone-600">Base biodegradable & aceites de alta pureza</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alternativas si está agotado o productos sugeridos -->
    @if($alternativas->isNotEmpty())
        <div class="mt-20 pt-12 border-t border-[#E8E2DA]">
            <h2 class="font-serif text-2xl font-bold text-[#231F20] mb-6">Alternativas recomendadas en la misma familia olfativa</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                @foreach($alternativas as $alt)
                    <div class="bg-white p-4 rounded-xl border border-[#E8E2DA] flex items-center gap-4 hover:shadow-sm transition">
                        <img src="{{ $alt->foto }}" alt="{{ $alt->nombre }}" class="w-20 h-20 rounded-lg object-cover">
                        <div class="flex-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#A3542E]">{{ $alt->aroma }}</span>
                            <h4 class="font-serif text-base font-bold text-stone-900 line-clamp-1">
                                <a href="{{ route('products.show', $alt->slug) }}" class="hover:underline">{{ $alt->nombre }}</a>
                            </h4>
                            <span class="text-xs font-bold text-stone-700">${{ number_format($alt->precio, 0, ',', '.') }} COP</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Reseñas Verificadas -->
    <div class="mt-20 pt-12 border-t border-[#E8E2DA]">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="font-serif text-3xl font-bold text-[#231F20]">Reseñas de Clientes</h2>
                <p class="text-xs text-stone-500 mt-1">Opiniones de compradores con entregas confirmadas</p>
            </div>
        </div>

        @if($producto->resenas->isEmpty())
            <div class="p-8 bg-white rounded-xl border border-[#E8E2DA] text-center text-stone-500 text-sm">
                Aún no hay reseñas para este producto. Sé el primero en compartir tu experiencia tras recibir tu pedido.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($producto->resenas as $resena)
                    <div class="bg-white p-6 rounded-xl border border-[#E8E2DA] shadow-sm">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-1 text-amber-500">
                                @for($i = 1; $i <= 5; $i++)
                                    <span>{{ $i <= $resena->calificacion ? '★' : '☆' }}</span>
                                @endfor
                            </div>
                            <span class="text-xs text-stone-400">{{ $resena->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-stone-700 leading-relaxed italic">"{{ $resena->comentario }}"</p>
                        <div class="mt-4 flex items-center gap-2 text-xs">
                            <span class="font-bold text-stone-900">{{ $resena->user->name }}</span>
                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px]">✓ Compra Verificada</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
