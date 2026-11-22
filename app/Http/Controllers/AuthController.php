<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        protected CartService $cartService
    ) {}

    public function showLogin(Request $request): View
    {
        $redirect = $request->query('redirect');

        return view('pages.auth.login', compact('redirect'));
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Ingresa tu correo registrado.',
            'password.required' => 'Ingresa tu contraseña.',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Migrar ítems del carrito de invitado a la cuenta del usuario
            $this->cartService->migrateGuestCartToUser($user->id);

            if ($request->filled('redirect') && $request->input('redirect') === 'checkout') {
                return redirect()->route('checkout.index');
            }

            if ($user->isAdmin() || $user->isColaborador()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended(route('home'))->with('success', "¡Bienvenida de nuevo, {$user->name}!");
        }

        return back()->withInput($request->only('email'))->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ]);
    }

    public function showRegister(Request $request): View
    {
        $redirect = $request->query('redirect');

        return view('pages.auth.register', compact('redirect'));
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|string|email|max:180|unique:users',
            'telefono' => 'required|regex:/^[0-9]{10}$/',
            'password' => 'required|string|min:6|confirmed',
            'autoriza_datos' => 'accepted',
        ], [
            'name.required' => 'Indica tu nombre completo.',
            'email.required' => 'Tu correo electrónico es obligatorio.',
            'email.unique' => 'Este correo ya tiene una cuenta registrada.',
            'telefono.required' => 'El teléfono / WhatsApp es obligatorio para el seguimiento de tu pedido.',
            'telefono.regex' => 'El teléfono debe contener exactamente 10 dígitos numéricos.',
            'password.required' => 'Crea una contraseña segura.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'autoriza_datos.accepted' => 'Debes autorizar el tratamiento de datos para crear tu cuenta y acumular puntos.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'telefono' => $validated['telefono'],
            'password' => Hash::make($validated['password']),
            'rol' => 'cliente',
            'autoriza_datos' => true,
            'fecha_autoriza_datos' => now(),
            'puntos_disponibles' => 50, // Bono de bienvenida en puntos
            'activo' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        $this->cartService->migrateGuestCartToUser($user->id);

        if ($request->filled('redirect') && $request->input('redirect') === 'checkout') {
            return redirect()->route('checkout.index');
        }

        return redirect()->route('home')->with('success', '¡Cuenta creada con éxito! Te obsequiamos 50 puntos de bienvenida para tus compras.');
    }

    /**
     * Acceso rápido para demostración y evaluación de roles.
     */
    public function quickLogin(string $rol): RedirectResponse
    {
        $email = match ($rol) {
            'admin' => 'admin@sparkle.com',
            'colaborador' => 'operaciones@sparkle.com',
            'cliente' => 'cliente@sparkle.com',
            default => null,
        };

        if (! $email) {
            return back()->with('error', 'Rol no válido.');
        }

        $user = User::where('email', $email)->first();
        if ($user) {
            Auth::login($user);
            request()->session()->regenerate();

            if ($user->isAdmin() || $user->isColaborador()) {
                return redirect()->route('admin.dashboard')->with('success', "Ingresaste como {$user->name} ({$user->rol})");
            }

            return redirect()->route('home')->with('success', "Ingresaste como {$user->name} ({$user->rol})");
        }

        return back()->with('error', 'Usuario de demostración no encontrado.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'Has cerrado tu sesión.');
    }
}
