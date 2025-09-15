{{-- resources/views/warehouses/show.blade.php --}}
@extends('layouts.app')

@section('title', $warehouse->name)

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
                    <span class="ml-1 md:ml-2 text-sm font-medium text-gray-500">{{ $warehouse->name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header avec statut -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start mb-6 space-y-4 lg:space-y-0">
        <div class="flex items-center space-x-4">
            <div class="bg-blue-100 rounded-full p-3">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <div class="flex items-center space-x-3">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $warehouse->name }}</h1>
                    @php
                    $statusConfig = [
                        'active' => ['bg-green-100', 'text-green-800', 'Actif'],
                        'inactive' => ['bg-red-100', 'text-red-800', 'Inactif'],
                        'maintenance' => ['bg-yellow-100', 'text-yellow-800', 'Maintenance']
                    ];
                    $config = $statusConfig[$warehouse->status] ?? ['bg-gray-100', 'text-gray-800', ucfirst($warehouse->status)];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $config[0] }} {{ $config[1] }}">
                        {{ $config[2] }}
                    </span>
                </div>
                <p class="text-gray-600 mt-1 capitalize">
                    {{ str_replace('_', ' ', $warehouse->type) }} - {{ $warehouse->location }}
                </p>
            </div>
        </div>
        
        <div class="flex flex-wrap gap-3">
            @can('edit warehouses')
            <a href="{{ route('warehouses.edit', $warehouse) }}" 
               class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Modifier</span>
            </a>
            @endcan

            <a href="{{ route('warehouses.inventory', $warehouse) }}" 
               class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span>Inventaire</span>
            </a>

            <a href="{{ route('warehouses.report', $warehouse) }}" target="_blank"
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Rapport</span>
            </a>

            <a href="{{ route('warehouses.index') }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                </svg>
                <span>Retour</span>
            </a>
        </div>
    </div>

    <!-- Alertes -->
    @if($warehouse->status === 'maintenance')
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700 font-medium">Entrepôt en maintenance</p>
                <p class="text-sm text-yellow-700 mt-1">
                    Cet entrepôt est actuellement en maintenance. Les opérations de stock peuvent être limitées.
                </p>
            </div>
        </div>
    </div>
    @endif

    @if($lowStockAlerts > 0)
    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700 font-medium">Alertes de stock</p>
                <p class="text-sm text-red-700 mt-1">
                    {{ $lowStockAlerts }} produit(s) en rupture ou stock faible dans cet entrepôt.
                    <a href="#stock-alerts" class="underline hover:text-red-800">Voir les alertes</a>
                </p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informations de l'entrepôt -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Informations générales
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Code de référence</label>
                        <p class="text-gray-900 font-mono">{{ $warehouse->code }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type d'entrepôt</label>
                        <p class="text-gray-900 capitalize">{{ str_replace('_', ' ', $warehouse->type) }}</p>
                    </div>

                    @if($warehouse->capacity)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Capacité maximale</label>
                        <p class="text-gray-900">{{ number_format($warehouse->capacity) }} unités</p>
                    </div>
                    @endif

                    @if($warehouse->manager)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Responsable</label>
                        <p class="text-gray-900">{{ $warehouse->manager }}</p>
                    </div>
                    @endif

                    @if($warehouse->phone)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                        <p class="text-gray-900">{{ $warehouse->phone }}</p>
                    </div>
                    @endif

                    @if($warehouse->email)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <p class="text-gray-900">{{ $warehouse->email }}</p>
                    </div>
                    @endif

                    @if($warehouse->operating_hours)
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Horaires d'ouverture</label>
                        <p class="text-gray-900">{{ $warehouse->operating_hours }}</p>
                    </div>
                    @endif
                </div>

                @if($warehouse->description)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-gray-900">{{ $warehouse->description }}</p>
                    </div>
                </div>
                @endif

                <!-- Adresse complète -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-gray-900">{{ $warehouse->address }}</p>
                        <p class="text-gray-600 mt-1">{{ $warehouse->postal_code }} {{ $warehouse->city }}, {{ $warehouse->country }}</p>
                    </div>
                </div>
            </div>

            <!-- Stock de l'entrepôt -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Stock des produits ({{ $warehouse->warehouseStocks->count() }})
                    </h2>
                    <div class="flex space-x-2">
                        <a href="{{ route('stock-movements.create-entry') }}?warehouse_id={{ $warehouse->id }}"
                           class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Entrée
                        </a>
                        <a href="{{ route('stock-movements.create-exit') }}?warehouse_id={{ $warehouse->id }}"
                           class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                            Sortie
                        </a>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock actuel</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock min</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dernière maj</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($warehouse->warehouseStocks->sortByDesc('current_stock')->take(20) as $stock)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($stock->product->image_url)
                                        <img src="{{ $stock->product->image_url }}" alt="{{ $stock->product->name }}" 
                                             class="h-8 w-8 rounded object-cover mr-3">
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $stock->product->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $stock->product->barcode }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                    $currentStock = $stock->current_stock;
                                    $minStock = $stock->product->minimum_stock ?? 5;
                                    @endphp
                                    <span class="text-sm font-semibold {{ $currentStock <= 0 ? 'text-red-600' : ($currentStock <= $minStock ? 'text-yellow-600' : 'text-green-600') }}">
                                        {{ $currentStock }}
                                    </span>
                                    @if($currentStock <= 0)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 ml-2">
                                        Rupture
                                    </span>
                                    @elseif($currentStock <= $minStock)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 ml-2">
                                        Stock bas
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $minStock }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">
                                        {{ number_format($currentStock * ($stock->product->purchase_price ?? 0), 2, ',', ' ') }} €
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-500">{{ $stock->updated_at->format('d/m/Y H:i') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <a href="{{ route('products.show', $stock->product) }}" 
                                           class="text-blue-600 hover:text-blue-900 transition duration-200" title="Voir le produit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>
                                        <a href="{{ route('stock-movements.index', ['product_id' => $stock->product->id, 'warehouse_id' => $warehouse->id]) }}" 
                                           class="text-purple-600 hover:text-purple-900 transition duration-200" title="Voir les mouvements">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    <p class="text-gray-500">Aucun produit en stock dans cet entrepôt.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($warehouse->warehouseStocks->count() > 20)
                <div class="px-6 py-3 border-t border-gray-200 bg-gray-50">
                    <p class="text-sm text-gray-600 text-center">
                        Affichage des 20 premiers produits sur {{ $warehouse->warehouseStocks->count() }} total.
                        <a href="{{ route('warehouses.full-inventory', $warehouse) }}" class="text-blue-600 hover:text-blue-800 underline">
                            Voir l'inventaire complet
                        </a>
                    </p>
                </div>
                @endif
            </div>

            <!-- Mouvements récents -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        Mouvements récents
                    </h2>
                    <a href="{{ route('stock-movements.index', ['warehouse_id' => $warehouse->id]) }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm">Voir tout →</a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Raison</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($recentMovements as $movement)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $movement->movement_date->format('d/m/Y H:i') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($movement->type === 'entree')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Entrée
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                        </svg>
                                        Sortie
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($movement->product->image_url)
                                        <img src="{{ $movement->product->image_url }}" alt="{{ $movement->product->name }}" 
                                             class="h-6 w-6 rounded object-cover mr-2">
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $movement->product->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold {{ $movement->type === 'entree' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $movement->type === 'entree' ? '+' : '-' }}{{ $movement->quantity }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $movement->reason) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $movement->user->name }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    <p class="text-gray-500">Aucun mouvement récent.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Colonne latérale -->
        <div class="space-y-6">
            <!-- Statistiques de l'entrepôt -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Statistiques
                </h2>
                
                <div class="space-y-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-2xl font-bold text-blue-600">{{ $warehouse->warehouseStocks->count() }}</p>
                                <p class="text-sm text-blue-700">Produits stockés</p>
                            </div>
                            <div class="text-blue-500">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-2xl font-bold text-green-600">{{ number_format($warehouse->warehouseStocks->sum('current_stock')) }}</p>
                                <p class="text-sm text-green-700">Quantité totale</p>
                            </div>
                            <div class="text-green-500">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-purple-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                @php $totalValue = $warehouse->warehouseStocks->sum(function($stock) {
                                    return $stock->current_stock * ($stock->product->purchase_price ?? 0);
                                }); @endphp
                                <p class="text-2xl font-bold text-purple-600">{{ number_format($totalValue, 0, ',', ' ') }} €</p>
                                <p class="text-sm text-purple-700">Valeur stock</p>
                            </div>
                            <div class="text-purple-500">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    @if($warehouse->capacity)
                    <div class="bg-yellow-50 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                @php 
                                $currentTotal = $warehouse->warehouseStocks->sum('current_stock');
                                $usagePercentage = min(100, ($currentTotal / $warehouse->capacity) * 100); 
                                @endphp
                                <p class="text-2xl font-bold text-yellow-600">{{ number_format($usagePercentage, 1) }}%</p>
                                <p class="text-sm text-yellow-700">Capacité utilisée</p>
                            </div>
                            <div class="text-yellow-500">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php $colorClass = $usagePercentage > 90 ? 'bg-red-500' : ($usagePercentage > 70 ? 'bg-yellow-500' : 'bg-green-500'); @endphp
                            <div class="{{ $colorClass }} h-2 rounded-full transition-all duration-300" 
                                 style="width: {{ $usagePercentage }}%"></div>
                        </div>
                        <p class="text-xs text-yellow-600 mt-1">
                            {{ number_format($currentTotal) }} / {{ number_format($warehouse->capacity) }} unités
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Alertes de stock -->
            @if($lowStockAlerts > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6" id="stock-alerts">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    Alertes de stock
                </h2>
                
                <div class="space-y-3">
                    @foreach($warehouse->warehouseStocks->where('current_stock', '<=', 0)->take(5) as $stock)
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg border border-red-200">
                        <div class="flex items-center">
                            @if($stock->product->image_url)
                            <img src="{{ $stock->product->image_url }}" alt="{{ $stock->product->name }}" 
                                 class="h-8 w-8 rounded object-cover mr-3">
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $stock->product->name }}</p>
                                <p class="text-xs text-red-600">Rupture de stock</p>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-red-600">{{ $stock->current_stock }}</span>
                    </div>
                    @endforeach
                    
                    @foreach($warehouse->warehouseStocks->filter(function($stock) {
                        $minStock = $stock->product->minimum_stock ?? 5;
                        return $stock->current_stock > 0 && $stock->current_stock <= $minStock;
                    })->take(5) as $stock)
                    <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                        <div class="flex items-center">
                            @if($stock->product->image_url)
                            <img src="{{ $stock->product->image_url }}" alt="{{ $stock->product->name }}" 
                                 class="h-8 w-8 rounded object-cover mr-3">
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $stock->product->name }}</p>
                                <p class="text-xs text-yellow-600">Stock faible</p>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-yellow-600">{{ $stock->current_stock }}</span>
                    </div>
                    @endforeach
                </div>
                
                @if($lowStockAlerts > 10)
                <div class="mt-4 text-center">
                    <a href="{{ route('alerts.stock', ['warehouse_id' => $warehouse->id]) }}" 
                       class="text-sm text-blue-600 hover:text-blue-800">
                        Voir toutes les alertes ({{ $lowStockAlerts }})
                    </a>
                </div>
                @endif
            </div>
            @endif

            <!-- Actions rapides -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Actions rapides
                </h2>
                
                <div class="space-y-3">
                    <a href="{{ route('stock-movements.index', ['warehouse_id' => $warehouse->id]) }}" 
                       class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 py-2 px-4 rounded-lg text-sm flex items-center transition duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        Voir tous les mouvements
                    </a>
                    
                    <a href="{{ route('purchase-orders.create') }}?warehouse_id={{ $warehouse->id }}" 
                       class="w-full bg-purple-50 hover:bg-purple-100 text-purple-700 py-2 px-4 rounded-lg text-sm flex items-center transition duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Nouvelle commande fournisseur
                    </a>
                    
                    <a href="{{ route('stock-regularizations.create') }}?warehouse_id={{ $warehouse->id }}" 
                       class="w-full bg-orange-50 hover:bg-orange-100 text-orange-700 py-2 px-4 rounded-lg text-sm flex items-center transition duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Nouvelle régularisation
                    </a>

                    <a href="{{ route('warehouses.transfer', $warehouse) }}" 
                       class="w-full bg-green-50 hover:bg-green-100 text-green-700 py-2 px-4 rounded-lg text-sm flex items-center transition duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        Transfert vers autre entrepôt
                    </a>
                </div>
            </div>

            <!-- Informations système -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Informations système
                </h2>
                
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Créé le:</span>
                        <span>{{ $warehouse->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Dernière maj:</span>
                        <span>{{ $warehouse->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Dernier mouvement:</span>
                        <span>
                            @if($recentMovements->count() > 0)
                            {{ $recentMovements->first()->created_at->format('d/m/Y H:i') }}
                            @else
                            Aucun
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection