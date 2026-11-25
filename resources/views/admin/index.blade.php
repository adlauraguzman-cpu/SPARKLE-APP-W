@extends('layouts.app')

@section('title', 'Panel de Administración y Gobernanza · SPARKLE')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-100 text-indigo-900 text-xs font-bold tracking-wide uppercase mb-2">
                Rol Interno: Administrador de Plataforma
            </div>
            <h1 class="text-3xl font-black text-stone-900 tracking-tight">Gobernanza, Roles y Auditoría de Seguridad</h1>
            <p class="mt-1 text-stone-600 text-sm">Control de acceso, seguimiento de consentimientos legales (Ley 1581 / GDPR) y registro de trazabilidad.</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('admin.consentimientos') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-indigo-900 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition">
                📜 Registro Legal de Consentimientos
            </a>
        </div>
    </div>

    <!-- STRICT PRIVACY NOTICE BANNER -->
    <div class="p-4 rounded-2xl bg-indigo-50 border-2 border-indigo-200 text-xs text-indigo-950 mb-8 flex items-start gap-3 shadow-sm">
        <span class="text-xl">🛡️</span>
        <div>
            <strong class="font-bold">Principio de Privacidad por Diseño (Separación de Roles):</strong>
            <p class="mt-0.5 text-indigo-800 leading-relaxed">
                Este panel de administración administra exclusivamente identidades, roles, consentimientos legales y bitácora técnica de cambios. Por mandato de diseño y protección de datos sensibles, <strong>el administrador no tiene acceso ni visualización a los diarios personales ni a los perfiles médicos o de alergias individuales de los usuarios</strong>.
            </p>
        </div>
    </div>

    <!-- Platform High-Level Metrics -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-10">
        <div class="bg-white rounded-2xl p-4 border border-stone-200 text-center shadow-sm">
            <div class="text-[10px] uppercase font-bold text-stone-400">Usuarios Registrados</div>
            <div class="text-2xl font-black text-stone-900 mt-1">{{ $metricas['usuarios_totales'] }}</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-stone-200 text-center shadow-sm">
            <div class="text-[10px] uppercase font-bold text-stone-400">Cuentas Activas</div>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ $metricas['usuarios_activos'] }}</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-stone-200 text-center shadow-sm">
            <div class="text-[10px] uppercase font-bold text-stone-400">Fórmulas Generadas</div>
            <div class="text-2xl font-black text-stone-900 mt-1">{{ $metricas['formulas_generadas'] }}</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-stone-200 text-center shadow-sm">
            <div class="text-[10px] uppercase font-bold text-stone-400">Reglas Publicadas</div>
            <div class="text-2xl font-black text-amber-600 mt-1">{{ $metricas['reglas_publicadas'] }}</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-stone-200 text-center shadow-sm col-span-2 sm:col-span-1">
            <div class="text-[10px] uppercase font-bold text-stone-400">Consentimientos Ley 1581</div>
            <div class="text-2xl font-black text-indigo-700 mt-1">{{ $metricas['consentimientos_firmados'] }}</div>
        </div>
    </div>

    <!-- User Management Table -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200 shadow-xl mb-10">
        <h2 class="text-xl font-bold text-stone-900 mb-4">Gestión de Usuarios y Asignación de Roles</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-stone-200 text-stone-500 font-bold uppercase tracking-wider">
                        <th class="py-3 px-4">Usuario</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Rol Asignado</th>
                        <th class="py-3 px-4">Estado Cuenta</th>
                        <th class="py-3 px-4">Fecha Registro</th>
                        <th class="py-3 px-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-stone-700">
                    @foreach($usuarios as $usr)
                        <tr class="hover:bg-stone-50/70">
                            <td class="py-3 px-4 font-bold text-stone-900">{{ $usr->name }}</td>
                            <td class="py-3 px-4 font-mono">{{ $usr->email }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded-full font-bold uppercase text-[10px] {{ $usr->rol === 'administrador' ? 'bg-indigo-100 text-indigo-900' : ($usr->rol === 'especialista' ? 'bg-emerald-100 text-emerald-900' : 'bg-amber-100 text-amber-900') }}">
                                    {{ $usr->rol }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ $usr->activo ? 'text-emerald-700 bg-emerald-50' : 'text-red-700 bg-red-50' }}">
                                    {{ $usr->activo ? 'Activa' : 'Deshabilitada' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-stone-500">{{ $usr->created_at->format('d/m/Y') }}</td>
                            <td class="py-3 px-4 text-right">
                                <form action="{{ route('admin.usuarios.update', $usr) }}" method="POST" class="inline-flex items-center gap-1.5">
                                    @csrf
                                    <select name="rol" class="px-2 py-1 rounded-lg border border-stone-300 text-[11px] font-semibold">
                                        <option value="usuario" {{ $usr->rol === 'usuario' ? 'selected' : '' }}>Usuario</option>
                                        <option value="especialista" {{ $usr->rol === 'especialista' ? 'selected' : '' }}>Especialista</option>
                                        <option value="administrador" {{ $usr->rol === 'administrador' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    <select name="activo" class="px-2 py-1 rounded-lg border border-stone-300 text-[11px] font-semibold">
                                        <option value="1" {{ $usr->activo ? 'selected' : '' }}>Activo</option>
                                        <option value="0" {{ !$usr->activo ? 'selected' : '' }}>Bloqueado</option>
                                    </select>
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-stone-900 text-white text-[11px] font-bold hover:bg-stone-800 transition">
                                        Actualizar
                                    </button>
                                </form>
                                <a href="{{ route('admin.usuarios.exportar', $usr) }}" title="Descargar paquete ARCO de datos" class="ml-1 p-1 text-stone-400 hover:text-indigo-600 inline-block text-xs font-bold">
                                    📥
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $usuarios->links() }}
        </div>
    </div>

    <!-- Change Audit Log (Bitácora de Trazabilidad) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-stone-200 shadow-sm">
        <h2 class="text-xl font-bold text-stone-900 mb-2">Bitácora de Auditoría y Trazabilidad (Registros de Cambios)</h2>
        <p class="text-xs text-stone-500 mb-6">Registro inmutable de publicaciones de reglas, revisiones y modificaciones de seguridad.</p>

        <div class="space-y-3">
            @forelse($registrosAuditoria as $reg)
                <div class="p-3.5 rounded-2xl bg-stone-50 border border-stone-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="space-y-0.5">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-stone-900">{{ ucfirst($reg->accion) }}</span>
                            <span class="text-[10px] px-2 py-0.5 rounded bg-stone-200 font-mono text-stone-700">{{ $reg->tipo_entidad }} #{{ $reg->entidad_id }}</span>
                            <span class="text-stone-400 font-medium">por {{ $reg->user?->name ?? 'Sistema' }}</span>
                        </div>
                        <div class="text-stone-600">{{ $reg->comentario }}</div>
                    </div>
                    <div class="text-stone-400 font-mono text-[11px] whitespace-nowrap">
                        {{ $reg->fecha->format('d/m/Y H:i') }}
                    </div>
                </div>
            @empty
                <div class="text-xs text-stone-500">No hay registros de auditoría aún.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
