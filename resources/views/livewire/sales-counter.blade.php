{{-- resources/views/livewire/sales-counter.blade.php --}}
<div class="min-h-screen bg-gray-100">
    <div class="flex h-screen">
        {{-- Section gauche - Produits et recherche --}}
        <div class="w-2/3 p-6 bg-white border-r border-gray-200">
            {{-- Header avec recherche --}}
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h1 class="text-2xl font-bold text-gray-900">Point de Vente</h1>
                    <div class="flex items-center space-x-4">
                        <select wire:model="warehouse_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Barre de recherche --}}
                <div class="relative">
                    <div class="relative">
                        <input type="text" 
                               wire:model.live="search" 
                               placeholder="Rechercher un produit par nom, code ou code-barres..."
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    {{-- Résultats de recherche --}}
                    @if($showSearch && count($searchResults) > 0)
                        <div class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-64 overflow-y-auto">
                            @foreach($searchResults as $product)
                                <div wire:click="addToCart({{ $product['id'] }})" 
                                     class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $product['name'] }}</div>
                                            <div class="text-sm text-gray-500">Code: {{ $product['code'] }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-medium text-gray-900">{{ number_format($product['price']) }} F</div>
                                            <div class="text-sm {{ $product['stock'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                Stock: {{ $product['stock'] }} {{ $product['unit'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Panier --}}
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Panier ({{ count($cart) }} articles)</h2>
                    @if(count($cart) > 0)
                        <button wire:click="clearCart" 
                                class="text-red-600 hover:text-red-800 text-sm font-medium">
                            Vider le panier
                        </button>
                    @endif
                </div>

                @if(count($cart) > 0)
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach($cart as $index => $item)
                            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900">{{ $item['name'] }}</h3>
                                        <p class="text-sm text-gray-500">Code: {{ $item['code'] }}</p>
                                        <p class="text-xs text-gray-400">Stock disponible: {{ $item['available_stock'] }} {{ $item['unit'] }}</p>
                                    </div>
                                    <button wire:click="removeFromCart({{ $index }})" 
                                            class="text-red-500 hover:text-red-700 ml-4">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                                
                                <div class="mt-3 flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <label class="text-sm text-gray-600">Quantité:</label>
                                        <input type="number" 
                                               wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                               value="{{ $item['quantity'] }}" 
                                               min="1" 
                                               max="{{ $item['available_stock'] }}"
                                               class="w-20 px-2 py-1 border border-gray-300 rounded text-center">
                                    </div>
                                    
                                    <div class="flex items-center space-x-2">
                                        <label class="text-sm text-gray-600">Prix:</label>
                                        <input type="number" 
                                               wire:change="updatePrice({{ $index }}, $event.target.value)"
                                               value="{{ $item['price'] }}" 
                                               min="0" 
                                               step="0.01"
                                               class="w-24 px-2 py-1 border border-gray-300 rounded text-right">
                                        <span class="text-sm text-gray-600">F</span>
                                    </div>
                                    
                                    <div class="text-right">
                                        <div class="font-medium text-gray-900">
                                            {{ number_format($item['quantity'] * $item['price']) }} F
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <p class="mt-2">Votre panier est vide</p>
                        <p class="text-sm">Recherchez et ajoutez des produits</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Section droite - Facture et paiement --}}
        <div class="w-1/3 p-6 bg-gray-50">
            <div class="space-y-6">
                {{-- Client --}}
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-medium text-gray-900">Client</h3>
                        <button wire:click="openCustomerModal" 
                                class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                            + Nouveau
                        </button>
                    </div>
                    <select wire:model="customer_id" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Client occasionnel</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Remise --}}
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <h3 class="font-medium text-gray-900 mb-3">Remise</h3>
                    <div class="flex items-center space-x-2">
                        <input type="number" 
                               wire:model.live="discount" 
                               min="0" 
                               max="100" 
                               step="0.01"
                               class="w-20 px-2 py-1 border border-gray-300 rounded text-center">
                        <span class="text-gray-600">%</span>
                    </div>
                </div>

                {{-- Totaux --}}
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <h3 class="font-medium text-gray-900 mb-3">Résumé</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Sous-total:</span>
                            <span>{{ number_format($subtotal) }} F</span>
                        </div>
                        @if($discount > 0)
                            <div class="flex justify-between text-red-600">
                                <span>Remise ({{ $discount }}%):</span>
                                <span>-{{ number_format(($subtotal * $discount) / 100) }} F</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span>TVA (18%):</span>
                            <span>{{ number_format($tax) }} F</span>
                        </div>
                        <div class="border-t pt-2 flex justify-between font-bold text-lg">
                            <span>Total:</span>
                            <span>{{ number_format($total) }} F</span>
                        </div>
                    </div>
                </div>

                {{-- Paiement --}}
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <h3 class="font-medium text-gray-900 mb-3">Paiement</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mode de paiement</label>
                            <select wire:model="payment_method" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="cash">Espèces</option>
                                <option value="card">Carte</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="check">Chèque</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Montant reçu</label>
                            <input type="number" 
                                   wire:model.live="payment_amount" 
                                   min="0" 
                                   step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        @if($payment_amount > 0 && $payment_amount >= $total)
                            <div class="bg-green-50 p-3 rounded-md">
                                <div class="flex justify-between text-sm">
                                    <span class="font-medium text-green-800">Monnaie à rendre:</span>
                                    <span class="font-bold text-green-900">{{ number_format($change) }} F</span>
                                </div>
                            </div>
                        @elseif($payment_amount > 0 && $payment_amount < $total)
                            <div class="bg-red-50 p-3 rounded-md">
                                <div class="flex justify-between text-sm">
                                    <span class="font-medium text-red-800">Montant manquant:</span>
                                    <span class="font-bold text-red-900">{{ number_format($total - $payment_amount) }} F</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Notes --}}
                <div class="bg-white p-4 rounded-lg shadow-sm">
                    <h3 class="font-medium text-gray-900 mb-3">Notes</h3>
                    <textarea wire:model="notes" 
                              rows="3" 
                              placeholder="Notes sur la vente..."
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>

                {{-- Actions --}}
                <div class="space-y-3">
                    <button wire:click="processSale" 
                            @disabled(count($cart) === 0 || $payment_amount < $total)
                            class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-indigo-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
                        Finaliser la vente
                    </button>
                    
                    @if(count($cart) > 0)
                        <button wire:click="clearCart" 
                                class="w-full bg-gray-600 text-white py-2 px-4 rounded-lg font-medium hover:bg-gray-700">
                            Annuler la vente
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Nouveau Client --}}
    @if($showCustomerModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Nouveau Client</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                            <input type="text" 
                                   wire:model="customerName" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                            @error('customerName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone *</label>
                            <input type="tel" 
                                   wire:model="customerPhone" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                            @error('customerPhone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" 
                                   wire:model="customerEmail" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                            @error('customerEmail') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 mt-6">
                        <button wire:click="closeCustomerModal" 
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Annuler
                        </button>
                        <button wire:click="createCustomer" 
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Créer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Messages Flash --}}
    @if (session()->has('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif
</div>