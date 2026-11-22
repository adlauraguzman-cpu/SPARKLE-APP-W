@extends('layouts.app')

@section('title', 'Guía Interactiva de Aromas · SPARKLE')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center space-y-4 mb-12">
        <span class="inline-block px-3 py-1 bg-[#F7EBE4] text-[#A3542E] text-xs font-bold uppercase tracking-widest rounded-full">
            Test Sensorial de 1 Minuto
        </span>
        <h1 class="font-serif text-4xl sm:text-5xl font-bold text-[#231F20]">Encuentra tu Aroma Signature</h1>
        <p class="text-stone-600 text-sm sm:text-base max-w-xl mx-auto">
            Cada espacio tiene un propósito y cada aroma despierta una emoción diferente. Responde estas tres breves preguntas.
        </p>
    </div>

    <form action="{{ route('guia.resultado') }}" method="POST" class="bg-white p-8 sm:p-12 rounded-2xl border border-[#E8E2DA] shadow-sm space-y-10">
        @csrf

        <!-- Pregunta 1 -->
        <div class="space-y-4">
            <span class="text-xs font-bold text-[#A3542E] uppercase tracking-wider block">Paso 1 de 3</span>
            <h2 class="font-serif text-2xl font-bold text-stone-900">¿Para qué espacio principal buscas ambientación?</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <label class="border-2 border-stone-200 rounded-xl p-5 cursor-pointer hover:border-[#A3542E] transition flex flex-col justify-between has-[:checked]:border-[#A3542E] has-[:checked]:bg-[#FAF8F5]">
                    <input type="radio" name="espacio" value="sala" class="sr-only" checked>
                    <div>
                        <div class="text-2xl mb-2">🛋️</div>
                        <strong class="text-sm block text-stone-900">Sala de estar / Comedor</strong>
                        <p class="text-xs text-stone-500 mt-1">Lugar de reunión social, cálido y acogedor.</p>
                    </div>
                </label>

                <label class="border-2 border-stone-200 rounded-xl p-5 cursor-pointer hover:border-[#A3542E] transition flex flex-col justify-between has-[:checked]:border-[#A3542E] has-[:checked]:bg-[#FAF8F5]">
                    <input type="radio" name="espacio" value="trabajo" class="sr-only">
                    <div>
                        <div class="text-2xl mb-2">☀️</div>
                        <strong class="text-sm block text-stone-900">Estudio / Oficina</strong>
                        <p class="text-xs text-stone-500 mt-1">Ambiente de enfoque diurno, claridad mental y trabajo.</p>
                    </div>
                </label>

                <label class="border-2 border-stone-200 rounded-xl p-5 cursor-pointer hover:border-[#A3542E] transition flex flex-col justify-between has-[:checked]:border-[#A3542E] has-[:checked]:bg-[#FAF8F5]">
                    <input type="radio" name="espacio" value="dormitorio" class="sr-only">
                    <div>
                        <div class="text-2xl mb-2">🌙</div>
                        <strong class="text-sm block text-stone-900">Dormitorio / Rincón de Paz</strong>
                        <p class="text-xs text-stone-500 mt-1">Espacio de descanso, desconexión y lectura.</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Pregunta 2 -->
        <div class="space-y-4 pt-6 border-t border-stone-100">
            <span class="text-xs font-bold text-[#A3542E] uppercase tracking-wider block">Paso 2 de 3</span>
            <h2 class="font-serif text-2xl font-bold text-stone-900">¿Qué sensación deseas activar al entrar a la habitación?</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <label class="border-2 border-stone-200 rounded-xl p-5 cursor-pointer hover:border-[#A3542E] transition flex flex-col justify-between has-[:checked]:border-[#A3542E] has-[:checked]:bg-[#FAF8F5]">
                    <input type="radio" name="sensacion" value="calma" class="sr-only" checked>
                    <div>
                        <div class="text-2xl mb-2">🪵</div>
                        <strong class="text-sm block text-stone-900">Calidez y Sofisticación</strong>
                        <p class="text-xs text-stone-500 mt-1">Notas amaderadas que envuelven y dan presencia.</p>
                    </div>
                </label>

                <label class="border-2 border-stone-200 rounded-xl p-5 cursor-pointer hover:border-[#A3542E] transition flex flex-col justify-between has-[:checked]:border-[#A3542E] has-[:checked]:bg-[#FAF8F5]">
                    <input type="radio" name="sensacion" value="energia" class="sr-only">
                    <div>
                        <div class="text-2xl mb-2">🍊</div>
                        <strong class="text-sm block text-stone-900">Frescura y Luz Solar</strong>
                        <p class="text-xs text-stone-500 mt-1">Notas cítricas que despiertan el buen humor y la vitalidad.</p>
                    </div>
                </label>

                <label class="border-2 border-stone-200 rounded-xl p-5 cursor-pointer hover:border-[#A3542E] transition flex flex-col justify-between has-[:checked]:border-[#A3542E] has-[:checked]:bg-[#FAF8F5]">
                    <input type="radio" name="sensacion" value="serenidad" class="sr-only">
                    <div>
                        <div class="text-2xl mb-2">🌸</div>
                        <strong class="text-sm block text-stone-900">Serenidad Floral Suave</strong>
                        <p class="text-xs text-stone-500 mt-1">Pétalos delicados y sensación de ligereza absoluta.</p>
                    </div>
                </label>
            </div>
        </div>

        <div class="text-center pt-6">
            <button type="submit" class="btn-terracotta px-10 py-4 rounded-full font-bold text-sm tracking-wider shadow-md">
                Ver Mi Recomendación Personalizada →
            </button>
        </div>
    </form>
</div>
@endsection
