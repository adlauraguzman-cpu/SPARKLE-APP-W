<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::latest();

        if ($request->filled('rol')) {
            $query->where('rol', $request->query('rol'));
        }

        $usuarios = $query->paginate(15)->withQueryString();

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:users,email',
            'rol' => 'required|in:administrador,colaborador,cliente',
            'telefono' => 'nullable|string',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'rol' => $validated['rol'],
            'telefono' => $validated['telefono'],
            'password' => Hash::make($validated['password']),
            'autoriza_datos' => true,
            'fecha_autoriza_datos' => now(),
            'activo' => true,
        ]);

        return back()->with('success', 'Usuario creado exitosamente.');
    }

    public function cambiarRol(Request $request, int $id): RedirectResponse
    {
        $usuario = User::findOrFail($id);

        if ($usuario->id === Auth::id()) {
            return back()->with('error', 'No puedes cambiar tu propio rol.');
        }

        $validated = $request->validate([
            'rol' => 'required|in:administrador,colaborador,cliente',
        ]);

        $usuario->rol = $validated['rol'];
        $usuario->save();

        return back()->with('success', "Rol de {$usuario->name} actualizado a {$usuario->rol}.");
    }
}
