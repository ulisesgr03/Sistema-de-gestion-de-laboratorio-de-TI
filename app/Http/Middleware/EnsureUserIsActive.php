<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            if (isset($user->activo) && !$user->activo) {
                Auth::logout();
                if ($request->hasSession()) {
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Su cuenta se encuentra desactivada por políticas institucionales.',
                    ], Response::HTTP_FORBIDDEN);
                }

                return redirect('/login')->withErrors([
                    'email' => 'Su cuenta se encuentra desactivada.',
                ]);
            }
        }

        return $next($request);
    }
}
