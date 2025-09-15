{{-- resources/views/stock-movements/create-exit.blade.php --}}
@extends('layouts.app')

@section('title', 'Nouvelle Sortie de Stock')

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
                    <span class="ml-1 md:ml-2 text-sm font-medium text-gray-500">Nouvelle Sortie</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                <div class="bg-red-100 rounded-full p-2 mr-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                </div>
                Nouvelle Sortie de Stock
            </h1>
            <p class="text-gray-600 mt-1">Enregistrer une nouvelle sortie de stock depuis l'entrepôt</p>
        </div>
        
        <a href="{{ route('stock-movements.index') }}" 
           class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
            </svg>
            <span>Retour</span>
        </a>
    </div>

    <!-- Alertes de stock -->
    <div id="stock_alerts" class="hidden mb-6">
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700 font-medium">Attention au stock !</p>
                    <div id="stock_warning_message" class="text-sm text-yellow-700 mt-1"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire -->
    <form action="{{ route('stock-movements.store-exit') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Informations générales -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Informations générales
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Date et heure -->
                <div>
                    <label for="movement_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Date et heure <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" id="movement_date" name="movement_date" 
                           value="{{ old('movement_date', now()->format('Y-m-d\TH:i')) }}" required
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('movement_date') border-red-500 @enderror">
                    @error('movement_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Entrepôt -->
                <div>
                    <label for="warehouse_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Entrepôt source <span class="text-red-500">*</span>
                    </label>
                    <select id="warehouse_id" name="warehouse_id" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('warehouse_id') border-red-500 @enderror">
                        <option value="">Choisir un entrepôt</option>
                        @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                            {{ $warehouse->name }} ({{ $warehouse->location }})
                        </option>
                        @endforeach
                    </select>
                    @error('warehouse_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Raison de la sortie -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Raison de la sortie <span class="text-red-500">*</span>
                    </label>
                    <select id="reason" name="reason" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('reason') border-red-500 @enderror">
                        <option value="">Choisir une raison</option>
                        <option value="vente" {{ old('reason') == 'vente' ? 'selected' : '' }}>
                            Vente
                        </option>
                        <option value="ajustement_inventaire" {{ old('reason') == 'ajustement_inventaire' ? 'selected' : '' }}>
                            Ajustement inventaire
                        </option>
                        <option value="perte" {{ old('reason') == 'perte' ? 'selected' : '' }}>
                            Perte
                        </option>
                        <option value="peremption" {{ old('reason') == 'peremption' ? 'selected' : '' }}>
                            Péremption
                        </option>
                        <option value="transfert_sortant" {{ old('reason') == 'transfert_sortant' ? 'selected' : '' }}>
                            Transfert sortant
                        </option>
                        <option value="autre" {{ old('reason') == 'autre' ? 'selected' : '' }}>
                            Autre
                        </option>
                    </select>
                    @error('reason')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Référence document -->
                <div>
                    <label for="document_reference" class="block text-sm font-medium text-gray-700 mb-2">
                        Référence document
                    </label>
                    <input type="text" id="document_reference" name="document_reference" 
                           value="{{ old('document_reference') }}"
                           placeholder="Ex: VTE2024-001, TR-456..."
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('document_reference') border-red-500 @enderror">
                    @error('document_reference')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Notes -->
            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Notes additionnelles
                </label>
                <textarea id="notes" name="notes" rows="3" 
                          placeholder="Informations complémentaires sur cette sortie de stock..."
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                @error('notes')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Sélection du produit -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Produit à sortir
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Recherche produit -->
                <div class="md:col-span-2">
                    <label for="product_search" class="block text-sm font-medium text-gray-700 mb-2">
                        Rechercher un produit <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" id="product_search" placeholder="Nom, code-barres ou référence du produit..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        
                        <!-- Liste des résultats -->
                        <div id="product_results" class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto hidden">
                            <!-- Les résultats seront ajoutés ici via JavaScript -->
                        </div>
                    </div>
                    
                    <!-- Produit sélectionné -->
                    <input type="hidden" id="product_id" name="product_id" value="{{ old('product_id') }}">
                    <div id="selected_product" class="mt-4 hidden">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <img id="product_image" src="" alt="" class="h-12 w-12 rounded-md object-cover mr-4">
                                    <div>
                                        <p id="product_name" class="text-sm font-medium text-gray-900"></p>
                                        <p id="product_barcode" class="text-sm text-gray-500"></p>
                                        <div class="flex space-x-4 mt-1">
                                            <p id="current_stock" class="text-sm font-medium"></p>
                                            <p id="warehouse_stock" class="text-sm text-red-600 font-medium"></p>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" onclick="clearProduct()" class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    @error('product_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Quantité -->
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                        Quantité à sortir <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="quantity" name="quantity" 
                           value="{{ old('quantity', 1) }}" min="1" step="1" required
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('quantity') border-red-500 @enderror">
                    @error('quantity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p id="quantity_help" class="mt-1 text-sm text-gray-500">Stock disponible: <span id="available_stock">0</span></p>
                </div>

                <!-- Coût unitaire (lecture seule) -->
                <div>
                    <label for="unit_cost" class="block text-sm font-medium text-gray-700 mb-2">
                        Coût unitaire (€)
                    </label>
                    <input type="number" id="unit_cost" name="unit_cost" 
                           value="{{ old('unit_cost') }}" readonly
                           class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-50 text-gray-600">
                    <p class="mt-1 text-sm text-gray-500">Coût automatique basé sur le prix d'achat</p>
                </div>

                <!-- Destination (optionnel) -->
                <div class="md:col-span-2">
                    <label for="destination" class="block text-sm font-medium text-gray-700 mb-2">
                        Destination ou bénéficiaire
                    </label>
                    <input type="text" id="destination" name="destination" 
                           value="{{ old('destination') }}"
                           placeholder="Ex: Client XYZ, Service Pharmacie, Transfert Entrepôt B..."
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 @error('destination') border-red-500 @enderror">
                    @error('destination')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Récapitulatif -->
        <div id="summary_section" class="bg-gray-50 rounded-lg border border-gray-200 p-6 hidden">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Récapitulatif de la sortie
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg p-4">
                    <p class="text-sm text-gray-500 mb-1">Produit</p>
                    <p id="summary_product" class="font-semibold text-gray-900"></p>
                </div>
                <div class="bg-white rounded-lg p-4">
                    <p class="text-sm text-gray-500 mb-1">Quantité sortante</p>
                    <p id="summary_quantity" class="font-semibold text-red-600"></p>
                </div>
                <div class="bg-white rounded-lg p-4">
                    <p class="text-sm text-gray-500 mb-1">Valeur totale</p>
                    <p id="summary_total" class="font-semibold text-gray-900"></p>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('stock-movements.index') }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-200">
                Annuler
            </a>
            <button type="submit" id="submit_button" disabled
                    class="bg-red-600 hover:bg-red-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Enregistrer la sortie</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
let searchTimeout;
let selectedProductData = null;
let selectedWarehouseStock = 0;

document.getElementById('product_search').addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    const query = e.target.value;
    
    if (query.length < 2) {
        document.getElementById('product_results').classList.add('hidden');
        return;
    }
    
    searchTimeout = setTimeout(() => {
        searchProducts(query);
    }, 300);
});

