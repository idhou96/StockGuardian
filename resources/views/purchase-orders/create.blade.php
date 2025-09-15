{{-- resources/views/purchase-orders/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nouvelle Commande Fournisseur')

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
                    <a href="{{ route('purchase-orders.index') }}" class="ml-1 md:ml-2 text-sm font-medium text-gray-700 hover:text-blue-600">Commandes</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 md:ml-2 text-sm font-medium text-gray-500">Nouvelle Commande</span>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                Nouvelle Commande Fournisseur
            </h1>
            <p class="text-gray-600 mt-1">Créer une nouvelle commande d'approvisionnement</p>
        </div>
        
        <a href="{{ route('purchase-orders.index') }}" 
           class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
            </svg>
            <span>Retour</span>
        </a>
    </div>

    <!-- Formulaire -->
    <form action="{{ route('purchase-orders.store') }}" method="POST" class="space-y-6" id="orderForm">
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
                <!-- Date de commande -->
                <div>
                    <label for="order_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Date de commande <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="order_date" name="order_date" 
                           value="{{ old('order_date', now()->format('Y-m-d')) }}" required
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('order_date') border-red-500 @enderror">
                    @error('order_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date de livraison souhaitée -->
                <div>
                    <label for="expected_delivery_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Date de livraison souhaitée
                    </label>
                    <input type="date" id="expected_delivery_date" name="expected_delivery_date" 
                           value="{{ old('expected_delivery_date') }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('expected_delivery_date') border-red-500 @enderror">
                    @error('expected_delivery_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fournisseur -->
                <div class="md:col-span-2">
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Fournisseur <span class="text-red-500">*</span>
                    </label>
                    <select id="supplier_id" name="supplier_id" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('supplier_id') border-red-500 @enderror">
                        <option value="">Sélectionner un fournisseur</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}
                                data-email="{{ $supplier->email }}" data-phone="{{ $supplier->phone }}" data-address="{{ $supplier->address }}">
                            {{ $supplier->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    
                    <!-- Informations du fournisseur sélectionné -->
                    <div id="supplier_info" class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200 hidden">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-700">Email:</span>
                                <span id="supplier_email" class="text-gray-600 ml-2">-</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Téléphone:</span>
                                <span id="supplier_phone" class="text-gray-600 ml-2">-</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-700">Adresse:</span>
                                <span id="supplier_address" class="text-gray-600 ml-2">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Entrepôt de livraison -->
                <div>
                    <label for="warehouse_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Entrepôt de livraison <span class="text-red-500">*</span>
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

                <!-- Référence fournisseur -->
                <div>
                    <label for="supplier_reference" class="block text-sm font-medium text-gray-700 mb-2">
                        Référence fournisseur
                    </label>
                    <input type="text" id="supplier_reference" name="supplier_reference" 
                           value="{{ old('supplier_reference') }}"
                           placeholder="Ex: DEVIS-2024-001"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('supplier_reference') border-red-500 @enderror">
                    @error('supplier_reference')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notes et instructions
                    </label>
                    <textarea id="notes" name="notes" rows="3" 
                              placeholder="Instructions particulières, conditions de livraison, etc..."
                              class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                    @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Articles de la commande -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Articles de la commande
                </h2>
                <button type="button" id="add_product_btn" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Ajouter un article</span>
                </button>
            </div>

            <!-- Section d'ajout de produit -->
            <div id="product_section" class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
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

                    <!-- Quantité -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                        <input type="number" id="product_quantity" min="1" value="1"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>

                    <!-- Prix unitaire -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prix unitaire (€)</label>
                        <input type="number" id="product_price" min="0" step="0.01" placeholder="0.00"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>

                    <!-- Bouton d'ajout -->
                    <div class="flex items-end">
                        <button type="button" onclick="addProductToOrder()" 
                                class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                            Ajouter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Liste des articles -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="products_table">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix unitaire</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="products_tbody" class="bg-white divide-y divide-gray-200">
                        <tr id="no_products_row">
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                <svg class="mx-auto h-8 w-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                Aucun article ajouté. Cliquez sur "Ajouter un article" pour commencer.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Totaux -->
            <div id="totals_section" class="mt-6 bg-gray-50 p-4 rounded-lg border border-gray-200 hidden">
                <div class="flex justify-between items-center text-lg font-semibold">
                    <span class="text-gray-900">Total HT:</span>
                    <span id="total_amount" class="text-blue-600">0,00 €</span>
                </div>
                <div class="flex justify-between items-center text-sm text-gray-600 mt-1">
                    <span>Nombre d'articles:</span>
                    <span id="items_count">0</span>
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('purchase-orders.index') }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-bold py-2 px-6 rounded-lg transition duration-200">
                Annuler
            </a>
            <button type="submit" name="action" value="draft" 
                    class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200">
                Enregistrer en brouillon
            </button>
            <button type="submit" name="action" value="send" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-200 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                <span>Créer et envoyer</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
let orderItems = [];
let searchTimeout;

// Afficher/masquer la section d'ajout de produit
document.getElementById('add_product_btn').addEventListener('click', function() {
    const section = document.getElementById('product_section');
    section.classList.toggle('hidden');
    if (!section.classList.contains('hidden')) {
        document.getElementById('product_search').focus();
    }
});

// Afficher les informations du fournisseur sélectionné
document.getElementById('supplier_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const infoDiv = document.getElementById('supplier_info');
    
    if (selectedOption.value) {
        document.getElementById('supplier_email').textContent = selectedOption.dataset.email || '-';
        document.getElementById('supplier_phone').textContent = selectedOption.dataset.phone || '-';
        document.getElementById('supplier_address').textContent = selectedOption.dataset.address || '-';
        infoDiv.classList.remove('hidden');
    } else {
        infoDiv.classList.add('hidden');
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
    try {
        const response = await fetch(`/api/products/search?q=${encodeURIComponent(query)}`);
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
                            <p class="text-xs text-gray-500">${product.barcode}</p>
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
    document.getElementById('product_search').value = product.name;
    document.getElementById('selected_product_id').value = product.id;
    document.getElementById('product_results').classList.add('hidden');
    
    // Mettre le prix d'achat par défaut s'il existe
    if (product.purchase_price) {
        document.getElementById('product_price').value = product.purchase_price;
    }
    
    document.getElementById('product_quantity').focus();
}

function addProductToOrder() {
    const productId = document.getElementById('selected_product_id').value;
    const productName = document.getElementById('product_search').value;
    const quantity = parseInt(document.getElementById('product_quantity').value) || 1;
    const price = parseFloat(document.getElementById('product_price').value) || 0;
    
    if (!productId || !productName || quantity <= 0 || price < 0) {
        alert('Veuillez sélectionner un produit et renseigner une quantité et un prix valides.');
        return;
    }
    
    // Vérifier si le produit n'est pas déjà dans la commande
    const existingIndex = orderItems.findIndex(item => item.product_id == productId);
    if (existingIndex !== -1) {
        // Mettre à jour la quantité existante
        orderItems[existingIndex].quantity += quantity;
        orderItems[existingIndex].total = orderItems[existingIndex].quantity * orderItems[existingIndex].price;
    } else {
        // Ajouter un nouveau produit
        orderItems.push({
            product_id: productId,
            product_name: productName,
            quantity: quantity,
            price: price,
            total: quantity * price
        });
    }
    
    // Réinitialiser le formulaire d'ajout
    document.getElementById('product_search').value = '';
    document.getElementById('selected_product_id').value = '';
    document.getElementById('product_quantity').value = 1;
    document.getElementById('product_price').value = '';
    document.getElementById('product_section').classList.add('hidden');
    
    updateOrderTable();
}

function updateOrderTable() {
    const tbody = document.getElementById('products_tbody');
    const noProductsRow = document.getElementById('no_products_row');
    
    if (orderItems.length === 0) {
        noProductsRow.style.display = 'table-row';
        document.getElementById('totals_section').classList.add('hidden');
        return;
    }
    
    noProductsRow.style.display = 'none';
    document.getElementById('totals_section').classList.remove('hidden');
    
    // Supprimer toutes les lignes sauf la ligne "aucun produit"
    while (tbody.children.length > 1) {
        tbody.removeChild(tbody.lastChild);
    }
    
    let totalAmount = 0;
    let totalItems = 0;
    
    orderItems.forEach((item, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-4 py-3">
                <div class="text-sm font-medium text-gray-900">${item.product_name}</div>
                <input type="hidden" name="products[${index}][product_id]" value="${item.product_id}">
                <input type="hidden" name="products[${index}][quantity]" value="${item.quantity}">
                <input type="hidden" name="products[${index}][price]" value="${item.price}">
            </td>
            <td class="px-4 py-3">
                <input type="number" value="${item.quantity}" min="1" 
                       class="w-20 border border-gray-300 rounded px-2 py-1" 
                       onchange="updateItemQuantity(${index}, this.value)">
            </td>
            <td class="px-4 py-3">
                <input type="number" value="${item.price}" min="0" step="0.01"
                       class="w-24 border border-gray-300 rounded px-2 py-1" 
                       onchange="updateItemPrice(${index}, this.value)">
            </td>
            <td class="px-4 py-3">
                <span class="font-medium">${item.total.toFixed(2)} €</span>
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
        
        totalAmount += item.total;
        totalItems += item.quantity;
    });
    
    document.getElementById('total_amount').textContent = totalAmount.toFixed(2).replace('.', ',') + ' €';
    document.getElementById('items_count').textContent = totalItems;
}

function updateItemQuantity(index, quantity) {
    quantity = parseInt(quantity) || 1;
    orderItems[index].quantity = quantity;
    orderItems[index].total = quantity * orderItems[index].price;
    updateOrderTable();
}

function updateItemPrice(index, price) {
    price = parseFloat(price) || 0;
    orderItems[index].price = price;
    orderItems[index].total = orderItems[index].quantity * price;
    updateOrderTable();
}

function removeItem(index) {
    orderItems.splice(index, 1);
    updateOrderTable();
}

// Fermer les résultats de recherche en cliquant ailleurs
document.addEventListener('click', function(e) {
    if (!e.target.closest('#product_search') && !e.target.closest('#product_results')) {
        document.getElementById('product_results').classList.add('hidden');
    }
});

// Validation du formulaire avant soumission
document.getElementById('orderForm').addEventListener('submit', function(e) {
    if (orderItems.length === 0) {
        e.preventDefault();
        alert('Veuillez ajouter au moins un article à la commande.');
        return false;
    }
});
</script>
@endpush
@endsection