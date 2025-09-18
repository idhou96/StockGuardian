<?php
//test 1
//test 22
<<<<<<< HEAD
// test breaking change
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    BaseController,
=======

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
>>>>>>> 2022c95 (essaie commit)
    DashboardController,
    ProductController,
    ProductFamilyController,
    ActivePrincipleController,
    CustomerController,
    SupplierController,
    SaleController,
    PurchaseOrderController,
    StockMovementController,
    InventoryController,
    StockRegularizationController,
    WarehouseController,
    UserController,
    RoleController,
    PermissionController,
    ReportController,
    SettingController,
<<<<<<< HEAD
    SystemSettingController,
    LogController,
    ActivityController,
    MaintenanceController,
    ImportExportController,
=======
>>>>>>> 2022c95 (essaie commit)
    NotificationController,
    ProfileController,
    HelpController,
    ReturnNoteController,
    DeliveryNoteController,
<<<<<<< HEAD
    AlertController,
    BackupController,
    PerformanceController
=======
    AlertController
>>>>>>> 2022c95 (essaie commit)
};

/*
|--------------------------------------------------------------------------
| Routes d'Authentification
|--------------------------------------------------------------------------
*/
<<<<<<< HEAD

=======
>>>>>>> 2022c95 (essaie commit)
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Routes Publiques
|--------------------------------------------------------------------------
*/
<<<<<<< HEAD

// Route principale avec redirection automatique par middleware
=======
>>>>>>> 2022c95 (essaie commit)
Route::get('/', function () {
    if (!auth()->check()) {
        return redirect('/login');
    }
<<<<<<< HEAD
    
    // Le middleware 'role.redirect' s'occupera de la redirection automatique
    return redirect('/dashboard');
})->name('home');

Route::get('/help/public', [HelpController::class, 'public'])->name('help.public');

/*
|--------------------------------------------------------------------------
| Routes Protégées - Middleware Auth Extended avec Redirection Automatique
|--------------------------------------------------------------------------
*/

