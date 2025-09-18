<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Général - StockGuardian</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <div class="flex items-center">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-sm mr-3">
                                SG
                            </div>
                            <h1 class="text-xl font-semibold text-gray-800">StockGuardian</h1>
                        </div>
                        <div class="ml-6 px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded-full">
                            Dashboard Invité
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- User Info -->
                        <div class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                <span class="text-sm font-medium text-gray-600">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </span>
                            </div>
                            <div class="hidden md:block">
                                <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ ucfirst(auth()->user()->role ?? 'Invité') }}</p>
                            </div>
                        </div>
                        
                        <!-- Logout -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <!-- Welcome Section -->
            <div class="px-4 py-6 sm:px-0">
                <div class="text-center mb-8">
                    <div class="mx-auto w-16 h-16 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">Bienvenue dans StockGuardian</h1>
                    <p class="mt-2 text-lg text-gray-600">Système de gestion de stock et de pharmacie</p>
                    <div class="mt-4 inline-flex items-center px-4 py-2 bg-yellow-100 border border-yellow-300 rounded-lg">
                        <svg class="w-4 h-4 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-yellow-800 text-sm font-medium">Accès en lecture seule</span>
                    </div>
                </div>

                <!-- Stats Overview (Read-only) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    @if(isset($dashboardData['cards']))
                        @foreach($dashboardData['cards'] as $card)
                            <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-200">
                                <div class="p-5">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-{{ $card['color'] }}-100 rounded-lg flex items-center justify-center">
                                                @if($card['icon'] === 'home')
                                                    <svg class="w-5 h-5 text-{{ $card['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                                    </svg>
                                                @else
                                                    <span class="text-{{ $card['color'] }}-600 text-lg">📊</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="ml-5 w-0 flex-1">
                                            <dl>
                                                <dt class="text-sm font-medium text-gray-500 truncate">
                                                    {{ $card['title'] }}
                                                </dt>
                                                <dd class="text-lg font-medium text-gray-900">
                                                    {{ $card['value'] }} 
                                                    @if($card['unit'])
                                                        <span class="text-sm text-gray-500">{{ $card['unit'] }}</span>
                                                    @endif
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Default cards if no data -->
                        <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-200">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500">Produits</dt>
                                            <dd class="text-lg font-medium text-gray-900">-</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-200">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500">Rapports</dt>
                                            <dd class="text-lg font-medium text-gray-900">Disponibles</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-200">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500">Utilisateurs</dt>
                                            <dd class="text-lg font-medium text-gray-900">Consulter</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white overflow-hidden shadow rounded-lg border border-gray-200">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500">Aide</dt>
                                            <dd class="text-lg font-medium text-gray-900">Disponible</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Available Actions -->
                <div class="bg-white shadow rounded-lg border border-gray-200 mb-8">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Actions disponibles</h3>
                        <p class="mt-1 text-sm text-gray-600">Fonctionnalités accessibles en mode lecture seule</p>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <a href="#" class="group relative bg-gray-50 p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-blue-500 rounded-lg hover:bg-gray-100 transition-colors">
                                <div>
                                    <span class="rounded-lg inline-flex p-3 bg-blue-50 text-blue-700 ring-4 ring-white">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="mt-8">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        <a href="#" class="focus:outline-none">
                                            Consulter les produits
                                            <span class="absolute inset-0" aria-hidden="true"></span>
                                        </a>
                                    </h3>
                                    <p class="mt-2 text-sm text-gray-500">
                                        Voir le catalogue complet des produits et leurs informations
                                    </p>
                                </div>
                            </a>

                            <a href="#" class="group relative bg-gray-50 p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-green-500 rounded-lg hover:bg-gray-100 transition-colors">
                                <div>
                                    <span class="rounded-lg inline-flex p-3 bg-green-50 text-green-700 ring-4 ring-white">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="mt-8">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        <a href="#" class="focus:outline-none">
                                            Rapports et statistiques
                                            <span class="absolute inset-0" aria-hidden="true"></span>
                                        </a>
                                    </h3>
                                    <p class="mt-2 text-sm text-gray-500">
                                        Consulter les rapports de ventes et analyses
                                    </p>
                                </div>
                            </a>

                            <a href="#" class="group relative bg-gray-50 p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-purple-500 rounded-lg hover:bg-gray-100 transition-colors">
                                <div>
                                    <span class="rounded-lg inline-flex p-3 bg-purple-50 text-purple-700 ring-4 ring-white">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="mt-8">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        <a href="#" class="focus:outline-none">
                                            Documentation
                                            <span class="absolute inset-0" aria-hidden="true"></span>
                                        </a>
                                    </h3>
                                    <p class="mt-2 text-sm text-gray-500">
                                        Accéder à la documentation et aux guides d'utilisation
                                    </p>
                                </div>
                            </a>

                            <a href="#" class="group relative bg-gray-50 p-6 focus-within:ring-2 focus-within:ring-inset focus-within:ring-gray-500 rounded-lg hover:bg-gray-100 transition-colors">
                                <div>
                                    <span class="rounded-lg inline-flex p-3 bg-gray-50 text-gray-700 ring-4 ring-white">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="mt-8">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        <a href="#" class="focus:outline-none">
                                            Aide et support
                                            <span class="absolute inset-0" aria-hidden="true"></span>
                                        </a>
                                    </h3>
                                    <p class="mt-2 text-sm text-gray-500">
                                        Obtenir de l'aide et contacter le support technique
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Information Notice -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Compte Invité/Stagiaire</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <p>
                                    Votre compte permet uniquement la consultation des données existantes. 
                                    Vous ne pouvez pas créer, modifier ou supprimer d'informations.
                                </p>
                                <p class="mt-2">
                                    Pour obtenir des permissions supplémentaires, contactez votre administrateur système.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="text-center text-sm text-gray-500">
                    <p>&copy; {{ date('Y') }} StockGuardian. Système de gestion de stock et de pharmacie.</p>
                    <p class="mt-1">Version {{ config('app.version', '1.0.0') }}</p>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>