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

        // Redirection automatique selon le rôle - CORRIGÉ pour correspondre aux routes
        $role = $user->role ?? 'invite';

        return match ($role) {
            'administrateur' => redirect()->route('dashboard.admin'),
            'responsable_commercial' => redirect()->route('dashboard.manager'), // CORRIGÉ
            'vendeur', 'caissiere' => redirect()->route('dashboard.sales'), // CORRIGÉ
            'magasinier' => redirect()->route('dashboard.stock'), // CORRIGÉ
            'responsable_achats' => redirect()->route('dashboard.purchases'), // CORRIGÉ
            'comptable' => redirect()->route('dashboard.accounting'), // CORRIGÉ
            'invite', 'stagiaire' => redirect()->route('dashboard.general'), // CORRIGÉ
            default => $this->generalDashboard()
        };
    }

    // =============================================================================
    // MÉTHODES POUR CORRESPONDRE AUX ROUTES (admin, manager, sales, etc.)
    // =============================================================================

    public function admin()
    {
        $user = auth()->user();
        $dashboardData = $this->getDashboardData($user);
        $dashboardData['dashboard_type'] = 'admin';
        
        $this->logActivity('view', null, [], [], 'Consultation du tableau de bord administrateur');
        return view('dashboard.admin', compact('dashboardData'));
    }

    public function manager()
    {
        $user = auth()->user();
        $dashboardData = $this->getDashboardData($user);
        $dashboardData['dashboard_type'] = 'manager';
        
        $this->logActivity('view', null, [], [], 'Consultation du tableau de bord commercial');
        return view('dashboard.manager', compact('dashboardData'));
    }

    public function sales()
    {
        $user = auth()->user();
        $dashboardData = $this->getDashboardData($user);
        $dashboardData['dashboard_type'] = 'sales';
        
        $this->logActivity('view', null, [], [], 'Consultation du tableau de bord ventes');
        return view('dashboard.sales', compact('dashboardData'));
    }

    public function stock()
    {
        $user = auth()->user();
        $dashboardData = $this->getDashboardData($user);
        $dashboardData['dashboard_type'] = 'stock';
        
        $this->logActivity('view', null, [], [], 'Consultation du tableau de bord stock');
        return view('dashboard.stock', compact('dashboardData'));
    }

    public function purchases()
    {
        $user = auth()->user();
        $dashboardData = $this->getDashboardData($user);
        $dashboardData['dashboard_type'] = 'purchases';
        
        $this->logActivity('view', null, [], [], 'Consultation du tableau de bord achats');
        return view('dashboard.purchases', compact('dashboardData'));
    }

    public function accounting()
    {
        $user = auth()->user();
        $dashboardData = $this->getDashboardData($user);
        $dashboardData['dashboard_type'] = 'accounting';
        
        $this->logActivity('view', null, [], [], 'Consultation du tableau de bord comptabilité');
        return view('dashboard.accounting', compact('dashboardData'));
    }

    public function general()
    {
        $user = auth()->user();
        $dashboardData = $this->getDashboardData($user);
        $dashboardData['dashboard_type'] = 'general';
        
        $this->logActivity('view', null, [], [], 'Consultation du tableau de bord général');
        return view('dashboard.general', compact('dashboardData'));
    }

    // =============================================================================
    // APIs POUR DONNÉES DYNAMIQUES
    // =============================================================================

    public function kpis()
    {
        $user = auth()->user();
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        
        try {
            return response()->json([
                'daily_sales' => $this->safeModelCall(fn() => Sale::whereDate('sale_date', $today)->sum('total_ttc'), 0),
                'monthly_sales' => $this->safeModelCall(fn() => Sale::where('sale_date', '>=', $thisMonth)->sum('total_ttc'), 0),
                'products_in_stock' => $this->safeModelCall(fn() => Product::where('current_stock', '>', 0)->count(), 0),
                'low_stock_alerts' => $this->safeModelCall(fn() => Product::whereColumn('current_stock', '<=', 'minimum_stock')->count(), 0),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'daily_sales' => 0,
                'monthly_sales' => 0,
                'products_in_stock' => 0,
                'low_stock_alerts' => 0,
            ]);
        }
    }

    public function charts()
    {
        $user = auth()->user();
        $role = $user->role ?? 'invite';
        
        $charts = $this->getChartsData($role, $user);
        
        return response()->json($charts);
    }

    // =============================================================================
    // VOTRE LOGIQUE MÉTIER ORIGINALE (avec protection contre les modèles manquants)
    // =============================================================================

    protected function canViewDashboard($user): bool
    {
        return $user !== null;
    }

    protected function getDashboardData($user): array
    {
        $role = $user->role ?? 'invite';
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
                        'value' => $this->safeFormat($this->safeModelCall(fn() => Sale::whereDate('sale_date', $today)->sum('total_ttc'), 0)),
                        'unit' => 'FCFA',
                        'icon' => 'trending-up',
                        'color' => 'green',
                        'change' => $this->getSalesChangePercentage($today)
                    ],
                    [
                        'title' => 'Ventes du mois',
                        'value' => $this->safeFormat($this->safeModelCall(fn() => Sale::where('sale_date', '>=', $thisMonth)->sum('total_ttc'), 0)),
                        'unit' => 'FCFA',
                        'icon' => 'bar-chart',
                        'color' => 'blue',
                        'change' => $this->getMonthlySalesChange()
                    ],
                    [
                        'title' => 'Produits en stock',
                        'value' => $this->safeModelCall(fn() => Product::where('current_stock', '>', 0)->count(), 0),
                        'unit' => 'produits',
                        'icon' => 'package',
                        'color' => 'purple',
                        'change' => null
                    ],
                    [
                        'title' => 'Alertes stock',
                        'value' => $this->safeModelCall(fn() => Product::whereColumn('current_stock', '<=', 'minimum_stock')->count(), 0),
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
                        'value' => $this->safeFormat($this->safeModelCall(fn() => Sale::where('cashier_id', auth()->id())->whereDate('sale_date', $today)->sum('total_ttc'), 0)),
                        'unit' => 'FCFA',
                        'icon' => 'dollar-sign',
                        'color' => 'green'
                    ],
                    [
                        'title' => 'Clients actifs',
                        'value' => $this->safeModelCall(fn() => Customer::where('is_active', true)->count(), 0),
                        'unit' => 'clients',
                        'icon' => 'users',
                        'color' => 'blue'
                    ],
                    [
                        'title' => 'Factures impayées',
                        'value' => $this->safeModelCall(fn() => Invoice::where('status', 'unpaid')->count(), 0),
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
                        'value' => $this->safeModelCall(fn() => StockMovement::whereDate('movement_date', $today)->count(), 0),
                        'unit' => 'mouvements',
                        'icon' => 'activity',
                        'color' => 'blue'
                    ],
                    [
                        'title' => 'Ruptures de stock',
                        'value' => $this->safeModelCall(fn() => StockOutage::where('status', 'active')->count(), 0),
                        'unit' => 'produits',
                        'icon' => 'alert-circle',
                        'color' => 'red'
                    ],
                    [
                        'title' => 'Produits expirés',
                        'value' => $this->safeModelCall(fn() => Product::where('expiry_date', '<', Carbon::today())->count(), 0),
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
                        'value' => $this->safeModelCall(fn() => PurchaseOrder::where('status', 'pending')->count(), 0),
                        'unit' => 'commandes',
                        'icon' => 'shopping-cart',
                        'color' => 'blue'
                    ],
                    [
                        'title' => 'Fournisseurs actifs',
                        'value' => $this->safeModelCall(fn() => Supplier::where('is_active', true)->count(), 0),
                        'unit' => 'fournisseurs',
                        'icon' => 'truck',
                        'color' => 'green'
                    ],
                    [
                        'title' => 'Réceptions en attente',
                        'value' => $this->safeModelCall(fn() => PurchaseOrder::where('status', 'confirme')->count(), 0),
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
                        'value' => $this->safeFormat($this->safeModelCall(fn() => Sale::where('sale_date', '>=', $thisMonth)->sum('total_ttc'), 0)),
                        'unit' => 'FCFA',
                        'icon' => 'dollar-sign',
                        'color' => 'green'
                    ],
                    [
                        'title' => 'Factures impayées',
                        'value' => $this->safeFormat($this->safeModelCall(fn() => Invoice::where('status', 'unpaid')->sum('amount_due'), 0)),
                        'unit' => 'FCFA',
                        'icon' => 'file-minus',
                        'color' => 'red'
                    ],
                    [
                        'title' => 'Paiements du jour',
                        'value' => $this->safeFormat($this->safeModelCall(fn() => \App\Models\Payment::whereDate('payment_date', $today)->sum('amount'), 0)),
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
                        'value' => $this->safeFormat($this->safeModelCall(fn() => Sale::where('cashier_id', auth()->id())->whereDate('sale_date', $today)->sum('total_ttc'), 0)),
                        'unit' => 'FCFA',
                        'icon' => 'dollar-sign',
                        'color' => 'green'
                    ],
                    [
                        'title' => 'Nombre de ventes',
                        'value' => $this->safeModelCall(fn() => Sale::where('cashier_id', auth()->id())->whereDate('sale_date', $today)->count(), 0),
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

        try {
            if (in_array($role, ['administrateur', 'magasinier'])) {
                $lowStockCount = $this->safeModelCall(fn() => Product::whereColumn('current_stock', '<=', 'minimum_stock')->count(), 0);
                if ($lowStockCount > 0) {
                    $alerts[] = [
                        'type' => 'warning',
                        'icon' => 'alert-triangle',
                        'title' => 'Stock faible',
                        'message' => "{$lowStockCount} produit(s) ont un stock faible",
                        'link' => route('products.index', ['filter' => 'low_stock'])
                    ];
                }

                $expiredCount = $this->safeModelCall(fn() => Product::where('expiry_date', '<', Carbon::today())->count(), 0);
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
                $overdueCount = $this->safeModelCall(fn() => Invoice::where('status', 'overdue')->count(), 0);
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
        } catch (\Exception $e) {
            // Ignorer les erreurs si les modèles n'existent pas
        }

        return $alerts;
    }

    protected function getRecentActivities($role, $user): array
    {
        try {
            if (!class_exists('\App\Models\ActivityLog')) {
                return [];
            }

            $query = \App\Models\ActivityLog::with('user')->orderBy('created_at', 'desc')->limit(10);

            if (!in_array($role, ['administrateur'])) {
                $query->where('user_id', $user->id);
            }

            return $query->get()->map(function($log) {
                return [
                    'user' => $log->user?->name ?? 'Système',
                    'action' => $log->action_label ?? $log->action ?? 'Action',
                    'model' => $log->model_name ?? $log->subject_type ?? 'N/A',
                    'description' => $log->description ?? 'Aucune description',
                    'time' => $log->time_ago ?? $log->created_at?->diffForHumans() ?? 'N/A',
                    'icon' => method_exists($log, 'getIcon') ? $log->getIcon() : 'activity',
                    'color' => method_exists($log, 'getColor') ? $log->getColor() : 'blue',
                ];
            })->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    protected function getSalesChangePercentage($today): ?float
    {
        try {
            $todaySales = $this->safeModelCall(fn() => Sale::whereDate('sale_date', $today)->sum('total_ttc'), 0);
            $yesterdaySales = $this->safeModelCall(fn() => Sale::whereDate('sale_date', $today->copy()->subDay())->sum('total_ttc'), 0);
            if ($yesterdaySales == 0) return null;
            return round((($todaySales - $yesterdaySales) / $yesterdaySales) * 100, 1);
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function getMonthlySalesChange(): ?float
    {
        try {
            $thisMonth = $this->safeModelCall(fn() => Sale::whereBetween('sale_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('total_ttc'), 0);
            $lastMonth = $this->safeModelCall(fn() => Sale::whereBetween('sale_date', [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()])->sum('total_ttc'), 0);
            if ($lastMonth == 0) return null;
            return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1);
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function getSalesChart(): array
    {
        try {
            $days = [];
            $amounts = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $days[] = $date->format('d/m');
                $amounts[] = $this->safeModelCall(fn() => Sale::whereDate('sale_date', $date)->sum('total_ttc'), 0);
            }
            return ['labels' => $days, 'data' => $amounts, 'title' => 'Ventes des 7 derniers jours'];
        } catch (\Exception $e) {
            return [
                'labels' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                'data' => [0, 0, 0, 0, 0, 0, 0],
                'title' => 'Ventes des 7 derniers jours'
            ];
        }
    }

    protected function getTopProductsChart(): array
    {
        try {
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
        } catch (\Exception $e) {
            return [
                'labels' => [],
                'data' => [],
                'title' => 'Top 10 des produits vendus (30 jours)'
            ];
        }
    }

    protected function getStockMovementsChart(): array
    {
        try {
            $entries = [];
            $exits = [];
            $days = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $days[] = $date->format('d/m');

                $entries[] = $this->safeModelCall(fn() => StockMovement::where('type', 'entree')->whereDate('movement_date', $date)->sum('quantity'), 0);
                $exits[] = $this->safeModelCall(fn() => StockMovement::where('type', 'sortie')->whereDate('movement_date', $date)->sum('quantity'), 0);
            }

            return ['labels' => $days, 'entries' => $entries, 'exits' => $exits, 'title' => 'Mouvements de stock (7 jours)'];
        } catch (\Exception $e) {
            return [
                'labels' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                'entries' => [0, 0, 0, 0, 0, 0, 0],
                'exits' => [0, 0, 0, 0, 0, 0, 0],
                'title' => 'Mouvements de stock (7 jours)'
            ];
        }
    }

    // =============================================================================
    // MÉTHODES UTILITAIRES
    // =============================================================================

    /**
     * Exécute une fonction de modèle de manière sécurisée
     */
    private function safeModelCall(callable $callback, $defaultValue = 0)
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            return $defaultValue;
        }
    }

    /**
     * Formate un nombre pour l'affichage
     */
    private function safeFormat($value): string
    {
        return number_format($value, 0, ',', ' ');
    }

    /**
     * Dashboard général (fallback)
     */
    private function generalDashboard()
    {
        return $this->general();
    }

    // =============================================================================
    // MÉTHODES LEGACY (pour compatibilité avec votre code existant)
    // =============================================================================

    public function adminDashboard() { return $this->admin(); }
    public function commercialDashboard() { return $this->manager(); }
    public function vendeurDashboard() { return $this->sales(); }
    public function magasinierDashboard() { return $this->stock(); }
    public function achatsDashboard() { return $this->purchases(); }
    public function comptableDashboard() { return $this->accounting(); }
    public function inviteDashboard() { return $this->general(); }

    public function apiStats() { return $this->kpis(); }
    public function apiRecentActivities() { 
        $user = auth()->user();
        return response()->json($this->getRecentActivities($user->role ?? 'invite', $user));
    }
}