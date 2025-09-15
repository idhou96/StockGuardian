@extends('layouts.app')

@section('title', $product->name)
@section('page-title', 'Détail du produit')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- En-tête avec actions -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $product->name }}</h1>
                    <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                        <span>Code: <span class="font-medium">{{ $product->code }}</span></span>
                        <span>•</span>
                        <span>Famille: <span class="font-medium">{{ $product->family->name ?? 'N/A' }}</span></span>
                        <span>•</span>
                        <span>Créé le {{ $product->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
                
                @if(in_array(auth()->user()->role, ['administrateur', 'magasinier']))
                <div class="flex items-center space-x-3">
                    <a href="{{ route('products.edit', $product) }}" 
                       class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Modifier
                    </a>
                    
                    <form method="POST" action="{{ route('products.destroy', $product) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Supprimer
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>

        <!-- Statut et badges -->
        <div class="px-6 py-4">
            <div class="flex items-center space-x-4">
                @if($product->is_active)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Produit actif
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        Produit inactif
                    </span>
                @endif

                @if($product->requires_prescription)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Sur ordonnance
                    </span>
                @endif

                @if($product->is_dangerous)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        Produit dangereux
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Grille principale -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations générales -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Détails du produit -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informations générales</h3>
                </div>
                <div class="px-6 py-4">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nom du produit</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $product->name }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Code produit</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white font-mono">{{ $product->code }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Famille</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $product->family->name ?? 'N/A' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Fournisseur principal</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                @if($product->supplier)
                                    <a href="{{ route('suppliers.show', $product->supplier) }}" class="text-primary-600 hover:text-primary-500">
                                        {{ $product->supplier->name }}
                                    </a>
                                @else
                                    Non défini
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Unité de mesure</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $product->unit }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Taux de TVA</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $product->tax_rate }}%</dd>
                        </div>

                        @if($product->description)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $product->description }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Prix et marges -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Prix et marges</h3>
                </div>
                <div class="px-6 py-4">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Prix d'achat</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">
                                {{ number_format($product->purchase_price, 0, ',', ' ') }} FCFA
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Prix de vente</dt>
                            <dd class="mt-1 text-lg font-semibold text-primary-600 dark:text-primary-400">
                                {{ number_format($product->sale_price, 0, ',', ' ') }} FCFA
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Marge</dt>
                            @php
                                $margin = $product->sale_price - $product->purchase_price;
                                $marginPercentage = $product->purchase_price > 0 ? ($margin / $product->purchase_price) * 100 : 0;
                                $marginClass = $margin > 0 ? 'text-green-600 dark:text-green-400' : ($margin < 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400');
                            @endphp
                            <dd class="mt-1 text-lg font-semibold {{ $marginClass }}">
                                {{ number_format($margin, 0, ',', ' ') }} FCFA
                                <div class="text-sm font-normal">{{ number_format($marginPercentage, 1) }}%</div>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Stock par entrepôt -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Stock par entrepôt</h3>
                </div>
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Entrepôt
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Quantité
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Valeur
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($product->warehouseStocks as $stock)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <a href="{{ route('warehouses.show', $stock->warehouse) }}" class="text-primary-600 hover:text-primary-500">
                                        {{ $stock->warehouse->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $stock->quantity }} {{ $product->unit }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($stock->quantity <= 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                            Rupture
                                        </span>
                                    @elseif($stock->quantity <= $product->stock_alert)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                            Stock faible
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Stock OK
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ number_format($stock->quantity * $product->purchase_price, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                    Aucun stock enregistré pour ce produit
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if($product->warehouseStocks->count() > 0)
                        <tfoot class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 dark:text-white uppercase tracking-wider">
                                    Total
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 dark:text-white">
                                    {{ $product->getTotalStock() }} {{ $product->unit }}
                                </th>
                                <th class="px-6 py-3"></th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-900 dark:text-white">
                                    {{ number_format($product->getTotalStock() * $product->purchase_price, 0, ',', ' ') }} FCFA
                                </th>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Colonne de droite - Résumé et actions -->
        <div class="space-y-6">
            <!-- Résumé du stock -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Résumé du stock</h3>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <!-- Stock total -->
                    <div class="flex items-center justify-between">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Stock total</dt>
                        <dd class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $product->getTotalStock() }}
                            <span class="text-sm font-normal text-gray-500 dark:text-gray-400">{{ $product->unit }}</span>
                        </dd>
                    </div>

                    <!-- Seuil d'alerte -->
                    <div class="flex items-center justify-between">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Seuil d'alerte</dt>
                        <dd class="text-sm text-gray-900 dark:text-white">{{ $product->stock_alert }} {{ $product->unit }}</dd>
                    </div>

                    <!-- Stock minimum -->
                    @if($product->stock_min)
                    <div class="flex items-center justify-between">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Stock minimum</dt>
                        <dd class="text-sm text-gray-900 dark:text-white">{{ $product->stock_min }} {{ $product->unit }}</dd>
                    </div>
                    @endif

                    <!-- Valeur du stock -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Valeur totale</dt>
                        <dd class="text-lg font-semibold text-primary-600 dark:text-primary-400">
                            {{ number_format($product->getTotalStock() * $product->purchase_price, 0, ',', ' ') }} FCFA
                        </dd>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Actions rapides</h3>
                </div>
                <div class="px-6 py-4 space-y-3">
                    @if(in_array(auth()->user()->role, ['administrateur', 'magasinier']))
                    <!-- Entrée de stock -->
                    <a href="{{ route('stock-movements.create-entry') }}?product_id={{ $product->id }}" 
                       class="w-full flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Entrée de stock
                    </a>

                    <!-- Sortie de stock -->
                    <a href="{{ route('stock-movements.create-exit') }}?product_id={{ $product->id }}" 
                       class="w-full flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                        </svg>
                        Sortie de stock
                    </a>
                    @endif

                    <!-- Voir mouvements -->
                    <a href="{{ route('stock-movements.index') }}?product_id={{ $product->id }}" 
                       class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Voir les mouvements
                    </a>

                    @if(in_array(auth()->user()->role, ['administrateur', 'responsable_achats']))
                    <!-- Commande fournisseur -->
                    <a href="{{ route('purchase-orders.create') }}?product_id={{ $product->id }}" 
                       class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11H6a2 2 0 00-2 2v6a2 2 0 002 2h2M16 11h2a2 2 0 012 2v6a2 2 0 01-2 2h-2"/>
                        </svg>
                        Commander
                    </a>
                    @endif
                </div>
            </div>

            <!-- Historique récent -->
            @if($recentMovements && $recentMovements->count() > 0)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Mouvements récents</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            @foreach($recentMovements->take(5) as $movement)
                            <li class="relative pb-8">
                                @if(!$loop->last)
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full {{ $movement->type === 'entree' ? 'bg-green-500' : 'bg-red-500' }} flex items-center justify-center ring-8 ring-white dark:ring-gray-800">
                                            @if($movement->type === 'entree')
                                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            @else
                                            <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                <span class="font-medium text-gray-900 dark:text-white">
                                                    {{ ucfirst($movement->type) }}
                                                </span>
                                                de {{ $movement->quantity }} {{ $product->unit }}
                                            </p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $movement->reason }}</p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                            <time datetime="{{ $movement->created_at->format('Y-m-d') }}">
                                                {{ $movement->created_at->format('d/m/Y') }}
                                            </time>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('stock-movements.index') }}?product_id={{ $product->id }}" class="w-full flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Voir tous les mouvements
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection