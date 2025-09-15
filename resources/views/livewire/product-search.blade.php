{{-- resources/views/livewire/product-search.blade.php --}}
<div class="space-y-6">
    {{-- Barre de recherche et contrôles --}}
    <div class="bg-white shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            {{-- Recherche principale --}}
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                    Rechercher un produit
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" 
                           type="text" 
                           id="search"
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                           placeholder="Code, nom, famille...">
                    @if($search)
                    <button wire:click="$set('search', '')" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    @endif
                </div>
            </div>

            {{-- Contrôles --}}
            <div class="flex justify-end space-x-2">
                <button wire:click="toggleFilters" 
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 {{ $showFilters ? 'bg-blue-50 border-blue-300 text-blue-700' : '' }}">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    Filtres
                </button>

                <button wire:click="toggleMode" 
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    @if($mode === 'list')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                    @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                    @endif
                </button>
            </div>
        </div>

        {{-- Filtres avancés --}}
        @if($showFilters)
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Entrepôt --}}
                <div>
                    <label for="selectedWarehouse" class="block text-sm font-medium text-gray-700 mb-1">Entrepôt</label>
                    <select wire:model.live="selectedWarehouse" 
                            id="selectedWarehouse"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Tous les entrepôts</option>
                        @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Famille --}}
                <div>
                    <label for="selectedFamily" class="block text-sm font-medium text-gray-700 mb-1">Famille</label>
                    <select wire:model.live="selectedFamily" 
                            id="selectedFamily"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Toutes les familles</option>
                        @foreach($families as $family)
                        <option value="{{ $family->id }}">{{ $family->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Statut stock --}}
                <div>
                    <label for="stockFilter" class="block text-sm font-medium text-gray-700 mb-1">Statut Stock</label>
                    <select wire:model.live="stockFilter" 
                            id="stockFilter"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="all">Tous</option>
                        <option value="in_stock">En stock</option>
                        <option value="low_stock">Stock faible</option>
                        <option value="out_of_stock">Rupture</option>
                    </select>
                </div>

                {{-- Prix --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prix de vente</label>
                    <div class="grid grid-cols-2 gap-2">
                        <input wire:model.live.debounce.500ms="priceMin" 
                               type="number" 
                               step="0.01"
                               placeholder="Min"
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <input wire:model.live.debounce.500ms="priceMax" 
                               type="number" 
                               step="0.01"
                               placeholder="Max"
                               class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                </div>
            </div>

            {{-- Actions filtres --}}
            <div class="mt-4 flex justify-end space-x-2">
                <button wire:click="clearFilters" 
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Effacer les filtres
                </button>
            </div>
        </div>
        @endif
    </div>

    {{-- Résultats --}}
    <div class="bg-white shadow rounded-lg">
        {{-- Header des résultats --}}
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">
                        Produits
                        <span class="text-sm text-gray-500">({{ $products->total() }} résultats)</span>
                    </h3>
                </div>

                {{-- Tri --}}
                <div class="flex items-center space-x-2">
                    <label class="text-sm text-gray-700">Trier par:</label>
                    <div class="flex space-x-1">
                        <button wire:click="sortBy('name')" 
                                class="px-2 py-1 text-xs font-medium rounded {{ $sortBy === 'name' ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:text-gray-800' }}">
                            Nom
                            @if($sortBy === 'name')
                            <svg class="inline w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                @if($sortDirection === 'asc')
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                @else
                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                @endif
                            </svg>
                            @endif
                        </button>
                        <button wire:click="sortBy('sale_price')" 
                                class="px-2 py-1 text-xs font-medium rounded {{ $sortBy === 'sale_price' ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:text-gray-800' }}">
                            Prix
                            @if($sortBy === 'sale_price')
                            <svg class="inline w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                @if($sortDirection === 'asc')
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                @else
                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                @endif
                            </svg>
                            @endif
                        </button>
                        <button wire:click="sortBy('code')" 
                                class="px-2 py-1 text-xs font-medium rounded {{ $sortBy === 'code' ? 'bg-blue-100 text-blue-800' : 'text-gray-600 hover:text-gray-800' }}">
                            Code
                            @if($sortBy === 'code')
                            <svg class="inline w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                @if($sortDirection === 'asc')
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                @else
                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                @endif
                            </svg>
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Liste/Grille des produits --}}
        <div class="p-6">
            @if($products->count() > 0)
                @if($mode === 'list')
                    {{-- Mode liste --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Famille</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($products as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if($product->image)
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}">
                                                @else
                                                <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                    </svg>
                                                </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $product->code }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $product->family->name ?? 'Sans famille' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($product->sale_price, 0, ',', ' ') }} F
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                        $stock = $this->getStockForWarehouse($product);
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $stock <= 0 ? 'bg-red-100 text-red-800' : ($stock <= ($product->minimum_stock ?? 0) ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                            {{ $stock }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button wire:click="viewProduct({{ $product->id }})" 
                                                    class="text-blue-600 hover:text-blue-900">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </button>
                                            @if($stock > 0)
                                            <button wire:click="addToCart({{ $product->id }})" 
                                                    class="text-green-600 hover:text-green-900">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"></path>
                                                </svg>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    {{-- Mode grille --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($products as $product)
                        <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                            {{-- Image --}}
                            <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden rounded-t-lg bg-gray-200">
                                @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="h-48 w-full object-cover object-center">
                                @else
                                <div class="h-48 w-full flex items-center justify-center bg-gray-100">
                                    <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                @endif
                            </div>

                            {{-- Contenu --}}
                            <div class="p-4">
                                <h3 class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</h3>
                                <p class="text-xs text-gray-500">{{ $product->code }}</p>
                                <p class="text-xs text-gray-500">{{ $product->family->name ?? 'Sans famille' }}</p>
                                
                                <div class="mt-2 flex items-center justify-between">
                                    <span class="text-lg font-medium text-gray-900">{{ number_format($product->sale_price, 0, ',', ' ') }} F</span>
                                    @php
                                    $stock = $this->getStockForWarehouse($product);
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                        {{ $stock <= 0 ? 'bg-red-100 text-red-800' : ($stock <= ($product->minimum_stock ?? 0) ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                        Stock: {{ $stock }}
                                    </span>
                                </div>

                                {{-- Actions --}}
                                <div class="mt-3 flex space-x-2">
                                    <button wire:click="viewProduct({{ $product->id }})" 
                                            class="flex-1 bg-gray-100 text-gray-900 text-xs font-medium py-2 px-3 rounded hover:bg-gray-200">
                                        Voir
                                    </button>
                                    @if($stock > 0)
                                    <button wire:click="addToCart({{ $product->id }})" 
                                            class="flex-1 bg-blue-600 text-white text-xs font-medium py-2 px-3 rounded hover:bg-blue-700">
                                        Ajouter
                                    </button>
                                    @else
                                    <button disabled 
                                            class="flex-1 bg-gray-300 text-gray-500 text-xs font-medium py-2 px-3 rounded cursor-not-allowed">
                                        Rupture
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif

                {{-- Pagination --}}
                <div class="mt-6">
                    {{ $products->links() }}
                </div>
            @else
                {{-- Aucun résultat --}}
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun produit trouvé</h3>
                    <p class="mt-1 text-sm text-gray-500">Essayez de modifier vos critères de recherche.</p>
                    @if($search || $selectedFamily || $selectedWarehouse || $stockFilter !== 'all')
                    <div class="mt-6">
                        <button wire:click="clearFilters" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Effacer les filtres
                        </button>
                    </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Messages flash --}}
    @if(session()->has('success'))
    <div class="fixed bottom-4 right-4 z-50">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg">
            {{ session('success') }}
        </div>
    </div>
    @endif
</div>

{{-- Loading indicator --}}
<div wire:loading class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-25">
    <div class="bg-white rounded-lg p-6 shadow-xl">
        <div class="flex items-center space-x-3">
            <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-900">Chargement...</span>
        </div>
    </div>
</div>