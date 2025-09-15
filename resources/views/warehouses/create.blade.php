{{-- resources/views/warehouses/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nouvel Entrepôt')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <nav class="flex mb-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <a href="{{ route('warehouses.index') }}" class="ml-1 md:ml-2 text-sm font-medium text-gray-700 hover:text-blue-600">Entrepôts</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 md:ml-2 text-sm font-medium text-gray-500">Nouvel Entrepôt</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                <div class="bg-blue-100 rounded-full p-2 mr-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                Nouvel Entrepôt
            </h1>
            <p class="text-gray-600 mt-1">Créer un nouveau lieu de stockage</p>
        </div>
        
        <a href="{{ route('warehouses.index') }}" 
           class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
            </svg>
            <span>Retour</span>
        </a>
    </div>

    <!-- Formulaire -->
    <form action="{{ route('warehouses.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Informations générales -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Informations générales
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nom de l'entrepôt -->
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom de l'entrepôt <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" 
                           value="{{ old('name') }}" required
                           placeholder="Ex: Entrepôt Principal Paris"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Code/Référence -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                        Code de référence <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="code" name="code" 
                           value="{{ old('code') }}" required
                           placeholder="Ex: ENT-PARIS-01"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('code') border-red-500 @enderror">
                    @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Code unique pour identifier l'entrepôt</p>
                </div>

                <!-- Type d'entrepôt -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Type d'entrepôt <span class="text-red-500">*</span>
                    </label>
                    <select id="type" name="type" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-500 @enderror">
                        <option value="">Sélectionner un type</option>
                        <option value="main" {{ old('type') == 'main' ? 'selected' : '' }}>Principal</option>
                        <option value="secondary" {{ old('type') == 'secondary' ? 'selected' : '' }}>Secondaire</option>
                        <option value="temporary" {{ old('type') == 'temporary' ? 'selected' : '' }}>Temporaire</option>
                        <option value="quarantine" {{ old('type') == 'quarantine' ? 'selected' : '' }}>Quarantaine</option>
                    </select>
                    @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Statut -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Statut <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                        <option value="">Sélectionner un statut</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>En maintenance</option>
                    </select>
                    @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Capacité -->
                <div>
                    <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">
                        Capacité maximale
                    </label>
                    <input type="number" id="capacity" name="capacity" 
                           value="{{ old('capacity') }}" min="0" step="1"
                           placeholder="Ex: 10000"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('capacity') border-red-500 @enderror">
                    @error('capacity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Nombre maximum d'unités pouvant être stockées</p>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="3" 
                              placeholder="Description et caractéristiques de l'entrepôt..."
                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Localisation -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Localisation
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Adresse -->
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        Adresse complète <span class="text-red-500">*</span>
                    </label>
                    <textarea id="address" name="address" rows="2" required
                              placeholder="Ex: 123 Avenue des Entrepreneurs, Zone Industrielle Nord"
                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>
                    @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Ville -->
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                        Ville <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="city" name="city" 
                           value="{{ old('city') }}" required
                           placeholder="Ex: Paris"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('city') border-red-500 @enderror">
                    @error('city')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Code postal -->
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                        Code postal <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="postal_code" name="postal_code" 
                           value="{{ old('postal_code') }}" required
                           placeholder="Ex: 75001"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('postal_code') border-red-500 @enderror">
                    @error('postal_code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Pays -->
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                        Pays <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="country" name="country" 
                           value="{{ old('country', 'France') }}" required
                           placeholder="Ex: France"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('country') border-red-500 @enderror">
                    @error('country')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Zone géographique -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                        Zone géographique
                    </label>
                    <input type="text" id="location" name="location" 
                           value="{{ old('location') }}"
                           placeholder="Ex: Île-de-France"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('location') border-red-500 @enderror">
                    @error('location')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Région ou zone géographique pour les rapports</p>
                </div>
            </div>
        </div>

        <!-- Informations de contact -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                </svg>
                Informations de contact
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Responsable/Manager -->
                <div>
                    <label for="manager" class="block text-sm font-medium text-gray-700 mb-2">
                        Responsable d'entrepôt
                    </label>
                    <input type="text" id="manager" name="manager" 
                           value="{{ old('manager') }}"
                           placeholder="Ex: Jean Dupont"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('manager') border-red-500 @enderror">
                    @error('manager')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Téléphone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Téléphone
                    </label>
                    <input type="tel" id="phone" name="phone" 
                           value="{{ old('phone') }}"
                           placeholder="Ex: +33 1 23 45 67 89"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror">
                    @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    <input type="email" id="email" name="email" 
                           value="{{ old('email') }}"
                           placeholder="Ex: entrepot.paris@entreprise.com"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Horaires -->
                <div>
                    <label for="operating_hours" class="block text-sm font-medium text-gray-700 mb-2">
                        Horaires d'ouverture
                    </label>
                    <input type="text" id="operating_hours" name="operating_hours" 
                           value="{{ old('operating_hours') }}"
                           placeholder="Ex: 8h-18h du Lundi au Vendredi"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('operating_hours') border-red-500 @enderror">
                    @error('operating_hours')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Paramètres avancés -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Paramètres avancés
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Température -->
                <div>
                    <label for="temperature_controlled" class="block text-sm font-medium text-gray-700 mb-2">
                        Contrôle de température
                    </label>
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="temperature_controlled" value="0" 
                                   {{ old('temperature_controlled', '0') == '0' ? 'checked' : '' }}
                                   class="form-radio h-4 w-4 text-blue-600">
                            <span class="ml-2">Non</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="temperature_controlled" value="1" 
                                   {{ old('temperature_controlled') == '1' ? 'checked' : '' }}
                                   class="form-radio h-4 w-4 text-blue-600">
                            <span class="ml-2">Oui</span>
                        </label>
                    </div>
                    @error('temperature_controlled')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Plage de température -->
                <div id="temperature_range" class="hidden">
                    <label for="temperature_range_input" class="block text-sm font-medium text-gray-700 mb-2">
                        Plage de température (°C)
                    </label>
                    <input type="text" id="temperature_range_input" name="temperature_range" 
                           value="{{ old('temperature_range') }}"
                           placeholder="Ex: 2-8°C"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Seuils d'alerte -->
                <div>
                    <label for="alert_threshold" class="block text-sm font-medium text-gray-700 mb-2">
                        Seuil d'alerte stock faible (%)
                    </label>
                    <input type="number" id="alert_threshold" name="alert_threshold" 
                           value="{{ old('alert_threshold', '20') }}" min="0" max="100" step="1"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('alert_threshold') border-red-500 @enderror">
                    @error('alert_threshold')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Alerte quand la capacité utilisée dépasse ce pourcentage</p>
                </div>

                <!-- Notifications -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Notifications automatiques
                    </label>
                    <div class="space-y-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="notifications[]" value="stock_low" 
                                   {{ in_array('stock_low', old('notifications', [])) ? 'checked' : '' }}
                                   class="form-checkbox h-4 w-4 text-blue-600">
                            <span class="ml-2 text-sm">Stock faible</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="notifications[]" value="expiry_alert" 
                                   {{ in_array('expiry_alert', old('notifications', [])) ? 'checked' : '' }}
                                   class="form-checkbox h-4 w-4 text-blue-600">
                            <span class="ml-2 text-sm">Dates d'expiration proches</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="notifications[]" value="capacity_alert" 
                                   {{ in_array('capacity_alert', old('notifications', [])) ? 'checked' : '' }}
                                   class="form-checkbox h-4 w-4 text-blue-600">
                            <span class="ml-2 text-sm">Capacité maximale atteinte</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('warehouses.index') }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-200">
                Annuler
            </a>
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Créer l'entrepôt</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Afficher/masquer la plage de température
document.querySelectorAll('input[name="temperature_controlled"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        const temperatureRange = document.getElementById('temperature_range');
        if (this.value === '1') {
            temperatureRange.classList.remove('hidden');
            temperatureRange.querySelector('input').required = true;
        } else {
            temperatureRange.classList.add('hidden');
            temperatureRange.querySelector('input').required = false;
        }
    });
});

