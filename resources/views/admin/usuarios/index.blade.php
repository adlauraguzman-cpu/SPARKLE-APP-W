@extends('layouts.admin')

@section('page_title', 'Usuarios y Gestión de Roles')

@section('content')
<div class="space-y-8">

    <!-- Crear Usuario Interno / Colaborador -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="bg-white p-6 rounded-2xl border border-stone-200 shadow-sm space-y-4">
            <h2 class="text-base font-bold text-stone-900">Registrar Usuario Interno</h2>
            <p class="text-xs text-stone-500">Crea credenciales para administradores o colaboradores de operación (Fase 2)</p>

            <form action="{{ route('admin.usuarios.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Nombre Completo</label>
                    <input type="text" name="name" required placeholder="Ej: Camilo Operador"
                           class="w-full px-3 py-2 text-xs border border-stone-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Correo Electrónico</label>
                    <input type="email" name="email" required placeholder="camilo@sparkle.com"
                           class="w-full px-3 py-2 text-xs border border-stone-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Rol a Asignar</label>
                    <select name="rol" required class="w-full px-3 py-2 text-xs border border-stone-300 rounded-lg bg-white">
                        <option value="colaborador">Colaborador de operación (Fase 2)</option>
                        <option value="administrador">Administrador (Dueña de la marca)</option>
                        <option value="cliente">Cliente registrado</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Teléfono</label>
                    <input type="text" name="telefono" placeholder="3001234567" class="w-full px-3 py-2 text-xs border border-stone-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-stone-700 mb-1">Contraseña</label>
                    <input type="password" name="password" required placeholder="Mínimo 6 caracteres" class="w-full px-3 py-2 text-xs border border-stone-300 rounded-lg">
                </div>

                <button type="submit" class="btn-terracotta w-full py-2.5 rounded-lg font-bold text-xs shadow mt-2">
                    Crear Usuario
                </button>
            </form>
        </div>

        <!-- Tabla de Usuarios Registrados y Roles -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-stone-200 overflow-hidden shadow-sm">
            <div class="p-6 border-b border-stone-100 flex justify-between items-center">
                <h3 class="text-base font-bold text-stone-900">Usuarios Registrados en el Sistema</h3>
                <span class="text-xs text-stone-500">Total: {{ $usuarios->total() }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-stone-700">
                    <thead class="bg-stone-50 text-stone-500 uppercase tracking-wider text-[10px] border-b border-stone-200">
                        <tr>
                            <th class="p-4">Nombre / Correo</th>
                            <th class="p-4">Rol Asignado</th>
                            <th class="p-4 text-center">Puntos</th>
                            <th class="p-4 text-right">Modificar Rol</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($usuarios as $usr)
                            <tr class="hover:bg-stone-50 transition">
                                <td class="p-4">
                                    <span class="font-bold text-stone-900 block">{{ $usr->name }}</span>
                                    <span class="text-stone-400 text-[11px]">{{ $usr->email }}</span>
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider
                                        {{ $usr->rol === 'administrador' ? 'bg-amber-100 text-amber-900 border border-amber-300' :
                                           ($usr->rol === 'colaborador' ? 'bg-blue-100 text-blue-900 border border-blue-300' : 'bg-stone-100 text-stone-700') }}">
                                        {{ $usr->rol }}
                                    </span>
                                </td>
                                <td class="p-4 text-center font-bold text-stone-700">
                                    {{ $usr->puntos_disponibles }} pts
                                </td>
                                <td class="p-4 text-right">
                                    @if($usr->id !== Auth::id())
                                        <form action="{{ route('admin.usuarios.rol', $usr->id) }}" method="POST" class="inline-flex items-center gap-1.5">
                                            @csrf
                                            <select name="rol" class="px-2 py-1 text-[11px] border border-stone-300 rounded bg-white">
                                                <option value="cliente" {{ $usr->rol === 'cliente' ? 'selected' : '' }}>Cliente</option>
                                                <option value="colaborador" {{ $usr->rol === 'colaborador' ? 'selected' : '' }}>Colaborador</option>
                                                <option value="administrador" {{ $usr->rol === 'administrador' ? 'selected' : '' }}>Administrador</option>
                                            </select>
                                            <button type="submit" class="px-2 py-1 bg-stone-800 text-white rounded text-[11px] font-semibold hover:bg-black">
                                                Guardar
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[11px] text-stone-400 italic">Tu usuario</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-stone-100">
                {{ $usuarios->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
