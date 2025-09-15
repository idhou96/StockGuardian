<div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <!-- En-tête -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Point de Vente</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Entrepôt: 
                        <select wire:model.live="selectedWarehouse" 
                                class="ml-2 text-sm border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </p>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Raccourcis -->
                    <div class="hidden md:flex items-center space-x-2">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Raccourcis:</span>
                        <kbd class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded-lg">F1</kbd>
                        <span class="text-xs text-gray-500">Nouveau</span>
                        <kbd class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded-lg">F3</kbd>
                        <span class="text-xs text-gray-500">Payer</span>
                    </div>
                    
                    <!-- Actions -->
                    <button wire:click="clearCart" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Vider
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale - Recherche et produits -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Recherche de produits -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" 
                               wire:model.live.debounce.300ms="searchProduct"
                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-lg"
                               placeholder="Rechercher un produit par nom ou code..."
                               autocomplete="off">
                               
                        <!-- Résultats de recherche -->
                        @if($showProductSearch && count($searchResults) > 0)
                        <div class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-800 shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                            @foreach($searchResults as $product)
                            @php
                                $stock = $product->warehouseStocks->first()->quantity ?? 0;
                            @endphp
                            <div wire:click="addProductToCart({{ $product->id }})"
                                 class="cursor-pointer select-none relative py-3 px-3 hover:bg-gray-100 dark:hover:bg-gray-700 {{ $stock <= 0 ? 'opacity-50' : '' }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $product->name }}
                                            </div>
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                {{ $product->family->name ?? 'N/A' }}
                                            </span>
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            Code: {{ $product->code }} | Stock: {{ $stock }}
                                        </div>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ number_format($product->sale_price, 0, ',', ' ') }} FCFA
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Panier -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            Panier ({{ $cartItems }} article{{ $cartItems > 1 ? 's' : '' }})
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        @if(empty($cart))
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 6M7 13l-1.5-6m10 6v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2-2"/>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">Votre panier est vide</p>
                            <p class="text-sm text-gray-400 dark:text-gray-500">Recherchez et ajoutez des produits</p>
                        </div>
                        @else
                        <div class="space-y-4">
                            @foreach($cart as $productId => $item)
                            <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-600 rounded-lg">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $item['name'] }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Code: {{ $item['code'] }} | Prix unitaire: {{ number_format($item['price'], 0, ',', ' ') }} FCFA
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">
                                        Stock disponible: {{ $item['available_stock'] }}
                                    </p>
                                </div>
                                
                                <div class="flex items-center space-x-3">
                                    <!-- Quantité -->
                                    <div class="flex items-center space-x-2">
                                        <button wire:click="updateCartQuantity({{ $productId }}, {{ $item['quantity'] - 1 }})"
                                                class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        
                                        <input type="number" 
                                               wire:change="updateCartQuantity({{ $productId }}, $event.target.value)"
                                               value="{{ $item['quantity'] }}"
                                               min="1" 
                                               max="{{ $item['available_stock'] }}"
                                               class="w-16 text-center text-sm border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        
                                        <button wire:click="updateCartQuantity({{ $productId }}, {{ $item['quantity'] + 1 }})"
                                                class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                                {{ $item['quantity'] >= $item['available_stock'] ? 'disabled' : '' }}>
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <!-- Total -->
                                    <div class="text-sm font-medium text-gray-900 dark:text-white w-24 text-right">
                                        {{ number_format($item['price'] * $item['quantity'], 0, ',', ' ') }} FCFA
                                    </div>
                                    
                                    <!-- Supprimer -->
                                    <button wire:click="removeFromCart({{ $productId }})"
                                            class="p-1 text-red-400 hover:text-red-600">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Colonne droite - Résumé et paiement -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow sticky top-6">
                    <!-- Client -->
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Client</h3>
                        
                        @if($selectedCustomer)
                        <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                    {{ $selectedCustomer->first_name }} {{ $selectedCustomer->last_name }}
                                </p>
                                @if($selectedCustomer->phone)
                                <p class="text-xs text-green-600 dark:text-green-300">{{ $selectedCustomer->phone }}</p>
                                @endif
                            </div>
                            <button wire:click="$set('selectedCustomer', null)" 
                                    class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        @elseif($newCustomerMode)
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Nouveau client</span>
                                <button wire:click="toggleNewCustomerMode" 
                                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="space-y-2">
                                <input type="text" 
                                       wire:model="customerData.first_name"
                                       placeholder="Prénom*"
                                       class="block w-full text-sm border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <input type="text" 
                                       wire:model="customerData.last_name"
                                       placeholder="Nom*"
                                       class="block w-full text-sm border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <input type="text" 
                                       wire:model="customerData.phone"
                                       placeholder="Téléphone"
                                       class="block w-full text-sm border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                        </div>
                        @else
                        <div class="relative">
                            <input type="text" 
                                   wire:model.live.debounce.300ms="searchCustomer"
                                   placeholder="Rechercher un client..."
                                   class="block w-full text-sm border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            
                            @if($showCustomerSearch && count($customerResults) > 0)
                            <div class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-800 shadow-lg max-h-40 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto">
                                @foreach($customerResults as $customer)
                                <div wire:click="selectCustomer({{ $customer->id }})"
                                     class="cursor-pointer select-none relative py-2 px-3 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $customer->first_name }} {{ $customer->last_name }}
                                    </div>
                                    @if($customer->phone)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $customer->phone }}</div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @endif
                            
                            <button wire:click="toggleNewCustomerMode"
                                    class="mt-2 text-sm text-primary-600 hover:text-primary-500">
                                + Nouveau client
                            </button>
                        </div>
                        @endif
                    </div>

                    <!-- Remise -->
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Remise</h4>
                        <div class="flex items-center space-x-2">
                            <input type="number" 
                                   wire:model.live="discount"
                                   wire:change="updateCartTotals"
                                   min="0"
                                   step="0.01"
                                   class="flex-1 text-sm border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <select wire:model.live="discountType" 
                                    wire:change="updateCartTotals"
                                    class="text-sm border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="amount">FCFA</option>
                                <option value="percentage">%</option>
                            </select>
                        </div>
                    </div>

                    <!-- Résumé des totaux -->
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 space-y-2">
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                            <span>Sous-total</span>
                            <span>{{ number_format($cartTotal + ($discount ?? 0), 0, ',', ' ') }} FCFA</span>
                        </div>
                        
                        @if($discount > 0)
                        <div class="flex justify-between text-sm text-green-600">
                            <span>Remise</span>
                            <span>-{{ number_format($discount, 0, ',', ' ') }} {{ $discountType === 'percentage' ? '%' : 'FCFA' }}</span>
                        </div>
                        @endif
                        
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                            <span>TVA</span>
                            <span>{{ number_format($cartTotalTax, 0, ',', ' ') }} FCFA</span>
                        </div>
                        
                        <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white border-t pt-2">
                            <span>Total</span>
                            <span>{{ number_format($cartTotalWithTax, 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="px-6 py-4 space-y-3">
                        <button wire:click="openPaymentModal" 
                                @disabled(empty($cart))
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Encaisser
                        </button>
                        
                        <button wire:click="clearCart" 
                                class="w-full flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Vider le panier
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de paiement -->
    @if($showPaymentModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="$set('showPaymentModal', false)"></div>
            
            <div class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div>
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-5">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                            Finaliser la vente
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Total à encaisser: <span class="font-semibold">{{ number_format($cartTotalWithTax, 0, ',', ' ') }} FCFA</span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-5 sm:mt-6 space-y-4">
                    <!-- Mode de paiement -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mode de paiement</label>
                        <select wire:model="paymentMethod" 
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                            <option value="especes">Espèces</option>
                            <option value="carte_bancaire">Carte bancaire</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="cheque">Chèque</option>
                            <option value="virement">Virement</option>
                        </select>
                    </div>

                    <!-- Montant reçu -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Montant reçu (FCFA)</label>
                        <input type="number" 
                               wire:model.live="paymentAmount"
                               min="{{ $cartTotalWithTax }}"
                               step="0.01"
                               class="mt-1 block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Raccourcis de montant -->
                    @if($paymentMethod === 'especes')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Raccourcis</label>
                        <div class="grid grid-cols-3 gap-2">
                            @php
                                $shortcuts = [
                                    ceil($cartTotalWithTax / 1000) * 1000,
                                    ceil($cartTotalWithTax / 5000) * 5000,
                                    ceil($cartTotalWithTax / 10000) * 10000
                                ];
                            @endphp
                            @foreach($shortcuts as $amount)
                            <button wire:click="addPaymentShortcut({{ $amount }})"
                                    class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                                {{ number_format($amount, 0, ',', ' ') }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Monnaie à rendre -->
                    @if($paymentAmount > $cartTotalWithTax)
                    <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                    Monnaie à rendre: {{ number_format($this->getChangeAmount(), 0, ',', ' ') }} FCFA
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <button wire:click="finalizeSale" 
                            wire:loading.attr="disabled"
                            @disabled($processing || $paymentAmount < $cartTotalWithTax)
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:col-start-2 sm:text-sm disabled:opacity-50">
                        <span wire:loading.remove>Confirmer la vente</span>
                        <span wire:loading>Traitement...</span>
                    </button>
                    
                    <button wire:click="$set('showPaymentModal', false)" 
                            type="button" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Loading overlay -->
    <div wire:loading.flex class="fixed inset-0 z-50 items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 flex items-center space-x-3">
            <svg class="animate-spin h-6 w-6 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-700 dark:text-gray-300">Traitement en cours...</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Raccourcis clavier
    document.addEventListener('keydown', function(e) {
        // F1 - Nouveau (vider panier)
        if (e.key === 'F1') {
            e.preventDefault();
            Livewire.emit('clearCart');
        }
        
        // F3 - Payer
        if (e.key === 'F3') {
            e.preventDefault();
            Livewire.emit('openPaymentModal');
        }
        
        // Échap - Fermer modal
        if (e.key === 'Escape') {
            Livewire.emit('$set', 'showPaymentModal', false);
        }
    });
});
</script>
@endpush