@extends('layouts.app')

@section('title', 'SPARKLE · Aromas de Autor & Bienestar')

@section('content')
<!-- Hero Section -->
<section class="relative bg-[#F4EFEA] border-b border-[#E8E2DA] overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div class="space-y-6">
            <span class="inline-block px-3 py-1 bg-[#F7EBE4] text-[#A3542E] text-xs font-bold uppercase tracking-widest rounded-full">
                Colección de Autor · Edición 2026
            </span>
            <h1 class="font-serif text-5xl sm:text-6xl lg:text-7xl font-bold text-[#231F20] leading-[1.08]">
                Aromas que transforman la energía de tu espacio.
            </h1>
            <p class="text-base sm:text-lg text-[#736B66] max-w-xl leading-relaxed">
                Difusores artesanales y recargas ecológicas formulados con aceites botánicos puros. Tres experiencias olfativas diseñadas para cada momento de tu día.
            </p>
            <div class="flex flex-wrap gap-4 pt-4">
                <a href="{{ route('products.index') }}" class="btn-terracotta px-8 py-4 rounded-full font-semibold text-sm tracking-wider shadow-sm">
                    Explorar Catálogo
                </a>
                <a href="{{ route('guia.index') }}" class="btn-outline-terracotta px-8 py-4 rounded-full font-semibold text-sm tracking-wider">
                    Descubrir mi Aroma Ideal →
                </a>
            </div>
            <div class="pt-6 flex items-center gap-6 text-xs text-[#736B66]">
                <div class="flex items-center gap-2">
                    <span class="text-emerald-700 font-bold">✓</span> Sistema Recargable (Refill)
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-emerald-700 font-bold">✓</span> Fórmulas 100% Libres de Tóxicos
                </div>
            </div>
        </div>
        <div class="relative">
            <div class="aspect-square rounded-2xl overflow-hidden shadow-2xl border border-stone-200">
                <img src="https://images.unsplash.com/photo-1608571423902-eed4a5ad8108?auto=format&fit=crop&w=1000&q=80" 
                     alt="Kit SPARKLE Difusor de autor" 
                     class="w-full h-full object-cover">
            </div>
            <div class="absolute -bottom-6 -left-6 bg-white p-6 rounded-xl border border-[#E8E2DA] shadow-lg max-w-xs hidden sm:block">
                <span class="text-xs uppercase tracking-widest text-[#A3542E] font-bold">Programa de Fidelización</span>
                <p class="text-sm font-semibold text-[#231F20] mt-1">Acumula puntos con cada compra y canjéalos por recargas y regalos.</p>
            </div>
        </div>
    </div>
</section>

<!-- Tres Aromas Insignia -->
<section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-2xl mx-auto mb-12">
        <span class="text-xs font-bold uppercase tracking-widest text-[#A3542E]">El Trilogía SPARKLE</span>
        <h2 class="font-serif text-3xl sm:text-4xl font-bold text-[#231F20] mt-2">Tres estados de ánimo, un mismo ritual</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- ALPHA -->
        <div class="bg-white rounded-xl p-8 border border-[#E8E2DA] hover:shadow-md transition">
            <span class="inline-block px-3 py-1 rounded text-xs font-bold badge-aroma-alpha mb-4">ALPHA</span>
            <h3 class="font-serif text-2xl font-bold text-[#231F20]">Maderas Nobles & Ámbar</h3>
            <p class="text-sm text-[#736B66] mt-3 leading-relaxed">
                Notas cálidas de cedro, sándalo y un toque de cardamomo. Diseñado para momentos de concentración, calma y elegancia ejecutiva.
            </p>
            <a href="{{ route('products.index', ['aroma' => 'ALPHA']) }}" class="inline-block mt-6 text-sm font-semibold text-[#A3542E] hover:underline">
                Ver colección ALPHA →
            </a>
        </div>

        <!-- HELIOS -->
        <div class="bg-white rounded-xl p-8 border border-[#E8E2DA] hover:shadow-md transition">
            <span class="inline-block px-3 py-1 rounded text-xs font-bold badge-aroma-helios mb-4">HELIOS</span>
            <h3 class="font-serif text-2xl font-bold text-[#231F20]">Cítrico Solar & Azahar</h3>
            <p class="text-sm text-[#736B66] mt-3 leading-relaxed">
                Destellos vibrantes de bergamota, flores blancas y té verde. Perfecto para iluminar las mañanas y llenar de vitalidad tu hogar.
            </p>
            <a href="{{ route('products.index', ['aroma' => 'HELIOS']) }}" class="inline-block mt-6 text-sm font-semibold text-[#A3542E] hover:underline">
                Ver colección HELIOS →
            </a>
        </div>

        <!-- JAZ -->
        <div class="bg-white rounded-xl p-8 border border-[#E8E2DA] hover:shadow-md transition">
            <span class="inline-block px-3 py-1 rounded text-xs font-bold badge-aroma-jaz mb-4">JAZ</span>
            <h3 class="font-serif text-2xl font-bold text-[#231F20]">Jazmín Nocturno & Té Blanco</h3>
            <p class="text-sm text-[#736B66] mt-3 leading-relaxed">
                Una caricia floral etérea y relajante. Ideal para el dormitorio, momentos de meditación, lectura o para despedir el día.
            </p>
            <a href="{{ route('products.index', ['aroma' => 'JAZ']) }}" class="inline-block mt-6 text-sm font-semibold text-[#A3542E] hover:underline">
                Ver colección JAZ →
            </a>
        </div>
    </div>
