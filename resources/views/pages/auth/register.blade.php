@extends('layouts.app')

@section('title', 'Crear Cuenta · SPARKLE')

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="bg-white rounded-2xl border border-[#E8E2DA] p-8 shadow-sm space-y-6">
        <div class="text-center">
            <span class="font-serif text-3xl font-bold tracking-widest text-stone-900">SPARKLE</span>
            <h1 class="font-serif text-2xl font-bold text-stone-900 mt-2">Crear Cuenta</h1>
            <p class="text-xs text-stone-500 mt-1">Regístrate para recibir 50 puntos de bienvenida y acumular beneficios.</p>
        </div>

        @if($errors->any())
            <div class="p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg space-y-1">
                @foreach($errors->all() as $error)
                    <div>• {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf
            @if(request('redirect'))
                <input type="hidden" name="redirect" value="{{ request('redirect') }}">
            @endif

            <!-- Nombre -->
            <div>
                <label for="name" class="block text-xs font-semibold text-stone-700 mb-1">Nombre completo</label>
                <input type="text" name="name" id="name" required value="{{ old('name') }}"
                       class="w-full px-3.5 py-2.5 text-sm border border-stone-300 rounded-xl focus:border-[#A3542E]"
                       placeholder="María Torres">
            </div>

            <!-- Correo -->
            <div>
                <label for="email" class="block text-xs font-semibold text-stone-700 mb-1">Correo Electrónico</label>
                <input type="email" name="email" id="email" required value="{{ old('email') }}"
                       class="w-full px-3.5 py-2.5 text-sm border border-stone-300 rounded-xl focus:border-[#A3542E]"
                       placeholder="tu@correo.com">
            </div>

            <!-- Teléfono / WhatsApp -->
            <div>
                <label for="telefono" class="block text-xs font-semibold text-stone-700 mb-1">Teléfono / WhatsApp (10 dígitos)</label>
                <input type="tel" name="telefono" id="telefono" required maxlength="10" value="{{ old('telefono') }}"
                       class="w-full px-3.5 py-2.5 text-sm border border-stone-300 rounded-xl focus:border-[#A3542E]"
                       placeholder="3105550142">
                <span class="text-[10px] text-stone-400">Usado para enviarte la guía de despacho y confirmaciones</span>
            </div>

            <!-- Contraseña -->
            <div>
                <label for="password" class="block text-xs font-semibold text-stone-700 mb-1">Contraseña</label>
                <input type="password" name="password" id="password" required
                       class="w-full px-3.5 py-2.5 text-sm border border-stone-300 rounded-xl focus:border-[#A3542E]"
                       placeholder="Mínimo 6 caracteres">
            </div>

            <!-- Confirmar Contraseña -->
            <div>
                <label for="password_confirmation" class="block text-xs font-semibold text-stone-700 mb-1">Confirmar Contraseña</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                       class="w-full px-3.5 py-2.5 text-sm border border-stone-300 rounded-xl focus:border-[#A3542E]"
                       placeholder="Repite tu contraseña">
            </div>

            <!-- Casilla Autorizo Uso de Datos (Página 3 & 7 Requisito Fundamental) -->
            <div class="pt-2">
                <div class="flex items-start gap-2.5">
                    <input type="checkbox" name="autoriza_datos" id="autoriza_datos" required value="1"
                           {{ old('autoriza_datos') ? 'checked' : '' }}
                           class="mt-1 rounded border-stone-300 text-[#A3542E] focus:ring-0">
                    <label for="autoriza_datos" class="text-xs text-stone-600 leading-snug">
                        Autorizo el tratamiento de mis datos personales para la gestión de pedidos, acumulación de puntos y comunicaciones de SPARKLE.
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-terracotta w-full py-3.5 rounded-full font-bold text-sm tracking-wide shadow">
                Crear mi Cuenta
            </button>
        </form>

        <div class="text-center pt-2 text-xs text-stone-500">
            ¿Ya tienes una cuenta registrada? 
            <a href="{{ route('login', ['redirect' => request('redirect')]) }}" class="text-[#A3542E] font-bold hover:underline">
                Inicia sesión aquí
            </a>
        </div>
    </div>
</div>
@endsection
