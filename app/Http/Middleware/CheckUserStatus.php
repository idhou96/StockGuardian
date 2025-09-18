<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Vérifier si l'utilisateur est actif
            if (isset($user->is_active) && !$user->is_active) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect('/login')
                    ->with('error', 'Votre compte a été désactivé.');
            }

            // Vérifier si l'utilisateur est bloqué
            if (isset($user->is_blocked) && $user->is_blocked) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect('/login')
                    ->with('error', 'Votre compte a été bloqué.');
            }
        }

        return $next($request);
    }
}