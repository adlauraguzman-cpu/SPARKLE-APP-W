<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a este módulo.');
        }

        if (! in_array($user->rol, $roles, true)) {
            abort(403, 'Acceso denegado. No tienes permisos para este módulo según las reglas de roles de SPARKLE.');
        }

        return $next($request);
    }
}