// Surveiller le changement d'entrepôt
document.getElementById('warehouse_id').addEventListener('change', function() {
    if (selectedProductData) {
        updateWarehouseStock();
    }
});

async function searchProducts(query) {
    try {
        const response = await fetch(`/api/products/search?q=${encodeURIComponent(query)}&with_stock=1`);
        const products = await response.json();
        
        const resultsDiv = document.getElementById('product_results');
        resultsDiv.innerHTML = '';
        
        if (products.length === 0) {
            resultsDiv.innerHTML = '<div class="p-4 text-gray-500">Aucun produit trouvé</div>';
        } else {
            products.forEach(product => {
                const div = document.createElement('div');
                div.className = 'p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200';
                
                let stockClass = 'text-green-600';
                if (product.total_stock <= 0) stockClass = 'text-red-600';
                else if (product.total_stock <= (product.minimum_stock || 5)) stockClass = 'text-yellow-600';
                
                div.innerHTML = `
                    <div class="flex items-center">
                        <img src="${product.image_url || '/images/no-image.png'}" alt="${product.name}" 
                             class="h-10 w-10 rounded object-cover mr-3">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">${product.name}</p>
                            <p class="text-sm text-gray-500">${product.barcode}</p>
                            <p class="text-sm ${stockClass}">Stock total: ${product.total_stock || 0}</p>
                        </div>
                    </div>
                `;
                div.addEventListener('click', () => selectProduct(product));
                resultsDiv.appendChild(div);
            });
        }
        
        resultsDiv.classList.remove('hidden');
    } catch (error) {
        console.error('Erreur lors de la recherche:', error);
    }
}

function selectProduct(product) {
    selectedProductData = product;
    
    // Cacher les résultats
    document.getElementById('product_results').classList.add('hidden');
    document.getElementById('product_search').value = product.name;
    
    // Remplir les champs
    document.getElementById('product_id').value = product.id;
    document.getElementById('product_image').src = product.image_url || '/images/no-image.png';
    document.getElementById('product_name').textContent = product.name;
    document.getElementById('product_barcode').textContent = product.barcode;
    document.getElementById('current_stock').textContent = `Stock total: ${product.total_stock || 0}`;
    document.getElementById('unit_cost').value = product.purchase_price || 0;
    
    // Afficher la section du produit sélectionné
    document.getElementById('selected_product').classList.remove('hidden');
    
    // Mettre à jour le stock de l'entrepôt
    updateWarehouseStock();
    
    // Mettre le focus sur la quantité
    document.getElementById('quantity').focus();
    
    // Activer le bouton de soumission
    validateForm();
}

