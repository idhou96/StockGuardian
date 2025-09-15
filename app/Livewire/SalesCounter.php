<?php

namespace App\Livewire;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\Payment;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesCounter extends Component
{
    // Propriétés de la vente
    public $customer_id;
    public $warehouse_id;
    public $discount = 0;
    public $notes = '';
    public $payment_method = 'cash';
    public $payment_amount = 0;
    
    // Propriétés de recherche produit
    public $search = '';
    public $searchResults = [];
    public $showSearch = false;
    
    // Panier
    public $cart = [];
    
    // Modal
    public $showCustomerModal = false;
    public $customerName = '';
    public $customerPhone = '';
    public $customerEmail = '';
    
    // Calculs
    public $subtotal = 0;
    public $tax = 0;
    public $total = 0;
    public $change = 0;

    protected $rules = [
        'customer_id' => 'nullable|exists:customers,id',
        'warehouse_id' => 'required|exists:warehouses,id',
        'discount' => 'numeric|min:0|max:100',
        'payment_amount' => 'required|numeric|min:0',
        'cart.*.quantity' => 'required|numeric|min:1',
        'cart.*.price' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        // Définir le dépôt par défaut
        $defaultWarehouse = Warehouse::active()->first();
        if ($defaultWarehouse) {
            $this->warehouse_id = $defaultWarehouse->id;
        }
        
        $this->calculateTotals();
    }

