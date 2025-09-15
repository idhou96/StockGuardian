{{-- resources/views/purchase-orders/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Commandes Fournisseurs')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <x-breadcrumb :items="[
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Commandes Fournisseurs', 'url' => null]
            ]" />
            <div class="mt-4 flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Commandes Fournisseurs</h1>
                    <p class="mt-1 text-sm text-gray-600">Gérez vos commandes d'achat auprès des fournisseurs</p>
                </div>
                @can('create', App\Models\PurchaseOrder::class)
                    <a href="{{ route('purchase-orders.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Nouvelle Commande
                    </a>
                @endcan
            </div>
        </div>

        {{-- Statistiques --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">En Attente</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['pending'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Confirmées</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['confirmed'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Livrées</p>
                        <p class="text-lg font-semibold text-gray-900">{{ $stats['delivered'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Montant Total</p>
                        <p class="text-lg font-semibold text-gray-900">{{ number_format($stats['total_amount'] ?? 0, 0, ',', ' ') }} F</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtres --}}
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Filtres</h3>
            </div>
            <div class="px-6 py-4">
                <form method="GET" action="{{ route('purchase-orders.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fournisseur</label>
                        <select name="supplier_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Tous les fournisseurs</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Statut</label>
                        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Tous les statuts</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmée</option>
                            <option value="partially_delivered" {{ request('status') == 'partially_delivered' ? 'selected' : '' }}>Partiellement livrée</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Livrée</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date début</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date fin</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Filtrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table des commandes --}}
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Liste des Commandes</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fournisseur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $order->reference }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $order->supplier->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $order->supplier->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $order->order_date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'draft' => 'gray',
                                            'pending' => 'yellow',
                                            'confirmed' => 'blue',
                                            'partially_delivered' => 'orange',
                                            'delivered' => 'green',
                                            'cancelled' => 'red'
                                        ];
                                        $statusLabels = [
                                            'draft' => 'Brouillon',
                                            'pending' => 'En attente',
                                            'confirmed' => 'Confirmée',
                                            'partially_delivered' => 'Partiellement livrée',
                                            'delivered' => 'Livrée',
                                            'cancelled' => 'Annulée'
                                        ];
                                        $color = $statusColors[$order->status] ?? 'gray';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800">
                                        {{ $statusLabels[$order->status] ?? $order->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ number_format($order->total_amount, 0, ',', ' ') }} F
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('purchase-orders.show', $order) }}" 
                                           class="text-indigo-600 hover:text-indigo-900">
                                            Voir
                                        </a>
                                        @can('update', $order)
                                            @if($order->status === 'draft')
                                                <a href="{{ route('purchase-orders.edit', $order) }}" 
                                                   class="text-blue-600 hover:text-blue-900">
                                                    Modifier
                                                </a>
                                            @endif
                                        @endcan
                                        @can('confirm', $order)
                                            @if($order->status === 'pending')
                                                <button onclick="confirmOrder({{ $order->id }})" 
                                                        class="text-green-600 hover:text-green-900">
                                                    Confirmer
                                                </button>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    Aucune commande trouvée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $orders->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Modal de confirmation --}}
