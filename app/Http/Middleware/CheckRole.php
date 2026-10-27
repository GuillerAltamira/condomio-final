<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Debes iniciar sesión para acceder al sistema.');
        }

        $user = Auth::user();

        if (!$user->activo) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Tu cuenta se encuentra inactiva. Comunícate con la administración.');
        }

        if (!empty($roles) && !in_array($user->rol, $roles)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'No tienes autorización para realizar esta acción.'], 403);
            }
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para acceder a esa sección.');
        }

        return $next($request);
    }
}
