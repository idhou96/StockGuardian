<?php
// app/Livewire/ProductSearch.php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Warehouse;
use Livewire\Component;
use Livewire\WithPagination;

class ProductSearch extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedWarehouse = '';
    public $selectedFamily = '';
    public $priceMin = '';
    public $priceMax = '';
    public $stockFilter = 'all'; // all, in_stock, low_stock, out_of_stock
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $mode = 'list'; // list, grid
    public $showFilters = false;
    
    public $warehouses;
    public $families;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedWarehouse' => ['except' => ''],
        'selectedFamily' => ['except' => ''],
        'stockFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'mode' => ['except' => 'list'],
    ];

    public function mount()
    {
        $this->warehouses = Warehouse::where('is_active', true)->orderBy('name')->get();
        $this->families = \App\Models\Family::orderBy('name')->get();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedWarehouse()
    {
        $this->resetPage();
    }

    public function updatedSelectedFamily()
    {
        $this->resetPage();
    }

    public function updatedStockFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedWarehouse = '';
        $this->selectedFamily = '';
        $this->priceMin = '';
        $this->priceMax = '';
        $this->stockFilter = 'all';
        $this->sortBy = 'name';
        $this->sortDirection = 'asc';
        $this->resetPage();
    }

    public function toggleMode()
    {
        $this->mode = $this->mode === 'list' ? 'grid' : 'list';
    }

    public function getProductsProperty()
    {
        $query = Product::with(['family', 'warehouseStocks.warehouse']);

        // Recherche textuelle
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%')
                  ->orWhere('generic_name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('family', function($familyQuery) {
                      $familyQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Filtre par famille
        if ($this->selectedFamily) {
            $query->where('family_id', $this->selectedFamily);
        }

        // Filtre par prix
        if ($this->priceMin) {
            $query->where('sale_price', '>=', $this->priceMin);
        }
        if ($this->priceMax) {
            $query->where('sale_price', '<=', $this->priceMax);
        }

        // Filtre par stock
        if ($this->stockFilter !== 'all') {
            $query->whereHas('warehouseStocks', function($stockQuery) {
                if ($this->selectedWarehouse) {
                    $stockQuery->where('warehouse_id', $this->selectedWarehouse);
                }

                switch ($this->stockFilter) {
                    case 'in_stock':
                        $stockQuery->where('current_stock', '>', 0);
                        break;
                    case 'low_stock':
                        $stockQuery->whereColumn('current_stock', '<=', 'minimum_stock');
                        break;
                    case 'out_of_stock':
                        $stockQuery->where('current_stock', '<=', 0);
                        break;
                }
            });
        }

        // Tri
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(12);
    }

    public function getStockForWarehouse($product, $warehouseId = null)
    {
        if (!$warehouseId && $this->selectedWarehouse) {
            $warehouseId = $this->selectedWarehouse;
        }

        if ($warehouseId) {
            $stock = $product->warehouseStocks->where('warehouse_id', $warehouseId)->first();
            return $stock ? $stock->current_stock : 0;
        }

        return $product->warehouseStocks->sum('current_stock');
    }

    public function addToCart($productId)
    {
        $this->dispatch('product-added-to-cart', productId: $productId);
        
        // Flash message
        session()->flash('success', 'Produit ajouté au panier');
    }

    public function viewProduct($productId)
    {
        return redirect()->route('products.show', $productId);
    }

    public function render()
    {
        return view('livewire.product-search', [
            'products' => $this->products,
        ]);
    }
}