</section>

<!-- Catálogo Destacado con Disponibilidad en Tiempo Real -->
<section class="py-16 bg-white border-y border-[#E8E2DA]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-10 gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-[#A3542E]">Selección Destacada</span>
                <h2 class="font-serif text-3xl sm:text-4xl font-bold text-[#231F20] mt-1">Nuestros Kits y Recargas</h2>
            </div>
            <a href="{{ route('products.index') }}" class="text-sm font-bold text-[#A3542E] hover:underline">
                Ver todos los productos ({{ \App\Models\Producto::where('activo', true)->count() }}) →
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($productosDestacados as $producto)
                <div class="group bg-[#FAF8F5] rounded-xl overflow-hidden border border-[#E8E2DA] flex flex-col justify-between hover:border-[#A3542E] transition">
                    <div>
                        <div class="relative aspect-square overflow-hidden bg-stone-100">
                            <img src="{{ $producto->foto }}" alt="{{ $producto->nombre }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                                <span class="px-2.5 py-1 text-[11px] font-bold rounded-full 
                                    {{ $producto->aroma === 'ALPHA' ? 'badge-aroma-alpha' : ($producto->aroma === 'HELIOS' ? 'badge-aroma-helios' : 'badge-aroma-jaz') }}">
                                    {{ $producto->aroma }}
                                </span>
                                <span class="px-2.5 py-0.5 text-[10px] font-semibold rounded-full bg-white/90 text-stone-800">
                                    {{ $producto->tipo }}
                                </span>
                            </div>

                            @if(!$producto->en_stock)
                                <div class="absolute inset-0 bg-stone-900/60 backdrop-blur-[2px] flex items-center justify-center">
                                    <span class="px-4 py-1.5 bg-red-600 text-white text-xs font-bold uppercase tracking-wider rounded-full shadow">
                                        Agotado
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="p-6">
                            <h3 class="font-serif text-xl font-bold text-[#231F20]">
                                <a href="{{ route('products.show', $producto->slug) }}" class="hover:text-[#A3542E] transition">
                                    {{ $producto->nombre }}
                                </a>
                            </h3>
                            <p class="text-xs text-[#736B66] mt-2 line-clamp-2 leading-relaxed">
                                {{ $producto->descripcion }}
                            </p>
                        </div>
                    </div>

                    <div class="px-6 pb-6 pt-2 border-t border-[#E8E2DA] flex items-center justify-between">
                        <div>
                            <span class="text-xs text-[#736B66] block">Precio</span>
                            <span class="text-lg font-bold text-[#231F20]">${{ number_format($producto->precio, 0, ',', '.') }} COP</span>
                        </div>

                        @if($producto->en_stock)
                            <form action="{{ route('cart.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                <input type="hidden" name="cantidad" value="1">
                                <button type="submit" class="btn-terracotta px-4 py-2 rounded-lg text-xs font-semibold">
                                    + Agregar
                                </button>
                            </form>
                        @else
                            <a href="{{ route('products.show', $producto->slug) }}" class="text-xs font-semibold text-[#A3542E] hover:underline">
                                Avísame →
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Banner Guía de Aromas Interactiva -->
<section class="py-16 bg-[#F4EFEA] border-b border-[#E8E2DA]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
        <span class="inline-block px-3 py-1 bg-white text-[#A3542E] text-xs font-bold uppercase tracking-widest rounded-full border border-stone-200">
            Experiencia Guiada
        </span>
        <h2 class="font-serif text-4xl sm:text-5xl font-bold text-[#231F20]">¿No sabes cuál aroma elegir?</h2>
        <p class="text-[#736B66] text-base sm:text-lg max-w-xl mx-auto leading-relaxed">
            Responde 3 preguntas rápidas sobre tus espacios y la energía que deseas evocar. Te recomendamos el kit perfecto para ti.
        </p>
        <div>
            <a href="{{ route('guia.index') }}" class="btn-terracotta px-8 py-4 rounded-full font-semibold text-sm tracking-wider inline-block">
                Iniciar Guía de Aromas (1 min) →
            </a>
        </div>
    </div>
</section>

<!-- Reseñas de Clientes -->
@if($resenasRecientes->isNotEmpty())
<section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center max-w-2xl mx-auto mb-10">
        <span class="text-xs font-bold uppercase tracking-widest text-[#A3542E]">Comunidad y Testimonios</span>
        <h2 class="font-serif text-3xl font-bold text-[#231F20] mt-1">Lo que dicen quienes ya viven la experiencia</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @foreach($resenasRecientes as $resena)
            <div class="bg-white p-6 rounded-xl border border-[#E8E2DA] shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-1 text-amber-500 mb-3">
                        @for($i = 1; $i <= 5; $i++)
                            <span>{{ $i <= $resena->calificacion ? '★' : '☆' }}</span>
                        @endfor
                    </div>
                    <p class="text-sm text-stone-700 italic leading-relaxed">
                        "{{ $resena->comentario }}"
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-stone-100 flex items-center justify-between text-xs">
                    <span class="font-bold text-stone-900">{{ $resena->user->name }}</span>
                    <span class="text-stone-500">{{ $resena->producto->nombre }}</span>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

@endsection
