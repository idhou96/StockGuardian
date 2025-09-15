<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SalesPos extends Component
{
    // Propriétés principales
    public $cart = [];
    public $cartTotal = 0;
    public $cartItems = 0;
    public $cartTotalTax = 0;
    public $cartTotalWithTax = 0;
    
    // Recherche et produits
    public $searchProduct = '';
    public $searchResults = [];
    public $showProductSearch = false;
    
    // Client
    public $selectedCustomer = null;
    public $searchCustomer = '';
    public $customerResults = [];
    public $showCustomerSearch = false;
    public $newCustomerMode = false;
    public $customerData = [
        'first_name' => '',
        'last_name' => '',
        'phone' => '',
        'email' => ''
    ];
    
    // Entrepôt
    public $selectedWarehouse = null;
    public $warehouses = [];
    
    // Paiement
    public $paymentMethod = 'especes';
    public $paymentAmount = 0;
    public $discount = 0;
    public $discountType = 'amount'; // 'amount' or 'percentage'
    
    // États
    public $showPaymentModal = false;
    public $processing = false;
    
    protected $listeners = [
        'productSelected' => 'addProductToCart',
        'customerSelected' => 'selectCustomer'
    ];

    protected $rules = [
        'selectedWarehouse' => 'required',
        'paymentAmount' => 'required|numeric|min:0',
        'discount' => 'nullable|numeric|min:0',
        'customerData.first_name' => 'required_if:newCustomerMode,true|string|max:255',
        'customerData.last_name' => 'required_if:newCustomerMode,true|string|max:255',
        'customerData.phone' => 'nullable|string|max:20',
        'customerData.email' => 'nullable|email|max:255'
    ];

    public function mount()
    {
        $this->warehouses = Warehouse::active()->get();
        if ($this->warehouses->isNotEmpty()) {
            $this->selectedWarehouse = $this->warehouses->first()->id;
        }
    }

    public function render()
    {
        return view('livewire.sales-pos')->layout('layouts.app');
    }

    // Recherche de produits
    public function updatedSearchProduct()
    {
        if (strlen($this->searchProduct) >= 2) {
            $this->searchResults = Product::active()
                ->where(function($query) {
                    $query->where('name', 'like', '%' . $this->searchProduct . '%')
                          ->orWhere('code', 'like', '%' . $this->searchProduct . '%');
                })
                ->with(['family', 'warehouseStocks' => function($query) {
                    $query->where('warehouse_id', $this->selectedWarehouse);
                }])
                ->limit(10)
                ->get();
            $this->showProductSearch = true;
        } else {
            $this->searchResults = [];
            $this->showProductSearch = false;
        }
    }

    // Ajouter un produit au panier
    public function addProductToCart($productId, $quantity = 1)
    {
        $product = Product::with(['warehouseStocks' => function($query) {
            $query->where('warehouse_id', $this->selectedWarehouse);
        }])->find($productId);

        if (!$product) {
            session()->flash('error', 'Produit non trouvé');
            return;
        }

        // Vérifier le stock disponible
        $availableStock = $product->warehouseStocks->first()->quantity ?? 0;
        $currentCartQuantity = $this->cart[$productId]['quantity'] ?? 0;
        
        if ($currentCartQuantity + $quantity > $availableStock) {
            session()->flash('error', 'Stock insuffisant. Disponible: ' . $availableStock);
            return;
        }

        // Ajouter ou mettre à jour dans le panier
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity'] += $quantity;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'code' => $product->code,
                'price' => $product->sale_price,
                'tax_rate' => $product->tax_rate,
                'quantity' => $quantity,
                'available_stock' => $availableStock
            ];
        }

        $this->updateCartTotals();
        $this->searchProduct = '';
        $this->searchResults = [];
        $this->showProductSearch = false;
    }

    // Mettre à jour la quantité dans le panier
    public function updateCartQuantity($productId, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        if (isset($this->cart[$productId])) {
            $availableStock = $this->cart[$productId]['available_stock'];
            
            if ($quantity > $availableStock) {
                session()->flash('error', 'Stock insuffisant');
                return;
            }

            $this->cart[$productId]['quantity'] = $quantity;
            $this->updateCartTotals();
        }
    }

    // Supprimer du panier
    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
        $this->updateCartTotals();
    }

    // Vider le panier
    public function clearCart()
    {
        $this->cart = [];
        $this->updateCartTotals();
        $this->selectedCustomer = null;
        $this->discount = 0;
        $this->paymentAmount = 0;
    }

    // Mettre à jour les totaux du panier
    public function updateCartTotals()
    {
        $this->cartTotal = 0;
        $this->cartItems = 0;
        $this->cartTotalTax = 0;

        foreach ($this->cart as $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $this->cartTotal += $subtotal;
            $this->cartItems += $item['quantity'];
            
            // Calcul de la TVA
            $taxAmount = $subtotal * ($item['tax_rate'] / 100);
            $this->cartTotalTax += $taxAmount;
        }

        // Appliquer la remise
        $discountAmount = 0;
        if ($this->discount > 0) {
            if ($this->discountType === 'percentage') {
                $discountAmount = $this->cartTotal * ($this->discount / 100);
            } else {
                $discountAmount = min($this->discount, $this->cartTotal);
            }
        }

        $this->cartTotal -= $discountAmount;
        $this->cartTotalWithTax = $this->cartTotal + $this->cartTotalTax;
        
        // Mettre à jour le montant de paiement par défaut
        $this->paymentAmount = $this->cartTotalWithTax;
    }

    // Recherche de clients
    public function updatedSearchCustomer()
    {
        if (strlen($this->searchCustomer) >= 2) {
            $this->customerResults = Customer::where(function($query) {
                $query->where('first_name', 'like', '%' . $this->searchCustomer . '%')
                      ->orWhere('last_name', 'like', '%' . $this->searchCustomer . '%')
                      ->orWhere('phone', 'like', '%' . $this->searchCustomer . '%');
            })
            ->limit(10)
            ->get();
            $this->showCustomerSearch = true;
        } else {
            $this->customerResults = [];
            $this->showCustomerSearch = false;
        }
    }

    // Sélectionner un client
    public function selectCustomer($customerId)
    {
        $this->selectedCustomer = Customer::find($customerId);
        $this->searchCustomer = '';
        $this->customerResults = [];
        $this->showCustomerSearch = false;
        $this->newCustomerMode = false;
    }

    // Mode nouveau client
    public function toggleNewCustomerMode()
    {
        $this->newCustomerMode = !$this->newCustomerMode;
        if ($this->newCustomerMode) {
            $this->selectedCustomer = null;
            $this->searchCustomer = '';
        }
    }

    // Ouvrir modal de paiement
    public function openPaymentModal()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Le panier est vide');
            return;
        }

        if (!$this->selectedWarehouse) {
            session()->flash('error', 'Veuillez sélectionner un entrepôt');
            return;
        }

        $this->showPaymentModal = true;
        $this->paymentAmount = $this->cartTotalWithTax;
    }

    // Finaliser la vente
    public function finalizeSale()
    {
        $this->validate();

        if (empty($this->cart)) {
            session()->flash('error', 'Le panier est vide');
            return;
        }

        if ($this->paymentAmount < $this->cartTotalWithTax) {
            session()->flash('error', 'Le montant payé est insuffisant');
            return;
        }

        $this->processing = true;

        try {
            DB::beginTransaction();

            // Créer le client si nécessaire
            $customer = null;
            if ($this->newCustomerMode && !empty($this->customerData['first_name'])) {
                $customer = Customer::create([
                    'first_name' => $this->customerData['first_name'],
                    'last_name' => $this->customerData['last_name'],
                    'phone' => $this->customerData['phone'],
                    'email' => $this->customerData['email'],
                    'customer_type' => 'particulier'
                ]);
            } elseif ($this->selectedCustomer) {
                $customer = $this->selectedCustomer;
            }

            // Créer la vente
            $sale = Sale::create([
                'reference' => Sale::generateReference(),
                'customer_id' => $customer?->id,
                'warehouse_id' => $this->selectedWarehouse,
                'sale_date' => now()->toDateString(),
                'sale_time' => now()->format('H:i:s'),
                'subtotal' => $this->cartTotal + ($this->discount ?? 0), // Avant remise
                'discount_amount' => $this->discount ?? 0,
                'tax_amount' => $this->cartTotalTax,
                'total_amount' => $this->cartTotalWithTax,
                'payment_method' => $this->paymentMethod,
                'amount_paid' => $this->paymentAmount,
                'change_amount' => $this->paymentAmount - $this->cartTotalWithTax,
                'status' => 'finalisee',
                'created_by' => Auth::id(),
            ]);

            // Ajouter les détails de vente et mettre à jour les stocks
            foreach ($this->cart as $item) {
                // Créer le détail de vente
                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'tax_rate' => $item['tax_rate'],
                    'tax_amount' => ($item['price'] * $item['quantity']) * ($item['tax_rate'] / 100),
                    'total_price' => $item['price'] * $item['quantity'],
                ]);

                // Mettre à jour le stock
                $warehouseStock = WarehouseStock::where([
                    'warehouse_id' => $this->selectedWarehouse,
                    'product_id' => $item['id']
                ])->first();

                if ($warehouseStock) {
                    $warehouseStock->updateQuantity($item['quantity'], 'subtract');
                }
            }

            DB::commit();

            // Réinitialiser le formulaire
            $this->clearCart();
            $this->showPaymentModal = false;
            $this->processing = false;

            session()->flash('success', 'Vente finalisée avec succès. Référence: ' . $sale->reference);
            
            // Rediriger vers la facture
            return redirect()->route('sales.show', $sale);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->processing = false;
            session()->flash('error', 'Erreur lors de la finalisation: ' . $e->getMessage());
        }
    }

    // Calculer la monnaie à rendre
    public function getChangeAmount()
    {
        return max(0, $this->paymentAmount - $this->cartTotalWithTax);
    }

    // Raccourcis clavier (méthode utilitaire)
    public function addPaymentShortcut($amount)
    {
        $this->paymentAmount = $amount;
    }
}