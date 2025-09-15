<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\StockMovement;
use App\Models\PurchaseOrder;
use App\Models\Family;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Page principale des rapports
     */
    public function index()
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'responsable_achats', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        // Statistiques générales pour la page d'accueil des rapports
        $stats = $this->getGeneralStats();

        return view('reports.index', compact('stats'));
    }

    /**
     * Rapport des ventes
     */
    public function salesReport(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        $query = Sale::with(['customer', 'createdBy', 'warehouse']);
        
        // Filtres
        $this->applySalesReportFilters($query, $request);

        // Données pour le rapport
        $sales = $query->orderBy('sale_date', 'desc')->paginate(100);
        
        // Statistiques du rapport
        $reportStats = $this->getSalesReportStats($query);
        
        // Données pour les graphiques
        $chartData = $this->getSalesChartData($request);

        $customers = Customer::active()->get();
        $warehouses = Warehouse::active()->get();

        return view('reports.sales', compact('sales', 'reportStats', 'chartData', 'customers', 'warehouses'));
    }

    /**
     * Rapport des achats
     */
    public function purchaseReport(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_achats', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        $query = PurchaseOrder::with(['supplier', 'createdBy', 'warehouse']);
        
        // Filtres
        $this->applyPurchaseReportFilters($query, $request);

        $orders = $query->orderBy('order_date', 'desc')->paginate(100);
        
        $reportStats = $this->getPurchaseReportStats($query);
        $chartData = $this->getPurchaseChartData($request);

        $suppliers = Supplier::active()->get();
        $warehouses = Warehouse::active()->get();

        return view('reports.purchase', compact('orders', 'reportStats', 'chartData', 'suppliers', 'warehouses'));
    }

    /**
     * Rapport de stock
     */
    public function stockReport(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        $query = WarehouseStock::with(['product.family', 'warehouse']);
        
        // Filtres
        $this->applyStockReportFilters($query, $request);

        $stocks = $query->orderBy('quantity', 'asc')->paginate(100);
        
        $reportStats = $this->getStockReportStats();
        
        // Produits en rupture et stock faible
        $outOfStockProducts = WarehouseStock::outOfStock()->with(['product', 'warehouse'])->get();
        $lowStockProducts = WarehouseStock::lowStock()->with(['product', 'warehouse'])->get();

        $families = Family::active()->get();
        $warehouses = Warehouse::active()->get();

        return view('reports.stock', compact('stocks', 'reportStats', 'outOfStockProducts', 'lowStockProducts', 'families', 'warehouses'));
    }

    /**
     * Rapport des mouvements de stock
     */
    public function stockMovementReport(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'magasinier', 'responsable_achats'])) {
            return $this->unauthorizedResponse();
        }

        $query = StockMovement::with(['product.family', 'warehouse', 'createdBy']);
        
        // Filtres
        $this->applyStockMovementReportFilters($query, $request);

        $movements = $query->orderBy('movement_date', 'desc')
                          ->orderBy('movement_time', 'desc')
                          ->paginate(100);
        
        $reportStats = $this->getStockMovementReportStats($query);
        $chartData = $this->getStockMovementChartData($request);

        $products = Product::active()->get();
        $warehouses = Warehouse::active()->get();

        return view('reports.stock-movements', compact('movements', 'reportStats', 'chartData', 'products', 'warehouses'));
    }

    /**
     * Rapport des clients
     */
    public function customerReport(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        // Clients avec leurs statistiques de ventes
        $query = Customer::withCount('sales')
                        ->withSum('sales as total_purchases', 'total_amount')
                        ->with(['sales' => function($q) {
                            $q->latest()->take(5);
                        }]);

        // Filtres
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($customerType = $request->get('customer_type')) {
            $query->where('customer_type', $customerType);
        }

        $customers = $query->orderBy('total_purchases', 'desc')->paginate(50);
        
        $reportStats = $this->getCustomerReportStats();

        return view('reports.customers', compact('customers', 'reportStats'));
    }

    /**
     * Rapport des produits les plus vendus
     */
    public function topProductsReport(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'responsable_commercial', 'magasinier'])) {
            return $this->unauthorizedResponse();
        }

        $dateFrom = $request->get('date_from', now()->subMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        // Produits les plus vendus
        $topProducts = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->join('families', 'products.family_id', '=', 'families.id')
            ->whereBetween('sales.sale_date', [$dateFrom, $dateTo])
            ->where('sales.status', 'finalisee')
            ->select(
                'products.id',
                'products.name',
                'products.code',
                'families.name as family_name',
                DB::raw('SUM(sale_details.quantity) as total_quantity'),
                DB::raw('SUM(sale_details.total_price) as total_revenue'),
                DB::raw('COUNT(DISTINCT sales.id) as number_of_sales'),
                DB::raw('AVG(sale_details.unit_price) as average_price')
            )
            ->groupBy('products.id', 'products.name', 'products.code', 'families.name')
            ->orderBy('total_quantity', 'desc')
            ->limit(50)
            ->get();

        // Familles les plus vendues
        $topFamilies = DB::table('sale_details')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->join('families', 'products.family_id', '=', 'families.id')
            ->whereBetween('sales.sale_date', [$dateFrom, $dateTo])
            ->where('sales.status', 'finalisee')
            ->select(
                'families.id',
                'families.name',
                DB::raw('SUM(sale_details.quantity) as total_quantity'),
                DB::raw('SUM(sale_details.total_price) as total_revenue'),
                DB::raw('COUNT(DISTINCT products.id) as products_count')
            )
            ->groupBy('families.id', 'families.name')
            ->orderBy('total_revenue', 'desc')
            ->get();

        return view('reports.top-products', compact('topProducts', 'topFamilies', 'dateFrom', 'dateTo'));
    }

    /**
     * Rapport financier
     */
    public function financialReport(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $financialData = [
            // Chiffre d'affaires
            'total_revenue' => Sale::whereBetween('sale_date', [$dateFrom, $dateTo])
                                 ->where('status', 'finalisee')
                                 ->sum('total_amount'),
            
            // Coût des achats
            'total_purchases' => PurchaseOrder::whereBetween('order_date', [$dateFrom, $dateTo])
                                             ->whereIn('status', ['recu_complet', 'recu_partiel'])
                                             ->sum('total_amount'),
            
            // Nombre de ventes
            'sales_count' => Sale::whereBetween('sale_date', [$dateFrom, $dateTo])
                               ->where('status', 'finalisee')
                               ->count(),
            
            // Panier moyen
            'average_basket' => Sale::whereBetween('sale_date', [$dateFrom, $dateTo])
                                  ->where('status', 'finalisee')
                                  ->avg('total_amount'),
        ];

        // Évolution mensuelle du CA
        $monthlyRevenue = Sale::select(
                DB::raw('YEAR(sale_date) as year'),
                DB::raw('MONTH(sale_date) as month'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->whereBetween('sale_date', [now()->subYear(), now()])
            ->where('status', 'finalisee')
            ->groupBy(DB::raw('YEAR(sale_date)'), DB::raw('MONTH(sale_date)'))
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        return view('reports.financial', compact('financialData', 'monthlyRevenue', 'dateFrom', 'dateTo'));
    }

    /**
     * Export des données
     */
    public function export(Request $request)
    {
        if (!$this->checkRoles(['administrateur', 'comptable'])) {
            return $this->unauthorizedResponse();
        }

        $type = $request->get('type');
        $format = $request->get('format', 'csv');

        switch ($type) {
            case 'sales':
                return $this->exportSales($request, $format);
            case 'purchases':
                return $this->exportPurchases($request, $format);
            case 'stock':
                return $this->exportStock($request, $format);
            case 'customers':
                return $this->exportCustomers($request, $format);
            default:
                return $this->errorResponse('Type d\'export non supporté');
        }
    }

    /**
     * Méthodes utilitaires privées
     */
    private function getGeneralStats()
    {
        $today = now()->toDateString();
        $thisMonth = now()->format('Y-m');

        return [
            'sales_today' => Sale::whereDate('sale_date', $today)->where('status', 'finalisee')->count(),
            'revenue_today' => Sale::whereDate('sale_date', $today)->where('status', 'finalisee')->sum('total_amount'),
            'sales_this_month' => Sale::whereRaw('DATE_FORMAT(sale_date, "%Y-%m") = ?', [$thisMonth])->where('status', 'finalisee')->count(),
            'revenue_this_month' => Sale::whereRaw('DATE_FORMAT(sale_date, "%Y-%m") = ?', [$thisMonth])->where('status', 'finalisee')->sum('total_amount'),
            'low_stock_products' => WarehouseStock::lowStock()->count(),
            'out_of_stock_products' => WarehouseStock::outOfStock()->count(),
            'pending_orders' => PurchaseOrder::whereIn('status', ['brouillon', 'envoye', 'confirme'])->count(),
        ];
    }

    private function applySalesReportFilters($query, Request $request)
    {
        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('sale_date', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('sale_date', '<=', $dateTo);
        }

        if ($customerId = $request->get('customer_id')) {
            $query->where('customer_id', $customerId);
        }

        if ($warehouseId = $request->get('warehouse_id')) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        } else {
            $query->where('status', 'finalisee');
        }
    }

    private function applyPurchaseReportFilters($query, Request $request)
    {
        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('order_date', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('order_date', '<=', $dateTo);
        }

        if ($supplierId = $request->get('supplier_id')) {
            $query->where('supplier_id', $supplierId);
        }

        if ($warehouseId = $request->get('warehouse_id')) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }
    }

    private function applyStockReportFilters($query, Request $request)
    {
        if ($warehouseId = $request->get('warehouse_id')) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($familyId = $request->get('family_id')) {
            $query->whereHas('product', function($q) use ($familyId) {
                $q->where('family_id', $familyId);
            });
        }

        if ($filter = $request->get('filter')) {
            switch ($filter) {
                case 'low_stock':
                    $query->lowStock();
                    break;
                case 'out_of_stock':
                    $query->outOfStock();
                    break;
            }
        }
    }

    private function applyStockMovementReportFilters($query, Request $request)
    {
        if ($dateFrom = $request->get('date_from')) {
            $query->whereDate('movement_date', '>=', $dateFrom);
        }

        if ($dateTo = $request->get('date_to')) {
            $query->whereDate('movement_date', '<=', $dateTo);
        }

        if ($productId = $request->get('product_id')) {
            $query->where('product_id', $productId);
        }

        if ($warehouseId = $request->get('warehouse_id')) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        if ($reason = $request->get('reason')) {
            $query->where('reason', $reason);
        }
    }

    private function getSalesReportStats($query)
    {
        $clonedQuery = clone $query;
        
        return [
            'total_sales' => $clonedQuery->count(),
            'total_revenue' => $clonedQuery->sum('total_amount'),
            'average_basket' => $clonedQuery->avg('total_amount'),
            'total_items_sold' => $clonedQuery->withSum('saleDetails', 'quantity')->get()->sum('sale_details_sum_quantity'),
        ];
    }

    private function getPurchaseReportStats($query)
    {
        $clonedQuery = clone $query;
        
        return [
            'total_orders' => $clonedQuery->count(),
            'total_amount' => $clonedQuery->sum('total_amount'),
            'average_order' => $clonedQuery->avg('total_amount'),
            'delivered_orders' => $clonedQuery->whereIn('status', ['recu_complet', 'recu_partiel'])->count(),
        ];
    }

    private function getStockReportStats()
    {
        return [
            'total_products' => WarehouseStock::count(),
            'total_quantity' => WarehouseStock::sum('quantity'),
            'low_stock_count' => WarehouseStock::lowStock()->count(),
            'out_of_stock_count' => WarehouseStock::outOfStock()->count(),
            'total_value' => WarehouseStock::with('product')->get()->sum(function($stock) {
                return $stock->quantity * $stock->product->purchase_price;
            }),
        ];
    }

    private function getStockMovementReportStats($query)
    {
        $clonedQuery = clone $query;
        
        return [
            'total_movements' => $clonedQuery->count(),
            'total_entries' => $clonedQuery->where('type', 'entree')->sum('quantity'),
            'total_exits' => $clonedQuery->where('type', 'sortie')->sum('quantity'),
            'total_value' => $clonedQuery->sum('total_cost'),
        ];
    }

    private function getCustomerReportStats()
    {
        return [
            'total_customers' => Customer::count(),
            'active_customers' => Customer::whereHas('sales')->count(),
            'new_customers_this_month' => Customer::whereMonth('created_at', now()->month)->count(),
            'average_customer_value' => Customer::withSum('sales', 'total_amount')->avg('sales_sum_total_amount'),
        ];
    }

    private function getSalesChartData(Request $request)
    {
        // Implémentation des données pour les graphiques
        return [];
    }

    private function getPurchaseChartData(Request $request)
    {
        // Implémentation des données pour les graphiques
        return [];
    }

    private function getStockMovementChartData(Request $request)
    {
        // Implémentation des données pour les graphiques
        return [];
    }

    private function exportSales(Request $request, string $format)
    {
        // Implémentation de l'export des ventes
        return response()->json(['message' => 'Export en cours de développement']);
    }

    private function exportPurchases(Request $request, string $format)
    {
        // Implémentation de l'export des achats
        return response()->json(['message' => 'Export en cours de développement']);
    }

    private function exportStock(Request $request, string $format)
    {
        // Implémentation de l'export du stock
        return response()->json(['message' => 'Export en cours de développement']);
    }

    private function exportCustomers(Request $request, string $format)
    {
        // Implémentation de l'export des clients
        return response()->json(['message' => 'Export en cours de développement']);
    }
}