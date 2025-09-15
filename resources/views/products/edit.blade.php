@extends('layouts.app')

@section('title', 'Modifier ' . $product->name)
@section('page-title', 'Modifier le produit')

@section('content')
<div class="max-w-4xl mx-auto">
    <form method="POST" action="{{ route('products.update', $product) }}" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- En-tête avec actions -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                            Modifier {{ $product->name }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Code: {{ $product->code }} • Créé le {{ $product->created_at->format('d/m/Y') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('products.show', $product) }}" 
                           class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            Voir
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations générales -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Informations générales</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Modifiez les informations de base du produit
                </p>
            </div>
            
            <div class="px-6 py-4 space-y-6">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                    <!-- Nom du produit -->
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Nom du produit <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ old('name', $product->name) }}"
                                   required
                                   class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white @error('name') border-red-300 @enderror">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Code produit -->
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Code produit <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="code" 
                                   id="code" 
                                   value="{{ old('code', $product->code) }}"
                                   required
                                   class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white @error('code') border-red-300 @enderror">
                            @error('code')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Code unique d'identification</p>
                    </div>

                    <!-- Famille -->
                    <div>
                        <label for="family_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Famille <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <select name="family_id" 
                                    id="family_id"
                                    required
                                    class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white @error('family_id') border-red-300 @enderror">
                                <option value="">Sélectionnez une famille</option>
                                @foreach($families ?? [] as $family)
                                    <option value="{{ $family->id }}" {{ old('family_id', $product->family_id) == $family->id ? 'selected' : '' }}>
                                        {{ $family->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('family_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Fournisseur -->
                    <div>
                        <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Fournisseur principal
                        </label>
                        <div class="mt-1">
                            <select name="supplier_id" 
                                    id="supplier_id"
                                    class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white @error('supplier_id') border-red-300 @enderror">
                                <option value="">Aucun fournisseur</option>
                                @foreach($suppliers ?? [] as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Unité de mesure -->
                    <div>
                        <label for="unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Unité de mesure <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <select name="unit" 
                                    id="unit"
                                    required
                                    class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white @error('unit') border-red-300 @enderror">
                                <option value="">Sélectionnez une unité</option>
                                @php
                                    $units = ['comprimé', 'boîte', 'flacon', 'tube', 'ampoule', 'gélule', 'ml', 'g', 'pièce'];
                                @endphp
                                @foreach($units as $unit)
                                <option value="{{ $unit }}" {{ old('unit', $product->unit) == $unit ? 'selected' : '' }}>
                                    {{ ucfirst($unit) }}
                                </option>
                                @endforeach
                            </select>
                            @error('unit')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="sm:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Description
                        </label>
                        <div class="mt-1">
                            <textarea name="description" 
                                      id="description" 
                                      rows="3"
                                      class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white @error('description') border-red-300 @enderror">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Description détaillée du produit (optionnel)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prix et coûts -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Prix et coûts</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Modifiez les prix d'achat et de vente
                </p>
            </div>
            
            <div class="px-6 py-4 space-y-6">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-3">
                    <!-- Prix d'achat -->
                    <div>
                        <label for="purchase_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Prix d'achat (FCFA) <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 relative">
                            <input type="number" 
                                   name="purchase_price" 
                                   id="purchase_price" 
                                   step="0.01"
                                   min="0"
                                   value="{{ old('purchase_price', $product->purchase_price) }}"
                                   required
                                   class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white @error('purchase_price') border-red-300 @enderror">
                            @error('purchase_price')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Prix actuel: {{ number_format($product->purchase_price, 0, ',', ' ') }} FCFA</p>
                    </div>

                    <!-- Prix de vente -->
                    <div>
                        <label for="sale_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Prix de vente (FCFA) <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input type="number" 
                                   name="sale_price" 
                                   id="sale_price" 
                                   step="0.01"
                                   min="0"
                                   value="{{ old('sale_price', $product->sale_price) }}"
                                   required
                                   class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white @error('sale_price') border-red-300 @enderror">
                            @error('sale_price')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Prix actuel: {{ number_format($product->sale_price, 0, ',', ' ') }} FCFA</p>
                    </div>

                    <!-- Marge calculée -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Marge calculée
                        </label>
                        <div class="mt-1 p-2 bg-gray-50 dark:bg-gray-600 rounded-md">
                            <span id="margin-display" class="text-sm font-medium text-gray-900 dark:text-white">
                                @php
                                    $currentMargin = $product->sale_price - $product->purchase_price;
                                    $currentMarginPercentage = $product->purchase_price > 0 ? ($currentMargin / $product->purchase_price) * 100 : 0;
                                @endphp
                                {{ number_format($currentMargin, 0, ',', ' ') }} FCFA ({{ number_format($currentMarginPercentage, 1) }}%)
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Marge actuelle</p>
                    </div>

                    <!-- Taux de TVA -->
                    <div>
                        <label for="tax_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Taux de TVA (%)
                        </label>
                        <div class="mt-1">
                            <input type="number" 
                                   name="tax_rate" 
                                   id="tax_rate" 
                                   step="0.01"
                                   min="0"
                                   max="100"
                                   value="{{ old('tax_rate', $product->tax_rate) }}"
                                   class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white @error('tax_rate') border-red-300 @enderror">
                            @error('tax_rate')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gestion des stocks -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Gestion des stocks</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Modifiez les seuils d'alerte et les paramètres de stock
                </p>
            </div>
            
            <div class="px-6 py-4 space-y-6">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                    <!-- Stock actuel (lecture seule) -->
                    <div class="sm:col-span-2 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-blue-800 dark:text-blue-200">Stock actuel total</h4>
                                <p class="text-xs text-blue-600 dark:text-blue-300 mt-1">
                                    Réparti dans {{ $product->warehouseStocks->count() }} entrepôt(s)
                                </p>
                            </div>
                            <div class="text-2xl font-bold text-blue-800 dark:text-blue-200">
                                {{ $product->getTotalStock() }} {{ $product->unit }}
                            </div>
                        </div>
                    </div>

                    <!-- Seuil d'alerte stock -->
                    <div>
                        <label for="stock_alert" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Seuil d'alerte stock <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input type="number" 
                                   name="stock_alert" 
                                   id="stock_alert" 
                                   min="0"
                                   value="{{ old('stock_alert', $product->stock_alert) }}"
                                   required
                                   class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white @error('stock_alert') border-red-300 @enderror">
                            @error('stock_alert')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Quantité en dessous de laquelle une alerte sera générée</p>
                    </div>

                    <!-- Stock minimum -->
                    <div>
                        <label for="stock_min" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Stock minimum
                        </label>
                        <div class="mt-1">
                            <input type="number" 
                                   name="stock_min" 
                                   id="stock_min" 
                                   min="0"
                                   value="{{ old('stock_min', $product->stock_min) }}"
                                   class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white @error('stock_min') border-red-300 @enderror">
                            @error('stock_min')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Propriétés spéciales -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Propriétés spéciales</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Modifiez les caractéristiques particulières du produit
                </p>
            </div>
            
            <div class="px-6 py-4 space-y-6">
                <div class="space-y-4">
                    <!-- Checkboxes -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="requires_prescription" 
                                   name="requires_prescription" 
                                   type="checkbox" 
                                   value="1"
                                   {{ old('requires_prescription', $product->requires_prescription) ? 'checked' : '' }}
                                   class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 dark:border-gray-600 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="requires_prescription" class="font-medium text-gray-700 dark:text-gray-300">
                                Nécessite une ordonnance
                            </label>
                            <p class="text-gray-500 dark:text-gray-400">Ce produit ne peut être vendu qu'avec une ordonnance médicale</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="is_dangerous" 
                                   name="is_dangerous" 
                                   type="checkbox" 
                                   value="1"
                                   {{ old('is_dangerous', $product->is_dangerous) ? 'checked' : '' }}
                                   class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 dark:border-gray-600 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_dangerous" class="font-medium text-gray-700 dark:text-gray-300">
                                Produit dangereux
                            </label>
                            <p class="text-gray-500 dark:text-gray-400">Produit nécessitant des précautions particulières</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="is_consumable" 
                                   name="is_consumable" 
                                   type="checkbox" 
                                   value="1"
                                   {{ old('is_consumable', $product->is_consumable) ? 'checked' : '' }}
                                   class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 dark:border-gray-600 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_consumable" class="font-medium text-gray-700 dark:text-gray-300">
                                Produit consommable
                            </label>
                            <p class="text-gray-500 dark:text-gray-400">Ce produit peut être consommé/utilisé</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="is_active" 
                                   name="is_active" 
                                   type="checkbox" 
                                   value="1"
                                   {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                                   class="focus:ring-primary-500 h-4 w-4 text-primary-600 border-gray-300 dark:border-gray-600 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_active" class="font-medium text-gray-700 dark:text-gray-300">
                                Produit actif
                            </label>
                            <p class="text-gray-500 dark:text-gray-400">Le produit peut être utilisé dans les ventes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('products.show', $product) }}" 
               class="bg-white dark:bg-gray-800 py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Annuler
            </a>
            
            <button type="submit" 
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calcul de la marge en temps réel
    const purchasePriceInput = document.getElementById('purchase_price');
    const salePriceInput = document.getElementById('sale_price');
    const marginDisplay = document.getElementById('margin-display');
    
    function calculateMargin() {
        const purchasePrice = parseFloat(purchasePriceInput.value) || 0;
        const salePrice = parseFloat(salePriceInput.value) || 0;
        
        const margin = salePrice - purchasePrice;
        const marginPercentage = purchasePrice > 0 ? (margin / purchasePrice) * 100 : 0;
        
        marginDisplay.textContent = `${margin.toLocaleString('fr-FR')} FCFA (${marginPercentage.toFixed(1)}%)`;
        
        // Changer la couleur selon la marge
        marginDisplay.className = 'text-sm font-medium ';
        if (margin < 0) {
            marginDisplay.className += 'text-red-600 dark:text-red-400';
        } else if (margin === 0) {
            marginDisplay.className += 'text-gray-600 dark:text-gray-400';
        } else {
            marginDisplay.className += 'text-green-600 dark:text-green-400';
        }
    }
    
    purchasePriceInput.addEventListener('input', calculateMargin);
    salePriceInput.addEventListener('input', calculateMargin);
    
    // Calcul initial
    calculateMargin();
});
</script>
@endpush