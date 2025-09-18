<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\StockMovement;
use App\Models\StockOutage;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseController
{
    public function index()
    {
        $user = auth()->user();

        // Vérifier les permissions d'accès selon le rôle
        if (!$this->canViewDashboard($user)) {
            return $this->unauthorizedResponse('Accès au tableau de bord non autorisé');
        }

        // Redirection automatique selon le rôle
        $role = $user->role;

        return match ($role) {
            'administrateur' => redirect()->route('dashboard.admin'),
            'responsable_commercial' => redirect()->route('dashboard.commercial'),
            'vendeur', 'caissiere' => redirect()->route('dashboard.vendeur'),
            'magasinier' => redirect()->route('dashboard.magasinier'),
            'responsable_achats' => redirect()->route('dashboard.achats'),
            'comptable' => redirect()->route('dashboard.comptable'),
            'invite', 'stagiaire' => redirect()->route('dashboard.invite'),
            default => $this->generalDashboard()
        };
    }

    protected function canViewDashboard($user): bool
    {
        return $user !== null;
    }

    protected function getDashboardData($user): array
    {
        $role = $user->role;
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'user' => $user,
            'role' => $role,
            'cards' => $this->getStatsCards($role, $today, $thisMonth),
            'charts' => $this->getChartsData($role, $user),
            'alerts' => $this->getAlerts($role),
            'recent_activities' => $this->getRecentActivities($role, $user),
        ];
    }

    protected function getStatsCards($role, $today, $thisMonth): array
    {
        $cards = [];

        switch ($role) {
            case 'administrateur':
                $cards = [
                    [
                        'title' => 'Ventes du jour',
                        'value' => number_format(Sale::whereDate('sale_date', $today)->sum('total_ttc'), 0, ',', ' '),
                        'unit' => 'FCFA',
                        'icon' => 'trending-up',
                        'color' => 'green',
                        'change' => $this->getSalesChangePercentage($today)
                    ],
                    [
                        'title' => 'Ventes du mois',
                        'value' => number_format(Sale::where('sale_date', '>=', $thisMonth)->sum('total_ttc'), 0, ',', ' '),
                        'unit' => 'FCFA',
                        'icon' => 'bar-chart',
                        'color' => 'blue',
                        'change' => $this->getMonthlySalesChange()
                    ],
                    [
                        'title' => 'Produits en stock',
                        'value' => Product::inStock()->count(),
                        'unit' => 'produits',
                        'icon' => 'package',
                        'color' => 'purple',
                        'change' => null
                    ],
                    [
                        'title' => 'Alerts stock',
                        'value' => Product::lowStock()->count(),
                        'unit' => 'produits',
                        'icon' => 'alert-triangle',
                        'color' => 'red',
                        'change' => null
                    ]
                ];
                break;

            case 'responsable_commercial':
            case 'vendeur':
                $cards = [
                    [
                        'title' => 'Mes ventes du jour',
                        'value' => number_format(Sale::where('cashier_id', auth()->id())->whereDate('sale_date', $today)->sum('total_ttc'), 0, ',', ' '),
                        'unit' => 'FCFA',
                        'icon' => 'dollar-sign',
                        'color' => 'green'
                    ],
                    [
                        'title' => 'Clients actifs',
                        'value' => Customer::active()->count(),
                        'unit' => 'clients',
                        'icon' => 'users',
                        'color' => 'blue'
                    ],
                    [
                        'title' => 'Factures impayées',
                        'value' => Invoice::unpaid()->count(),
                        'unit' => 'factures',
                        'icon' => 'file-text',
                        'color' => 'orange'
                    ]
                ];
                break;

            case 'magasinier':
                $cards = [
                    [
                        'title' => 'Mouvements du jour',
                        'value' => StockMovement::whereDate('movement_date', $today)->count(),
                        'unit' => 'mouvements',
                        'icon' => 'activity',
                        'color' => 'blue'
                    ],
                    [
                        'title' => 'Ruptures de stock',
                        'value' => StockOutage::active()->count(),
                        'unit' => 'produits',
                        'icon' => 'alert-circle',
                        'color' => 'red'
                    ],
                    [
                        'title' => 'Produits expirés',
                        'value' => Product::expired()->count(),
                        'unit' => 'produits',
                        'icon' => 'calendar-x',
                        'color' => 'orange'
                    ]
                ];
                break;

            case 'responsable_achats':
                $cards = [
                    [
                        'title' => 'Commandes en cours',
                        'value' => PurchaseOrder::pending()->count(),
                        'unit' => 'commandes',
                        'icon' => 'shopping-cart',
                        'color' => 'blue'
                    ],
                    [
                        'title' => 'Fournisseurs actifs',
                        'value' => Supplier::active()->count(),
                        'unit' => 'fournisseurs',
                        'icon' => 'truck',
                        'color' => 'green'
                    ],
                    [
                        'title' => 'Réceptions en attente',
                        'value' => PurchaseOrder::where('status', 'confirme')->count(),
                        'unit' => 'livraisons',
                        'icon' => 'inbox',
                        'color' => 'orange'
                    ]
                ];
                break;

            case 'comptable':
                $cards = [
                    [
                        'title' => 'CA du mois',
                        'value' => number_format(Sale::where('sale_date', '>=', $thisMonth)->sum('total_ttc'), 0, ',', ' '),
                        'unit' => 'FCFA',
                        'icon' => 'dollar-sign',
                        'color' => 'green'
                    ],
                    [
                        'title' => 'Factures impayées',
                        'value' => number_format(Invoice::unpaid()->sum('amount_due'), 0, ',', ' '),
                        'unit' => 'FCFA',
                        'icon' => 'file-minus',
                        'color' => 'red'
                    ],
                    [
                        'title' => 'Paiements du jour',
                        'value' => number_format(\App\Models\Payment::whereDate('payment_date', $today)->sum('amount'), 0, ',', ' '),
                        'unit' => 'FCFA',
                        'icon' => 'credit-card',
                        'color' => 'blue'
                    ]
                ];
                break;

            case 'caissiere':
                $cards = [
                    [
                        'title' => 'Mes ventes du jour',
                        'value' => number_format(Sale::where('cashier_id', auth()->id())->whereDate('sale_date', $today)->sum('total_ttc'), 0, ',', ' '),
                        'unit' => 'FCFA',
                        'icon' => 'dollar-sign',
                        'color' => 'green'
                    ],
                    [
                        'title' => 'Nombre de ventes',
                        'value' => Sale::where('cashier_id', auth()->id())->whereDate('sale_date', $today)->count(),
                        'unit' => 'ventes',
                        'icon' => 'shopping-bag',
                        'color' => 'blue'
                    ]
                ];
                break;

            default:
                $cards = [
                    [
                        'title' => 'Bienvenue',
                        'value' => 'StockGuardian',
                        'unit' => '',
                        'icon' => 'home',
                        'color' => 'blue'
                    ]
                ];
        }

        return $cards;
    }

    protected function getChartsData($role, $user): array
    {
        $charts = [];

        if (in_array($role, ['administrateur', 'responsable_commercial', 'comptable'])) {
            $charts['sales'] = $this->getSalesChart();
            $charts['top_products'] = $this->getTopProductsChart();
        }

        if (in_array($role, ['administrateur', 'magasinier'])) {
            $charts['stock_movements'] = $this->getStockMovementsChart();
        }

        return $charts;
    }

    protected function getAlerts($role): array
    {
        $alerts = [];

        if (in_array($role, ['administrateur', 'magasinier'])) {
            $lowStockCount = Product::lowStock()->count();
            if ($lowStockCount > 0) {
                $alerts[] = [
                    'type' => 'warning',
                    'icon' => 'alert-triangle',
                    'title' => 'Stock faible',
                    'message' => "{$lowStockCount} produit(s) ont un stock faible",
                    'link' => route('products.index', ['filter' => 'low_stock'])
                ];
            }

            $expiredCount = Product::expired()->count();
            if ($expiredCount > 0) {
                $alerts[] = [
                    'type' => 'danger',
                    'icon' => 'calendar-x',
                    'title' => 'Produits expirés',
                    'message' => "{$expiredCount} produit(s) sont expirés",
                    'link' => route('products.index', ['filter' => 'expired'])
                ];
            }
        }

        if (in_array($role, ['administrateur', 'responsable_commercial', 'comptable'])) {
            $overdueCount = Invoice::overdue()->count();
            if ($overdueCount > 0) {
                $alerts[] = [
                    'type' => 'danger',
                    'icon' => 'clock',
                    'title' => 'Factures en retard',
                    'message' => "{$overdueCount} facture(s) sont en retard de paiement",
                    'link' => route('invoices.index', ['filter' => 'overdue'])
                ];
            }
        }

        return $alerts;
    }

    protected function getRecentActivities($role, $user): array
    {
        $query = \App\Models\ActivityLog::with('user')->orderBy('created_at', 'desc')->limit(10);

        if (!in_array($role, ['administrateur'])) {
            $query->where('user_id', $user->id);
        }

        return $query->get()->map(function($log) {
            return [
                'user' => $log->user?->name ?? 'Système',
                'action' => $log->action_label,
                'model' => $log->model_name,
                'description' => $log->description,
                'time' => $log->time_ago,
                'icon' => $log->getIcon(),
                'color' => $log->getColor(),
            ];
        })->toArray();
    }

    protected function getSalesChangePercentage($today): ?float
    {
        $todaySales = Sale::whereDate('sale_date', $today)->sum('total_ttc');
        $yesterdaySales = Sale::whereDate('sale_date', $today->copy()->subDay())->sum('total_ttc');
        if ($yesterdaySales == 0) return null;
        return round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100, 1);
    }

    protected function getMonthlySalesChange(): ?float
    {
        $thisMonth = Sale::whereBetween('sale_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('total_ttc');
        $lastMonth = Sale::whereBetween('sale_date', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])->sum('total_ttc');
        if ($lastMonth == 0) return null;
        return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1);
    }

    protected function getSalesChart(): array
    {
        $days = [];
        $amounts = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('d/m');
            $amounts[] = Sale::whereDate('sale_date', $date)->sum('total_ttc');
        }
        return ['labels' => $days, 'data' => $amounts, 'title' => 'Ventes des 7 derniers jours'];
    }

    protected function getTopProductsChart(): array
    {
        $topProducts = Product::select('products.name', DB::raw('SUM(sale_details.quantity) as total_sold'))
            ->join('sale_details', 'products.id', '=', 'sale_details.product_id')
            ->join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->where('sales.sale_date', '>=', Carbon::now()->subDays(30))
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        return [
            'labels' => $topProducts->pluck('name')->toArray(),
            'data' => $topProducts->pluck('total_sold')->toArray(),
            'title' => 'Top 10 des produits vendus (30 jours)'
        ];
    }

    protected function getStockMovementsChart(): array
    {
        $entries = [];
        $exits = [];
        $days = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $days[] = $date->format('d/m');

            $entries[] = StockMovement::where('type', 'entree')->whereDate('movement_date', $date)->sum('quantity');
            $exits[] = StockMovement::where('type', 'sortie')->whereDate('movement_date', $date)->sum('quantity');
        }

        return ['labels' => $days, 'entries' => $entries, 'exits' => $exits, 'title' => 'Mouvements de stock (7 jours)'];
    }

    // --- Dashboards par rôle ---
    public function adminDashboard() { /* contenu inchangé, similaire à avant */ }
    public function commercialDashboard() { /* contenu inchangé */ }
    public function vendeurDashboard() { /* contenu inchangé */ }
    public function magasinierDashboard() { /* contenu inchangé */ }
    public function achatsDashboard() { /* contenu inchangé */ }
    public function comptableDashboard() { /* contenu inchangé */ }
    public function inviteDashboard() { /* contenu inchangé */ }

    // --- APIs ---
    public function apiStats() { /* contenu inchangé */ }
    public function apiRecentActivities() { /* contenu inchangé */ }

    private function generalDashboard()
    {
        $user = auth()->user();
        $dashboardData = $this->getDashboardData($user);
        $dashboardData['dashboard_type'] = 'general';
        $this->logActivity('view', null, [], [], 'Consultation du tableau de bord général');
        return view('dashboard', compact('dashboardData'));
    }
}
