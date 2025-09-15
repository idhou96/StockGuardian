
{{-- resources/views/invoices/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nouvelle Facture')

@section('content')
<div class="py-6" x-data="invoiceForm()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <x-breadcrumb :items="[
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Factures', 'url' => route('invoices.index')],
                ['label' => 'Nouvelle Facture', 'url' => null]
            ]" />
            <div class="mt-4">
                <h1 class="text-2xl font-bold text-gray-900">Nouvelle Facture</h1>
                <p class="mt-1 text-sm text-gray-600">Créez une nouvelle facture pour un client</p>
            </div>
        </div>

        <form action="{{ route('invoices.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Informations client --}}
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informations client</h3>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2">
                        <label for="customer_id" class="block text-sm font-medium text-gray-700">Client *</label>
                        <select name="customer_id" id="customer_id" required x-model="selectedCustomerId" @change="updateCustomerInfo"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('customer_id') border-red-300 @enderror">
                            <option value="">Sélectionner un client</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} - {{ $customer->phone ?? $customer->email }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="invoice_date" class="block text-sm font-medium text-gray-700">Date de facture *</label>
                        <input type="date" name="invoice_date" id="invoice_date" required
                               value="{{ old('invoice_date', date('Y-m-d')) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('invoice_date') border-red-300 @enderror">
                        @error('invoice_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700">Date d'échéance</label>
                        <input type="date" name="due_date" id="due_date"
                               value="{{ old('due_date') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('due_date') border-red-300 @enderror">
                        @error('due_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sale_id" class="block text-sm font-medium text-gray-700">Vente associée</label>
                        <select name="sale_id" id="sale_id" x-model="selectedSaleId" @change="loadSaleItems"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Aucune vente associée</option>
                            @foreach($sales as $sale)
                                <option value="{{ $sale->id }}" {{ old('sale_id') == $sale->id ? 'selected' : '' }}>
                                    {{ $sale->reference }} - {{ $sale->customer->name }} - {{ number_format($sale->total_amount) }} F
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Informations client sélectionné --}}
                    <div x-show="customerInfo.name" class="md:col-span-3 p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-2">Informations client</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Nom:</span>
                                <span x-text="customerInfo.name" class="block font-medium"></span>
                            </div>
                            <div>
                                <span class="text-gray-500">Email:</span>
                                <span x-text="customerInfo.email || 'Non renseigné'" class="block font-medium"></span>
                            </div>
                            <div>
                                <span class="text-gray-500">Téléphone:</span>
                                <span x-text="customerInfo.phone || 'Non renseigné'" class="block font-medium"></span>
                            </div>
                            <div>
                                <span class="text-gray-500">Type:</span>
                                <span x-text="customerInfo.type" class="block font-medium"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Articles de la facture --}}
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Articles</h3>
                    <button type="button" @click="addItem()" x-show="!selectedSaleId"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Ajouter un article
                    </button>
                </div>
                <div class="px-6 py-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Article</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" x-show="!selectedSaleId">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <template x-if="selectedSaleId">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900" x-text="item.product_name"></div>
                                                    <div class="text-sm text-gray-500" x-text="item.product_code"></div>
                                                    <input type="hidden" :name="`items[${index}][product_id]`" :value="item.product_id">
                                                </div>
                                            </template>
                                            <template x-if="!selectedSaleId">
                                                <select :name="`items[${index}][product_id]`" x-model="item.product_id" @change="updateItemProduct(index)" required
                                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                    <option value="">Sélectionner un produit</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" data-price="{{ $product->sale_price }}">
                                                            {{ $product->name }} ({{ $product->code }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </template>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity" @input="calculateItemTotal(index)" 
                                                   min="1" step="1" required :readonly="selectedSaleId"
                                                   class="block w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" :name="`items[${index}][unit_price]`" x-model="item.unit_price" @input="calculateItemTotal(index)" 
                                                   min="0" step="0.01" required :readonly="selectedSaleId"
                                                   class="block w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-medium text-gray-900" x-text="formatCurrency(item.total)"></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap" x-show="!selectedSaleId">
                                            <button type="button" @click="removeItem(index)" 
                                                    class="text-red-600 hover:text-red-900">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="items.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        Aucun article ajouté
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Remise et totaux --}}
                    <div class="mt-6 flex justify-end">
                        <div class="w-80">
                            <div class="flex justify-between items-center mb-4">
                                <label for="discount_percentage" class="text-sm font-medium text-gray-700">Remise (%):</label>
                                <input type="number" name="discount_percentage" x-model="discountPercentage" @input="calculateTotals"
                                       min="0" max="100" step="0.01" placeholder="0"
                                       class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span>Sous-total :</span>
                                    <span x-text="formatCurrency(subtotal)"></span>
                                </div>
                                <div class="flex justify-between text-red-600" x-show="discountPercentage > 0">
                                    <span>Remise (<span x-text="discountPercentage"></span>%) :</span>
                                    <span>-<span x-text="formatCurrency(discountAmount)"></span></span>
                                </div>
                                <div class="flex justify-between">
                                    <span>TVA (18%) :</span>
                                    <span x-text="formatCurrency(taxAmount)"></span>
                                </div>
                                <div class="flex justify-between text-lg font-bold pt-2 border-t">
                                    <span>Total :</span>
                                    <span x-text="formatCurrency(totalAmount)"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Notes et conditions</h3>
                </div>
                <div class="px-6 py-4">
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" rows="3" 
                                  placeholder="Notes additionnelles sur la facture..."
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('notes') border-red-300 @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end space-x-4">
                <a href="{{ route('invoices.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" name="status" value="draft"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700">
                    Enregistrer en brouillon
                </button>
                <button type="submit" name="status" value="sent"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Créer et envoyer
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function invoiceForm() {
    return {
        selectedCustomerId: '',
        selectedSaleId: '',
        customerInfo: {},
        items: [],
        discountPercentage: 0,
        
        get subtotal() {
            return this.items.reduce((sum, item) => sum + (item.total || 0), 0);
        },
        
        get discountAmount() {
            return this.subtotal * (this.discountPercentage / 100);
        },
        
        get taxAmount() {
            return (this.subtotal - this.discountAmount) * 0.18;
        },
        
        get totalAmount() {
            return this.subtotal - this.discountAmount + this.taxAmount;
        },

        updateCustomerInfo() {
            if (!this.selectedCustomerId) {
                this.customerInfo = {};
                return;
            }

            fetch(`/api/internal/customers/select?id=${this.selectedCustomerId}`)
                .then(response => response.json())
                .then(data => {
                    this.customerInfo = data;
                })
                .catch(error => console.error('Erreur:', error));
        },

        loadSaleItems() {
            if (!this.selectedSaleId) {
                this.items = [];
                return;
            }

            fetch(`/sales/${this.selectedSaleId}?format=json`)
                .then(response => response.json())
                .then(data => {
                    this.items = data.details.map(detail => ({
                        product_id: detail.product.id,
                        product_name: detail.product.name,
                        product_code: detail.product.code,
                        quantity: detail.quantity,
                        unit_price: detail.unit_price,
                        total: detail.total_price
                    }));
                    this.calculateTotals();
                })
                .catch(error => console.error('Erreur:', error));
        },

        addItem() {
            this.items.push({
                product_id: '',
                quantity: 1,
                unit_price: 0,
                total: 0
            });
        },

        removeItem(index) {
            this.items.splice(index, 1);
            this.calculateTotals();
        },

        updateItemProduct(index) {
            const select = event.target;
            const option = select.options[select.selectedIndex];
            if (option.dataset.price) {
                this.items[index].unit_price = parseFloat(option.dataset.price);
                this.calculateItemTotal(index);
            }
        },

        calculateItemTotal(index) {
            const item = this.items[index];
            item.total = (item.quantity || 0) * (item.unit_price || 0);
            this.calculateTotals();
        },

        calculateTotals() {
            // Les totaux sont calculés via les getters
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'XOF',
                minimumFractionDigits: 0
            }).format(amount || 0);
        }
    }
}
</script>
@endpush
@endsection