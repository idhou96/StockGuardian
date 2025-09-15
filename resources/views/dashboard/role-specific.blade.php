<?php
// 🎯 VUES FINALES ABSOLUES POUR COMPLÉTER STOCKGUARDIAN

// ===================================
// 6. DASHBOARD SPÉCIALISÉ PAR RÔLE
// ===================================
// File: resources/views/dashboard/role-specific.blade.php
?>

@extends('layouts.app')

@section('title', 'Tableau de Bord')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header dynamique selon le rôle -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">
                        @hasrole('administrateur')
                            Tableau de Bord Administrateur
                        @elsehasrole('responsable_commercial')
                            Tableau de Bord Commercial
                        @elsehasrole('vendeur')
                            Tableau de Bord Vendeur
                        @elsehasrole('magasinier')
                            Tableau de Bord Stock
                        @elsehasrole('responsable_achats')
                            Tableau de Bord Achats
                        @elsehasrole('comptable')
                            Tableau de Bord Comptabilité
                        @elsehasrole('caissiere')
                            Point de Vente
                        @else
                            Tableau de Bord
                        @endhasrole
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">
                        Bonjour {{ auth()->user()->first_name }}, bienvenue sur StockGuardian
                    </p>
                </div>
                <div class="flex items-center space-x-4">
                    <!-- Indicateurs temps réel -->
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span>En ligne</span>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ now()->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-6">
        <!-- Dashboard Administrateur -->
        @hasrole('administrateur')
        <div class="space-y-6">
            <!-- Métriques globales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">CA du jour</dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">{{ number_format($todayRevenue ?? 0, 0, ',', ' ') }} F</div>
                                    <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                        +{{ $revenueGrowth ?? 0 }}%
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Utilisateurs actifs</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ $activeUsers ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Alertes système</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ $systemAlerts ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Valeur stock</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ number_format($stockValue ?? 0, 0, ',', ' ') }} F</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Graphiques et analyses -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Performance du Système</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">CPU</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-32 bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: 25%"></div>
                                </div>
                                <span class="text-sm text-gray-900">25%</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Mémoire</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-32 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: 60%"></div>
                                </div>
                                <span class="text-sm text-gray-900">60%</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Stockage</span>
                            <div class="flex items-center space-x-2">
                                <div class="w-32 bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-600 h-2 rounded-full" style="width: 78%"></div>
                                </div>
                                <span class="text-sm text-gray-900">78%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Actions Rapides</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('users.index') }}" class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50 text-center">
                            <svg class="w-6 h-6 text-blue-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                            <span class="text-sm text-gray-700">Utilisateurs</span>
                        </a>
                        <a href="{{ route('settings.index') }}" class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50 text-center">
                            <svg class="w-6 h-6 text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            </svg>
                            <span class="text-sm text-gray-700">Paramètres</span>
                        </a>
                        <a href="{{ route('logs.index') }}" class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50 text-center">
                            <svg class="w-6 h-6 text-purple-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="text-sm text-gray-700">Logs</span>
                        </a>
                        <a href="{{ route('maintenance.index') }}" class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50 text-center">
                            <svg class="w-6 h-6 text-orange-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            </svg>
                            <span class="text-sm text-gray-700">Maintenance</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endhasrole

        <!-- Dashboard Vendeur -->
        @hasrole('vendeur')
        <div class="space-y-6">
            <!-- Métriques vendeur -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Mes ventes du jour</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ number_format($mySalesToday ?? 0, 0, ',', ' ') }} F</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Transactions</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ $myTransactionsToday ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Clients servis</dt>
                                <dd class="text-2xl font-semibold text-gray-900">{{ $myClientsToday ?? 0 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides vendeur -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Actions Rapides</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ route('sales.pos') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-lg text-center transition-colors">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span class="text-sm font-medium">Nouvelle Vente</span>
                    </a>
                    <a href="{{ route('clients.create') }}" 
                       class="bg-green-600 hover:bg-green-700 text-white p-4 rounded-lg text-center transition-colors">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        <span class="text-sm font-medium">Nouveau Client</span>
                    </a>
                    <a href="{{ route('products.search') }}" 
                       class="bg-purple-600 hover:bg-purple-700 text-white p-4 rounded-lg text-center transition-colors">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span class="text-sm font-medium">Rechercher Produit</span>
                    </a>
                    <a href="{{ route('sales.index') }}" 
                       class="bg-orange-600 hover:bg-orange-700 text-white p-4 rounded-lg text-center transition-colors">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span class="text-sm font-medium">Mes Ventes</span>
                    </a>
                </div>
            </div>
        </div>
        @endhasrole

        <!-- Dashboard Magasinier -->
        @hasrole('magasinier')
        <div class="space-y-6">
            <!-- Alertes stock prioritaires -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <h3 class="text-lg font-medium text-red-800 mb-4">🚨 Alertes Stock Prioritaires</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white rounded-lg p-4 border border-red-200">
                        <div class="text-2xl font-bold text-red-600">{{ $outOfStockCount ?? 0 }}</div>
                        <div class="text-sm text-red-700">Ruptures de stock</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-orange-200">
                        <div class="text-2xl font-bold text-orange-600">{{ $lowStockCount ?? 0 }}</div>
                        <div class="text-sm text-orange-700">Stock faible</div>
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-yellow-200">
                        <div class="text-2xl font-bold text-yellow-600">{{ $expiringProductsCount ?? 0 }}</div>
                        <div class="text-sm text-yellow-700">Expiration proche</div>
                    </div>
                </div>
            </div>

            <!-- Actions stock -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Gestion du Stock</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ route('stock-movements.create-entry') }}" 
                       class="bg-green-600 hover:bg-green-700 text-white p-4 rounded-lg text-center transition-colors">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span class="text-sm font-medium">Entrée Stock</span>
                    </a>
                    <a href="{{ route('stock-movements.create-exit') }}" 
                       class="bg-red-600 hover:bg-red-700 text-white p-4 rounded-lg text-center transition-colors">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                        </svg>
                        <span class="text-sm font-medium">Sortie Stock</span>
                    </a>
                    <a href="{{ route('inventories.create') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-lg text-center transition-colors">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span class="text-sm font-medium">Inventaire</span>
                    </a>
                    <a href="{{ route('stock-regularizations.create') }}" 
                       class="bg-purple-600 hover:bg-purple-700 text-white p-4 rounded-lg text-center transition-colors">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        </svg>
                        <span class="text-sm font-medium">Régularisation</span>
                    </a>
                </div>
            </div>
        </div>
        @endhasrole

        <!-- Dashboard pour Caissière (POS simplifié) -->
        @hasrole('caissiere')
        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Point de Vente</h3>
                <div class="text-center">
                    <a href="{{ route('sales.pos') }}" 
                       class="inline-flex items-center px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white text-lg font-medium rounded-lg transition-colors">
                        <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        Ouvrir la Caisse
                    </a>
                </div>
            </div>

            <!-- Résumé de session -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
                    <div class="text-2xl font-bold text-green-600">{{ number_format($cashierSalesToday ?? 0, 0, ',', ' ') }} F</div>
                    <div class="text-sm text-gray-600">Ventes du jour</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $cashierTransactionsToday ?? 0 }}</div>
                    <div class="text-sm text-gray-600">Transactions</div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ number_format($cashierAverageBasket ?? 0, 0, ',', ' ') }} F</div>
                    <div class="text-sm text-gray-600">Panier moyen</div>
                </div>
            </div>
        </div>
        @endhasrole

        <!-- Autres rôles... -->
        @hasanyrole('responsable_commercial|comptable|responsable_achats')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Tableau de Bord Spécialisé</h3>
            <div class="text-center text-gray-500">
                <p>Dashboard spécialisé pour votre rôle en cours de développement.</p>
                <p class="mt-2">Accédez aux modules correspondant à vos permissions via le menu de navigation.</p>
            </div>
        </div>
        @endhasanyrole
    </div>
</div>
@endsection
