<?php
// 🎯 VUES RAPPORTS AVANCÉS & ANALYTICS

// ===================================
// 1. VUE DASHBOARD RAPPORTS AVANCÉS
// ===================================
// File: resources/views/reports/index.blade.php
?>

@extends('layouts.app')

@section('title', 'Rapports et Analyses')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Rapports et Analyses</h1>
                    <p class="text-sm text-gray-600 mt-1">Tableaux de bord et rapports détaillés</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="exportAllReports()" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Exporter tout</span>
                    </button>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Paramètres</span>
                        </button>
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
                            <div class="py-1">
                                <a href="{{ route('reports.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Configurer rapports</a>
                                <a href="{{ route('reports.schedule') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Rapports automatiques</a>
                                <a href="{{ route('reports.templates') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Modèles de rapport</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-6">
        <!-- Filtres période globaux -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <form method="GET" action="{{ route('reports.index') }}" class="flex flex-wrap items-center gap-4">
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Période :</label>
                    <select name="period" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                        <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Cette semaine</option>
                        <option value="month" {{ request('period', 'month') == 'month' ? 'selected' : '' }}>Ce mois</option>
                        <option value="quarter" {{ request('period') == 'quarter' ? 'selected' : '' }}>Ce trimestre</option>
                        <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Cette année</option>
                        <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Personnalisée</option>
                    </select>
                </div>
                <div class="flex items-center space-x-2" x-show="document.querySelector('select[name=period]').value === 'custom'">
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <span class="text-gray-500">à</span>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                    Actualiser
                </button>
            </form>
        </div>

        <!-- Grille des rapports -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            
            <!-- 1. Rapport Ventes -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-gray-900">Rapport des Ventes</h3>
                                <p class="text-sm text-gray-500">Analyse des performances commerciales</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">CA total :</span>
                            <span class="font-semibold text-green-600">{{ number_format($salesReport['total_revenue'] ?? 0, 0, ',', ' ') }} F</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nb ventes :</span>
                            <span class="font-medium">{{ $salesReport['total_sales'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Panier moyen :</span>
                            <span class="font-medium">{{ number_format($salesReport['average_sale'] ?? 0, 0, ',', ' ') }} F</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Evolution :</span>
                            <span class="font-medium {{ ($salesReport['growth'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ ($salesReport['growth'] ?? 0) >= 0 ? '+' : '' }}{{ number_format($salesReport['growth'] ?? 0, 1) }}%
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="{{ route('reports.sales.detailed') }}" 
                           class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center px-4 py-2 rounded-lg text-sm transition-colors">
                            Voir détails
                        </a>
                        <a href="{{ route('reports.sales.export') }}" 
                           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition-colors">
                            Export
                        </a>
                    </div>
                </div>
            </div>

            <!-- 2. Rapport Stock -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-gray-900">Rapport de Stock</h3>
                                <p class="text-sm text-gray-500">État et valorisation du stock</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Valeur stock :</span>
                            <span class="font-semibold text-blue-600">{{ number_format($stockReport['total_value'] ?? 0, 0, ',', ' ') }} F</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Produits actifs :</span>
                            <span class="font-medium">{{ $stockReport['active_products'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Alertes stock :</span>
                            <span class="font-medium text-red-600">{{ $stockReport['low_stock_alerts'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Rotation moy. :</span>
                            <span class="font-medium">{{ number_format($stockReport['average_rotation'] ?? 0, 1) }} jours</span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="{{ route('reports.stock.detailed') }}" 
                           class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded-lg text-sm transition-colors">
                            Voir détails
                        </a>
                        <a href="{{ route('reports.stock.export') }}" 
                           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition-colors">
                            Export
                        </a>
                    </div>
                </div>
            </div>

            <!-- 3. Rapport Achats -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-gray-900">Rapport des Achats</h3>
                                <p class="text-sm text-gray-500">Analyse des approvisionnements</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total achats :</span>
                            <span class="font-semibold text-purple-600">{{ number_format($purchaseReport['total_amount'] ?? 0, 0, ',', ' ') }} F</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nb commandes :</span>
                            <span class="font-medium">{{ $purchaseReport['total_orders'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Fournisseurs :</span>
                            <span class="font-medium">{{ $purchaseReport['active_suppliers'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Délai moyen :</span>
                            <span class="font-medium">{{ number_format($purchaseReport['average_delay'] ?? 0, 1) }} jours</span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="{{ route('reports.purchases.detailed') }}" 
                           class="flex-1 bg-purple-600 hover:bg-purple-700 text-white text-center px-4 py-2 rounded-lg text-sm transition-colors">
                            Voir détails
                        </a>
                        <a href="{{ route('reports.purchases.export') }}" 
                           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition-colors">
                            Export
                        </a>
                    </div>
                </div>
            </div>

            <!-- 4. Rapport Financier -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-gray-900">Rapport Financier</h3>
                                <p class="text-sm text-gray-500">Bénéfices et marge</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Marge brute :</span>
                            <span class="font-semibold text-yellow-600">{{ number_format($financialReport['gross_margin'] ?? 0, 0, ',', ' ') }} F</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">% marge :</span>
                            <span class="font-medium">{{ number_format($financialReport['margin_percentage'] ?? 0, 1) }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Créances :</span>
                            <span class="font-medium text-orange-600">{{ number_format($financialReport['receivables'] ?? 0, 0, ',', ' ') }} F</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Dettes :</span>
                            <span class="font-medium text-red-600">{{ number_format($financialReport['payables'] ?? 0, 0, ',', ' ') }} F</span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="{{ route('reports.financial.detailed') }}" 
                           class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white text-center px-4 py-2 rounded-lg text-sm transition-colors">
                            Voir détails
                        </a>
                        <a href="{{ route('reports.financial.export') }}" 
                           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition-colors">
                            Export
                        </a>
                    </div>
                </div>
            </div>

            <!-- 5. Analyse ABC -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-gray-900">Analyse ABC</h3>
                                <p class="text-sm text-gray-500">Classification des produits</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Classe A (80%) :</span>
                            <span class="font-medium text-green-600">{{ $abcAnalysis['class_a'] ?? 0 }} produits</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Classe B (15%) :</span>
                            <span class="font-medium text-yellow-600">{{ $abcAnalysis['class_b'] ?? 0 }} produits</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Classe C (5%) :</span>
                            <span class="font-medium text-red-600">{{ $abcAnalysis['class_c'] ?? 0 }} produits</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Dernière MAJ :</span>
                            <span class="font-medium text-sm">{{ $abcAnalysis['last_update'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="{{ route('reports.abc.detailed') }}" 
                           class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-center px-4 py-2 rounded-lg text-sm transition-colors">
                            Voir analyse
                        </a>
                        <button onclick="refreshABC()" 
                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition-colors">
                            Actualiser
                        </button>
                    </div>
                </div>
            </div>

            <!-- 6. Rapport Clients -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-gray-900">Rapport Clients</h3>
                                <p class="text-sm text-gray-500">Fidélisation et segmentation</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Clients actifs :</span>
                            <span class="font-semibold text-pink-600">{{ $customerReport['active_customers'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nouveaux :</span>
                            <span class="font-medium text-green-600">{{ $customerReport['new_customers'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Récurrents :</span>
                            <span class="font-medium">{{ $customerReport['recurring_customers'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">LTV moyenne :</span>
                            <span class="font-medium">{{ number_format($customerReport['average_ltv'] ?? 0, 0, ',', ' ') }} F</span>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="{{ route('reports.customers.detailed') }}" 
                           class="flex-1 bg-pink-600 hover:bg-pink-700 text-white text-center px-4 py-2 rounded-lg text-sm transition-colors">
                            Voir détails
                        </a>
                        <a href="{{ route('reports.customers.export') }}" 
                           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm transition-colors">
                            Export
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques et analytics -->
        <div class="mt-8 grid grid-cols-1 xl:grid-cols-2 gap-6">
            <!-- Graphique évolution CA -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Évolution du Chiffre d'Affaires</h3>
                    <div class="flex space-x-2">
                        <select class="text-sm border border-gray-300 rounded px-2 py-1">
                            <option>12 derniers mois</option>
                            <option>6 derniers mois</option>
                            <option>3 derniers mois</option>
                        </select>
                    </div>
                </div>
                <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                    <div class="text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <p class="mt-2 text-sm">Graphique interactif - CA par mois</p>
                        <p class="text-xs text-gray-400">Intégrer Chart.js ou Recharts</p>
                    </div>
                </div>
            </div>

            <!-- Top produits -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Top 10 Produits</h3>
                    <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Voir tous
                    </button>
                </div>
                <div class="space-y-3">
                    @if(isset($topProducts))
                        @foreach($topProducts as $index => $product)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-sm font-medium text-blue-600">
                                    {{ $index + 1 }}
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $product['name'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $product['code'] }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900">{{ number_format($product['quantity']) }}</div>
                                <div class="text-xs text-gray-500">{{ number_format($product['revenue'], 0, ',', ' ') }} F</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p class="text-sm">Aucune donnée de vente disponible</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportAllReports() {
    // Fonction pour exporter tous les rapports
    alert('Export de tous les rapports en cours...');
}

function refreshABC() {
    // Fonction pour actualiser l'analyse ABC
    alert('Actualisation de l\'analyse ABC en cours...');
}
</script>
@endsection
