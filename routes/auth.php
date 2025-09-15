<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes d'Authentification Personnalisées pour StockGuardian
|--------------------------------------------------------------------------
| Ces routes gèrent l'authentification avec redirection automatique
| par rôle et logging des activités de connexion.
*/

Route::middleware('guest')->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | INSCRIPTION (Désactivée par défaut)
    |--------------------------------------------------------------------------
    | L'inscription est généralement désactivée pour les applications de gestion.
    | Seuls les administrateurs peuvent créer des comptes utilisateurs.
    */
    
    // Route::get('register', [RegisteredUserController::class, 'create'])
    //     ->name('register');
    // Route::post('register', [RegisteredUserController::class, 'store']);

    /*
    |--------------------------------------------------------------------------
    | CONNEXION avec Redirection par Rôle
    |--------------------------------------------------------------------------
    */
    
    // Afficher le formulaire de connexion
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    // Traiter la connexion avec redirection automatique par rôle
    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware(['throttle:login'])
        ->name('login.store');

    /*
    |--------------------------------------------------------------------------
    | RÉINITIALISATION DE MOT DE PASSE
    |--------------------------------------------------------------------------
    */
    
    // Demander un lien de réinitialisation
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    // Envoyer le lien de réinitialisation
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware(['throttle:6,1'])
        ->name('password.email');

    // Afficher le formulaire de réinitialisation
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    // Traiter la réinitialisation
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->middleware(['throttle:6,1'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | VÉRIFICATION D'EMAIL
    |--------------------------------------------------------------------------
    */
    
    // Prompt pour vérification email
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    // Traiter la vérification d'email
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    // Renvoyer l'email de vérification
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    /*
    |--------------------------------------------------------------------------
    | CONFIRMATION DE MOT DE PASSE
    |--------------------------------------------------------------------------
    */
    
    // Demander confirmation du mot de passe
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    // Traiter la confirmation
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store'])
        ->middleware(['throttle:6,1']);

    /*
    |--------------------------------------------------------------------------
    | MODIFICATION DE MOT DE PASSE
    |--------------------------------------------------------------------------
    */
    
    // Mettre à jour le mot de passe
    Route::put('password', [PasswordController::class, 'update'])
        ->name('password.update');

    /*
    |--------------------------------------------------------------------------
    | DÉCONNEXION avec Logging
    |--------------------------------------------------------------------------
    */
    
    // Déconnexion avec enregistrement de l'activité
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

/*
|--------------------------------------------------------------------------
| ROUTES D'AUTHENTIFICATION SUPPLÉMENTAIRES POUR STOCKGUARDIAN
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | Vérification de Session Active
    |--------------------------------------------------------------------------
    */
    
    // API pour vérifier si la session est toujours active
    Route::get('auth/check-session', function () {
        return response()->json([
            'authenticated' => auth()->check(),
            'user' => auth()->user() ? [
                'id' => auth()->user()->id,
                'name' => auth()->user()->name,
                'role' => auth()->user()->role,
                'last_activity' => now()->toISOString(),
            ] : null,
        ]);
    })->name('auth.check-session');

    /*
    |--------------------------------------------------------------------------
    | Changement de Rôle (Pour les administrateurs)
    |--------------------------------------------------------------------------
    */
    
    // Permettre aux administrateurs de "se faire passer" pour un autre utilisateur
    Route::post('auth/impersonate/{user}', function ($userId) {
        if (!auth()->user()->hasRole('administrateur')) {
            abort(403, 'Action non autorisée');
        }

        $user = \App\Models\User::findOrFail($userId);
        
        // Sauvegarder l'ID de l'administrateur original
        session(['impersonating' => [
            'original_user_id' => auth()->id(),
            'impersonated_user_id' => $user->id,
            'started_at' => now(),
        ]]);

        // Se connecter en tant que l'utilisateur ciblé
        auth()->loginUsingId($user->id);

        // Enregistrer l'activité
        \App\Models\ActivityLog::create([
            'user_id' => session('impersonating.original_user_id'),
            'action' => 'impersonate_start',
            'model_type' => \App\Models\User::class,
            'model_id' => $user->id,
            'description' => "Début d'impersonification de {$user->name}",
            'properties' => [
                'impersonated_user' => $user->only(['id', 'name', 'email', 'role']),
                'started_at' => now(),
            ],
        ]);

        $redirectUrl = \App\Providers\RouteServiceProvider::redirectTo();
        
        return redirect($redirectUrl)
            ->with('warning', "Vous êtes maintenant connecté en tant que {$user->name}. Cliquez ici pour revenir à votre compte.");
            
    })->middleware(['role:administrateur'])->name('auth.impersonate');

    // Arrêter l'impersonification
    Route::post('auth/stop-impersonate', function () {
        $impersonating = session('impersonating');
        
        if (!$impersonating) {
            return redirect()->route('dashboard')->with('error', 'Aucune impersonification en cours.');
        }

        // Enregistrer l'activité
        \App\Models\ActivityLog::create([
            'user_id' => $impersonating['original_user_id'],
            'action' => 'impersonate_stop',
            'model_type' => \App\Models\User::class,
            'model_id' => $impersonating['impersonated_user_id'],
            'description' => "Fin d'impersonification",
            'properties' => [
                'duration_minutes' => now()->diffInMinutes($impersonating['started_at']),
                'ended_at' => now(),
            ],
        ]);

        // Revenir au compte administrateur original
        auth()->loginUsingId($impersonating['original_user_id']);
        
        // Supprimer les données d'impersonification
        session()->forget('impersonating');

        return redirect()->route('dashboard.admin')
            ->with('success', 'Vous êtes revenu à votre compte administrateur.');
            
    })->name('auth.stop-impersonate');

    /*
    |--------------------------------------------------------------------------
    | Déconnexion Forcée (Pour administrateurs)
    |--------------------------------------------------------------------------
    */
    
    // Forcer la déconnexion d'un utilisateur
    Route::post('auth/force-logout/{user}', function ($userId) {
        if (!auth()->user()->hasRole('administrateur')) {
            abort(403, 'Action non autorisée');
        }

        $user = \App\Models\User::findOrFail($userId);
        
        // Invalider toutes les sessions de l'utilisateur
        \DB::table('sessions')->where('user_id', $user->id)->delete();

        // Enregistrer l'activité
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'force_logout',
            'model_type' => \App\Models\User::class,
            'model_id' => $user->id,
            'description' => "Déconnexion forcée de {$user->name}",
            'properties' => [
                'forced_by' => auth()->user()->name,
                'forced_at' => now(),
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => "L'utilisateur {$user->name} a été déconnecté de force.",
        ]);
        
    })->middleware(['role:administrateur'])->name('auth.force-logout');

    /*
    |--------------------------------------------------------------------------
    | Informations de Session Extended
    |--------------------------------------------------------------------------
    */
    
    // Obtenir des informations étendues sur la session courante
    Route::get('auth/session-info', function () {
        $user = auth()->user();
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'permissions' => $user->permissions ?? [],
                'is_active' => $user->is_active,
                'last_login_at' => $user->last_login_at,
                'login_count' => $user->login_count,
            ],
            'session' => [
                'started_at' => session()->get('login_time', now()),
                'last_activity' => now(),
                'impersonating' => session()->has('impersonating'),
                'ip_address' => request()->ip(),
            ],
            'permissions' => [
                'dashboard_access' => \App\Providers\RouteServiceProvider::getDashboardRouteName(),
                'can_impersonate' => $user->hasRole('administrateur'),
                'can_access_admin' => $user->hasRole('administrateur'),
            ]
        ]);
    })->name('auth.session-info');
});

/*
|--------------------------------------------------------------------------
| WEBHOOKS D'AUTHENTIFICATION (Optionnel)
|--------------------------------------------------------------------------
*/

// Route pour recevoir des notifications de sécurité externes
Route::post('auth/security-webhook', function () {
    // Traitement des webhooks de sécurité (connexions suspectes, etc.)
    return response()->json(['status' => 'received']);
})->name('auth.security-webhook');