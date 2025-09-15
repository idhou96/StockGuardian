{{-- resources/views/stock-movements/create-entry.blade.php --}}
@extends('layouts.app')

@section('title', 'Nouvelle Entrée de Stock')

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
                    <span class="ml-1 md:ml-2 text-sm font-medium text-gray-500">Nouvelle Entrée</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                <div class="bg-green-100 rounded-full p-2 mr-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                Nouvelle Entrée de Stock
            </h1>
            <p class="text-gray-600 mt-1">Enregistrer une nouvelle entrée de stock dans l'entrepôt</p>
        </div>
        
        <a href="{{ route('stock-movements.index') }}" 
           class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
            </svg>
            <span>Retour</span>
        </a>
    </div>

    <!-- Formulaire -->
    <form action="{{ route('stock-movements.store-entry') }}" method="POST" class="space-y-6">
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
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('movement_date') border-red-500 @enderror">
                    @error('movement_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Entrepôt -->
                <div>
                    <label for="warehouse_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Entrepôt de destination <span class="text-red-500">*</span>
                    </label>
                    <select id="warehouse_id" name="warehouse_id" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('warehouse_id') border-red-500 @enderror">
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

                <!-- Raison de l'entrée -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Raison de l'entrée <span class="text-red-500">*</span>
                    </label>
                    <select id="reason" name="reason" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('reason') border-red-500 @enderror">
                        <option value="">Choisir une raison</option>
                        <option value="reception_commande" {{ old('reason') == 'reception_commande' ? 'selected' : '' }}>
                            Réception commande
                        </option>
                        <option value="ajustement_inventaire" {{ old('reason') == 'ajustement_inventaire' ? 'selected' : '' }}>
                            Ajustement inventaire
                        </option>
                        <option value="retour_client" {{ old('reason') == 'retour_client' ? 'selected' : '' }}>
                            Retour client
                        </option>
                        <option value="transfert_entrant" {{ old('reason') == 'transfert_entrant' ? 'selected' : '' }}>
                            Transfert entrant
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
                           placeholder="Ex: BC2024-001, BL-456..."
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('document_reference') border-red-500 @enderror">
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
                          placeholder="Informations complémentaires sur cette entrée de stock..."
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
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
                Produit à ajouter
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
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        
                        <!-- Liste des résultats -->
                        <div id="product_results" class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto hidden">
                            <!-- Les résultats seront ajoutés ici via JavaScript -->
                        </div>
                    </div>
                    
                    <!-- Produit sélectionné -->
                    <input type="hidden" id="product_id" name="product_id" value="{{ old('product_id') }}">
                    <div id="selected_product" class="mt-4 hidden">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <img id="product_image" src="" alt="" class="h-12 w-12 rounded-md object-cover mr-4">
                                    <div>
                                        <p id="product_name" class="text-sm font-medium text-gray-900"></p>
                                        <p id="product_barcode" class="text-sm text-gray-500"></p>
                                        <p id="current_stock" class="text-sm text-green-600 font-medium"></p>
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
                        Quantité à ajouter <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="quantity" name="quantity" 
                           value="{{ old('quantity', 1) }}" min="1" step="1" required
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('quantity') border-red-500 @enderror">
                    @error('quantity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Prix unitaire -->
                <div>
                    <label for="unit_price" class="block text-sm font-medium text-gray-700 mb-2">
                        Prix unitaire d'achat (€)
                    </label>
                    <input type="number" id="unit_price" name="unit_price" 
                           value="{{ old('unit_price') }}" min="0" step="0.01"
                           placeholder="0.00"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('unit_price') border-red-500 @enderror">
                    @error('unit_price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Le prix d'achat sera mis à jour pour ce produit</p>
                </div>

                <!-- Lot (optionnel) -->
                <div>
                    <label for="batch_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Numéro de lot
                    </label>
                    <input type="text" id="batch_number" name="batch_number" 
                           value="{{ old('batch_number') }}"
                           placeholder="Ex: LOT2024001"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('batch_number') border-red-500 @enderror">
                    @error('batch_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date d'expiration (optionnel) -->
                <div>
                    <label for="expiration_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Date d'expiration
                    </label>
                    <input type="date" id="expiration_date" name="expiration_date" 
                           value="{{ old('expiration_date') }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('expiration_date') border-red-500 @enderror">
                    @error('expiration_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('stock-movements.index') }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-200">
                Annuler
            </a>
            <button type="submit" 
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Enregistrer l'entrée</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
let searchTimeout;

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
    try {
        const response = await fetch(`/api/products/search?q=${encodeURIComponent(query)}`);
        const products = await response.json();
        
        const resultsDiv = document.getElementById('product_results');
        resultsDiv.innerHTML = '';
        
        if (products.length === 0) {
            resultsDiv.innerHTML = '<div class="p-4 text-gray-500">Aucun produit trouvé</div>';
        } else {
            products.forEach(product => {
                const div = document.createElement('div');
                div.className = 'p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200';
                div.innerHTML = `
                    <div class="flex items-center">
                        <img src="${product.image_url || '/images/no-image.png'}" alt="${product.name}" 
                             class="h-10 w-10 rounded object-cover mr-3">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">${product.name}</p>
                            <p class="text-sm text-gray-500">${product.barcode}</p>
                            <p class="text-sm text-blue-600">Stock actuel: ${product.total_stock || 0}</p>
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
    // Cacher les résultats
    document.getElementById('product_results').classList.add('hidden');
    document.getElementById('product_search').value = product.name;
    
    // Remplir les champs cachés et afficher le produit sélectionné
    document.getElementById('product_id').value = product.id;
    document.getElementById('product_image').src = product.image_url || '/images/no-image.png';
    document.getElementById('product_name').textContent = product.name;
    document.getElementById('product_barcode').textContent = product.barcode;
    document.getElementById('current_stock').textContent = `Stock actuel: ${product.total_stock || 0}`;
    
    // Afficher la section du produit sélectionné
    document.getElementById('selected_product').classList.remove('hidden');
    
    // Mettre le focus sur la quantité
    document.getElementById('quantity').focus();
}

function clearProduct() {
    document.getElementById('product_search').value = '';
    document.getElementById('product_id').value = '';
    document.getElementById('selected_product').classList.add('hidden');
    document.getElementById('product_results').classList.add('hidden');
}

// Fermer les résultats en cliquant en dehors
document.addEventListener('click', function(e) {
    if (!e.target.closest('#product_search') && !e.target.closest('#product_results')) {
        document.getElementById('product_results').classList.add('hidden');
    }
});

// Calcul du total si prix unitaire rempli
document.getElementById('quantity').addEventListener('input', updateTotal);
document.getElementById('unit_price').addEventListener('input', updateTotal);

function updateTotal() {
    const quantity = parseFloat(document.getElementById('quantity').value) || 0;
    const unitPrice = parseFloat(document.getElementById('unit_price').value) || 0;
    const total = quantity * unitPrice;
    
    // Afficher le total quelque part si nécessaire
    // console.log('Total:', total.toFixed(2));
}
</script>
@endpush
@endsection