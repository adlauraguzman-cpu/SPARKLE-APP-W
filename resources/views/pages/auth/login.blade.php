@extends('layouts.app')

@section('title', 'Ingresar a mi Cuenta · SPARKLE')

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="bg-white rounded-2xl border border-[#E8E2DA] p-8 shadow-sm space-y-6">
        <div class="text-center">
            <span class="font-serif text-3xl font-bold tracking-widest text-stone-900">SPARKLE</span>
            <h1 class="font-serif text-2xl font-bold text-stone-900 mt-2">Iniciar Sesión</h1>
            <p class="text-xs text-stone-500 mt-1">Ingresa para acumular puntos, ver tus pedidos y canjear beneficios.</p>
        </div>

        @if(session('info'))
            <div class="p-3 bg-amber-50 border border-amber-200 text-amber-900 text-xs rounded-lg">
                {{ session('info') }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            @if(request('redirect'))
                <input type="hidden" name="redirect" value="{{ request('redirect') }}">
            @endif

            <div>
                <label for="email" class="block text-xs font-semibold text-stone-700 mb-1">Correo Electrónico</label>
                <input type="email" name="email" id="email" required value="{{ old('email') }}"
                       class="w-full px-3.5 py-2.5 text-sm border {{ $errors->has('email') ? 'border-red-500' : 'border-stone-300' }} rounded-xl focus:border-[#A3542E]"
                       placeholder="tu@correo.com">
                @error('email')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-stone-700 mb-1">Contraseña</label>
                <input type="password" name="password" id="password" required
                       class="w-full px-3.5 py-2.5 text-sm border border-stone-300 rounded-xl focus:border-[#A3542E]"
                       placeholder="••••••••">
                @error('password')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 text-stone-600">
                    <input type="checkbox" name="remember" class="rounded border-stone-300 text-[#A3542E]">
                    <span>Recordar sesión</span>
                </label>
                <span class="text-stone-400">Protección SSL activa</span>
            </div>

            <button type="submit" class="btn-terracotta w-full py-3.5 rounded-full font-bold text-sm tracking-wide shadow">
                Ingresar a mi Cuenta
            </button>
        </form>

        <div class="text-center pt-2 text-xs text-stone-500">
            ¿Aún no tienes cuenta? 
            <a href="{{ route('register', ['redirect' => request('redirect')]) }}" class="text-[#A3542E] font-bold hover:underline">
                Crear una cuenta aquí
            </a>
        </div>

        <!-- Acceso Rápido para Demostración -->
        <div class="pt-6 border-t border-stone-100 text-center space-y-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-stone-400 block">Acceso Rápido Demo (1 Clic)</span>
            <div class="flex flex-col gap-1.5">
                <a href="{{ route('demo.login', 'cliente') }}" class="py-2 px-3 bg-stone-50 hover:bg-stone-100 border border-stone-200 text-stone-800 text-xs rounded-lg font-medium text-left flex justify-between">
                    <span>👤 María Torres</span>
                    <span class="text-stone-400 font-normal">Cliente registrado (150 pts)</span>
                </a>
                <a href="{{ route('demo.login', 'colaborador') }}" class="py-2 px-3 bg-amber-50 hover:bg-amber-100 border border-amber-200 text-amber-900 text-xs rounded-lg font-medium text-left flex justify-between">
                    <span>📦 Carlos Operaciones</span>
                    <span class="text-amber-700 font-normal">Colaborador Operativo</span>
                </a>
                <a href="{{ route('demo.login', 'admin') }}" class="py-2 px-3 bg-stone-900 hover:bg-black text-white text-xs rounded-lg font-medium text-left flex justify-between">
                    <span>👑 Laura Guzmán</span>
                    <span class="text-stone-300 font-normal">Administrador (Dueña)</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
