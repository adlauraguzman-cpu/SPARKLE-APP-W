@extends('layouts.app')

@section('title', 'Crear Cuenta y Consentimiento Informado · SPARKLE')

@section('content')
<div class="max-w-xl mx-auto px-4 sm:px-6 py-12">
    <div class="bg-white rounded-3xl p-8 sm:p-10 border border-stone-200 shadow-xl">
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-sparkle-600 to-amber-400 flex items-center justify-center text-white mx-auto mb-3 shadow-md">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
            </div>
            <h1 class="text-2xl font-black text-stone-900">Crear Cuenta en SPARKLE</h1>
            <p class="text-xs text-stone-500 mt-1">Tu perfil privado para calibrar y registrar tus experiencias olfativas</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label for="name" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Nombre Completo</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-sparkle-500 focus:border-sparkle-500 text-sm" placeholder="Ej. María Torres">
                @error('name')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Correo Electrónico</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-sparkle-500 focus:border-sparkle-500 text-sm" placeholder="maria@ejemplo.com">
                @error('email')
                    <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Contraseña</label>
                    <input type="password" name="password" id="password" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-sparkle-500 focus:border-sparkle-500 text-sm" placeholder="Mínimo 8 caracteres">
                    @error('password')
                        <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 focus:ring-2 focus:ring-sparkle-500 focus:border-sparkle-500 text-sm" placeholder="Repite la clave">
                </div>
            </div>

            <!-- Bloque Legal: Consentimientos Obligatorios (Ley 1581 / GDPR) -->
            <div class="bg-amber-50/70 rounded-2xl p-4 border border-amber-200/80 space-y-3 mt-4">
                <div class="text-xs font-bold text-amber-900 flex items-center gap-1.5 uppercase tracking-wide">
                    <span>📜</span> Consentimientos Legales Informados (v2.0)
                </div>

                <div class="space-y-2.5 text-xs text-stone-700">
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" name="consent_terminos" value="1" {{ old('consent_terminos') ? 'checked' : '' }} required class="mt-0.5 rounded text-sparkle-600 focus:ring-sparkle-500">
                        <span>Acepto los <strong class="text-stone-900">Términos del Servicio de SPARKLE</strong> y reconozco que la plataforma proporciona sugerencias aromáticas de bienestar sin constituir diagnóstico médico.</span>
                    </label>
                    @error('consent_terminos')
                        <span class="text-xs text-red-600 block pl-6">{{ $message }}</span>
                    @enderror

                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" name="consent_datos_personales" value="1" {{ old('consent_datos_personales') ? 'checked' : '' }} required class="mt-0.5 rounded text-sparkle-600 focus:ring-sparkle-500">
                        <span>Autorizo el tratamiento de mis <strong class="text-stone-900">Datos Personales</strong> conforme a la Ley 1581 / GDPR para la gestión de mi cuenta y rutinas.</span>
                    </label>
                    @error('consent_datos_personales')
                        <span class="text-xs text-red-600 block pl-6">{{ $message }}</span>
                    @enderror

                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" name="consent_bienestar" value="1" {{ old('consent_bienestar') ? 'checked' : '' }} required class="mt-0.5 rounded text-sparkle-600 focus:ring-sparkle-500">
                        <span>Consiento de manera expresa el tratamiento de mis <strong class="text-stone-900">Datos Sensibles de Bienestar</strong> (alergias reportadas, sensibilidad y diario de ánimo) exclusivamente para calibrar la seguridad de mis fórmulas.</span>
                    </label>
                    @error('consent_bienestar')
                        <span class="text-xs text-red-600 block pl-6">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 rounded-xl text-sm font-bold text-white bg-sparkle-600 hover:bg-sparkle-700 shadow-md transition">
                Completar Registro y Calibrar Mi Perfil →
            </button>
        </form>

        <div class="mt-6 pt-6 border-t border-stone-100 text-center text-xs text-stone-600">
            ¿Ya tienes una cuenta registrada?
            <a href="{{ route('login') }}" class="font-bold text-sparkle-600 hover:underline">Inicia sesión</a>
        </div>
    </div>
</div>
@endsection
