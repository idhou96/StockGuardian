
// ===================================
// 3. VUE PROFIL UTILISATEUR AVANCÉ
// ===================================
// File: resources/views/profile/show.blade.php

@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Mon Profil</h1>
                    <p class="text-sm text-gray-600 mt-1">Gérez vos informations personnelles et préférences</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="changePassword()" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-11.639 2.755M15 7H9a2 2 0 00-2 2v9a2 2 0 002 2h6a2 2 0 002-2V9z"/>
                        </svg>
                        <span>Changer mot de passe</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-6">
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Informations personnelles -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Informations personnelles</h3>
                
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Photo de profil -->
                        <div class="md:col-span-1">
                            <div class="flex flex-col items-center">
                                <div class="relative">
                                    @if(auth()->user()->avatar)
                                    <img class="h-24 w-24 rounded-full object-cover" src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Photo de profil">
                                    @else
                                    <div class="h-24 w-24 rounded-full bg-gray-300 flex items-center justify-center">
                                        <span class="text-xl font-medium text-gray-700">
                                            {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name, 0, 1)) }}
                                        </span>
                                    </div>
                                    @endif
                                    <button type="button" 
                                            onclick="document.getElementById('avatar').click()"
                                            class="absolute bottom-0 right-0 bg-blue-600 rounded-full p-2 text-white hover:bg-blue-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </button>
                                </div>
                                <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden">
                                <p class="mt-2 text-sm text-gray-500 text-center">Cliquez pour changer la photo</p>
                            </div>
                        </div>

                        <!-- Informations -->
                        <div class="md:col-span-2 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">Prénom</label>
                                    <input type="text" 
                                           id="first_name" 
                                           name="first_name" 
                                           value="{{ old('first_name', auth()->user()->first_name) }}"
                                           required
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Nom</label>
                                    <input type="text" 
                                           id="last_name" 
                                           name="last_name" 
                                           value="{{ old('last_name', auth()->user()->last_name) }}"
                                           required
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input type="email" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', auth()->user()->email) }}"
                                           required
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                                    <input type="tel" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone', auth()->user()->phone) }}"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                            Mettre à jour
                        </button>
                    </div>
                </form>
            </div>

            <!-- Préférences -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Préférences</h3>
                
                <form action="{{ route('profile.preferences') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="language" class="block text-sm font-medium text-gray-700 mb-2">Langue</label>
                            <select id="language" 
                                    name="language" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="fr" {{ (auth()->user()->preferences['language'] ?? 'fr') == 'fr' ? 'selected' : '' }}>Français</option>
                                <option value="en" {{ (auth()->user()->preferences['language'] ?? '') == 'en' ? 'selected' : '' }}>English</option>
                            </select>
                        </div>

                        <div>
                            <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">Fuseau horaire</label>
                            <select id="timezone" 
                                    name="timezone" 
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="Africa/Abidjan" {{ (auth()->user()->preferences['timezone'] ?? 'Africa/Abidjan') == 'Africa/Abidjan' ? 'selected' : '' }}>Afrique/Abidjan</option>
                                <option value="Europe/Paris" {{ (auth()->user()->preferences['timezone'] ?? '') == 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Notifications</label>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="notifications[]" 
                                           value="email_notifications"
                                           {{ in_array('email_notifications', auth()->user()->preferences['notifications'] ?? []) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Recevoir les notifications par email</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="notifications[]" 
                                           value="stock_alerts"
                                           {{ in_array('stock_alerts', auth()->user()->preferences['notifications'] ?? []) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Alertes de stock</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="notifications[]" 
                                           value="daily_reports"
                                           {{ in_array('daily_reports', auth()->user()->preferences['notifications'] ?? []) ? 'checked' : '' }}
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Rapports quotidiens</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                            Enregistrer les préférences
                        </button>
                    </div>
                </form>
            </div>

            <!-- Activité récente -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Activité récente</h3>
                
                <div class="space-y-4">
                    @forelse(auth()->user()->recentLogs()->take(5)->get() as $log)
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0">
                            <span class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $log->action }}</p>
                            <p class="text-sm text-gray-500">{{ $log->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6 text-gray-500">
                        <p class="text-sm">Aucune activité récente</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function changePassword() {
    // Modal pour changer le mot de passe
    alert('Fonctionnalité de changement de mot de passe à implémenter');
}
</script>
@endsection