Route::middleware(['auth.extended'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD - Redirection Automatique par Rôle via Middleware
    |--------------------------------------------------------------------------
    */
    
    // Route dashboard principale - Le middleware 'role.redirect' gère la redirection automatique
    Route::get('/dashboard', function () {
        // Cette route ne devrait jamais être atteinte directement
        // Le middleware RedirectBasedOnRole redirige automatiquement vers le bon dashboard
        return redirect()->route('dashboard.general');
    })->name('dashboard');
    
    // API pour actualisation temps réel des KPIs
    Route::get('/api/dashboard/kpis', [DashboardController::class, 'kpis'])
        ->name('dashboard.kpis');
    Route::get('/api/dashboard/charts', [DashboardController::class, 'charts'])
        ->name('dashboard.charts');
    
    // Dashboards spécialisés par rôle avec middleware appropriés
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
        ->middleware('role:administrateur')
        ->name('dashboard.admin');
    
    Route::get('/dashboard/manager', [DashboardController::class, 'manager'])
        ->middleware('role:responsable_commercial')
        ->name('dashboard.manager');
    
    Route::get('/dashboard/sales', [DashboardController::class, 'sales'])
        ->middleware('role:vendeur|caissiere|responsable_commercial')
        ->name('dashboard.sales');
    
    Route::get('/dashboard/stock', [DashboardController::class, 'stock'])
        ->middleware('role:magasinier|administrateur')
        ->name('dashboard.stock');
        
    Route::get('/dashboard/purchases', [DashboardController::class, 'purchases'])
        ->middleware('role:responsable_achats|administrateur')
        ->name('dashboard.purchases');
    
    Route::get('/dashboard/accounting', [DashboardController::class, 'accounting'])
        ->middleware('role:comptable|administrateur')
        ->name('dashboard.accounting');
        
    Route::get('/dashboard/general', [DashboardController::class, 'general'])
        ->middleware('role:invite|stagiaire')
        ->name('dashboard.general');

    /*
    |--------------------------------------------------------------------------
    | GESTION DES PRODUITS & FAMILLES
    |--------------------------------------------------------------------------
    */
    
    Route::resource('products', ProductController::class)
        ->middleware('permission:products.view|products.create|products.edit|products.delete');
    
    Route::prefix('products')->name('products.')->group(function () {
        // Recherche et filtres
        Route::get('/search', [ProductController::class, 'search'])->name('search');
        Route::get('/search-ajax', [ProductController::class, 'searchAjax'])->name('search-ajax');
        Route::get('/barcode/{barcode}', [ProductController::class, 'findByBarcode'])->name('barcode');
        
        // Import/Export
        Route::get('/export', [ProductController::class, 'export'])
            ->middleware('permission:reports.export')
            ->name('export');
        Route::post('/import', [ProductController::class, 'import'])
            ->middleware('permission:products.create')
            ->name('import');
        
        // Actions spécifiques
        Route::post('/{product}/duplicate', [ProductController::class, 'duplicate'])
            ->middleware('permission:products.create')
            ->name('duplicate');
        Route::get('/{product}/print-label', [ProductController::class, 'printLabel'])
            ->middleware('permission:reports.view')
            ->name('print-label');
        Route::post('/{product}/toggle-favorite', [ProductController::class, 'toggleFavorite'])
            ->name('toggle-favorite');
        Route::get('/{product}/stock-history', [ProductController::class, 'stockHistory'])
            ->middleware('permission:inventory.view')
            ->name('stock-history');
    });

    // Familles de produits
    Route::resource('product-families', ProductFamilyController::class)
        ->middleware('permission:products.view|products.create|products.edit|products.delete');

    // Principes actifs
    Route::resource('active-principles', ActivePrincipleController::class)
        ->middleware('permission:products.view|products.create|products.edit|products.delete');

    /*
    |--------------------------------------------------------------------------
    | GESTION DES VENTES & POS - Groupe Sales
    |--------------------------------------------------------------------------
    */
    
    Route::middleware(['sales'])->group(function () {
        Route::resource('sales', SaleController::class);
        
        Route::prefix('sales')->name('sales.')->group(function () {
            // Impression et actions
            Route::get('/{sale}/print', [SaleController::class, 'print'])->name('print');
            Route::get('/{sale}/pdf', [SaleController::class, 'pdf'])->name('pdf');
            Route::post('/{sale}/cancel', [SaleController::class, 'cancel'])->name('cancel');
            Route::post('/{sale}/refund', [SaleController::class, 'refund'])->name('refund');
            
            // Rapports ventes
            Route::get('/report/daily', [SaleController::class, 'dailyReport'])->name('report.daily');
            Route::get('/report/monthly', [SaleController::class, 'monthlyReport'])->name('report.monthly');
            Route::get('/export/excel', [SaleController::class, 'exportExcel'])->name('export.excel');
        });
=======
    return redirect('/dashboard');
})->name('home');

/*
|--------------------------------------------------------------------------
| Routes Protégées - Middleware Auth Standard
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD - Redirection par rôle
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Dashboards spécialisés par rôle
    Route::get('/dashboard/admin', [DashboardController::class, 'adminDashboard'])->name('dashboard.admin');
    Route::get('/dashboard/commercial', [DashboardController::class, 'commercialDashboard'])->name('dashboard.commercial');
    Route::get('/dashboard/vendeur', [DashboardController::class, 'vendeurDashboard'])->name('dashboard.vendeur');
    Route::get('/dashboard/magasinier', [DashboardController::class, 'magasinierDashboard'])->name('dashboard.magasinier');
    Route::get('/dashboard/achats', [DashboardController::class, 'achatsDashboard'])->name('dashboard.achats');
    Route::get('/dashboard/comptable', [DashboardController::class, 'comptableDashboard'])->name('dashboard.comptable');
    Route::get('/dashboard/invite', [DashboardController::class, 'inviteDashboard'])->name('dashboard.invite');

    /*
    |--------------------------------------------------------------------------
    | GESTION DES PRODUITS
    |--------------------------------------------------------------------------
    */
    Route::resource('products', ProductController::class);
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/search', [ProductController::class, 'search'])->name('search');
        Route::get('/search-ajax', [ProductController::class, 'searchAjax'])->name('search-ajax');
        Route::get('/barcode/{barcode}', [ProductController::class, 'findByBarcode'])->name('barcode');
        Route::get('/export', [ProductController::class, 'export'])->name('export');
        Route::post('/import', [ProductController::class, 'import'])->name('import');
        Route::post('/{product}/duplicate', [ProductController::class, 'duplicate'])->name('duplicate');
        Route::get('/{product}/print-label', [ProductController::class, 'printLabel'])->name('print-label');
        Route::post('/{product}/toggle-favorite', [ProductController::class, 'toggleFavorite'])->name('toggle-favorite');
        Route::get('/{product}/stock-history', [ProductController::class, 'stockHistory'])->name('stock-history');
    });

    // Familles et principes actifs
    Route::resource('product-families', ProductFamilyController::class);
    Route::resource('active-principles', ActivePrincipleController::class);

    /*
    |--------------------------------------------------------------------------
    | GESTION DES VENTES (accessible selon les rôles)
    |--------------------------------------------------------------------------
    */
    Route::resource('sales', SaleController::class);
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/{sale}/print', [SaleController::class, 'print'])->name('print');
        Route::get('/{sale}/pdf', [SaleController::class, 'pdf'])->name('pdf');
        Route::post('/{sale}/cancel', [SaleController::class, 'cancel'])->name('cancel');
        Route::post('/{sale}/refund', [SaleController::class, 'refund'])->name('refund');
        Route::get('/report/daily', [SaleController::class, 'dailyReport'])->name('report.daily');
        Route::get('/report/monthly', [SaleController::class, 'monthlyReport'])->name('report.monthly');
        Route::get('/export/excel', [SaleController::class, 'exportExcel'])->name('export.excel');