<div id="confirmModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmer la commande</h3>
            <p class="text-sm text-gray-500 mb-6">
                Êtes-vous sûr de vouloir confirmer cette commande ? Elle ne pourra plus être modifiée.
            </p>
            <div class="flex justify-center space-x-4">
                <button onclick="closeModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Annuler
                </button>
                <form id="confirmForm" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Confirmer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmOrder(orderId) {
    document.getElementById('confirmForm').action = `/purchase-orders/${orderId}/confirm`;
    document.getElementById('confirmModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('confirmModal').classList.add('hidden');
}
</script>
@endpush
@endsection

{{-- resources/views/purchase-orders/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nouvelle Commande Fournisseur')

@section('content')
<div class="py-6" x-data="purchaseOrderForm()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <x-breadcrumb :items="[
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Commandes Fournisseurs', 'url' => route('purchase-orders.index')],
                ['label' => 'Nouvelle Commande', 'url' => null]
            ]" />
            <div class="mt-4">
                <h1 class="text-2xl font-bold text-gray-900">Nouvelle Commande Fournisseur</h1>
                <p class="mt-1 text-sm text-gray-600">Créez une nouvelle commande d'achat</p>
            </div>
        </div>

        <form action="{{ route('purchase-orders.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Informations générales --}}
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informations de la commande</h3>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="supplier_id" class="block text-sm font-medium text-gray-700">Fournisseur *</label>
                        <select name="supplier_id" id="supplier_id" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('supplier_id') border-red-300 @enderror">
                            <option value="">Sélectionner un fournisseur</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                    {{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="warehouse_id" class="block text-sm font-medium text-gray-700">Dépôt de livraison *</label>
                        <select name="warehouse_id" id="warehouse_id" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('warehouse_id') border-red-300 @enderror">
                            <option value="">Sélectionner un dépôt</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('warehouse_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="order_date" class="block text-sm font-medium text-gray-700">Date de commande *</label>
                        <input type="date" name="order_date" id="order_date" 
                               value="{{ old('order_date', date('Y-m-d')) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('order_date') border-red-300 @enderror">
                        @error('order_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="expected_date" class="block text-sm font-medium text-gray-700">Date de livraison prévue</label>
                        <input type="date" name="expected_date" id="expected_date" 
                               value="{{ old('expected_date') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('expected_date') border-red-300 @enderror">
                        @error('expected_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" rows="3" 
                                  placeholder="Notes internes sur cette commande..."
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('notes') border-red-300 @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Produits --}}
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Produits commandés</h3>
                    <button type="button" @click="addProduct()" 
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Ajouter un produit
                    </button>
                </div>
                <div class="px-6 py-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <template x-for="(item, index) in items" :key="index">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <select :name="`items[${index}][product_id]`" x-model="item.product_id" @change="updateProduct(index)" required
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="">Sélectionner un produit</option>
                                                @foreach($products as $product)
                                                    <option value="{{ $product->id }}" data-price="{{ $product->purchase_price }}">
                                                        {{ $product->name }} ({{ $product->code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" :name="`items[${index}][quantity]`" x-model="item.quantity" @input="calculateTotal(index)" 
                                                   min="1" step="1" required placeholder="Quantité"
                                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="number" :name="`items[${index}][unit_price]`" x-model="item.unit_price" @input="calculateTotal(index)" 
                                                   min="0" step="0.01" required placeholder="Prix"
                                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-medium text-gray-900" x-text="formatCurrency(item.total)"></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button type="button" @click="removeProduct(index)" 
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
                                        Aucun produit ajouté. Cliquez sur "Ajouter un produit" pour commencer.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Totaux --}}
                    <div class="mt-6 flex justify-end">
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

            {{-- Actions --}}
            <div class="flex justify-end space-x-4">
                <a href="{{ route('purchase-orders.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" name="status" value="draft"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700">
                    Enregistrer en brouillon
                </button>
                <button type="submit" name="status" value="pending"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Enregistrer et envoyer
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function purchaseOrderForm() {
    return {
        items: [],
        
        get subtotal() {
            return this.items.reduce((sum, item) => sum + (item.total || 0), 0);
        },
        
        get tax() {
            return this.subtotal * 0.18;
        },
        
        get total() {
            return this.subtotal + this.tax;
        },
        
        addProduct() {
            this.items.push({
                product_id: '',
                quantity: 1,
                unit_price: 0,
                total: 0
            });
        },
        
        removeProduct(index) {
            this.items.splice(index, 1);
        },
        
        updateProduct(index) {
            const select = event.target;
            const option = select.options[select.selectedIndex];
            if (option.dataset.price) {
                this.items[index].unit_price = parseFloat(option.dataset.price);
                this.calculateTotal(index);
            }
        },
        
        calculateTotal(index) {
            const item = this.items[index];
            item.total = (item.quantity || 0) * (item.unit_price || 0);
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