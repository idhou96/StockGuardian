<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Déterminer si l'utilisateur est autorisé à faire cette demande.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtenir les règles de validation qui s'appliquent à la demande.
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
            ],
            'remember' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    /**
     * Messages de validation personnalisés.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.max' => 'L\'adresse email ne peut pas dépasser 255 caractères.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        ];
    }

    /**
     * Tentative d'authentification de l'utilisateur.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $credentials = $this->only('email', 'password');
        $remember = $this->boolean('remember');

        // Tentative d'authentification
        if (!Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($this->throttleKey());

            // Vérifier si l'email existe pour donner un message plus précis
            $user = \App\Models\User::where('email', $this->input('email'))->first();
            
            if ($user) {
                if (!$user->is_active) {
                    throw ValidationException::withMessages([
                        'email' => ['Votre compte est désactivé. Contactez l\'administrateur.'],
                    ]);
                } else {
                    throw ValidationException::withMessages([
                        'password' => ['Le mot de passe est incorrect.'],
                    ]);
                }
            } else {
                throw ValidationException::withMessages([
                    'email' => ['Aucun compte trouvé avec cette adresse email.'],
                ]);
            }
        }

        // Vérifications supplémentaires après authentification réussie
        $user = Auth::user();

        // Vérifier si l'utilisateur est actif
        if (!$user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => ['Votre compte a été désactivé. Contactez l\'administrateur.'],
            ]);
        }

        // Vérifier si l'utilisateur a un rôle valide
        if (!$this->hasValidRole($user)) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => ['Votre compte n\'a pas de rôle valide. Contactez l\'administrateur.'],
            ]);
        }

        // Vérifier les restrictions d'horaires (si applicable)
        if (!$this->isWithinAllowedHours($user)) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => ['Connexion non autorisée en dehors des heures de travail.'],
            ]);
        }

        // Vérifier les restrictions d'IP (si applicable)
        if (!$this->isFromAllowedIP($user)) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => ['Connexion non autorisée depuis cette adresse IP.'],
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * S'assurer que la demande n'est pas limitée par le taux.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => [
                "Trop de tentatives de connexion. Réessayez dans {$seconds} secondes."
            ],
        ]);
    }

    /**
     * Obtenir la clé de limitation du taux pour la demande.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
    }

    /**
     * Vérifier si l'utilisateur a un rôle valide.
     */
    private function hasValidRole($user): bool
    {
        $validRoles = [
            'administrateur',
            'responsable_commercial',
            'vendeur',
            'magasinier',
            'responsable_achats',
            'comptable',
            'caissiere',
            'invite',
            'stagiaire',
        ];

        return in_array($user->role, $validRoles);
    }

    /**
     * Vérifier si la connexion est dans les heures autorisées.
     * Cette méthode peut être personnalisée selon les besoins de l'entreprise.
     */
    private function isWithinAllowedHours($user): bool
    {
        // Exemple : Certains rôles peuvent se connecter 24h/24
        $alwaysAllowedRoles = ['administrateur', 'responsable_commercial'];
        
        if (in_array($user->role, $alwaysAllowedRoles)) {
            return true;
        }

        // Pour les autres rôles, vérifier les heures de travail
        $currentHour = now()->hour;
        $currentDay = now()->dayOfWeek; // 0 = Dimanche, 1 = Lundi, etc.

        // Vérifier si c'est un jour ouvrable (Lundi à Samedi)
        if ($currentDay == 0) { // Dimanche
            return false;
        }

        // Heures de travail : 6h à 20h
        if ($currentHour < 6 || $currentHour > 20) {
            return false;
        }

        return true;
    }

    /**
     * Vérifier si la connexion provient d'une IP autorisée.
     * Cette méthode peut être personnalisée pour des restrictions d'IP.
     */
    private function isFromAllowedIP($user): bool
    {
        // Pour l'instant, autoriser toutes les IPs
        // En production, vous pourriez avoir une liste d'IPs autorisées
        
        // Exemple d'implémentation :
        /*
        $allowedIPs = config('auth.allowed_ips', []);
        
        if (!empty($allowedIPs)) {
            $userIP = $this->ip();
            return in_array($userIP, $allowedIPs);
        }
        */

        return true;
    }

    /**
     * Obtenir les données de validation après nettoyage.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim($this->input('email'))),
        ]);
    }

    /**
     * Configuration des attributs pour les messages d'erreur.
     */
    public function attributes(): array
    {
        return [
            'email' => 'adresse email',
            'password' => 'mot de passe',
            'remember' => 'se souvenir de moi',
        ];
    }

    /**
     * Préparer les données pour la validation.
     */
    protected function getValidatorInstance()
    {
        $validator = parent::getValidatorInstance();

        // Ajouter une validation personnalisée pour l'email
        $validator->after(function ($validator) {
            $email = $this->input('email');
            
            // Vérifier le format de l'email de manière plus stricte
            if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $validator->errors()->add('email', 'Le format de l\'adresse email n\'est pas valide.');
            }

            // Vérifier si l'email contient des caractères suspects
            if ($email && preg_match('/[<>"\']/', $email)) {
                $validator->errors()->add('email', 'L\'adresse email contient des caractères non autorisés.');
            }
        });

        return $validator;
    }
}