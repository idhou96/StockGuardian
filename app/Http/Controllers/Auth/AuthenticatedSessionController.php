<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Afficher la vue de connexion.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Gérer une demande d'authentification entrante.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Vérifier le rate limiting
        $this->checkRateLimit($request);

        try {
            // Tentative d'authentification
            $request->authenticate();

            // Régénérer la session pour sécurité
            $request->session()->regenerate();

            // Récupérer l'utilisateur connecté
            $user = Auth::user();

            // Vérifier si l'utilisateur est actif
            if (!$user->is_active) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                throw ValidationException::withMessages([
                    'email' => ['Votre compte est désactivé. Contactez l\'administrateur.'],
                ]);
            }

            // Mettre à jour les informations de dernière connexion
            $this->updateLastLogin($user, $request);

            // Enregistrer l'activité de connexion
            $this->logLoginActivity($user, $request);

            // Effacer le rate limiting en cas de succès
            RateLimiter::clear($this->throttleKey($request));

            // Redirection intelligente selon le rôle
            $redirectUrl = $this->getRedirectUrl($user, $request);

            // Message de bienvenue personnalisé
            $welcomeMessage = $this->getWelcomeMessage($user);
            
            return redirect()->intended($redirectUrl)
                           ->with('success', $welcomeMessage);

        } catch (ValidationException $e) {
            // Incrémenter le compteur de tentatives échouées
            RateLimiter::hit($this->throttleKey($request));
            
            // Enregistrer la tentative de connexion échouée
            $this->logFailedLogin($request);
            
            throw $e;
        }
    }

    /**
     * Détruire une session authentifiée.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // Enregistrer l'activité de déconnexion
        if ($user) {
            $this->logLogoutActivity($user, $request);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Vous avez été déconnecté avec succès.');
    }

    /**
     * Déterminer l'URL de redirection selon le rôle de l'utilisateur.
     */
    private function getRedirectUrl(User $user, Request $request): string
    {
        // Si une URL intended existe et que l'utilisateur y a accès, l'utiliser
        $intendedUrl = $request->session()->get('url.intended');
        if ($intendedUrl && $this->userHasAccessToUrl($user, $intendedUrl)) {
            return $intendedUrl;
        }

        // Sinon, redirection selon le rôle
        return RouteServiceProvider::redirectTo();
    }

    /**
     * Vérifier si l'utilisateur a accès à une URL donnée.
     */
    private function userHasAccessToUrl(User $user, string $url): bool
    {
        // Extraire le nom de route de l'URL si possible
        try {
            $route = app('router')->getRoutes()->match(
                app('request')->create($url)
            );
            
            if ($route && $route->getName()) {
                return RouteServiceProvider::hasAccessToRoute($route->getName());
            }
        } catch (\Exception $e) {
            // En cas d'erreur, autoriser l'accès par défaut
            return true;
        }

        return true;
    }

    /**
     * Générer un message de bienvenue personnalisé selon le rôle.
     */
    private function getWelcomeMessage(User $user): string
    {
        $greeting = $this->getTimeBasedGreeting();
        
        $roleMessages = [
            'administrateur' => "🔧 Tableau de bord administrateur chargé",
            'responsable_commercial' => "📈 Tableau de bord commercial prêt",
            'vendeur' => "💰 Interface de vente activée",
            'caissiere' => "🛒 Point de vente prêt",
            'magasinier' => "📦 Gestion de stock accessible",
            'responsable_achats' => "🛍️ Module achats disponible",
            'comptable' => "💹 Dashboard financier ouvert",
            'invite' => "👋 Accès consultation activé",
            'stagiaire' => "🎓 Mode apprentissage chargé",
        ];

        $roleMessage = $roleMessages[$user->role] ?? "🎯 StockGuardian prêt";

        return "{$greeting} {$user->name} ! {$roleMessage}";
    }

    /**
     * Obtenir un salut basé sur l'heure.
     */
    private function getTimeBasedGreeting(): string
    {
        $hour = (int) now()->format('H');

        return match (true) {
            $hour >= 5 && $hour < 12 => 'Bonjour',
            $hour >= 12 && $hour < 17 => 'Bon après-midi',
            $hour >= 17 && $hour < 21 => 'Bonsoir',
            default => 'Bonne nuit',
        };
    }

    /**
     * Mettre à jour les informations de dernière connexion.
     */
    private function updateLastLogin(User $user, Request $request): void
    {
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
            'login_count' => $user->login_count + 1,
        ]);
    }

    /**
     * Enregistrer l'activité de connexion.
     */
    private function logLoginActivity(User $user, Request $request): void
    {
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'model_type' => User::class,
            'model_id' => $user->id,
            'description' => "Connexion réussie depuis {$request->ip()}",
            'properties' => [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'role' => $user->role,
                'login_time' => now()->toISOString(),
            ],
            'created_at' => now(),
        ]);
    }

    /**
     * Enregistrer une tentative de connexion échouée.
     */
    private function logFailedLogin(Request $request): void
    {
        ActivityLog::create([
            'user_id' => null,
            'action' => 'failed_login',
            'model_type' => User::class,
            'model_id' => null,
            'description' => "Tentative de connexion échouée pour {$request->input('email')} depuis {$request->ip()}",
            'properties' => [
                'email' => $request->input('email'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'attempt_time' => now()->toISOString(),
            ],
            'created_at' => now(),
        ]);
    }

    /**
     * Enregistrer l'activité de déconnexion.
     */
    private function logLogoutActivity(User $user, Request $request): void
    {
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'logout',
            'model_type' => User::class,
            'model_id' => $user->id,
            'description' => "Déconnexion depuis {$request->ip()}",
            'properties' => [
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'logout_time' => now()->toISOString(),
                'session_duration' => $user->last_login_at ? 
                    now()->diffInMinutes($user->last_login_at) . ' minutes' : 'unknown',
            ],
            'created_at' => now(),
        ]);
    }

    /**
     * Vérifier le rate limiting pour les tentatives de connexion.
     */
    private function checkRateLimit(Request $request): void
    {
        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            
            throw ValidationException::withMessages([
                'email' => [
                    "Trop de tentatives de connexion. Réessayez dans {$seconds} secondes."
                ],
            ]);
        }
    }

    /**
     * Générer la clé de throttling.
     */
    private function throttleKey(Request $request): string
    {
        return strtolower($request->input('email')).'|'.$request->ip();
    }
}