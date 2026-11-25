@extends('layouts.app')

@section('title', 'Iniciar Sesión · SPARKLE Mi Espacio Vitamina')

@section('content')
<div class="max-w-md mx-auto px-4 sm:px-6 py-16">
    <div class="bg-white rounded-3xl p-8 border border-stone-200 shadow-xl">
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-sparkle-600 to-amber-400 flex items-center justify-center text-white mx-auto mb-3 shadow-md">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
            </div>
            <h1 class="text-2xl font-black text-stone-900">Iniciar Sesión</h1>
            <p class="text-xs text-stone-500 mt-1">Accede a tus fórmulas, rutinas y diario íntimo</p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Correo Electrónico</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-sparkle-500 focus:border-sparkle-500 text-sm" placeholder="tu@email.com">
                @error('email')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Contraseña</label>
                <input type="password" name="password" id="password" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-sparkle-500 focus:border-sparkle-500 text-sm" placeholder="••••••••">
                @error('password')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 text-stone-600">
                    <input type="checkbox" name="remember" class="rounded text-sparkle-600 focus:ring-sparkle-500">
                    Recordar mi sesión
                </label>
                <span class="text-sparkle-600 hover:underline cursor-pointer">¿Olvidaste tu clave?</span>
            </div>

            <button type="submit" class="w-full py-3 px-4 rounded-xl text-sm font-bold text-white bg-sparkle-600 hover:bg-sparkle-700 shadow-md transition">
                Entrar a Mi Espacio
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-stone-100 text-center text-xs text-stone-600">
            ¿Aún no tienes cuenta?
            <a href="{{ route('register') }}" class="font-bold text-sparkle-600 hover:underline">Regístrate gratis</a>
            o
            <a href="{{ route('configurador.index') }}" class="font-bold text-stone-700 hover:underline">Prueba como invitado</a>
        </div>
    </div>
</div>
@endsection
