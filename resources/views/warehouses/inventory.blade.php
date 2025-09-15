{{-- resources/views/warehouses/inventory.blade.php --}}
@extends('layouts.app')

@section('title', 'Inventaire - ' . $warehouse->name)

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
                    <a href="{{ route('warehouses.show', $warehouse) }}" class="ml-1 md:ml-2 text-sm font-medium text-gray-700 hover:text-blue-600">{{ $warehouse->name }}</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 md:ml-2 text-sm font-medium text-gray-500">Inventaire</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start mb-6 space-y-4 lg:space-y-0">
        <div class="flex items-center space-x-4">
            <div class="bg-purple-100 rounded-full p-3">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Inventaire - {{ $warehouse->name }}</h1>
                <p class="text-gray-600 mt-1">Stock complet et valorisation de l'entrepôt</p>
            </div>
        </div>
        
        <div class="flex flex-wrap gap-3">
            <button onclick="window.print()" 
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Imprimer</span>
            </button>

            <a href="{{ route('warehouses.inventory.export', $warehouse) }}" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Export Excel</span>
            </a>

            <a href="{{ route('stock-regularizations.create') }}?warehouse_id={{ $warehouse->id }}" 
               class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span>Régularisation</span>
            </a>

            <a href="{{ route('warehouses.show', $warehouse) }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                </svg>
                <span>Retour</span>
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Famille -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Famille</label>
                <select id="family_filter" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Toutes les familles</option>
                    @foreach($families as $family)
                    <option value="{{ $family->id }}">{{ $family->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Alerte stock -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alerte stock</label>
                <select id="stock_alert_filter" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">Tous les produits</option>
                    <option value="rupture">Rupture de stock</option>
                    <option value="stock_bas">Stock bas</option>
                    <option value="stock_ok">Stock OK</option>
                </select>
            </div>

            <!-- Recherche -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Recherche</label>
                <input type="text" id="search_filter" placeholder="Nom ou code-barres..."
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>

            <!-- Tri -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trier par</label>
                <select id="sort_filter" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="name">Nom (A-Z)</option>
                    <option value="stock_desc">Stock (décroissant)</option>
                    <option value="stock_asc">Stock (croissant)</option>
                    <option value="value_desc">Valeur (décroissant)</option>
                    <option value="value_asc">Valeur (croissant)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Résumé général -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Produits différents</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $stocks->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Quantité totale</p>
                    <p class="text-xl font-semibold text-gray-900">{{ number_format($stocks->sum('current_stock')) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Valeur d'achat</p>
                    @php $purchaseValue = $stocks->sum(function($stock) { return $stock->current_stock * ($stock->product->purchase_price ?? 0); }); @endphp
                    <p class="text-xl font-semibold text-gray-900">{{ number_format($purchaseValue, 0, ',', ' ') }} €</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Valeur de vente</p>
                    @php $saleValue = $stocks->sum(function($stock) { return $stock->current_stock * ($stock->product->selling_price ?? 0); }); @endphp
                    <p class="text-xl font-semibold text-gray-900">{{ number_format($saleValue, 0, ',', ' ') }} €</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertes de stock -->
    @php 
    $ruptureStock = $stocks->where('current_stock', '<=', 0);
    $stockBas = $stocks->filter(function($stock) {
        $minStock = $stock->product->minimum_stock ?? 5;
        return $stock->current_stock > 0 && $stock->current_stock <= $minStock;
    });
    @endphp

    @if($ruptureStock->count() > 0 || $stockBas->count() > 0)
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            Alertes de stock
        </h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if($ruptureStock->count() > 0)
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h3 class="font-medium text-red-800 mb-2">Ruptures de stock ({{ $ruptureStock->count() }})</h3>
                <div class="space-y-2 max-h-32 overflow-y-auto">
                    @foreach($ruptureStock->take(5) as $stock)
                    <div class="text-sm text-red-700">
                        • {{ $stock->product->name }}
                    </div>
                    @endforeach
                    @if($ruptureStock->count() > 5)
                    <div class="text-sm text-red-600 font-medium">
                        ... et {{ $ruptureStock->count() - 5 }} autres
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if($stockBas->count() > 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <h3 class="font-medium text-yellow-800 mb-2">Stock bas ({{ $stockBas->count() }})</h3>
                <div class="space-y-2 max-h-32 overflow-y-auto">
                    @foreach($stockBas->take(5) as $stock)
                    <div class="text-sm text-yellow-700">
                        • {{ $stock->product->name }} ({{ $stock->current_stock }})
                    </div>
                    @endforeach
                    @if($stockBas->count() > 5)
                    <div class="text-sm text-yellow-600 font-medium">
                        ... et {{ $stockBas->count() - 5 }} autres
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Table d'inventaire -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Détail de l'inventaire</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="inventory-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code-barres</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Famille</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock actuel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock min</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix achat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix vente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dernière maj</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="inventory-tbody">
                    @foreach($stocks as $stock)
                    <tr class="hover:bg-gray-50 inventory-row" 
                        data-family="{{ $stock->product->family_id }}" 
                        data-name="{{ strtolower($stock->product->name) }}" 
                        data-barcode="{{ strtolower($stock->product->barcode) }}"
                        data-stock="{{ $stock->current_stock }}"
                        data-min-stock="{{ $stock->product->minimum_stock ?? 5 }}"
                        data-purchase-value="{{ $stock->current_stock * ($stock->product->purchase_price ?? 0) }}"
                        data-sale-value="{{ $stock->current_stock * ($stock->product->selling_price ?? 0) }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($stock->product->image_url)
                                <img src="{{ $stock->product->image_url }}" alt="{{ $stock->product->name }}" 
                                     class="h-10 w-10 rounded object-cover mr-3">
                                @else
                                <div class="h-10 w-10 rounded bg-gray-200 flex items-center justify-center mr-3">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $stock->product->name }}</div>
                                    @if($stock->product->active_principle_id)
                                    <div class="text-xs text-gray-500">{{ $stock->product->activePrinciple->name ?? '' }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-mono text-gray-900">{{ $stock->product->barcode }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ $stock->product->family->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                            $currentStock = $stock->current_stock;
                            $minStock = $stock->product->minimum_stock ?? 5;
                            @endphp
                            <div class="flex items-center">
                                <span class="text-sm font-semibold mr-2 {{ $currentStock <= 0 ? 'text-red-600' : ($currentStock <= $minStock ? 'text-yellow-600' : 'text-green-600') }}">
                                    {{ $currentStock }}
                                </span>
                                @if($currentStock <= 0)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Rupture
                                </span>
                                @elseif($currentStock <= $minStock)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Stock bas
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ $minStock }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ number_format($stock->product->purchase_price ?? 0, 2, ',', ' ') }} €</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ number_format($stock->product->selling_price ?? 0, 2, ',', ' ') }} €</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php $stockValue = $currentStock * ($stock->product->purchase_price ?? 0); @endphp
                            <span class="text-sm font-semibold text-purple-600">{{ number_format($stockValue, 2, ',', ' ') }} €</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-500">{{ $stock->updated_at->format('d/m/Y') }}</span>
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
                                   class="text-purple-600 hover:text-purple-900 transition duration-200" title="Mouvements">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                </a>
                                @if($currentStock <= 0 || $currentStock <= $minStock)
                                <a href="{{ route('stock-movements.create-entry') }}?product_id={{ $stock->product->id }}&warehouse_id={{ $warehouse->id }}" 
                                   class="text-green-600 hover:text-green-900 transition duration-200" title="Entrée stock">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Résumé par famille -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Répartition par famille de produits</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($stocksByFamily as $familyId => $familyStocks)
            @php 
            $family = $families->find($familyId);
            $familyQuantity = $familyStocks->sum('current_stock');
            $familyValue = $familyStocks->sum(function($stock) { return $stock->current_stock * ($stock->product->purchase_price ?? 0); });
            @endphp
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-medium text-gray-900 mb-2">{{ $family->name ?? 'Non classé' }}</h3>
                <div class="space-y-1 text-sm text-gray-600">
                    <p><span class="font-medium">Produits:</span> {{ $familyStocks->count() }}</p>
                    <p><span class="font-medium">Quantité:</span> {{ number_format($familyQuantity) }}</p>
                    <p><span class="font-medium">Valeur:</span> {{ number_format($familyValue, 2, ',', ' ') }} €</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
