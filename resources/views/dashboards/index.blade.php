{{-- resources/views/dashboard/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard - StockGuardian')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header avec salutation -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Bonjour {{ auth()->user()->name }} ! 👋
            </h1>
            <p class="text-gray-600 mt-1">
                {{ now()->format('l d F Y') }} - 
                Voici un aperçu de votre activité StockGuardian
            </p>
        </div>
        
        <div class="flex space-x-3">
            @can('create stock_movements')
            <a href="{{ route('stock-movements.create-entry') }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span>Entrée Stock</span>
            </a>
            @endcan

            @can('create sales')
            @if(auth()->user()->hasRole(['administrateur', 'vendeur', 'caissiere']))
            <a href="{{ route('pos.index') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-7a2 2 0 00-2-2H9a2 2 0 00-2 2v7a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Point de Vente</span>
            </a>
            @endif
            @endcan
        </div>
    </div>

    <!-- Alertes importantes -->
    @if($criticalAlerts > 0)
    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700 font-medium">Alertes critiques !</p>
                <p class="text-sm text-red-700 mt-1">
                    {{ $criticalAlerts }} produit(s) en rupture de stock nécessitent une attention immédiate.
                    <a href="{{ route('alerts.stock') }}" class="underline hover:text-red-800">Voir toutes les alertes</a>
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- KPIs principaux -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Chiffre d'affaires du jour -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">CA Aujourd'hui</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($todaySales ?? 0, 0, ',', ' ') }} €</p>
                    @if(isset($yesterdaySales) && $yesterdaySales > 0)
                    @php $salesGrowth = (($todaySales - $yesterdaySales) / $yesterdaySales) * 100; @endphp
                    <p class="text-xs {{ $salesGrowth >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $salesGrowth >= 0 ? '+' : '' }}{{ number_format($salesGrowth, 1) }}% vs hier
                    </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Commandes en cours -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Commandes en cours</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $pendingOrders ?? 0 }}</p>
                    <p class="text-xs text-gray-600">{{ $pendingOrdersValue ?? 0 }} € en attente</p>
                </div>
            </div>
        </div>

        <!-- Stock total -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Valeur Stock Total</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalStockValue ?? 0, 0, ',', ' ') }} €</p>
                    <p class="text-xs text-gray-600">{{ $totalProducts ?? 0 }} produits différents</p>
                </div>
            </div>
        </div>

        <!-- Alertes -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="bg-red-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Alertes Stock</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stockAlerts ?? 0 }}</p>
                    <p class="text-xs text-red-600">{{ $criticalAlerts ?? 0 }} ruptures critiques</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Graphique des ventes -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Ventes des 7 derniers jours</h2>
                <a href="{{ route('reports.sales') }}" class="text-sm text-blue-600 hover:text-blue-800">Voir le rapport complet →</a>
            </div>
            
            <!-- Graphique simple avec CSS -->
            <div class="space-y-3">
                @foreach($salesChart ?? [] as $day)
                <div class="flex items-center space-x-3">
                    <div class="w-16 text-xs text-gray-600">{{ $day['label'] }}</div>
                    <div class="flex-1 bg-gray-200 rounded-full h-4 relative">
                        @php $percentage = $day['max'] > 0 ? ($day['value'] / $day['max']) * 100 : 0; @endphp
                        <div class="bg-blue-600 h-4 rounded-full transition-all duration-500" 
                             style="width: {{ $percentage }}%"></div>
                        <span class="absolute right-2 top-0 text-xs text-white leading-4">
                            {{ number_format($day['value'], 0, ',', ' ') }} €
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Alertes de stock -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Alertes Stock</h2>
                <a href="{{ route('alerts.stock') }}" class="text-sm text-blue-600 hover:text-blue-800">Voir toutes →</a>
            </div>
            
            <div class="space-y-3">
                @forelse($lowStockProducts ?? [] as $product)
                <div class="flex items-center justify-between p-3 {{ $product['stock'] <= 0 ? 'bg-red-50 border border-red-200' : 'bg-yellow-50 border border-yellow-200' }} rounded-lg">
                    <div class="flex items-center">
                        @if($product['image'])
                        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" 
                             class="h-8 w-8 rounded object-cover mr-3">
                        @endif
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $product['name'] }}</p>
                            <p class="text-xs {{ $product['stock'] <= 0 ? 'text-red-600' : 'text-yellow-600' }}">
                                {{ $product['stock'] <= 0 ? 'Rupture' : 'Stock bas' }}
                            </p>
                        </div>
                    </div>
                    <span class="text-sm font-bold {{ $product['stock'] <= 0 ? 'text-red-600' : 'text-yellow-600' }}">
                        {{ $product['stock'] }}
                    </span>
                </div>
                @empty
                <div class="text-center py-4">
                    <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <p class="text-sm text-gray-500">Aucune alerte stock !</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Activité récente et Actions rapides -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Activité récente -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Activité Récente</h2>
                <a href="{{ route('activity.index') }}" class="text-sm text-blue-600 hover:text-blue-800">Voir tout →</a>
            </div>
            
            <div class="space-y-4">
                @forelse($recentActivities ?? [] as $activity)
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        @php
                        $activityConfig = [
                            'sale' => ['bg-green-100', 'text-green-600', 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-7a2 2 0 00-2-2H9a2 2 0 00-2 2v7a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
                            'stock_entry' => ['bg-blue-100', 'text-blue-600', 'M12 6v6m0 0v6m0-6h6m-6 0H6'],
                            'stock_exit' => ['bg-red-100', 'text-red-600', 'M20 12H4'],
                            'order_created' => ['bg-purple-100', 'text-purple-600', 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            'user_login' => ['bg-gray-100', 'text-gray-600', 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z']
                        ];
                        $config = $activityConfig[$activity['type']] ?? ['bg-gray-100', 'text-gray-600', 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'];
                        @endphp
                        <div class="w-8 h-8 rounded-full {{ $config[0] }} flex items-center justify-center">
                            <svg class="w-4 h-4 {{ $config[1] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config[2] }}"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">{{ $activity['description'] }}</p>
                        <p class="text-sm text-gray-500">
                            Par {{ $activity['user'] }} - {{ $activity['time'] }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <p class="text-sm text-gray-500">Aucune activité récente</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Actions Rapides</h2>
            
            <div class="grid grid-cols-2 gap-4">
                @can('create products')
                <a href="{{ route('products.create') }}" 
                   class="flex flex-col items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 transition duration-200">
                    <svg class="w-8 h-8 text-blue-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span class="text-sm font-medium text-blue-700">Nouveau Produit</span>
                </a>
                @endcan

                @can('create customers')
                <a href="{{ route('customers.create') }}" 
                   class="flex flex-col items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg border border-green-200 transition duration-200">
                    <svg class="w-8 h-8 text-green-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-sm font-medium text-green-700">Nouveau Client</span>
                </a>
                @endcan

                @can('create suppliers')
                <a href="{{ route('suppliers.create') }}" 
                   class="flex flex-col items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg border border-purple-200 transition duration-200">
                    <svg class="w-8 h-8 text-purple-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span class="text-sm font-medium text-purple-700">Nouveau Fournisseur</span>
                </a>
                @endcan

                @can('create purchase_orders')
                <a href="{{ route('purchase-orders.create') }}" 
                   class="flex flex-col items-center p-4 bg-yellow-50 hover:bg-yellow-100 rounded-lg border border-yellow-200 transition duration-200">
                    <svg class="w-8 h-8 text-yellow-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="text-sm font-medium text-yellow-700">Commande Fournisseur</span>
                </a>
                @endcan

                @can('view reports')
                <a href="{{ route('reports.index') }}" 
                   class="flex flex-col items-center p-4 bg-indigo-50 hover:bg-indigo-100 rounded-lg border border-indigo-200 transition duration-200">
                    <svg class="w-8 h-8 text-indigo-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="text-sm font-medium text-indigo-700">Rapports</span>
                </a>
                @endcan

                @can('create stock_regularizations')
                <a href="{{ route('stock-regularizations.create') }}" 
                   class="flex flex-col items-center p-4 bg-orange-50 hover:bg-orange-100 rounded-lg border border-orange-200 transition duration-200">
                    <svg class="w-8 h-8 text-orange-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="text-sm font-medium text-orange-700">Régularisation</span>
                </a>
                @endcan
            </div>

            <!-- Liens supplémentaires -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="grid grid-cols-1 gap-2">
                    @can('view stock_movements')
                    <a href="{{ route('stock-movements.index') }}" 
                       class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        Mouvements de stock
                    </a>
                    @endcan

                    @can('view warehouses')
                    <a href="{{ route('warehouses.index') }}" 
                       class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Entrepôts
                    </a>
                    @endcan

                    @can('view sales')
                    <a href="{{ route('sales.index') }}" 
                       class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-7a2 2 0 00-2-2H9a2 2 0 00-2 2v7a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Ventes
                    </a>
                    @endcan

                    @can('view users')
                    <a href="{{ route('users.index') }}" 
                       class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-lg">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Utilisateurs
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <!-- Raccourcis rapides par rôle -->
    @if(auth()->user()->hasRole(['administrateur']))
    <div class="mt-6 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg border border-blue-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Administration
        </h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('system-settings.index') }}" 
               class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-300 transition duration-200">
                <svg class="w-5 h-5 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700">Paramètres</span>
            </a>

            <a href="{{ route('backups.index') }}" 
               class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-300 transition duration-200">
                <svg class="w-5 h-5 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                <span class="text-sm font-medium text-gray-700">Sauvegardes</span>
            </a>

            <a href="{{ route('activity.logs') }}" 
               class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-300 transition duration-200">
                <svg class="w-5 h-5 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <span class="text-sm font-medium text-gray-700">Logs d'activité</span>
            </a>

            <a href="{{ route('performance.monitor') }}" 
               class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-300 transition duration-200">
                <svg class="w-5 h-5 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span class="text-sm font-medium text-gray-700">Performance</span>
            </a>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
// Actualisation automatique des KPIs toutes les 5 minutes
setInterval(function() {
    fetch('/api/dashboard/kpis')
        .then(response => response.json())
        .then(data => {
            // Mettre à jour les KPIs sans recharger la page
            if (data.todaySales !== undefined) {
                document.querySelector('.today-sales').textContent = 
                    new Intl.NumberFormat('fr-FR').format(data.todaySales) + ' €';
            }
            if (data.pendingOrders !== undefined) {
                document.querySelector('.pending-orders').textContent = data.pendingOrders;
            }
            if (data.stockAlerts !== undefined) {
                document.querySelector('.stock-alerts').textContent = data.stockAlerts;
            }
        })
        .catch(error => console.error('Erreur lors de la mise à jour des KPIs:', error));
}, 5 * 60 * 1000); // 5 minutes

// Animation des barres de progression
document.addEventListener('DOMContentLoaded', function() {
    const progressBars = document.querySelectorAll('.bg-blue-600');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 100);
    });
});
</script>
@endpush
@endsection