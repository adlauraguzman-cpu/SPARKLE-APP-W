@extends('layouts.app')

@section('title', 'Catálogo de Aromas y Difusores · SPARKLE')

@section('content')
<div class="bg-[#F4EFEA] border-b border-[#E8E2DA] py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="font-serif text-4xl sm:text-5xl font-bold text-[#231F20]">Catálogo SPARKLE</h1>
        <p class="text-stone-600 mt-2 text-sm sm:text-base">Explora nuestra gama de difusores de autor, sets de regalo y recargas botánicas.</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Barra de Filtros -->
    <div class="bg-white p-6 rounded-xl border border-[#E8E2DA] shadow-sm mb-10 space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Tipos de Producto -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider mr-2">Formato:</span>
                <a href="{{ route('products.index', array_merge(request()->query(), ['tipo' => null])) }}"
                   class="px-3.5 py-1.5 rounded-full text-xs font-semibold {{ !request('tipo') ? 'bg-[#A3542E] text-white' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">
                   Todos
                </a>
                @foreach($tipos as $tipo)
                    <a href="{{ route('products.index', array_merge(request()->query(), ['tipo' => $tipo])) }}"
                       class="px-3.5 py-1.5 rounded-full text-xs font-semibold {{ request('tipo') === $tipo ? 'bg-[#A3542E] text-white' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}">
                       {{ $tipo }}
                    </a>
                @endforeach
            </div>

            <!-- Búsqueda -->
            <form action="{{ route('products.index') }}" method="GET" class="flex items-center gap-2">
                @if(request('tipo')) <input type="hidden" name="tipo" value="{{ request('tipo') }}"> @endif
                @if(request('aroma')) <input type="hidden" name="aroma" value="{{ request('aroma') }}"> @endif
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por aroma o nombre..."
                       class="px-3.5 py-1.5 text-xs bg-stone-50 border border-stone-300 rounded-lg focus:outline-none focus:border-[#A3542E] w-60">
                <button type="submit" class="px-3 py-1.5 bg-stone-800 text-white text-xs font-semibold rounded-lg hover:bg-black transition">
                    Buscar
                </button>
            </form>
        </div>

        <!-- Filtro por Aroma -->
        <div class="flex flex-wrap items-center gap-2 pt-4 border-t border-stone-100">
            <span class="text-xs font-bold text-stone-500 uppercase tracking-wider mr-2">Aroma:</span>
            <a href="{{ route('products.index', array_merge(request()->query(), ['aroma' => null])) }}"
               class="px-3 py-1 rounded-md text-xs font-medium {{ !request('aroma') ? 'bg-stone-800 text-white' : 'text-stone-600 hover:bg-stone-100' }}">
               Cualquier Aroma
            </a>
            @foreach($aromas as $aroma)
                <a href="{{ route('products.index', array_merge(request()->query(), ['aroma' => $aroma])) }}"
                   class="px-3 py-1 rounded-md text-xs font-semibold flex items-center gap-1.5
                   {{ request('aroma') === $aroma ? 'ring-2 ring-[#A3542E] bg-stone-100' : 'text-stone-700 hover:bg-stone-100' }}">
                    <span class="w-2 h-2 rounded-full {{ $aroma === 'ALPHA' ? 'bg-stone-800' : ($aroma === 'HELIOS' ? 'bg-amber-500' : 'bg-purple-700') }}"></span>
                    {{ $aroma }}
                </a>
            @endforeach
        </div>
    </div>

    <!-- Grilla de Productos -->
    @if($productos->isEmpty())
        <div class="text-center py-16 bg-white rounded-xl border border-[#E8E2DA] p-8">
            <p class="text-stone-500 text-base">No se encontraron productos con los filtros seleccionados.</p>
            <a href="{{ route('products.index') }}" class="btn-outline-terracotta inline-block mt-4 px-6 py-2.5 rounded-full text-xs font-bold">
                Restablecer filtros
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($productos as $producto)
                <div class="group bg-white rounded-xl overflow-hidden border border-[#E8E2DA] flex flex-col justify-between hover:shadow-md transition">
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
                            @elseif($producto->stock_disponible <= 5)
                                <div class="absolute bottom-3 left-3">
                                    <span class="px-2.5 py-0.5 bg-amber-100 text-amber-900 border border-amber-300 text-[10px] font-bold rounded-full">
                                        ¡Solo quedan {{ $producto->stock_disponible }}!
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

                    <div class="px-6 pb-6 pt-3 border-t border-[#E8E2DA] flex items-center justify-between">
                        <div>
                            <span class="text-[11px] text-[#736B66] block">Precio</span>
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
                                Solicitar aviso →
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-12">
            {{ $productos->links() }}
        </div>
    @endif
</div>
@endsection