// Variables pour les filtres
let allRows = document.querySelectorAll('.inventory-row');
let familyFilter = document.getElementById('family_filter');
let stockAlertFilter = document.getElementById('stock_alert_filter');
let searchFilter = document.getElementById('search_filter');
let sortFilter = document.getElementById('sort_filter');

// Event listeners pour les filtres
familyFilter.addEventListener('change', applyFilters);
stockAlertFilter.addEventListener('change', applyFilters);
searchFilter.addEventListener('input', debounce(applyFilters, 300));
sortFilter.addEventListener('change', applyFilters);

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function applyFilters() {
    const familyValue = familyFilter.value;
    const stockAlertValue = stockAlertFilter.value;
    const searchValue = searchFilter.value.toLowerCase();
    const sortValue = sortFilter.value;
    
    // Filtrer les lignes
    let visibleRows = Array.from(allRows).filter(row => {
        // Filtre par famille
        if (familyValue && row.dataset.family !== familyValue) {
            return false;
        }
        
        // Filtre par alerte de stock
        if (stockAlertValue) {
            const stock = parseInt(row.dataset.stock);
            const minStock = parseInt(row.dataset.minStock);
            
            if (stockAlertValue === 'rupture' && stock > 0) return false;
            if (stockAlertValue === 'stock_bas' && (stock <= 0 || stock > minStock)) return false;
            if (stockAlertValue === 'stock_ok' && stock <= minStock) return false;
        }
        
        // Filtre par recherche
        if (searchValue) {
            const name = row.dataset.name;
            const barcode = row.dataset.barcode;
            if (!name.includes(searchValue) && !barcode.includes(searchValue)) {
                return false;
            }
        }
        
        return true;
    });
    
    // Trier les lignes visibles
    visibleRows.sort((a, b) => {
        switch (sortValue) {
            case 'name':
                return a.dataset.name.localeCompare(b.dataset.name);
            case 'stock_desc':
                return parseInt(b.dataset.stock) - parseInt(a.dataset.stock);
            case 'stock_asc':
                return parseInt(a.dataset.stock) - parseInt(b.dataset.stock);
            case 'value_desc':
                return parseFloat(b.dataset.purchaseValue) - parseFloat(a.dataset.purchaseValue);
            case 'value_asc':
                return parseFloat(a.dataset.purchaseValue) - parseFloat(b.dataset.purchaseValue);
            default:
                return 0;
        }
    });
    
    // Masquer toutes les lignes
    allRows.forEach(row => row.style.display = 'none');
    
    // Afficher les lignes filtrées et triées
    const tbody = document.getElementById('inventory-tbody');
    visibleRows.forEach(row => {
        row.style.display = 'table-row';
        tbody.appendChild(row); // Réorganiser dans l'ordre trié
    });
    
    // Mettre à jour le compteur
    updateSummary(visibleRows);
}

function updateSummary(visibleRows) {
    // Calculer les totaux des lignes visibles
    let totalProducts = visibleRows.length;
    let totalQuantity = 0;
    let totalPurchaseValue = 0;
    let totalSaleValue = 0;
    
    visibleRows.forEach(row => {
        totalQuantity += parseInt(row.dataset.stock);
        totalPurchaseValue += parseFloat(row.dataset.purchaseValue);
        totalSaleValue += parseFloat(row.dataset.saleValue);
    });
    
    // Mettre à jour l'affichage (vous pouvez ajouter une section de résumé filtré si nécessaire)
    console.log('Produits visibles:', totalProducts);
    console.log('Quantité totale:', totalQuantity);
    console.log('Valeur d\'achat:', totalPurchaseValue);
    console.log('Valeur de vente:', totalSaleValue);
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    applyFilters(); // Appliquer les filtres par défaut
});
</script>
@endpush
@endsection