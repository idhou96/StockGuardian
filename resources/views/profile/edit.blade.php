{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <x-breadcrumb :items="[
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Mon Profil', 'url' => null]
            ]" />
            <div class="mt-4">
                <h1 class="text-2xl font-bold text-gray-900">Mon Profil</h1>
                <p class="mt-1 text-sm text-gray-600">Gérez vos informations personnelles et préférences de compte</p>
            </div>
        </div>

        <div class="space-y-6">
            {{-- Photo de profil et informations de base --}}
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informations personnelles</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="flex items-center space-x-6">
                        {{-- Photo de profil --}}
                        <div class="flex-shrink-0">
                            <div class="relative">
                                @if(auth()->user()->avatar)
                                <img class="h-20 w-20 rounded-full object-cover" src="{{ Storage::url(auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}">
                                @else
                                <div class="h-20 w-20 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                                    <span class="text-xl font-semibold text-white">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                    </span>
                                </div>
                                @endif
                                <button type="button" 
                                        onclick="document.getElementById('avatar-upload').click()"
                                        class="absolute -bottom-1 -right-1 bg-white rounded-full p-1 shadow-lg border border-gray-300 hover:bg-gray-50">
                                    <svg class="h-4 w-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </button>
                                <input type="file" id="avatar-upload" accept="image/*" class="hidden" onchange="uploadAvatar(this)">
                            </div>
                        </div>

                        {{-- Informations de base --}}
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-gray-900">{{ auth()->user()->name }}</h4>
                            <p class="text-sm text-gray-600">{{ auth()->user()->email }}</p>
                            <div class="mt-2 flex items-center space-x-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ auth()->user()->role === 'administrateur' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    Membre depuis {{ auth()->user()->created_at->format('M Y') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Formulaire de modification des informations --}}
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Modifier mes informations</h3>
                </div>
                <div class="px-6 py-4">
                    <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Nom --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Nom complet <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       name="name" 
                                       id="name" 
                                       value="{{ old('name', auth()->user()->name) }}"
                                       required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('name') border-red-300 @enderror">
                                @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">
                                    Adresse email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       value="{{ old('email', auth()->user()->email) }}"
                                       required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('email') border-red-300 @enderror">
                                @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Téléphone --}}
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">
                                    Téléphone
                                </label>
                                <input type="tel" 
                                       name="phone" 
                                       id="phone" 
                                       value="{{ old('phone', auth()->user()->phone) }}"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('phone') border-red-300 @enderror">
                                @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Rôle (lecture seule) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Rôle
                                </label>
                                <input type="text" 
                                       value="{{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}" 
                                       readonly
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500 sm:text-sm">
                                <p class="mt-1 text-xs text-gray-500">Contactez un administrateur pour modifier votre rôle</p>
                            </div>
                        </div>

                        {{-- Préférences --}}
                        <div class="border-t border-gray-200 pt-6">
                            <h4 class="text-base font-medium text-gray-900 mb-4">Préférences</h4>
                            <div class="space-y-4">
                                {{-- Langue --}}
                                <div>
                                    <label for="language" class="block text-sm font-medium text-gray-700">
                                        Langue
                                    </label>
                                    <select name="language" 
                                            id="language"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="fr" {{ old('language', auth()->user()->language ?? 'fr') === 'fr' ? 'selected' : '' }}>Français</option>
                                        <option value="en" {{ old('language', auth()->user()->language) === 'en' ? 'selected' : '' }}>English</option>
                                    </select>
                                </div>

                                {{-- Fuseau horaire --}}
                                <div>
                                    <label for="timezone" class="block text-sm font-medium text-gray-700">
                                        Fuseau horaire
                                    </label>
                                    <select name="timezone" 
                                            id="timezone"
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="Africa/Abidjan" {{ old('timezone', auth()->user()->timezone ?? 'Africa/Abidjan') === 'Africa/Abidjan' ? 'selected' : '' }}>GMT (Abidjan)</option>
                                        <option value="Europe/Paris" {{ old('timezone', auth()->user()->timezone) === 'Europe/Paris' ? 'selected' : '' }}>CET (Paris)</option>
                                        <option value="America/New_York" {{ old('timezone', auth()->user()->timezone) === 'America/New_York' ? 'selected' : '' }}>EST (New York)</option>
                                    </select>
                                </div>

                                {{-- Notifications --}}
                                <div class="space-y-3">
                                    <label class="text-sm font-medium text-gray-700">Notifications</label>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="checkbox" 
                                                   name="notifications[]" 
                                                   value="email_alerts" 
                                                   {{ in_array('email_alerts', old('notifications', auth()->user()->notification_preferences ?? [])) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-700">Alertes par email</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" 
                                                   name="notifications[]" 
                                                   value="stock_alerts" 
                                                   {{ in_array('stock_alerts', old('notifications', auth()->user()->notification_preferences ?? [])) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-700">Alertes de stock faible</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="checkbox" 
                                                   name="notifications[]" 
                                                   value="system_updates" 
                                                   {{ in_array('system_updates', old('notifications', auth()->user()->notification_preferences ?? [])) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                            <span class="ml-2 text-sm text-gray-700">Mises à jour système</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Boutons d'action --}}
                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <button type="button" 
                                    onclick="resetForm()"
                                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                                Annuler
                            </button>
                            <button type="submit" 
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Sauvegarder les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Changement de mot de passe --}}
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Changer mon mot de passe</h3>
                    <p class="mt-1 text-sm text-gray-600">Assurez-vous d'utiliser un mot de passe sécurisé</p>
                </div>
                <div class="px-6 py-4">
                    <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-6">
                            {{-- Mot de passe actuel --}}
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700">
                                    Mot de passe actuel <span class="text-red-500">*</span>
                                </label>
                                <input type="password" 
                                       name="current_password" 
                                       id="current_password" 
                                       required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('current_password') border-red-300 @enderror">
                                @error('current_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Nouveau mot de passe --}}
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    Nouveau mot de passe <span class="text-red-500">*</span>
                                </label>
                                <input type="password" 
                                       name="password" 
                                       id="password" 
                                       required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('password') border-red-300 @enderror">
                                @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <div class="mt-1 text-xs text-gray-500">
                                    Le mot de passe doit contenir au moins 8 caractères
                                </div>
                            </div>

                            {{-- Confirmation du mot de passe --}}
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                    Confirmer le nouveau mot de passe <span class="text-red-500">*</span>
                                </label>
                                <input type="password" 
                                       name="password_confirmation" 
                                       id="password_confirmation" 
                                       required
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                        </div>

                        {{-- Bouton de soumission --}}
                        <div class="flex justify-end pt-6 border-t border-gray-200">
                            <button type="submit" 
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Mettre à jour le mot de passe
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Activité récente --}}
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Activité récente</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-4">
                        {{-- Dernière connexion --}}
                        <div class="flex items-center justify-between py-3 border-b border-gray-100">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">Dernière connexion</p>
                                    <p class="text-sm text-gray-500">{{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'Première connexion' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Sessions actives --}}
                        <div class="flex items-center justify-between py-3">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">Session active</p>
                                    <p class="text-sm text-gray-500">Cette session • {{ request()->ip() }}</p>
                                </div>
                            </div>
                            <button type="button" 
                                    class="text-sm text-red-600 hover:text-red-800"
                                    onclick="logoutOtherSessions()">
                                Déconnecter les autres sessions
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function resetForm() {
    if (confirm('Êtes-vous sûr de vouloir annuler vos modifications ?')) {
        location.reload();
    }
}

function uploadAvatar(input) {
    if (input.files && input.files[0]) {
        const formData = new FormData();
        formData.append('avatar', input.files[0]);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        fetch('{{ route("profile.avatar.update") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de la mise à jour de la photo de profil');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la mise à jour de la photo de profil');
        });
    }
}

function logoutOtherSessions() {
    if (confirm('Êtes-vous sûr de vouloir déconnecter toutes les autres sessions ?')) {
        fetch('{{ route("profile.logout-other-sessions") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Toutes les autres sessions ont été déconnectées');
            } else {
                alert('Erreur lors de la déconnexion des autres sessions');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de la déconnexion des autres sessions');
        });
    }
}
</script>
@endsection 