>>>>>>> 2022c95 (essaie commit)
    });

    /*
    |--------------------------------------------------------------------------
<<<<<<< HEAD
    | GESTION DU STOCK & INVENTAIRES - Groupe Stock
    |--------------------------------------------------------------------------
    */
    
    Route::middleware(['stock'])->group(function () {
        // Mouvements de stock
        Route::prefix('stock-movements')->name('stock-movements.')->group(function () {
            Route::get('/', [StockMovementController::class, 'index'])->name('index');
            
            // Entrées de stock
            Route::get('/create-entry', [StockMovementController::class, 'createEntry'])->name('create-entry');
            Route::post('/store-entry', [StockMovementController::class, 'storeEntry'])->name('store-entry');
            
            // Sorties de stock
            Route::get('/create-exit', [StockMovementController::class, 'createExit'])->name('create-exit');
            Route::post('/store-exit', [StockMovementController::class, 'storeExit'])->name('store-exit');
            
            // Affichage et actions
            Route::get('/{movement}', [StockMovementController::class, 'show'])->name('show');
            Route::post('/{movement}/cancel', [StockMovementController::class, 'cancel'])->name('cancel');
            
            // Rapports
            Route::get('/export/report', [StockMovementController::class, 'exportReport'])->name('export.report');
        });

        // Inventaires
        Route::resource('inventories', InventoryController::class);
        
        Route::prefix('inventories')->name('inventories.')->group(function () {
            Route::post('/{inventory}/start', [InventoryController::class, 'start'])->name('start');
            Route::post('/{inventory}/validate', [InventoryController::class, 'validate'])->name('validate');
            Route::post('/{inventory}/apply', [InventoryController::class, 'apply'])->name('apply');
            Route::get('/{inventory}/print', [InventoryController::class, 'print'])->name('print');
            Route::get('/{inventory}/export', [InventoryController::class, 'export'])->name('export');
        });

        // Régularisations de stock
        Route::resource('stock-regularizations', StockRegularizationController::class);
            
        Route::prefix('stock-regularizations')->name('stock-regularizations.')->group(function () {
            Route::post('/{regularization}/validate', [StockRegularizationController::class, 'validate'])->name('validate');
            Route::post('/{regularization}/cancel', [StockRegularizationController::class, 'cancel'])->name('cancel');
        });

        // Entrepôts/Dépôts
        Route::resource('warehouses', WarehouseController::class);
        
        Route::prefix('warehouses')->name('warehouses.')->group(function () {
            Route::get('/{warehouse}/stock', [WarehouseController::class, 'stock'])->name('stock');
            Route::get('/{warehouse}/movements', [WarehouseController::class, 'movements'])->name('movements');
            Route::post('/{warehouse}/transfer', [WarehouseController::class, 'transfer'])->name('transfer');
        });
=======
    | POINT DE VENTE (POS)
    |--------------------------------------------------------------------------
    */
    Route::get('/pos', function () {
        return view('pos.index');
    })->name('pos.index');

    /*
    |--------------------------------------------------------------------------
    | GESTION DU STOCK
    |--------------------------------------------------------------------------
    */
    Route::prefix('stock-movements')->name('stock-movements.')->group(function () {
        Route::get('/', [StockMovementController::class, 'index'])->name('index');
        Route::get('/create-entry', [StockMovementController::class, 'createEntry'])->name('create-entry');
        Route::post('/store-entry', [StockMovementController::class, 'storeEntry'])->name('store-entry');
        Route::get('/create-exit', [StockMovementController::class, 'createExit'])->name('create-exit');
        Route::post('/store-exit', [StockMovementController::class, 'storeExit'])->name('store-exit');
        Route::get('/{movement}', [StockMovementController::class, 'show'])->name('show');
        Route::post('/{movement}/cancel', [StockMovementController::class, 'cancel'])->name('cancel');
        Route::get('/export/report', [StockMovementController::class, 'exportReport'])->name('export.report');
    });

    // Inventaires
    Route::resource('inventories', InventoryController::class);
    Route::prefix('inventories')->name('inventories.')->group(function () {
        Route::post('/{inventory}/start', [InventoryController::class, 'start'])->name('start');
        Route::post('/{inventory}/validate', [InventoryController::class, 'validate'])->name('validate');
        Route::post('/{inventory}/apply', [InventoryController::class, 'apply'])->name('apply');
        Route::get('/{inventory}/print', [InventoryController::class, 'print'])->name('print');
        Route::get('/{inventory}/export', [InventoryController::class, 'export'])->name('export');
    });

    // Régularisations et entrepôts
    Route::resource('stock-regularizations', StockRegularizationController::class);
    Route::resource('warehouses', WarehouseController::class);
    Route::prefix('warehouses')->name('warehouses.')->group(function () {
        Route::get('/{warehouse}/stock', [WarehouseController::class, 'stock'])->name('stock');
        Route::get('/{warehouse}/movements', [WarehouseController::class, 'movements'])->name('movements');
        Route::post('/{warehouse}/transfer', [WarehouseController::class, 'transfer'])->name('transfer');
>>>>>>> 2022c95 (essaie commit)
    });

    /*
    |--------------------------------------------------------------------------
<<<<<<< HEAD
    | GESTION DES ACHATS & FOURNISSEURS - Groupe Purchases
    |--------------------------------------------------------------------------
    */
    
    Route::middleware(['purchases'])->group(function () {
        Route::resource('purchase-orders', PurchaseOrderController::class);
        
        Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
            Route::post('/{order}/send', [PurchaseOrderController::class, 'send'])->name('send');
            Route::get('/{order}/receive', [PurchaseOrderController::class, 'receiveForm'])->name('receive-form');
            Route::post('/{order}/receive', [PurchaseOrderController::class, 'receive'])->name('receive');
            Route::get('/{order}/print', [PurchaseOrderController::class, 'print'])->name('print');
            Route::post('/{order}/cancel', [PurchaseOrderController::class, 'cancel'])->name('cancel');
        });

        // Bons de livraison
        Route::resource('delivery-notes', DeliveryNoteController::class);

        // Bons de retour
        Route::resource('return-notes', ReturnNoteController::class);

        // Fournisseurs
        Route::resource('suppliers', SupplierController::class);
        
        Route::prefix('suppliers')->name('suppliers.')->group(function () {
            Route::get('/export', [SupplierController::class, 'export'])->name('export');
            Route::post('/import', [SupplierController::class, 'import'])->name('import');
            Route::get('/{supplier}/purchase-history', [SupplierController::class, 'purchaseHistory'])->name('purchase-history');
            Route::get('/{supplier}/account-statement', [SupplierController::class, 'accountStatement'])->name('account-statement');
        });
=======
    | GESTION DES ACHATS
    |--------------------------------------------------------------------------
    */
    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
        Route::post('/{order}/send', [PurchaseOrderController::class, 'send'])->name('send');
        Route::get('/{order}/receive', [PurchaseOrderController::class, 'receiveForm'])->name('receive-form');
        Route::post('/{order}/receive', [PurchaseOrderController::class, 'receive'])->name('receive');
        Route::get('/{order}/print', [PurchaseOrderController::class, 'print'])->name('print');
        Route::post('/{order}/cancel', [PurchaseOrderController::class, 'cancel'])->name('cancel');
    });

    // Bons de livraison et retour
    Route::resource('delivery-notes', DeliveryNoteController::class);
    Route::resource('return-notes', ReturnNoteController::class);

    // Fournisseurs
    Route::resource('suppliers', SupplierController::class);
    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/export', [SupplierController::class, 'export'])->name('export');
        Route::post('/import', [SupplierController::class, 'import'])->name('import');
        Route::get('/{supplier}/purchase-history', [SupplierController::class, 'purchaseHistory'])->name('purchase-history');
        Route::get('/{supplier}/account-statement', [SupplierController::class, 'accountStatement'])->name('account-statement');
