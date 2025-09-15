@extends('layouts.app')

@section('title', 'Nouveau Produit')
@section('page-title', 'Nouveau Produit')

@section('content')
<div class="max-w-4xl mx-auto">
    <form method="POST" action="{{ route('products.store') }}" class="space-y-8">
        @csrf

        <!-- En-tête -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Informations générales</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Renseignez les informations de base du produit
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
                                   value="{{ old('name') }}"
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
                                   value="{{ old('code') }}"
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
                                    <option value="{{ $family->id }}" {{ old('family_id') == $family->id ? 'selected' : '' }}>
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
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
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
                                <option value="comprimé" {{ old('unit') == 'comprimé' ? 'selected' : '' }}>Comprimé</option>
                                <option value="boîte" {{ old('unit') == 'boîte' ? 'selected' : '' }}>Boîte</option>
                                <option value="flacon" {{ old('unit') == 'flacon' ? 'selected' : '' }}>Flacon</option>
                                <option value="tube" {{ old('unit') == 'tube' ? 'selected' : '' }}>Tube</option>
                                <option value="ampoule" {{ old('unit') == 'ampoule' ? 'selected' : '' }}>Ampoule</option>
                                <option value="gélule" {{ old('unit') == 'gélule' ? 'selected' : '' }}>Gélule</option>
                                <option value="ml" {{ old('unit') == 'ml' ? 'selected' : '' }}>ml</option>
                                <option value="g" {{ old('unit') == 'g' ? 'selected' : '' }}>g</option>
                                <option value="pièce" {{ old('unit') == 'pièce' ? 'selected' : '' }}>Pièce</option>
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
                                      class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
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
                    Définissez les prix d'achat et de vente
                </p>
            </div>
            
            <div class="px-6 py-4 space-y-6">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-3">
                    <!-- Prix d'achat -->
                    <div>
                        <label for="purchase_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Prix d'achat (FCFA) <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input type="number" 
                                   name="purchase_price" 
                                   id="purchase_price" 
                                   step="0.01"
                                   min="0"
                                   value="{{ old('purchase_price') }}"
                                   required
                                   class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white @error('purchase_price') border-red-300 @enderror">
                            @error('purchase_price')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
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
                                   value="{{ old('sale_price') }}"
                                   required
                                   class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white @error('sale_price') border-red-300 @enderror">
                            @error('sale_price')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Marge calculée -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Marge calculée
                        </label>
                        <div class="mt-1 p-2 bg-gray-50 dark:bg-gray-600 rounded-md">
                            <span id="margin-display" class="text-sm font-medium text-gray-900 dark:text-white">
                                0 FCFA (0%)
                            </span>
                        </div>
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
                                   value="{{ old('tax_rate', '18') }}"
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
                    Configurez les seuils d'alerte et les paramètres de stock
                </p>
            </div>
            
            <div class="px-6 py-4 space-y-6">
                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
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
                                   value="{{ old('stock_alert', '10') }}"
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
                                   value="{{ old('stock_min', '5') }}"
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
                    Définissez les caractéristiques particulières du produit
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
                                   {{ old('requires_prescription') ? 'checked' : '' }}
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
                                   {{ old('is_dangerous') ? 'checked' : '' }}
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
                                   {{ old('is_consumable', true) ? 'checked' : '' }}
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
                                   {{ old('is_active', true) ? 'checked' : '' }}
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
            <a href="{{ route('products.index') }}" 
               class="bg-white dark:bg-gray-800 py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Annuler
            </a>
            
            <button type="submit" 
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Créer le produit
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
        if (margin < 0) {
            marginDisplay.className = 'text-sm font-medium text-red-600';
        } else if (margin === 0) {
            marginDisplay.className = 'text-sm font-medium text-gray-600';
        } else {
            marginDisplay.className = 'text-sm font-medium text-green-600';
        }
    }
    
    purchasePriceInput.addEventListener('input', calculateMargin);
    salePriceInput.addEventListener('input', calculateMargin);
    
    // Calcul initial
    calculateMargin();
    
    // Auto-génération du code produit si vide
    const nameInput = document.getElementById('name');
    const codeInput = document.getElementById('code');
    
    nameInput.addEventListener('input', function() {
        if (!codeInput.value) {
            // Générer un code basé sur les premières lettres du nom
            const name = this.value.toUpperCase();
            const words = name.split(' ');
            let code = '';
            
            words.forEach(word => {
                if (word.length > 0) {
                    code += word.substring(0, 3);
                }
            });
            
            // Ajouter un nombre aléatoire
            if (code) {
                code += Math.floor(Math.random() * 1000).toString().padStart(3, '0');
                codeInput.value = code.substring(0, 20); // Limiter à 20 caractères
            }
        }
    });
});
</script>
@endpush