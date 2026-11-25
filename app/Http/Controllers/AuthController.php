<?php

namespace App\Http\Controllers;

use App\Models\Consentimiento;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (! $user->activo) {
                Auth::logout();

                return back()->withErrors(['email' => 'Tu cuenta ha sido deshabilitada por administración.']);
            }

            return match ($user->rol) {
                'especialista' => redirect()->intended(route('especialista.index')),
                'administrador' => redirect()->intended(route('admin.index')),
                default => redirect()->intended(route('configurador.index')),
            };
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'consent_terminos' => ['required', 'accepted'],
            'consent_datos_personales' => ['required', 'accepted'],
            'consent_bienestar' => ['required', 'accepted'],
        ], [
            'consent_terminos.required' => 'Debes aceptar los Términos de Servicio.',
            'consent_datos_personales.required' => 'Debes autorizar el tratamiento de datos personales (Ley 1581 / GDPR).',
            'consent_bienestar.required' => 'Debes consentir el tratamiento de datos de bienestar y perfil sensorial.',
            'consent_terminos.accepted' => 'Debes aceptar los Términos de Servicio.',
            'consent_datos_personales.accepted' => 'Debes autorizar el tratamiento de datos personales (Ley 1581 / GDPR).',
            'consent_bienestar.accepted' => 'Debes consentir el tratamiento de datos de bienestar y perfil sensorial.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'rol' => 'usuario',
            'zona_horaria' => 'America/Bogota',
            'activo' => true,
            'fecha_registro' => now(),
        ]);

        // Registrar consentimientos legales
        Consentimiento::create([
            'user_id' => $user->id,
            'tipo' => 'terminos',
            'aceptado' => true,
            'version_del_texto' => '2.0',
            'fecha' => now(),
        ]);
        Consentimiento::create([
            'user_id' => $user->id,
            'tipo' => 'datos_personales',
            'aceptado' => true,
            'version_del_texto' => '2.0',
            'fecha' => now(),
        ]);
        Consentimiento::create([
            'user_id' => $user->id,
            'tipo' => 'datos_bienestar',
            'aceptado' => true,
            'version_del_texto' => '2.0',
            'fecha' => now(),
        ]);

        Auth::login($user);

        return redirect()->route('onboarding.index')->with('success', '¡Cuenta creada! Configuremos tu perfil sensorial para calibrar tus fórmulas.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'Has cerrado sesión correctamente.');
    }

    /**
     * Quick demo login for evaluation.
     */
    public function quickLogin(string $role): RedirectResponse
    {
        $email = match ($role) {
            'usuario' => 'usuario@sparkle.com',
            'especialista' => 'especialista1@sparkle.com',
            'revisor' => 'especialista2@sparkle.com',
            'admin' => 'admin@sparkle.com',
            default => 'usuario@sparkle.com',
        };

        $user = User::where('email', $email)->first();
        if ($user) {
            Auth::login($user);

            return match ($user->rol) {
                'especialista' => redirect()->route('especialista.index')->with('success', "Accediste como {$user->name} ({$user->rol})"),
                'administrador' => redirect()->route('admin.index')->with('success', "Accediste como {$user->name} ({$user->rol})"),
                default => redirect()->route('configurador.index')->with('success', "Accediste como {$user->name} ({$user->rol})"),
            };
        }

        return redirect()->route('login')->with('error', 'Usuario demo no encontrado.');
    }
}
