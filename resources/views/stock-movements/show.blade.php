{{-- resources/views/stock-movements/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détails du Mouvement #' . $movement->reference)

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
                    <a href="{{ route('stock-movements.index') }}" class="ml-1 md:ml-2 text-sm font-medium text-gray-700 hover:text-blue-600">Mouvements</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 md:ml-2 text-sm font-medium text-gray-500">{{ $movement->reference }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div class="flex items-center space-x-4">
            <div class="{{ $movement->type === 'entree' ? 'bg-green-100' : 'bg-red-100' }} rounded-full p-3">
                @if($movement->type === 'entree')
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                @else
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                </svg>
                @endif
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $movement->reference }}</h1>
                <p class="text-gray-600 mt-1">
                    {{ $movement->type === 'entree' ? 'Entrée' : 'Sortie' }} de stock - 
                    {{ $movement->movement_date->format('d/m/Y à H:i') }}
                </p>
            </div>
        </div>
        
        <div class="flex space-x-3">
            @can('cancel stock_movements')
            @if(!$movement->cancelled_at && $movement->created_at->diffInHours(now()) < 24)
            <button onclick="cancelMovement({{ $movement->id }})" 
                    class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span>Annuler</span>
            </button>
            @endif
            @endcan

            <button onclick="window.print()" 
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Imprimer</span>
            </button>

            <a href="{{ route('stock-movements.index') }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                </svg>
                <span>Retour</span>
            </a>
        </div>
    </div>

    <!-- Statut du mouvement -->
    @if($movement->cancelled_at)
    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700 font-medium">Mouvement annulé</p>
                <p class="text-sm text-red-700 mt-1">
                    Annulé le {{ $movement->cancelled_at->format('d/m/Y à H:i') }}
                    @if($movement->cancellation_reason)
                    - Raison: {{ $movement->cancellation_reason }}
                    @endif
                </p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Détails du produit -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Produit concerné
                </h2>
                
                <div class="flex items-start space-x-4">
                    @if($movement->product->image_url)
                    <img src="{{ $movement->product->image_url }}" alt="{{ $movement->product->name }}" 
                         class="w-20 h-20 rounded-lg object-cover border border-gray-200">
                    @else
                    <div class="w-20 h-20 rounded-lg bg-gray-200 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    @endif
                    
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $movement->product->name }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                            <div>
                                <span class="font-medium">Code-barres:</span>
                                <span class="ml-2">{{ $movement->product->barcode }}</span>
                            </div>
                            <div>
                                <span class="font-medium">Famille:</span>
                                <span class="ml-2">{{ $movement->product->family->name ?? 'N/A' }}</span>
                            </div>
                            @if($movement->product->active_principle)
                            <div>
                                <span class="font-medium">Principe actif:</span>
                                <span class="ml-2">{{ $movement->product->activePrinciple->name }}</span>
                            </div>
                            @endif
                            <div>
                                <span class="font-medium">Prix de vente:</span>
                                <span class="ml-2">{{ number_format($movement->product->selling_price, 2, ',', ' ') }} €</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock actuel -->
                <div class="mt-6 bg-gray-50 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-3">Stock actuel dans {{ $movement->warehouse->name }}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600">{{ $currentStock ?? 0 }}</p>
                            <p class="text-sm text-gray-500">Quantité actuelle</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600">{{ $movement->product->minimum_stock ?? 0 }}</p>
                            <p class="text-sm text-gray-500">Stock minimum</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-purple-600">{{ $movement->product->warehouses->sum('pivot.current_stock') ?? 0 }}</p>
                            <p class="text-sm text-gray-500">Stock total</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Détails du mouvement -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Détails de l'opération
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type de mouvement</label>
                        <div class="flex items-center">
                            @if($movement->type === 'entree')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Entrée de stock
                            </span>
                            @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                                Sortie de stock
                            </span>
                            @endif
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantité</label>
                        <p class="text-lg font-semibold {{ $movement->type === 'entree' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $movement->type === 'entree' ? '+' : '-' }}{{ $movement->quantity }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Raison</label>
                        <p class="text-gray-900 capitalize">{{ str_replace('_', ' ', $movement->reason) }}</p>
                    </div>

                    @if($movement->unit_price)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Prix unitaire</label>
                        <p class="text-gray-900">{{ number_format($movement->unit_price, 2, ',', ' ') }} €</p>
                    </div>
                    @endif

                    @if($movement->batch_number)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Numéro de lot</label>
                        <p class="text-gray-900 font-mono">{{ $movement->batch_number }}</p>
                    </div>
                    @endif

                    @if($movement->expiration_date)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date d'expiration</label>
                        <p class="text-gray-900">{{ $movement->expiration_date->format('d/m/Y') }}</p>
                        @if($movement->expiration_date->isPast())
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1">
                            Expiré
                        </span>
                        @elseif($movement->expiration_date->diffInDays(now()) <= 30)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                            Expire bientôt
                        </span>
                        @endif
                    </div>
                    @endif

                    @if($movement->document_reference)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Référence document</label>
                        <p class="text-gray-900 font-mono">{{ $movement->document_reference }}</p>
                    </div>
                    @endif

                    @if($movement->destination)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Destination</label>
                        <p class="text-gray-900">{{ $movement->destination }}</p>
                    </div>
                    @endif
                </div>

                @if($movement->notes)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ $movement->notes }}</p>
                </div>
                @endif

                <!-- Valeur totale -->
                @if($movement->unit_price)
                <div class="mt-6 bg-blue-50 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-medium text-gray-900">Valeur totale:</span>
                        <span class="text-2xl font-bold text-blue-600">
                            {{ number_format($movement->quantity * $movement->unit_price, 2, ',', ' ') }} €
                        </span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Colonne latérale -->
        <div class="space-y-6">
            <!-- Informations système -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Informations système
                </h2>
                
                <div class="space-y-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Référence:</span>
                        <span class="font-mono">{{ $movement->reference }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Utilisateur:</span>
                        <span>{{ $movement->user->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Créé le:</span>
                        <span>{{ $movement->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Dernière maj:</span>
                        <span>{{ $movement->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($movement->cancelled_at)
                    <div class="flex justify-between">
                        <span class="text-gray-500">Annulé le:</span>
                        <span class="text-red-600">{{ $movement->cancelled_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Entrepôt -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Entrepôt
                </h2>
                
                <div class="text-center">
                    <h3 class="font-semibold text-gray-900">{{ $movement->warehouse->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $movement->warehouse->location }}</p>
                    @if($movement->warehouse->manager)
                    <p class="text-sm text-gray-600 mt-2">
                        <span class="font-medium">Responsable:</span> {{ $movement->warehouse->manager }}
                    </p>
                    @endif
                    
                    <a href="{{ route('warehouses.show', $movement->warehouse) }}" 
                       class="inline-flex items-center px-3 py-2 mt-3 text-sm font-medium text-blue-600 bg-blue-100 rounded-lg hover:bg-blue-200 transition duration-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Voir l'entrepôt
                    </a>
                </div>
            </div>

            <!-- Mouvements liés -->
            @if($relatedMovements->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Mouvements liés
                </h2>
                
                <div class="space-y-3">
                    @foreach($relatedMovements as $relatedMovement)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $relatedMovement->reference }}</p>
                            <p class="text-xs text-gray-500">{{ $relatedMovement->movement_date->format('d/m/Y') }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-sm {{ $relatedMovement->type === 'entree' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $relatedMovement->type === 'entree' ? '+' : '-' }}{{ $relatedMovement->quantity }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
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
                    <a href="{{ route('products.show', $movement->product) }}" 
                       class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 py-2 px-4 rounded-lg text-sm flex items-center transition duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Voir le produit
                    </a>
                    
                    <a href="{{ route('stock-movements.index', ['product_id' => $movement->product->id]) }}" 
                       class="w-full bg-purple-50 hover:bg-purple-100 text-purple-700 py-2 px-4 rounded-lg text-sm flex items-center transition duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        Historique produit
                    </a>
                    
                    @can('create stock_movements')
                    @if($movement->type === 'entree')
                    <a href="{{ route('stock-movements.create-exit') }}?product_id={{ $movement->product->id }}&warehouse_id={{ $movement->warehouse->id }}" 
                       class="w-full bg-red-50 hover:bg-red-100 text-red-700 py-2 px-4 rounded-lg text-sm flex items-center transition duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                        </svg>
                        Créer une sortie
                    </a>
                    @else
                    <a href="{{ route('stock-movements.create-entry') }}?product_id={{ $movement->product->id }}&warehouse_id={{ $movement->warehouse->id }}" 
                       class="w-full bg-green-50 hover:bg-green-100 text-green-700 py-2 px-4 rounded-lg text-sm flex items-center transition duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Créer une entrée
                    </a>
                    @endif
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation d'annulation -->
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <svg class="mx-auto mb-4 w-14 h-14 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Confirmer l'annulation</h3>
            <p class="text-sm text-gray-500 mb-4">
                Êtes-vous sûr de vouloir annuler ce mouvement de stock ? 
                Cette action créera un mouvement inverse et ne peut pas être annulée.
            </p>
            <div class="flex justify-center space-x-4">
                <button id="confirmCancel" type="button" 
                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Oui, annuler
                </button>
                <button id="cancelCancel" type="button" 
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Non, garder
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function cancelMovement(movementId) {
    document.getElementById('cancelModal').classList.remove('hidden');
    
    document.getElementById('confirmCancel').onclick = function() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/stock-movements/${movementId}/cancel`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'POST';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    };
}

document.getElementById('cancelCancel').addEventListener('click', function() {
    document.getElementById('cancelModal').classList.add('hidden');
});

// Fermer modal en cliquant en dehors
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});
</script>
@endpush
@endsection