>>>>>>> 2022c95 (essaie commit)
    });

    /*
    |--------------------------------------------------------------------------
    | GESTION DES CLIENTS
    |--------------------------------------------------------------------------
    */
<<<<<<< HEAD
    
    Route::resource('customers', CustomerController::class)
        ->middleware('permission:customers.view|customers.create|customers.edit|customers.delete');
    
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/export', [CustomerController::class, 'export'])
            ->middleware('permission:reports.export')
            ->name('export');
        Route::post('/import', [CustomerController::class, 'import'])
            ->middleware('permission:customers.create')
            ->name('import');
        Route::get('/{customer}/sales-history', [CustomerController::class, 'salesHistory'])
            ->middleware('permission:sales.view')
            ->name('sales-history');
        Route::get('/{customer}/account-statement', [CustomerController::class, 'accountStatement'])
            ->middleware('permission:sales.view')
            ->name('account-statement');
=======
    Route::resource('customers', CustomerController::class);
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/export', [CustomerController::class, 'export'])->name('export');
        Route::post('/import', [CustomerController::class, 'import'])->name('import');
        Route::get('/{customer}/sales-history', [CustomerController::class, 'salesHistory'])->name('sales-history');
        Route::get('/{customer}/account-statement', [CustomerController::class, 'accountStatement'])->name('account-statement');
>>>>>>> 2022c95 (essaie commit)
    });

    /*
    |--------------------------------------------------------------------------
    | RAPPORTS ET ANALYSES
    |--------------------------------------------------------------------------
    */