    public function updatedSearch()
    {
        if (strlen($this->search) >= 2) {
            $this->searchResults = Product::active()
                ->where(function($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('code', 'like', '%' . $this->search . '%')
                          ->orWhere('barcode', 'like', '%' . $this->search . '%');
                })
                ->with(['warehouseStocks' => function($query) {
                    $query->where('warehouse_id', $this->warehouse_id);
                }])
                ->limit(10)
                ->get()
                ->map(function($product) {
                    $stock = $product->warehouseStocks->first();
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'code' => $product->code,
                        'price' => $product->sale_price,
                        'stock' => $stock ? $stock->quantity : 0,
                        'unit' => $product->unit,
                    ];
                });
            
            $this->showSearch = true;
        } else {
            $this->searchResults = [];
            $this->showSearch = false;
        }
    }

    public function addToCart($productId)
    {
        $product = Product::with(['warehouseStocks' => function($query) {
            $query->where('warehouse_id', $this->warehouse_id);
        }])->find($productId);

        if (!$product) {
            session()->flash('error', 'Produit non trouvé');
            return;
        }

        $stock = $product->warehouseStocks->first();
        $availableStock = $stock ? $stock->quantity : 0;

        // Vérifier si le produit est déjà dans le panier
        $existingKey = collect($this->cart)->search(function($item) use ($productId) {
            return $item['product_id'] == $productId;
        });

        if ($existingKey !== false) {
            // Vérifier le stock disponible
            $newQuantity = $this->cart[$existingKey]['quantity'] + 1;
            if ($newQuantity > $availableStock) {
                session()->flash('error', 'Stock insuffisant pour ' . $product->name);
                return;
            }
            $this->cart[$existingKey]['quantity'] = $newQuantity;
        } else {
            // Vérifier le stock disponible
            if ($availableStock < 1) {
                session()->flash('error', 'Stock insuffisant pour ' . $product->name);
                return;
            }
            
            // Ajouter le nouveau produit
            $this->cart[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'code' => $product->code,
                'price' => $product->sale_price,
                'quantity' => 1,
                'unit' => $product->unit,
                'available_stock' => $availableStock,
            ];
        }

        $this->search = '';
        $this->searchResults = [];
        $this->showSearch = false;
        $this->calculateTotals();
        
        session()->flash('success', 'Produit ajouté au panier');
    }

    public function updateQuantity($index, $quantity)
    {
        if ($quantity <= 0) {
            unset($this->cart[$index]);
            $this->cart = array_values($this->cart); // Réindexer
        } else {
            // Vérifier le stock disponible
            if ($quantity > $this->cart[$index]['available_stock']) {
                session()->flash('error', 'Stock insuffisant');
                return;
            }
            $this->cart[$index]['quantity'] = $quantity;
        }
        
        $this->calculateTotals();
    }

    public function updatePrice($index, $price)
    {
        if ($price >= 0) {
            $this->cart[$index]['price'] = $price;
            $this->calculateTotals();
        }
    }

    public function removeFromCart($index)
    {
        unset($this->cart[$index]);
        $this->cart = array_values($this->cart); // Réindexer
        $this->calculateTotals();
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->calculateTotals();
        session()->flash('success', 'Panier vidé');
    }

    public function calculateTotals()
    {
        $this->subtotal = collect($this->cart)->sum(function($item) {
            return $item['quantity'] * $item['price'];
        });

        $discountAmount = ($this->subtotal * $this->discount) / 100;
        $this->tax = ($this->subtotal - $discountAmount) * 0.18; // TVA 18%
        $this->total = $this->subtotal - $discountAmount + $this->tax;
        
        // Calculer la monnaie
        $this->change = max(0, $this->payment_amount - $this->total);
    }

    public function updatedPaymentAmount()
    {
        $this->calculateTotals();
    }

    public function updatedDiscount()
    {
        $this->calculateTotals();
    }

    public function openCustomerModal()
    {
        $this->showCustomerModal = true;
    }

    public function closeCustomerModal()
    {
        $this->showCustomerModal = false;
        $this->customerName = '';
        $this->customerPhone = '';
        $this->customerEmail = '';
    }

    public function createCustomer()
    {
        $this->validate([
            'customerName' => 'required|string|max:255',
            'customerPhone' => 'required|string|max:20',
            'customerEmail' => 'nullable|email|max:255',
        ]);

        $customer = Customer::create([
            'name' => $this->customerName,
            'phone' => $this->customerPhone,
            'email' => $this->customerEmail,
            'type' => 'individual',
            'is_active' => true,
        ]);

        $this->customer_id = $customer->id;
        $this->closeCustomerModal();
        session()->flash('success', 'Client créé avec succès');
    }

    public function processSale()
    {
        $this->validate();

        if (empty($this->cart)) {
            session()->flash('error', 'Le panier est vide');
            return;
        }

        if ($this->payment_amount < $this->total) {
            session()->flash('error', 'Montant payé insuffisant');
            return;
        }

        DB::beginTransaction();
        try {
            // Créer la vente
            $sale = Sale::create([
                'reference' => Sale::generateReference(),
                'customer_id' => $this->customer_id,
                'warehouse_id' => $this->warehouse_id,
                'user_id' => Auth::id(),
                'sale_date' => now(),
                'status' => 'completed',
                'subtotal' => $this->subtotal,
                'discount_percentage' => $this->discount,
                'discount_amount' => ($this->subtotal * $this->discount) / 100,
                'tax_amount' => $this->tax,
                'total_amount' => $this->total,
                'notes' => $this->notes,
            ]);

            // Ajouter les détails de vente
            foreach ($this->cart as $item) {
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'total_price' => $item['quantity'] * $item['price'],
                ]);

                // Mettre à jour le stock
                $warehouseStock = WarehouseStock::where('warehouse_id', $this->warehouse_id)
                    ->where('product_id', $item['product_id'])
                    ->first();

                if ($warehouseStock) {
                    $warehouseStock->decrement('quantity', $item['quantity']);
                }
            }

            // Créer le paiement
            Payment::create([
                'sale_id' => $sale->id,
                'payment_method' => $this->payment_method,
                'amount' => $this->payment_amount,
                'status' => 'completed',
                'payment_date' => now(),
                'user_id' => Auth::id(),
            ]);

            DB::commit();

            // Réinitialiser le formulaire
            $this->reset(['cart', 'customer_id', 'discount', 'notes', 'payment_amount']);
            $this->calculateTotals();

            session()->flash('success', 'Vente enregistrée avec succès - Référence: ' . $sale->reference);
            
            // Rediriger vers l'impression ou retourner au POS
            return redirect()->route('sales.receipt', $sale);

        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Erreur lors de l\'enregistrement: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $customers = Customer::active()->orderBy('name')->get();
        $warehouses = Warehouse::active()->get();

        return view('livewire.sales-counter', [
            'customers' => $customers,
            'warehouses' => $warehouses,
        ]);
    }
}