function updateWarehouseStock() {
    const warehouseId = document.getElementById('warehouse_id').value;
    
    if (!selectedProductData || !warehouseId) return;
    
    // Simuler une requête pour obtenir le stock de l'entrepôt spécifique
    // Dans un vrai projet, vous feriez une requête AJAX ici
    selectedWarehouseStock = selectedProductData.warehouse_stocks ? 
        (selectedProductData.warehouse_stocks.find(ws => ws.warehouse_id == warehouseId)?.current_stock || 0) : 
        selectedProductData.total_stock || 0;
    
    document.getElementById('warehouse_stock').textContent = `Stock entrepôt: ${selectedWarehouseStock}`;
    document.getElementById('available_stock').textContent = selectedWarehouseStock;
    
    // Mettre à jour la quantité maximale
    const quantityInput = document.getElementById('quantity');
    quantityInput.max = selectedWarehouseStock;
    
    // Vérifier les alertes de stock
    checkStockAlerts();
    
    validateForm();
}

function checkStockAlerts() {
    const quantity = parseInt(document.getElementById('quantity').value) || 0;
    const alertsDiv = document.getElementById('stock_alerts');
    const messageDiv = document.getElementById('stock_warning_message');
    
    if (quantity > selectedWarehouseStock) {
        messageDiv.textContent = `La quantité demandée (${quantity}) dépasse le stock disponible (${selectedWarehouseStock}).`;
        alertsDiv.classList.remove('hidden');
        alertsDiv.className = 'mb-6 bg-red-50 border-l-4 border-red-400 p-4';
        alertsDiv.querySelector('svg').className = 'h-5 w-5 text-red-400';
        alertsDiv.querySelector('p').textContent = 'Stock insuffisant !';
        alertsDiv.querySelector('div').className = 'text-sm text-red-700 mt-1';
    } else if (selectedWarehouseStock - quantity <= (selectedProductData.minimum_stock || 5)) {
        messageDiv.textContent = `Attention : après cette sortie, le stock sera très bas (${selectedWarehouseStock - quantity} restant).`;
        alertsDiv.classList.remove('hidden');
        alertsDiv.className = 'mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4';
        alertsDiv.querySelector('svg').className = 'h-5 w-5 text-yellow-400';
        alertsDiv.querySelector('p').textContent = 'Attention au stock !';
        alertsDiv.querySelector('div').className = 'text-sm text-yellow-700 mt-1';
    } else {
        alertsDiv.classList.add('hidden');
    }
}

function clearProduct() {
    selectedProductData = null;
    selectedWarehouseStock = 0;
    document.getElementById('product_search').value = '';
    document.getElementById('product_id').value = '';
    document.getElementById('selected_product').classList.add('hidden');
    document.getElementById('product_results').classList.add('hidden');
    document.getElementById('stock_alerts').classList.add('hidden');
    document.getElementById('summary_section').classList.add('hidden');
    validateForm();
}

function validateForm() {
    const productId = document.getElementById('product_id').value;
    const warehouseId = document.getElementById('warehouse_id').value;
    const quantity = parseInt(document.getElementById('quantity').value) || 0;
    const submitButton = document.getElementById('submit_button');
    
    const isValid = productId && warehouseId && quantity > 0 && quantity <= selectedWarehouseStock;
    
    submitButton.disabled = !isValid;
    
    if (isValid) {
        updateSummary();
    } else {
        document.getElementById('summary_section').classList.add('hidden');
    }
}

function updateSummary() {
    const quantity = parseInt(document.getElementById('quantity').value) || 0;
    const unitCost = parseFloat(document.getElementById('unit_cost').value) || 0;
    const total = quantity * unitCost;
    
    document.getElementById('summary_product').textContent = selectedProductData.name;
    document.getElementById('summary_quantity').textContent = `-${quantity}`;
    document.getElementById('summary_total').textContent = `${total.toFixed(2)} €`;
    
    document.getElementById('summary_section').classList.remove('hidden');
}

// Event listeners
document.getElementById('quantity').addEventListener('input', function() {
    checkStockAlerts();
    validateForm();
});

document.getElementById('warehouse_id').addEventListener('change', validateForm);

// Fermer les résultats en cliquant en dehors
document.addEventListener('click', function(e) {
    if (!e.target.closest('#product_search') && !e.target.closest('#product_results')) {
        document.getElementById('product_results').classList.add('hidden');
    }
});
</script>
@endpush
@endsection