<<<<<<< HEAD
    
    Route::prefix('reports')->name('reports.')->middleware('permission:reports.view')->group(function () {
=======
    Route::prefix('reports')->name('reports.')->group(function () {
>>>>>>> 2022c95 (essaie commit)
        Route::get('/', [ReportController::class, 'index'])->name('index');
        
        // Rapports de ventes
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/sales/detailed', [ReportController::class, 'salesDetailed'])->name('sales.detailed');
        Route::get('/sales/by-customer', [ReportController::class, 'salesByCustomer'])->name('sales.by-customer');
        Route::get('/sales/by-product', [ReportController::class, 'salesByProduct'])->name('sales.by-product');
        Route::post('/sales/export', [ReportController::class, 'salesExport'])->name('sales.export');
        
        // Rapports de stock
        Route::get('/stock', [ReportController::class, 'stock'])->name('stock');
        Route::get('/stock/valuation', [ReportController::class, 'stockValuation'])->name('stock.valuation');
        Route::get('/stock/alerts', [ReportController::class, 'stockAlerts'])->name('stock.alerts');
        Route::get('/stock/expiry', [ReportController::class, 'stockExpiry'])->name('stock.expiry');
        Route::post('/stock/export', [ReportController::class, 'stockExport'])->name('stock.export');
        
        // Rapports d'achats
        Route::get('/purchases', [ReportController::class, 'purchases'])->name('purchases');
        Route::get('/purchases/by-supplier', [ReportController::class, 'purchasesBySupplier'])->name('purchases.by-supplier');
        Route::post('/purchases/export', [ReportController::class, 'purchasesExport'])->name('purchases.export');
        
        // Rapports financiers
        Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
        Route::get('/profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('/cash-flow', [ReportController::class, 'cashFlow'])->name('cash-flow');
        
        // Analyses ABC/XYZ
        Route::get('/abc-analysis', [ReportController::class, 'abcAnalysis'])->name('abc-analysis');
        Route::post('/abc-analysis/refresh', [ReportController::class, 'refreshAbcAnalysis'])->name('abc-analysis.refresh');
<<<<<<< HEAD
        
        // Configuration rapports
        Route::get('/settings', [ReportController::class, 'settings'])
            ->middleware('permission:settings.edit')
            ->name('settings');
=======
>>>>>>> 2022c95 (essaie commit)
    });

    /*
    |--------------------------------------------------------------------------
    | ALERTES SYSTÈME
    |--------------------------------------------------------------------------
    */
<<<<<<< HEAD
    
    Route::prefix('alerts')->name('alerts.')->group(function () {
        Route::get('/stock', [AlertController::class, 'stock'])
            ->middleware('permission:inventory.view')
            ->name('stock');
        Route::get('/expiry', [AlertController::class, 'expiry'])
            ->middleware('permission:products.view')
            ->name('expiry');
        Route::get('/low-stock', [AlertController::class, 'lowStock'])
            ->middleware('permission:inventory.view')
            ->name('low-stock');
        Route::post('/mark-resolved/{alert}', [AlertController::class, 'markResolved'])
            ->name('mark-resolved');
        Route::post('/mark-all-resolved', [AlertController::class, 'markAllResolved'])
            ->name('mark-all-resolved');
=======
    Route::prefix('alerts')->name('alerts.')->group(function () {
        Route::get('/stock', [AlertController::class, 'stock'])->name('stock');
        Route::get('/expiry', [AlertController::class, 'expiry'])->name('expiry');
        Route::get('/low-stock', [AlertController::class, 'lowStock'])->name('low-stock');
        Route::post('/mark-resolved/{alert}', [AlertController::class, 'markResolved'])->name('mark-resolved');
        Route::post('/mark-all-resolved', [AlertController::class, 'markAllResolved'])->name('mark-all-resolved');
>>>>>>> 2022c95 (essaie commit)
    });

    /*
    |--------------------------------------------------------------------------
<<<<<<< HEAD
    | ADMINISTRATION UTILISATEURS - Groupe Admin
    |--------------------------------------------------------------------------
    */
    
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', UserController::class);
        
        Route::prefix('users')->name('users.')->group(function () {
            Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        });

        Route::resource('roles', RoleController::class);

        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/', [PermissionController::class, 'index'])->name('index');
            Route::post('/assign', [PermissionController::class, 'assign'])->name('assign');
            Route::post('/revoke', [PermissionController::class, 'revoke'])->name('revoke');
            Route::post('/toggle', [PermissionController::class, 'toggle'])->name('toggle');
            Route::post('/toggle-module', [PermissionController::class, 'toggleModule'])->name('toggle-module');
        });
=======
    | ADMINISTRATION UTILISATEURS
    |--------------------------------------------------------------------------
    */
    Route::resource('users', UserController::class);
    Route::prefix('users')->name('users.')->group(function () {
        Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
    });

    Route::resource('roles', RoleController::class);

    Route::prefix('permissions')->name('permissions.')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::post('/assign', [PermissionController::class, 'assign'])->name('assign');
        Route::post('/revoke', [PermissionController::class, 'revoke'])->name('revoke');
        Route::post('/toggle', [PermissionController::class, 'toggle'])->name('toggle');
        Route::post('/toggle-module', [PermissionController::class, 'toggleModule'])->name('toggle-module');
>>>>>>> 2022c95 (essaie commit)
    });

    /*
    |--------------------------------------------------------------------------
<<<<<<< HEAD
    | PARAMÈTRES & CONFIGURATION SYSTÈME - Groupe Admin
    |--------------------------------------------------------------------------
    */
    
    Route::middleware(['admin'])->group(function () {
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingController::class, 'index'])->name('index');
            Route::post('/', [SettingController::class, 'store'])->name('store');
            Route::get('/export', [SettingController::class, 'export'])->name('export');
            Route::post('/import', [SettingController::class, 'import'])->name('import');
            Route::post('/reset', [SettingController::class, 'reset'])->name('reset');
        });

        // Paramètres système avancés
        Route::prefix('system-settings')->name('system-settings.')->group(function () {
            Route::get('/', [SystemSettingController::class, 'index'])->name('index');
            Route::post('/', [SystemSettingController::class, 'store'])->name('store');
            Route::post('/optimize-system', [SystemSettingController::class, 'optimizeSystem'])->name('optimize-system');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | ACTIVITÉ & LOGS - Groupe Admin
    |--------------------------------------------------------------------------
    */
    
    Route::middleware(['admin'])->group(function () {
        Route::prefix('activity')->name('activity.')->group(function () {
            Route::get('/', [ActivityController::class, 'index'])->name('index');
            Route::get('/logs', [ActivityController::class, 'logs'])->name('logs');
            Route::get('/user/{user}', [ActivityController::class, 'userActivity'])->name('user');
            Route::delete('/clear/{days?}', [ActivityController::class, 'clear'])->name('clear');
        });
        
        Route::prefix('logs')->name('logs.')->group(function () {
            Route::get('/', [LogController::class, 'index'])->name('index');
            Route::get('/export', [LogController::class, 'export'])->name('export');
            Route::delete('/clear-old', [LogController::class, 'clearOld'])->name('clear-old');
            Route::get('/download/{file}', [LogController::class, 'download'])->name('download');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | MAINTENANCE & SYSTÈME - Groupe Admin
    |--------------------------------------------------------------------------
    */
    
    Route::middleware(['admin'])->group(function () {
        Route::prefix('maintenance')->name('maintenance.')->group(function () {
            Route::get('/', [MaintenanceController::class, 'index'])->name('index');
            
            // Optimisation système
            Route::post('/optimize-database', [MaintenanceController::class, 'optimizeDatabase'])->name('optimize-database');
            Route::post('/clear-cache', [MaintenanceController::class, 'clearCache'])->name('clear-cache');
            Route::post('/clear-logs', [MaintenanceController::class, 'clearLogs'])->name('clear-logs');
            Route::post('/clear-sessions', [MaintenanceController::class, 'clearSessions'])->name('clear-sessions');
            
            // Mode maintenance
            Route::post('/enable-maintenance', [MaintenanceController::class, 'enableMaintenance'])->name('enable-maintenance');
            Route::post('/disable-maintenance', [MaintenanceController::class, 'disableMaintenance'])->name('disable-maintenance');
        });

        Route::prefix('backups')->name('backups.')->group(function () {
            Route::get('/', [BackupController::class, 'index'])->name('index');
            Route::post('/create', [BackupController::class, 'create'])->name('create');
            Route::post('/create-scheduled', [BackupController::class, 'createScheduled'])->name('create-scheduled');
            Route::get('/{backup}/download', [BackupController::class, 'download'])->name('download');
            Route::post('/{backup}/restore', [BackupController::class, 'restore'])->name('restore');
            Route::delete('/{backup}', [BackupController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('performance')->name('performance.')->group(function () {
            Route::get('/monitor', [PerformanceController::class, 'monitor'])->name('monitor');
            Route::get('/system-info', [PerformanceController::class, 'systemInfo'])->name('system-info');
            Route::get('/database-stats', [PerformanceController::class, 'databaseStats'])->name('database-stats');
            Route::post('/optimize', [PerformanceController::class, 'optimize'])->name('optimize');
        });
=======
    | PARAMÈTRES & CONFIGURATION
    |--------------------------------------------------------------------------
    */
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/', [SettingController::class, 'store'])->name('store');
        Route::get('/export', [SettingController::class, 'export'])->name('export');
        Route::post('/import', [SettingController::class, 'import'])->name('import');
        Route::post('/reset', [SettingController::class, 'reset'])->name('reset');
>>>>>>> 2022c95 (essaie commit)
    });

    /*
    |--------------------------------------------------------------------------
    | NOTIFICATIONS
    |--------------------------------------------------------------------------
    */
<<<<<<< HEAD
    
=======
>>>>>>> 2022c95 (essaie commit)
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread', [NotificationController::class, 'unread'])->name('unread');
        Route::post('/mark-as-read/{notification}', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
        Route::delete('/{notification}', [NotificationController::class, 'destroy'])->name('destroy');
        Route::delete('/clear-all', [NotificationController::class, 'clearAll'])->name('clear-all');
    });

    /*
    |--------------------------------------------------------------------------
    | PROFIL UTILISATEUR
    |--------------------------------------------------------------------------
    */
<<<<<<< HEAD
    
=======
>>>>>>> 2022c95 (essaie commit)
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::post('/preferences', [ProfileController::class, 'updatePreferences'])->name('preferences');
        Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('avatar');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
        Route::delete('/avatar', [ProfileController::class, 'deleteAvatar'])->name('avatar.delete');
    });

    /*
    |--------------------------------------------------------------------------
    | AIDE ET DOCUMENTATION
    |--------------------------------------------------------------------------
    */
<<<<<<< HEAD
    
=======
>>>>>>> 2022c95 (essaie commit)
    Route::prefix('help')->name('help.')->group(function () {
        Route::get('/', [HelpController::class, 'index'])->name('index');
        Route::get('/search', [HelpController::class, 'search'])->name('search');
        Route::get('/pdf', [HelpController::class, 'downloadPdf'])->name('pdf');
        Route::get('/section/{section}', [HelpController::class, 'section'])->name('section');
        Route::get('/faq', [HelpController::class, 'faq'])->name('faq');
        Route::get('/contact', [HelpController::class, 'contact'])->name('contact');
        Route::post('/contact', [HelpController::class, 'sendContact'])->name('contact.send');
    });

    /*
    |--------------------------------------------------------------------------
<<<<<<< HEAD
    | IMPORT/EXPORT DE DONNÉES - Groupe Admin
    |--------------------------------------------------------------------------
    */
    
    Route::middleware(['admin'])->group(function () {
        Route::prefix('import-export')->name('import-export.')->group(function () {
            Route::get('/', [ImportExportController::class, 'index'])->name('index');
            Route::get('/templates', [ImportExportController::class, 'templates'])->name('templates');
            Route::get('/template/{type}', [ImportExportController::class, 'downloadTemplate'])->name('template');
            Route::post('/import/{type}', [ImportExportController::class, 'import'])->name('import');
            Route::get('/export/{type}', [ImportExportController::class, 'export'])->name('export');
            Route::get('/export-all', [ImportExportController::class, 'exportAll'])->name('export-all');
            Route::post('/import-all', [ImportExportController::class, 'importAll'])->name('import-all');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | UTILITAIRES
    |--------------------------------------------------------------------------
    */
    
    // Génération de codes-barres et QR codes
    Route::get('/barcode/{code}', function ($code) {
        return app(\App\Services\BarcodeService::class)->generate($code);
    })->name('barcode.generate');
    
    Route::get('/qr/{type}/{id}', function ($type, $id) {
        return app(\App\Services\QRCodeService::class)->generate($type, $id);
    })->name('qr.generate');
    
    // Téléchargement de fichiers temporaires
    Route::get('/temp/{file}', function ($file) {
        $path = storage_path('app/temp/' . $file);
        if (file_exists($path)) {
            return response()->download($path)->deleteFileAfterSend();
        }
        abort(404);
    })->middleware('signed')->name('temp.download');
=======
    | UTILITAIRES
    |--------------------------------------------------------------------------
    */
    Route::get('/barcode/{code}', function ($code) {
        // Génération de codes-barres si service disponible
        return response()->json(['barcode' => $code]);
    })->name('barcode.generate');
    
    Route::get('/qr/{type}/{id}', function ($type, $id) {
        // Génération de QR codes si service disponible
        return response()->json(['qr' => $type . '/' . $id]);
    })->name('qr.generate');

    
/*
|--------------------------------------------------------------------------
| MAINTENANCE & SYSTÈME
|--------------------------------------------------------------------------
*/
Route::prefix('maintenance')->name('maintenance.')->group(function () {
    Route::get('/', function () {
        return view('maintenance.index');
    })->name('index');
    
    // Optimisation système  
    Route::post('/optimize-database', function () {
        Artisan::call('optimize:clear');
        Artisan::call('config:cache');
        return redirect()->back()->with('success', 'Base de données optimisée');
    })->name('optimize-database');
    
    Route::post('/clear-cache', function () {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        return redirect()->back()->with('success', 'Cache nettoyé');
    })->name('clear-cache');
    
    Route::post('/clear-logs', function () {
        $files = glob(storage_path('logs/*.log'));
        foreach($files as $file) {
            if(is_file($file)) unlink($file);
        }
        return redirect()->back()->with('success', 'Logs nettoyés');
    })->name('clear-logs');
    
    Route::post('/clear-sessions', function () {
        Artisan::call('session:clear');
        return redirect()->back()->with('success', 'Sessions nettoyées');
    })->name('clear-sessions');
});

/*
|--------------------------------------------------------------------------
| SAUVEGARDES
|--------------------------------------------------------------------------
*/
Route::prefix('backups')->name('backups.')->group(function () {
    Route::get('/', function () {
        return view('backups.index');
    })->name('index');
    
    Route::post('/create', function () {
        // Logique de création de sauvegarde
        return redirect()->back()->with('success', 'Sauvegarde créée');
    })->name('create');
    
    Route::post('/create-scheduled', function () {
        return redirect()->back()->with('success', 'Sauvegarde programmée');
    })->name('create-scheduled');
});

/*
|--------------------------------------------------------------------------
| LOGS & ACTIVITÉ
|--------------------------------------------------------------------------
*/
Route::prefix('activity')->name('activity.')->group(function () {
    Route::get('/', function () {
        return view('activity.index');
    })->name('index');
    
    Route::get('/logs', function () {
        return view('activity.logs');
    })->name('logs');
    
    Route::get('/user/{user}', function ($user) {
        return view('activity.user', compact('user'));
    })->name('user');
    
    Route::delete('/clear/{days?}', function ($days = 30) {
        // Logique de nettoyage des logs
        return redirect()->back()->with('success', 'Activités nettoyées');
    })->name('clear');
});

Route::prefix('logs')->name('logs.')->group(function () {
    Route::get('/', function () {
        $logFiles = glob(storage_path('logs/*.log'));
        return view('logs.index', compact('logFiles'));
    })->name('index');
    
    Route::get('/export', function () {
        // Logique d'export des logs
        return redirect()->back()->with('success', 'Logs exportés');
    })->name('export');
    
    Route::delete('/clear-old', function () {
        $files = glob(storage_path('logs/*.log'));
        $cleared = 0;
        foreach($files as $file) {
            if(is_file($file) && filemtime($file) < strtotime('-30 days')) {
                unlink($file);
                $cleared++;
            }
        }
        return redirect()->back()->with('success', "$cleared ancien(s) fichier(s) log supprimé(s)");
    })->name('clear-old');
    
    Route::get('/download/{file}', function ($file) {
        $path = storage_path('logs/' . $file);
        if (file_exists($path)) {
            return response()->download($path);
        }
        abort(404);
    })->name('download');
});

/*
|--------------------------------------------------------------------------
| PERFORMANCE & MONITORING
|--------------------------------------------------------------------------
*/
Route::prefix('performance')->name('performance.')->group(function () {
    Route::get('/monitor', function () {
        return view('performance.monitor');
    })->name('monitor');
    
    Route::get('/system-info', function () {
        return response()->json([
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
        ]);
    })->name('system-info');
    
    Route::get('/database-stats', function () {
        return response()->json([
            'connection' => 'OK',
            'tables_count' => 'N/A'
        ]);
    })->name('database-stats');
    
    Route::post('/optimize', function () {
        Artisan::call('optimize');
        return redirect()->back()->with('success', 'Système optimisé');
    })->name('optimize');
});

/*
|--------------------------------------------------------------------------
| IMPORT/EXPORT DE DONNÉES
|--------------------------------------------------------------------------
*/
Route::prefix('import-export')->name('import-export.')->group(function () {
    Route::get('/', function () {
        return view('import-export.index');
    })->name('index');
    
    Route::get('/templates', function () {
        return view('import-export.templates');
    })->name('templates');
    
    Route::get('/template/{type}', function ($type) {
        // Génération de template selon le type
        return response()->json(['template' => $type]);
    })->name('template');
    
    Route::post('/import/{type}', function ($type) {
        return redirect()->back()->with('success', 'Import ' . $type . ' terminé');
    })->name('import');
    
    Route::get('/export/{type}', function ($type) {
        return redirect()->back()->with('success', 'Export ' . $type . ' terminé');
    })->name('export');
    
    Route::get('/export-all', function () {
        return redirect()->back()->with('success', 'Export complet terminé');
    })->name('export-all');
    
    Route::post('/import-all', function () {
        return redirect()->back()->with('success', 'Import complet terminé');
    })->name('import-all');
});

/*
|--------------------------------------------------------------------------
| ROUTES UTILITAIRES SUPPLÉMENTAIRES
|--------------------------------------------------------------------------
*/

// Route pour les factures (si référencées quelque part)
Route::prefix('invoices')->name('invoices.')->group(function () {
    Route::get('/', function () {
        return view('invoices.index');
    })->name('index');
});

// Route pour les paiements (si référencées)
Route::prefix('payments')->name('payments.')->group(function () {
    Route::get('/', function () {
        return view('payments.index');
    })->name('index');
});
>>>>>>> 2022c95 (essaie commit)

});

/*
|--------------------------------------------------------------------------
<<<<<<< HEAD
| ROUTES API (v1) - Middleware Auth Sanctum avec Groupes
|--------------------------------------------------------------------------
*/

=======
| ROUTES API SIMPLES
|--------------------------------------------------------------------------
*/
>>>>>>> 2022c95 (essaie commit)
Route::prefix('api/v1')->middleware(['auth:sanctum'])->name('api.')->group(function () {
    
    // Dashboard API
    Route::get('dashboard/stats', [DashboardController::class, 'apiStats'])->name('dashboard.stats');
<<<<<<< HEAD
    Route::get('dashboard/recent-activities', [DashboardController::class, 'apiRecentActivities'])->name('dashboard.activities');
=======
>>>>>>> 2022c95 (essaie commit)
    
    // Products API
    Route::apiResource('products', ProductController::class);
    Route::get('products/search/{term}', [ProductController::class, 'apiSearch'])->name('products.search');
<<<<<<< HEAD
    Route::get('products/barcode/{barcode}', [ProductController::class, 'apiFindByBarcode'])->name('products.barcode');
    
    // Sales API
    Route::middleware(['sales'])->group(function () {
        Route::apiResource('sales', SaleController::class);
        Route::get('sales/today/summary', [SaleController::class, 'apiTodaySummary'])->name('sales.today-summary');
    });
    
    // Stock API
    Route::middleware(['stock'])->group(function () {
        Route::get('stock/alerts', [StockMovementController::class, 'apiAlerts'])->name('stock.alerts');
        Route::get('stock/movements/recent', [StockMovementController::class, 'apiRecentMovements'])->name('stock.recent-movements');
    });
=======
    
    // Sales API
    Route::apiResource('sales', SaleController::class);
>>>>>>> 2022c95 (essaie commit)
    
    // Customers API
    Route::apiResource('customers', CustomerController::class);
    Route::get('customers/search/{term}', [CustomerController::class, 'apiSearch'])->name('customers.search');
<<<<<<< HEAD
    
    // Suppliers API
    Route::middleware(['purchases'])->group(function () {
        Route::apiResource('suppliers', SupplierController::class);
    });
    
    // Reports API
    Route::get('reports/dashboard-data', [ReportController::class, 'apiDashboardData'])
        ->middleware('permission:reports.view')
        ->name('reports.dashboard-data');
    
=======
>>>>>>> 2022c95 (essaie commit)
});

/*
|--------------------------------------------------------------------------
<<<<<<< HEAD
| ROUTES WEBHOOKS (Optionnel pour intégrations externes)
|--------------------------------------------------------------------------
*/

Route::prefix('webhooks')->group(function () {
    Route::post('/sms-status', function () {
        // Traitement des retours de statut SMS
        logger('SMS Status Webhook received');
        return response()->json(['status' => 'success']);
    })->name('webhooks.sms-status');
    
    Route::post('/mobile-payment', function () {
        // Traitement des notifications de paiement mobile
        logger('Mobile Payment Webhook received');
        return response()->json(['status' => 'success']);
    })->name('webhooks.mobile-payment');
});

/*
|--------------------------------------------------------------------------
| ROUTES DE DÉVELOPPEMENT (à supprimer en production)
|--------------------------------------------------------------------------
*/

if (app()->environment(['local', 'testing'])) {
    Route::prefix('dev')->name('dev.')->group(function () {
        
=======
| ROUTES DE DÉVELOPPEMENT (local uniquement)
|--------------------------------------------------------------------------
*/
if (app()->environment('local')) {
    Route::prefix('dev')->name('dev.')->group(function () {
>>>>>>> 2022c95 (essaie commit)
        Route::get('/clear-all', function () {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('route:clear');
            \Artisan::call('view:clear');
<<<<<<< HEAD
            \Artisan::call('optimize:clear');
            return response()->json(['message' => 'All caches cleared!']);
        })->name('clear-all');
        
        Route::get('/seed-test-data', function () {
            \Artisan::call('db:seed', ['--class' => 'TestDataSeeder']);
            return response()->json(['message' => 'Test data seeded!']);
        })->name('seed-test-data');
        
        Route::get('/generate-fake-sales', function () {
            // Génération de fausses ventes pour les tests
            return response()->json(['message' => 'Fake sales generated!']);
        })->name('generate-fake-sales');
        
=======
            return response()->json(['message' => 'All caches cleared!']);
        })->name('clear-all');
        
>>>>>>> 2022c95 (essaie commit)
        Route::get('/phpinfo', function () {
            return phpinfo();
        })->name('phpinfo');
        
<<<<<<< HEAD
        // Route de test pour les redirections par rôle
        Route::get('/test-roles', function () {
            $user = auth()->user();
            return response()->json([
                'user' => $user->only(['id', 'name', 'email', 'role']),
                'redirect_url' => \App\Providers\RouteServiceProvider::redirectTo(),
                'dashboard_route' => \App\Providers\RouteServiceProvider::getDashboardRouteName(),
                'roles_check' => [
                    'administrateur' => $user->hasRole('administrateur'),
                    'vendeur' => $user->hasRole('vendeur'),
                    'magasinier' => $user->hasRole('magasinier'),
                ]
            ]);
        })->name('test-roles');
        
=======
        Route::get('/test-auth', function () {
            $user = auth()->user();
            if ($user) {
                return response()->json([
                    'authenticated' => true,
                    'user' => $user->only(['id', 'name', 'email']),
                    'redirect_url' => '/dashboard',
                ]);
            }
            return response()->json(['authenticated' => false]);
        })->middleware('auth')->name('test-auth');
>>>>>>> 2022c95 (essaie commit)
    });
}