// Initialiser l'affichage au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    const checkedRadio = document.querySelector('input[name="temperature_controlled"]:checked');
    if (checkedRadio && checkedRadio.value === '1') {
        document.getElementById('temperature_range').classList.remove('hidden');
        document.getElementById('temperature_range').querySelector('input').required = true;
    }
});

// Générer automatiquement le code basé sur le nom
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const codeField = document.getElementById('code');
    
    if (name && !codeField.dataset.userModified) {
        // Générer un code basé sur le nom
        const code = name
            .toUpperCase()
            .replace(/[^A-Z0-9\s]/g, '') // Supprimer les caractères spéciaux
            .replace(/\s+/g, '-') // Remplacer les espaces par des tirets
            .substring(0, 20); // Limiter à 20 caractères
        
        codeField.value = code;
    }
});

// Marquer le code comme modifié manuellement
document.getElementById('code').addEventListener('input', function() {
    this.dataset.userModified = 'true';
});

// Validation en temps réel
document.getElementById('capacity').addEventListener('input', function() {
    const value = parseInt(this.value);
    if (value < 0) {
        this.value = 0;
    }
});

document.getElementById('alert_threshold').addEventListener('input', function() {
    const value = parseInt(this.value);
    if (value < 0) {
        this.value = 0;
    } else if (value > 100) {
        this.value = 100;
    }
});
</script>
@endpush
@endsection