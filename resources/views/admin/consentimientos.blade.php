@extends('layouts.app')

@section('title', 'Registro Legal de Consentimientos y ARCO · SPARKLE')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-6">
        <a href="{{ route('admin.index') }}" class="text-xs font-bold text-indigo-600 hover:underline">
            ← Volver al panel de administración
        </a>
        <h1 class="text-3xl font-black text-stone-900 mt-2">Registro de Consentimientos y Derechos ARCO</h1>
        <p class="text-xs text-stone-500 mt-1">Conforme a Ley 1581 de Protección de Datos Personales y GDPR</p>
    </div>

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200 shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-stone-200 text-stone-500 font-bold uppercase tracking-wider">
                        <th class="py-3 px-4">Usuario</th>
                        <th class="py-3 px-4">Tipo de Consentimiento</th>
                        <th class="py-3 px-4">Versión del Texto</th>
                        <th class="py-3 px-4">Estado</th>
                        <th class="py-3 px-4">Fecha y Hora de Firma</th>
                        <th class="py-3 px-4 text-right">Portabilidad (ARCO)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @forelse($consentimientos as $c)
                        <tr class="hover:bg-stone-50/70">
                            <td class="py-3 px-4 font-bold text-stone-900">{{ $c->user->name }} ({{ $c->user->email }})</td>
                            <td class="py-3 px-4 font-mono capitalize">{{ str_replace('_', ' ', $c->tipo) }}</td>
                            <td class="py-3 px-4 font-semibold text-stone-500">v{{ $c->version_del_texto }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $c->aceptado ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                    {{ $c->aceptado ? 'Otorgado' : 'Revocado' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-stone-500">{{ $c->fecha->format('d/m/Y H:i:s') }}</td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('admin.usuarios.exportar', $c->user) }}" class="px-3 py-1 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-lg text-xs font-semibold inline-block">
                                    📥 Exportar JSON
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-stone-500 text-xs">No hay consentimientos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $consentimientos->links() }}
        </div>
    </div>
</div>
@endsection
