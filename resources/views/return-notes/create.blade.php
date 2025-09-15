
<?php
// ===============================================
// VUE CRÉATION BON DE RETOUR CLIENT
// ===============================================

// 🎯 VUE CREATE BON DE RETOUR CLIENT
// resources/views/return-notes/create.blade.php
?>

@extends('layouts.app')

@section('title', 'Créer un Bon de Retour')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    {{-- En-tête --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Créer un Bon de Retour 
                    @if(request('type') == 'customer')
                        <span class="text-blue-600">Client</span>
                    @else
                        <span class="text-green-600">Fournisseur</span>
                    @endif
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                    @if(request('type') == 'customer')
                        Enregistrez le retour de produits par un client
                    @else
                        Enregistrez le retour de produits vers un fournisseur
                    @endif
                </p>
            </div>
            <a href="{{ route('return-notes.index') }}" 
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour
            </a>
        </div>
    </div>

    {{-- Formulaire --}}
    <form method="POST" action="{{ route('return-notes.store') }}" class="space-y-6" x-data="returnNoteForm()">
        @csrf
        <input type="hidden" name="type" value="{{ request('type', 'customer') }}">

        {{-- Informations générales --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 @if(request('type') == 'customer') text-blue-600 @else text-green-600 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/>
                </svg>
                Informations Générales
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Référence --}}
                <div>
                    <label for="reference" class="block text-sm font-medium text-gray-700 mb-2">
                        Référence BR <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="reference" 
                           name="reference" 
                           value="{{ old('reference', $nextReference ?? '') }}"
                           required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 @error('reference') border-red-300 @enderror">
                    @error('reference')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Date de retour --}}
                <div>
                    <label for="return_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Date de Retour <span class="text-red-500">*</span>
                    </label>
                    <input type="date" 
                           id="return_date" 
                           name="return_date" 
                           value="{{ old('return_date', date('Y-m-d')) }}"
                           required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 @error('return_date') border-red-300 @enderror">
                    @error('return_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Entrepôt --}}
                <div>
                    <label for="warehouse_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Entrepôt <span class="text-red-500">*</span>
                    </label>
                    <select id="warehouse_id" 
                            name="warehouse_id" 
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 @error('warehouse_id') border-red-300 @enderror">
                        <option value="">Sélectionner un entrepôt</option>
                        @foreach($warehouses ?? [] as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }} - {{ $warehouse->location }}
                            </option>
                        @endforeach
                    </select>
                    @error('warehouse_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if(request('type') == 'customer')
                {{-- Client --}}
                <div>
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Client <span class="text-red-500">*</span>
                    </label>
                    <select id="customer_id" 
                            name="customer_id" 
                            required
                            x-model="selectedCustomer"
                            @change="loadCustomerSales()"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('customer_id') border-red-300 @enderror">
                        <option value="">Sélectionner un client</option>
                        @foreach($customers ?? [] as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} - {{ $customer->code ?? $customer->phone }}
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Vente originale --}}
                <div>
                    <label for="sale_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Vente Originale
                    </label>
                    <select id="sale_id" 
                            name="sale_id"
                            x-model="selectedSale" 
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('sale_id') border-red-300 @enderror">
                        <option value="">Sélectionner une vente</option>
                        <template x-for="sale in customerSales" :key="sale.id">
                            <option :value="sale.id" x-text="`${sale.reference} (${sale.total_amount} F)`"></option>
                        </template>
                    </select>
                    @error('sale_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @else
                {{-- Fournisseur --}}
                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Fournisseur <span class="text-red-500">*</span>
                    </label>
                    <select id="supplier_id" 
                            name="supplier_id" 
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 @error('supplier_id') border-red-300 @enderror">
                        <option value="">Sélectionner un fournisseur</option>
                        @foreach($suppliers ?? [] as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }} - {{ $supplier->code }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @endif

                {{-- Raison du retour --}}
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Raison du Retour <span class="text-red-500">*</span>
                    </label>
                    <select id="reason" 
                            name="reason" 
                            required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 @error('reason') border-red-300 @enderror">
                        <option value="">Sélectionner une raison</option>
                        <option value="defective" {{ old('reason') == 'defective' ? 'selected' : '' }}>Produit défectueux</option>
                        <option value="expired" {{ old('reason') == 'expired' ? 'selected' : '' }}>Produit expiré</option>
                        <option value="damaged" {{ old('reason') == 'damaged' ? 'selected' : '' }}>Produit endommagé</option>
                        <option value="wrong_product" {{ old('reason') == 'wrong_product' ? 'selected' : '' }}>Mauvais produit livré</option>
                        @if(request('type') == 'supplier')
                        <option value="overstocked" {{ old('reason') == 'overstocked' ? 'selected' : '' }}>Surstocké</option>
                        @endif
                        <option value="other" {{ old('reason') == 'other' ? 'selected' : '' }}>Autre raison</option>
                    </select>
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Description & Notes --}}
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description du Problème
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              placeholder="Décrivez le problème en détail..."
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notes Internes
                    </label>
                    <textarea id="notes" 
                              name="notes" 
                              rows="3"
                              placeholder="Notes internes sur le retour..."
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 @error('notes') border-red-300 @enderror">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Articles retournés --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Articles Retournés
                </h3>
                <button type="button" 
                        @click="addItem()"
                        class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Ajouter Article
                </button>
            </div>

            {{-- Tableau des articles --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Article</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qté Retournée</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix Unitaire</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">État</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Supprimer</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td class="px-4 py-4">
                                    <select :name="`items[${index}][product_id]`" 
                                            x-model="item.product_id"
                                            @change="updateItemPrice(index)"
                                            required
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                        <option value="">Sélectionner un article</option>
                                        @foreach($products ?? [] as $product)
                                            <option value="{{ $product->id }}">
                                                {{ $product->name }} - {{ $product->code }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-4">
                                    <input type="number" 
                                           :name="`items[${index}][quantity]`"
                                           x-model="item.quantity"
                                           @input="calculateItemTotal(index)"
                                           required
                                           min="1"
                                           step="0.01"
                                           class="w-20 rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                </td>
                                <td class="px-4 py-4">
                                    <input type="number" 
                                           :name="`items[${index}][unit_price]`"
                                           x-model="item.unit_price"
                                           @input="calculateItemTotal(index)"
                                           required
                                           min="0"
                                           step="0.01"
                                           class="w-24 rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                </td>
                                <td class="px-4 py-4">
                                    <span x-text="formatPrice(item.total)" class="font-medium text-gray-900"></span>
                                </td>
                                <td class="px-4 py-4">
                                    <select :name="`items[${index}][condition]`" 
                                            x-model="item.condition"
                                            class="w-32 rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                        <option value="good">Bon état</option>
                                        <option value="damaged">Endommagé</option>
                                        <option value="expired">Expiré</option>
                                        <option value="defective">Défectueux</option>
                                    </select>
                                </td>
                                <td class="px-4 py-4">
                                    <select :name="`items[${index}][action]`" 
                                            x-model="item.action"
                                            class="w-32 rounded-lg border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                                        <option value="return_stock">Remettre en stock</option>
                                        <option value="dispose">Détruire</option>
                                        <option value="repair">Réparer</option>
                                        <option value="return_supplier">Retourner fournisseur</option>
                                    </select>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <button type="button" 
                                            @click="removeItem(index)"
                                            class="text-red-600 hover:text-red-900 p-1 rounded transition-colors duration-200"
                                            title="Supprimer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                        <template x-if="items.length === 0">
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    Aucun article ajouté. Cliquez sur "Ajouter Article" pour commencer.
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- Total --}}
            <div class="mt-6 flex justify-end">
                <div class="bg-gray-50 rounded-lg p-4 w-72">
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span>Total articles:</span>
                            <span x-text="totalItems"></span>
                        </div>
                        <div class="flex justify-between font-semibold text-lg">
                            <span>Montant retour:</span>
                            <span x-text="formatPrice(totalAmount)" class="text-red-600"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end space-x-4">
            <a href="{{ route('return-notes.index') }}" 
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                Annuler
            </a>
            <button type="submit" 
                    name="status" 
                    value="draft"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                Sauvegarder Brouillon
            </button>
            <button type="submit" 
                    name="status" 
                    value="pending"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                Créer le Bon de Retour
            </button>
        </div>
    </form>
</div>

<script>
function returnNoteForm() {
    return {
        selectedCustomer: @json(old('customer_id', '')),
        selectedSale: @json(old('sale_id', '')),
        customerSales: [],
        items: @json(old('items', [])),
        
        init() {
            if (this.items.length === 0) {
                this.addItem();
            }
        },
        
        addItem() {
            this.items.push({
                product_id: '',
                quantity: 1,
                unit_price: 0,
                total: 0,
                condition: 'good',
                action: 'return_stock'
            });
        },
        
        removeItem(index) {
            this.items.splice(index, 1);
        },
        
        calculateItemTotal(index) {
            const item = this.items[index];
            item.total = (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0);
        },
        
        updateItemPrice(index) {
            // Logique pour mettre à jour le prix automatiquement si nécessaire
            this.calculateItemTotal(index);
        },
        
        formatPrice(amount) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'XOF',
                minimumFractionDigits: 0
            }).format(amount || 0).replace('XOF', 'F');
        },
        
        get totalItems() {
            return this.items.reduce((sum, item) => sum + (parseFloat(item.quantity) || 0), 0);
        },
        
        get totalAmount() {
            return this.items.reduce((sum, item) => sum + (parseFloat(item.total) || 0), 0);
        },
        
        async loadCustomerSales() {
            if (!this.selectedCustomer) {
                this.customerSales = [];
                return;
            }
            
            try {
                const response = await fetch(`/api/customers/${this.selectedCustomer}/sales`);
                const data = await response.json();
                this.customerSales = data;
            } catch (error) {
                console.error('Erreur lors du chargement des ventes:', error);
            }
        }
    }
}
</script>
@endsection