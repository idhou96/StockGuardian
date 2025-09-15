
{{-- resources/views/delivery-notes/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nouveau Bon de Livraison')

@section('content')
<div class="py-6" x-data="deliveryNoteForm()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <x-breadcrumb :items="[
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Bons de Livraison', 'url' => route('delivery-notes.index')],
                ['label' => 'Nouveau Bon', 'url' => null]
            ]" />
            <div class="mt-4">
                <h1 class="text-2xl font-bold text-gray-900">Nouveau Bon de Livraison</h1>
                <p class="mt-1 text-sm text-gray-600">Créez un nouveau bon de livraison pour une commande fournisseur</p>
            </div>
        </div>

        <form action="{{ route('delivery-notes.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Sélection de la commande --}}
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Commande fournisseur</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="purchase_order_id" class="block text-sm font-medium text-gray-700">Commande *</label>
                            <select name="purchase_order_id" id="purchase_order_id" required x-model="selectedOrderId" @change="loadOrderDetails"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('purchase_order_id') border-red-300 @enderror">
                                <option value="">Sélectionner une commande</option>
                                @foreach($purchaseOrders as $order)
                                    <option value="{{ $order->id }}" {{ old('purchase_order_id') == $order->id ? 'selected' : '' }}>
                                        {{ $order->reference }} - {{ $order->supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('purchase_order_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="delivery_date" class="block text-sm font-medium text-gray-700">Date de livraison *</label>
                            <input type="date" name="delivery_date" id="delivery_date" required
                                   value="{{ old('delivery_date', date('Y-m-d')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('delivery_date') border-red-300 @enderror">
                            @error('delivery_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Informations de la commande sélectionnée --}}
                    <div x-show="selectedOrderId" class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-2">Détails de la commande</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Fournisseur:</span>
                                <span x-text="orderDetails.supplier" class="block font-medium"></span>
                            </div>
                            <div>
                                <span class="text-gray-500">Date commande:</span>
                                <span x-text="orderDetails.date" class="block font-medium"></span>
                            </div>
                            <div>
                                <span class="text-gray-500">Articles:</span>
                                <span x-text="orderDetails.items_count" class="block font-medium"></span>
                            </div>
                            <div>
                                <span class="text-gray-500">Montant:</span>
                                <span x-text="orderDetails.total_amount" class="block font-medium"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Articles livrés --}}
            <div class="bg-white shadow rounded-lg" x-show="selectedOrderId">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Articles livrés</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commandé</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Livré</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <template x-for="(item, index) in orderItems" :key="index">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900" x-text="item.product_name"></div>
                                            <div class="text-sm text-gray-500" x-text="item.product_code"></div>
                                            <input type="hidden" :name="`items[${index}][product_id]`" :value="item.product_id">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-900" x-text="item.ordered_quantity"></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" 
                                                   :name="`items[${index}][delivered_quantity]`" 
                                                   x-model="item.delivered_quantity" 
                                                   @input="calculateItemTotal(index)"
                                                   :max="item.ordered_quantity"
                                                   min="0" 
                                                   step="1" 
                                                   required
                                                   class="block w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" 
                                                   :name="`items[${index}][unit_price]`" 
                                                   x-model="item.unit_price" 
                                                   @input="calculateItemTotal(index)"
                                                   min="0" 
                                                   step="0.01" 
                                                   required
                                                   class="block w-24 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-medium text-gray-900" x-text="formatCurrency(item.total)"></span>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="orderItems.length === 0">
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        Sélectionnez une commande pour voir les articles
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Totaux --}}
                    <div class="mt-6 flex justify-end" x-show="orderItems.length > 0">
                        <div class="w-64">
                            <div class="flex justify-between text-sm">
                                <span>Sous-total :</span>
                                <span x-text="formatCurrency(subtotal)"></span>
                            </div>
                            <div class="flex justify-between text-sm mt-2">
                                <span>TVA (18%) :</span>
                                <span x-text="formatCurrency(tax)"></span>
                            </div>
                            <div class="flex justify-between text-lg font-bold mt-2 pt-2 border-t">
                                <span>Total :</span>
                                <span x-text="formatCurrency(total)"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Informations complémentaires --}}
            <div class="bg-white shadow rounded-lg" x-show="selectedOrderId">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informations complémentaires</h3>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="delivery_reference" class="block text-sm font-medium text-gray-700">Référence fournisseur</label>
                        <input type="text" name="delivery_reference" id="delivery_reference"
                               value="{{ old('delivery_reference') }}" 
                               placeholder="Référence du bon de livraison fournisseur"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('delivery_reference') border-red-300 @enderror">
                        @error('delivery_reference')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="carrier" class="block text-sm font-medium text-gray-700">Transporteur</label>
                        <input type="text" name="carrier" id="carrier"
                               value="{{ old('carrier') }}" 
                               placeholder="Nom du transporteur"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('carrier') border-red-300 @enderror">
                        @error('carrier')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" rows="3" 
                                  placeholder="Notes sur la livraison..."
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('notes') border-red-300 @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end space-x-4" x-show="selectedOrderId">
                <a href="{{ route('delivery-notes.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" name="status" value="pending"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-yellow-600 hover:bg-yellow-700">
                    Enregistrer en attente
                </button>
                <button type="submit" name="status" value="received"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Marquer comme reçu
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function deliveryNoteForm() {
    return {
        selectedOrderId: '',
        orderDetails: {},
        orderItems: [],
        
        get subtotal() {
            return this.orderItems.reduce((sum, item) => sum + (item.total || 0), 0);
        },
        
        get tax() {
            return this.subtotal * 0.18;
        },
        
        get total() {
            return this.subtotal + this.tax;
        },

        loadOrderDetails() {
            if (!this.selectedOrderId) {
                this.orderDetails = {};
                this.orderItems = [];
                return;
            }

            fetch(`/purchase-orders/${this.selectedOrderId}?format=json`)
                .then(response => response.json())
                .then(data => {
                    this.orderDetails = {
                        supplier: data.supplier.name,
                        date: new Date(data.order_date).toLocaleDateString('fr-FR'),
                        items_count: data.details.length,
                        total_amount: this.formatCurrency(data.total_amount)
                    };

                    this.orderItems = data.details.map(detail => ({
                        product_id: detail.product.id,
                        product_name: detail.product.name,
                        product_code: detail.product.code,
                        ordered_quantity: detail.quantity,
                        delivered_quantity: detail.quantity,
                        unit_price: detail.unit_price,
                        total: detail.quantity * detail.unit_price
                    }));
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des détails:', error);
                });
        },

        calculateItemTotal(index) {
            const item = this.orderItems[index];
            item.total = (item.delivered_quantity || 0) * (item.unit_price || 0);
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