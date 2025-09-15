{{-- resources/views/stock-regularizations/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nouvelle Régularisation de Stock')

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
                    <a href="{{ route('stock-regularizations.index') }}" class="ml-1 md:ml-2 text-sm font-medium text-gray-700 hover:text-blue-600">Régularisations</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 md:ml-2 text-sm font-medium text-gray-500">Nouvelle Régularisation</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                <div class="bg-blue-100 rounded-full p-2 mr-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                Nouvelle Régularisation de Stock
            </h1>
            <p class="text-gray-600 mt-1">Créer un ajustement de stock pour corriger les écarts d'inventaire</p>
        </div>
        
        <a href="{{ route('stock-regularizations.index') }}" 
           class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
            </svg>
            <span>Retour</span>
        </a>
    </div>

    <!-- Formulaire -->
    <form action="{{ route('stock-regularizations.store') }}" method="POST" class="space-y-6" id="regularizationForm">
        @csrf
        
        <!-- Informations générales -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Informations générales
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Type de régularisation -->
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Type de régularisation <span class="text-red-500">*</span>
                    </label>
                    <select id="type" name="type" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-500 @enderror">
                        <option value="">Sélectionner un type</option>
                        <option value="inventory_adjustment" {{ old('type') == 'inventory_adjustment' ? 'selected' : '' }}>
                            Ajustement inventaire
                        </option>
                        <option value="loss" {{ old('type') == 'loss' ? 'selected' : '' }}>
                            Perte
                        </option>
                        <option value="damage" {{ old('type') == 'damage' ? 'selected' : '' }}>
                            Détérioration
                        </option>
                        <option value="expiry" {{ old('type') == 'expiry' ? 'selected' : '' }}>
                            Péremption
                        </option>
                        <option value="theft" {{ old('type') == 'theft' ? 'selected' : '' }}>
                            Vol
                        </option>
                        <option value="correction" {{ old('type') == 'correction' ? 'selected' : '' }}>
                            Correction d'erreur
                        </option>
                    </select>
                    @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Entrepôt -->
                <div>
                    <label for="warehouse_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Entrepôt concerné <span class="text-red-500">*</span>
                    </label>
                    <select id="warehouse_id" name="warehouse_id" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('warehouse_id') border-red-500 @enderror">
                        <option value="">Sélectionner un entrepôt</option>
                        @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                            {{ $warehouse->name }} - {{ $warehouse->location }}
                        </option>
                        @endforeach
                    </select>
                    @error('warehouse_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Priorité -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                        Priorité
                    </label>
                    <select id="priority" name="priority" 
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('priority') border-red-500 @enderror">
                        <option value="low" {{ old('priority', 'medium') == 'low' ? 'selected' : '' }}>Basse</option>
                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Moyenne</option>
                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Haute</option>
                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgente</option>
                    </select>
                    @error('priority')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date planifiée -->
                <div>
                    <label for="planned_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Date planifiée d'application
                    </label>
                    <input type="datetime-local" id="planned_date" name="planned_date" 
                           value="{{ old('planned_date', now()->addDay()->format('Y-m-d\TH:i')) }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('planned_date') border-red-500 @enderror">
                    @error('planned_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Date à laquelle la régularisation sera appliquée</p>
                </div>

                <!-- Titre/Description courte -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Titre de la régularisation <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" 
                           value="{{ old('title') }}" required
                           placeholder="Ex: Ajustement inventaire suite au comptage physique"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror">
                    @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description détaillée -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description détaillée
                    </label>
                    <textarea id="description" name="description" rows="3" 
                              placeholder="Décrivez les raisons de cette régularisation, les circonstances, etc..."
                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Articles concernés -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Articles concernés par la régularisation
                </h2>
                <button type="button" id="add_item_btn" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Ajouter un article</span>
                </button>
            </div>

            <!-- Section d'ajout de produit -->
            <div id="item_section" class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                    <!-- Recherche produit -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rechercher un produit</label>
                        <div class="relative">
                            <input type="text" id="product_search" placeholder="Nom, code-barres ou référence..."
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <div id="product_results" class="absolute z-20 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-40 overflow-y-auto hidden">
                                <!-- Résultats de recherche -->
                            </div>
                        </div>
                        <input type="hidden" id="selected_product_id">
                    </div>

                    <!-- Stock théorique -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock théorique</label>
                        <input type="number" id="theoretical_quantity" readonly
                               class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100 text-gray-600">
                        <p class="text-xs text-gray-500 mt-1">Stock système actuel</p>
                    </div>

                    <!-- Stock physique -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock physique</label>
                        <input type="number" id="physical_quantity" min="0" value="0"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <p class="text-xs text-gray-500 mt-1">Stock réellement compté</p>
                    </div>

                    <!-- Écart -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Écart</label>
                        <input type="number" id="variance" readonly
                               class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100">
                        <p class="text-xs text-gray-500 mt-1">Différence calculée</p>
                    </div>

                    <!-- Bouton d'ajout -->
                    <div class="flex items-end">
                        <button type="button" onclick="addItemToRegularization()" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                            Ajouter
                        </button>
                    </div>
                </div>

                <!-- Informations du produit sélectionné -->
                <div id="selected_product_info" class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg hidden">
                    <div class="flex items-center">
                        <img id="product_image" src="" alt="" class="h-12 w-12 rounded-md object-cover mr-4">
                        <div class="flex-1">
                            <p id="product_name" class="text-sm font-medium text-gray-900"></p>
                            <p id="product_barcode" class="text-xs text-gray-500"></p>
                            <p id="product_price" class="text-xs text-blue-600"></p>
                        </div>
                        <button type="button" onclick="clearProductSelection()" class="text-red-600 hover:text-red-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Liste des articles -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="items_table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock théorique</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock physique</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Écart</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Impact valeur</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="items_tbody" class="bg-white divide-y divide-gray-200">
                        <tr id="no_items_row">
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                Aucun article ajouté. Cliquez sur "Ajouter un article" pour commencer.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Résumé -->
            <div id="summary_section" class="mt-6 bg-gray-50 p-4 rounded-lg border border-gray-200 hidden">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-600" id="total_items">0</p>
                        <p class="text-sm text-gray-500">Articles concernés</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-red-600" id="total_negative_variance">0</p>
                        <p class="text-sm text-gray-500">Écarts négatifs</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600" id="total_positive_variance">0</p>
                        <p class="text-sm text-gray-500">Écarts positifs</p>
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <p class="text-lg font-semibold">Impact financier total: 
                        <span id="total_value_impact" class="text-purple-600">0,00 €</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Justifications et pièces jointes -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Justifications et documents
            </h2>
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Justification -->
                <div>
                    <label for="justification" class="block text-sm font-medium text-gray-700 mb-2">
                        Justification détaillée <span class="text-red-500">*</span>
                    </label>
                    <textarea id="justification" name="justification" rows="4" required
                              placeholder="Expliquez en détail les raisons qui justifient cette régularisation de stock. Cette information sera importante pour l'approbation..."
                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('justification') border-red-500 @enderror">{{ old('justification') }}</textarea>
                    @error('justification')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions correctives -->
                <div>
                    <label for="corrective_actions" class="block text-sm font-medium text-gray-700 mb-2">
                        Actions correctives mises en place
                    </label>
                    <textarea id="corrective_actions" name="corrective_actions" rows="3" 
                              placeholder="Décrivez les mesures prises pour éviter que cette situation se reproduise..."
                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('corrective_actions') border-red-500 @enderror">{{ old('corrective_actions') }}</textarea>
                    @error('corrective_actions')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('stock-regularizations.index') }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-200">
                Annuler
            </a>
            <button type="submit" name="action" value="draft" 
                    class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                Enregistrer en brouillon
            </button>
            <button type="submit" name="action" value="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                <span>Soumettre pour approbation</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
let regularizationItems = [];
let searchTimeout;
let selectedProduct = null;

// Afficher/masquer la section d'ajout d'article
document.getElementById('add_item_btn').addEventListener('click', function() {
    const section = document.getElementById('item_section');
    section.classList.toggle('hidden');
    if (!section.classList.contains('hidden')) {
        document.getElementById('product_search').focus();
    }
});

// Recherche de produits
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

async function searchProducts(query) {
    const warehouseId = document.getElementById('warehouse_id').value;
    if (!warehouseId) {
        alert('Veuillez d\'abord sélectionner un entrepôt.');
        return;
    }
    
    try {
        const response = await fetch(`/api/products/search?q=${encodeURIComponent(query)}&warehouse_id=${warehouseId}&with_stock=1`);
        const products = await response.json();
        
        const resultsDiv = document.getElementById('product_results');
        resultsDiv.innerHTML = '';
        
        if (products.length === 0) {
            resultsDiv.innerHTML = '<div class="p-3 text-gray-500">Aucun produit trouvé</div>';
        } else {
            products.forEach(product => {
                const div = document.createElement('div');
                div.className = 'p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200';
                div.innerHTML = `
                    <div class="flex items-center">
                        <img src="${product.image_url || '/images/no-image.png'}" alt="${product.name}" 
                             class="h-8 w-8 rounded object-cover mr-3">
                        <div>
                            <p class="text-sm font-medium text-gray-900">${product.name}</p>
                            <p class="text-xs text-gray-500">${product.barcode} - Stock: ${product.current_stock || 0}</p>
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
    selectedProduct = product;
    
    // Cacher les résultats
    document.getElementById('product_results').classList.add('hidden');
    document.getElementById('product_search').value = product.name;
    document.getElementById('selected_product_id').value = product.id;
    
    // Remplir les informations du produit
    document.getElementById('product_image').src = product.image_url || '/images/no-image.png';
    document.getElementById('product_name').textContent = product.name;
    document.getElementById('product_barcode').textContent = product.barcode;
    document.getElementById('product_price').textContent = `Prix: ${product.selling_price || 0} €`;
    
    // Remplir le stock théorique
    document.getElementById('theoretical_quantity').value = product.current_stock || 0;
    
    // Afficher la section d'informations
    document.getElementById('selected_product_info').classList.remove('hidden');
    
    // Mettre le focus sur la quantité physique
    document.getElementById('physical_quantity').focus();
    
    // Calculer l'écart initial
    calculateVariance();
}

function clearProductSelection() {
    selectedProduct = null;
    document.getElementById('product_search').value = '';
    document.getElementById('selected_product_id').value = '';
    document.getElementById('theoretical_quantity').value = '';
    document.getElementById('physical_quantity').value = '0';
    document.getElementById('variance').value = '';
    document.getElementById('selected_product_info').classList.add('hidden');
    document.getElementById('product_results').classList.add('hidden');
}

// Calculer l'écart
function calculateVariance() {
    const theoretical = parseFloat(document.getElementById('theoretical_quantity').value) || 0;
    const physical = parseFloat(document.getElementById('physical_quantity').value) || 0;
    const variance = physical - theoretical;
    
    document.getElementById('variance').value = variance;
    
    // Changer la couleur selon le type d'écart
    const varianceField = document.getElementById('variance');
    if (variance > 0) {
        varianceField.className = varianceField.className.replace('bg-gray-100', 'bg-green-100 text-green-700');
    } else if (variance < 0) {
        varianceField.className = varianceField.className.replace('bg-gray-100', 'bg-red-100 text-red-700');
    } else {
        varianceField.className = varianceField.className.replace(/bg-(green|red)-100 text-(green|red)-700/, 'bg-gray-100');
    }
}

document.getElementById('physical_quantity').addEventListener('input', calculateVariance);

function addItemToRegularization() {
    if (!selectedProduct) {
        alert('Veuillez sélectionner un produit.');
        return;
    }
    
    const theoretical = parseFloat(document.getElementById('theoretical_quantity').value) || 0;
    const physical = parseFloat(document.getElementById('physical_quantity').value) || 0;
    const variance = physical - theoretical;
    
    if (variance === 0) {
        alert('Aucun écart détecté. Pas besoin de régularisation pour ce produit.');
        return;
    }
    
    // Vérifier si le produit n'est pas déjà dans la liste
    const existingIndex = regularizationItems.findIndex(item => item.product_id == selectedProduct.id);
    if (existingIndex !== -1) {
        // Mettre à jour l'article existant
        regularizationItems[existingIndex].theoretical_quantity = theoretical;
        regularizationItems[existingIndex].physical_quantity = physical;
        regularizationItems[existingIndex].variance = variance;
        regularizationItems[existingIndex].value_impact = variance * (selectedProduct.selling_price || 0);
    } else {
        // Ajouter un nouvel article
        regularizationItems.push({
            product_id: selectedProduct.id,
            product_name: selectedProduct.name,
            product_barcode: selectedProduct.barcode,
            product_image: selectedProduct.image_url,
            theoretical_quantity: theoretical,
            physical_quantity: physical,
            variance: variance,
            unit_price: selectedProduct.selling_price || 0,
            value_impact: variance * (selectedProduct.selling_price || 0)
        });
    }
    
    // Réinitialiser le formulaire d'ajout
    clearProductSelection();
    document.getElementById('item_section').classList.add('hidden');
    
    updateItemsTable();
}

function updateItemsTable() {
    const tbody = document.getElementById('items_tbody');
    const noItemsRow = document.getElementById('no_items_row');
    
    if (regularizationItems.length === 0) {
        noItemsRow.style.display = 'table-row';
        document.getElementById('summary_section').classList.add('hidden');
        return;
    }
    
    noItemsRow.style.display = 'none';
    document.getElementById('summary_section').classList.remove('hidden');
    
    // Supprimer toutes les lignes sauf la ligne "aucun article"
    while (tbody.children.length > 1) {
        tbody.removeChild(tbody.lastChild);
    }
    
    let totalItems = 0;
    let totalNegativeVariance = 0;
    let totalPositiveVariance = 0;
    let totalValueImpact = 0;
    
    regularizationItems.forEach((item, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-4 py-3">
                <div class="flex items-center">
                    <img src="${item.product_image || '/images/no-image.png'}" alt="${item.product_name}" 
                         class="h-8 w-8 rounded object-cover mr-3">
                    <div>
                        <div class="text-sm font-medium text-gray-900">${item.product_name}</div>
                        <div class="text-xs text-gray-500">${item.product_barcode}</div>
                    </div>
                </div>
                <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                <input type="hidden" name="items[${index}][theoretical_quantity]" value="${item.theoretical_quantity}">
                <input type="hidden" name="items[${index}][physical_quantity]" value="${item.physical_quantity}">
                <input type="hidden" name="items[${index}][variance]" value="${item.variance}">
                <input type="hidden" name="items[${index}][value_impact]" value="${item.value_impact}">
            </td>
            <td class="px-4 py-3">
                <span class="text-sm text-gray-900">${item.theoretical_quantity}</span>
            </td>
            <td class="px-4 py-3">
                <span class="text-sm text-gray-900">${item.physical_quantity}</span>
            </td>
            <td class="px-4 py-3">
                <span class="text-sm font-semibold ${item.variance >= 0 ? 'text-green-600' : 'text-red-600'}">
                    ${item.variance >= 0 ? '+' : ''}${item.variance}
                </span>
            </td>
            <td class="px-4 py-3">
                <span class="text-sm font-semibold ${item.value_impact >= 0 ? 'text-green-600' : 'text-red-600'}">
                    ${item.value_impact >= 0 ? '+' : ''}${item.value_impact.toFixed(2)} €
                </span>
            </td>
            <td class="px-4 py-3">
                <button type="button" onclick="removeItem(${index})" 
                        class="text-red-600 hover:text-red-900">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </td>
        `;
        tbody.appendChild(row);
        
        totalItems++;
        if (item.variance < 0) totalNegativeVariance += Math.abs(item.variance);
        if (item.variance > 0) totalPositiveVariance += item.variance;
        totalValueImpact += item.value_impact;
    });
    
    // Mettre à jour le résumé
    document.getElementById('total_items').textContent = totalItems;
    document.getElementById('total_negative_variance').textContent = totalNegativeVariance;
    document.getElementById('total_positive_variance').textContent = totalPositiveVariance;
    document.getElementById('total_value_impact').textContent = totalValueImpact.toFixed(2).replace('.', ',') + ' €';
    
    // Changer la couleur de l'impact total
    const impactElement = document.getElementById('total_value_impact');
    if (totalValueImpact >= 0) {
        impactElement.className = 'text-green-600';
    } else {
        impactElement.className = 'text-red-600';
    }
}

function removeItem(index) {
    regularizationItems.splice(index, 1);
    updateItemsTable();
}

// Fermer les résultats de recherche en cliquant ailleurs
document.addEventListener('click', function(e) {
    if (!e.target.closest('#product_search') && !e.target.closest('#product_results')) {
        document.getElementById('product_results').classList.add('hidden');
    }
});

// Validation du formulaire
document.getElementById('regularizationForm').addEventListener('submit', function(e) {
    if (regularizationItems.length === 0) {
        e.preventDefault();
        alert('Veuillez ajouter au moins un article à la régularisation.');
        return false;
    }
});
</script>
@endpush
@endsection