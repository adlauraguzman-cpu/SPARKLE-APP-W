@extends('layouts.app')

@section('title', 'Tu Aroma Recomendado: ' . $aromaRecomendado . ' · SPARKLE')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center space-y-4 mb-12">
        <span class="inline-block px-3 py-1 bg-[#F7EBE4] text-[#A3542E] text-xs font-bold uppercase tracking-widest rounded-full">
            Resultado de tu Guía Sensorial
        </span>
        <h1 class="font-serif text-4xl sm:text-6xl font-bold text-[#231F20]">Tu Match Ideal: {{ $aromaRecomendado }}</h1>
        <p class="text-lg font-serif italic text-stone-600">{{ $perfil }}</p>
    </div>

    <!-- Tarjeta del Aroma -->
    <div class="bg-white rounded-2xl border border-[#E8E2DA] p-8 sm:p-12 shadow-sm mb-14 text-center max-w-2xl mx-auto space-y-6">
        <div class="inline-flex p-4 rounded-full text-white text-3xl
            {{ $aromaRecomendado === 'ALPHA' ? 'badge-aroma-alpha' : ($aromaRecomendado === 'HELIOS' ? 'badge-aroma-helios' : 'badge-aroma-jaz') }}">
            {{ $aromaRecomendado === 'ALPHA' ? '🪵' : ($aromaRecomendado === 'HELIOS' ? '🍊' : '🌸') }}
        </div>
        <p class="text-stone-700 text-base leading-relaxed">
            {{ $descripcion }}
        </p>
        <div class="pt-4 flex justify-center gap-4">
            <a href="{{ route('guia.index') }}" class="text-xs text-stone-500 hover:text-stone-800 underline">
                ← Repetir el test
            </a>
        </div>
    </div>

    <!-- Productos Disponibles con este Aroma -->
    <div>
        <h2 class="font-serif text-2xl font-bold text-stone-900 mb-6 text-center">Productos disponibles en aroma {{ $aromaRecomendado }}</h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($productosAroma as $producto)
                <div class="bg-white rounded-xl overflow-hidden border border-[#E8E2DA] flex flex-col justify-between hover:shadow-md transition">
                    <div>
                        <div class="aspect-square bg-stone-100 relative">
                            <img src="{{ $producto->foto }}" alt="{{ $producto->nombre }}" class="w-full h-full object-cover">
                            @if(!$producto->en_stock)
                                <div class="absolute inset-0 bg-stone-900/60 backdrop-blur-[2px] flex items-center justify-center">
                                    <span class="px-3 py-1 bg-red-600 text-white text-xs font-bold uppercase rounded-full">Agotado</span>
                                </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <span class="text-[10px] font-bold text-[#A3542E] uppercase">{{ $producto->tipo }}</span>
                            <h3 class="font-serif text-xl font-bold text-stone-900 mt-1">
                                <a href="{{ route('products.show', $producto->slug) }}" class="hover:text-[#A3542E] transition">{{ $producto->nombre }}</a>
                            </h3>
                            <p class="text-xs text-stone-500 mt-2 line-clamp-2">{{ $producto->descripcion }}</p>
                        </div>
                    </div>

                    <div class="px-6 pb-6 pt-2 border-t border-[#E8E2DA] flex items-center justify-between">
                        <span class="text-base font-bold text-stone-900">${{ number_format($producto->precio, 0, ',', '.') }} COP</span>
                        @if($producto->en_stock)
                            <form action="{{ route('cart.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                <button type="submit" class="btn-terracotta px-4 py-2 rounded-lg text-xs font-semibold">
                                    + Agregar
                                </button>
                            </form>
                        @else
                            <a href="{{ route('products.show', $producto->slug) }}" class="text-xs font-semibold text-[#A3542E] hover:underline">
                                Solicitar aviso →
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
