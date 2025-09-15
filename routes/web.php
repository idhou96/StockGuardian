<?php
//test
//test 2
// test 3
// test 4
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    BaseController,
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
    SystemSettingController,
    LogController,
    ActivityController,
    MaintenanceController,
    ImportExportController,
    NotificationController,
    ProfileController,
    HelpController,
    ReturnNoteController,
    DeliveryNoteController,
    AlertController,
    BackupController,
    PerformanceController
};

/*
|--------------------------------------------------------------------------
| Routes d'Authentification
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Routes Publiques
|--------------------------------------------------------------------------
*/

// Route principale avec redirection intelligente par rôle
Route::get('/', function () {
    if (!auth()->check()) {
        return redirect('/login');
    }
    
    $user = auth()->user();
    
    // Redirection selon le rôle de l'utilisateur
    if ($user->hasRole('administrateur')) {
        return redirect('/dashboard/admin');
    } elseif ($user->hasRole('responsable_commercial')) {
        return redirect('/dashboard/manager');
    } elseif ($user->hasRole(['vendeur', 'caissiere'])) {
        return redirect('/dashboard/sales');
    } elseif ($user->hasRole('magasinier')) {
        return redirect('/dashboard/stock');
    } elseif ($user->hasRole('responsable_achats')) {
        return redirect('/dashboard/purchases');
    } elseif ($user->hasRole('comptable')) {
        return redirect('/dashboard/accounting');
    } else {
        // Pour les rôles invité/stagiaire ou autres
        return redirect('/dashboard/general');
    }
})->name('home');

Route::get('/help/public', [HelpController::class, 'public'])->name('help.public');

/*
|--------------------------------------------------------------------------
| Routes Protégées - Middleware Auth & Verified
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD - Tableaux de Bord par Rôle avec Redirection Automatique
    |--------------------------------------------------------------------------
    */
    
    // Route dashboard principale avec redirection automatique par rôle
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        // Redirection selon le rôle principal de l'utilisateur
        if ($user->hasRole('administrateur')) {
            return redirect()->route('dashboard.admin');
        } elseif ($user->hasRole('responsable_commercial')) {
            return redirect()->route('dashboard.manager');
        } elseif ($user->hasRole(['vendeur', 'caissiere'])) {
            return redirect()->route('dashboard.sales');
        } elseif ($user->hasRole('magasinier')) {
            return redirect()->route('dashboard.stock');
        } elseif ($user->hasRole('responsable_achats')) {
            return redirect()->route('dashboard.purchases');
        } elseif ($user->hasRole('comptable')) {
            return redirect()->route('dashboard.accounting');
        } else {
            // Pour les rôles invité/stagiaire
            return redirect()->route('dashboard.general');
        }
    })->name('dashboard');
    
    // API pour actualisation temps réel des KPIs
    Route::get('/api/dashboard/kpis', [DashboardController::class, 'kpis'])
        ->name('dashboard.kpis');
    Route::get('/api/dashboard/charts', [DashboardController::class, 'charts'])
        ->name('dashboard.charts');
    
    // Dashboards spécialisés par rôle
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
        ->middleware('role:magasinier')
        ->name('dashboard.stock');
        
    Route::get('/dashboard/purchases', [DashboardController::class, 'purchases'])
        ->middleware('role:responsable_achats')
        ->name('dashboard.purchases');
    
    Route::get('/dashboard/accounting', [DashboardController::class, 'accounting'])
        ->middleware('role:comptable')
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
    | GESTION DES VENTES & POS
    |--------------------------------------------------------------------------
    */
    
    Route::resource('sales', SaleController::class)
        ->middleware('permission:sales.view|sales.create|sales.edit|sales.delete');
    
    Route::prefix('sales')->name('sales.')->group(function () {
        // Impression et actions
        Route::get('/{sale}/print', [SaleController::class, 'print'])
            ->middleware('permission:sales.view')
            ->name('print');
        Route::get('/{sale}/pdf', [SaleController::class, 'pdf'])
            ->middleware('permission:sales.view')
            ->name('pdf');
        Route::post('/{sale}/cancel', [SaleController::class, 'cancel'])
            ->middleware('permission:sales.delete')
            ->name('cancel');
        Route::post('/{sale}/refund', [SaleController::class, 'refund'])
            ->middleware('permission:sales.edit')
            ->name('refund');
        
        // Rapports ventes
        Route::get('/report/daily', [SaleController::class, 'dailyReport'])
            ->middleware('permission:reports.view')
            ->name('report.daily');
        Route::get('/report/monthly', [SaleController::class, 'monthlyReport'])
            ->middleware('permission:reports.view')
            ->name('report.monthly');
        Route::get('/export/excel', [SaleController::class, 'exportExcel'])
            ->middleware('permission:reports.export')
            ->name('export.excel');
    });

    /*
    |--------------------------------------------------------------------------
    | GESTION DU STOCK & INVENTAIRES
    |--------------------------------------------------------------------------
    */
    
    // Mouvements de stock
    Route::prefix('stock-movements')->name('stock-movements.')->group(function () {
        Route::get('/', [StockMovementController::class, 'index'])
            ->middleware('permission:inventory.view')
            ->name('index');
        
        // Entrées de stock
        Route::get('/create-entry', [StockMovementController::class, 'createEntry'])
            ->middleware('permission:inventory.create')
            ->name('create-entry');
        Route::post('/store-entry', [StockMovementController::class, 'storeEntry'])
            ->middleware('permission:inventory.create')
            ->name('store-entry');
        
        // Sorties de stock
        Route::get('/create-exit', [StockMovementController::class, 'createExit'])
            ->middleware('permission:inventory.create')
            ->name('create-exit');
        Route::post('/store-exit', [StockMovementController::class, 'storeExit'])
            ->middleware('permission:inventory.create')
            ->name('store-exit');
        
        // Affichage et actions
        Route::get('/{movement}', [StockMovementController::class, 'show'])
            ->middleware('permission:inventory.view')
            ->name('show');
        Route::post('/{movement}/cancel', [StockMovementController::class, 'cancel'])
            ->middleware('permission:inventory.edit')
            ->name('cancel');
        
        // Rapports
        Route::get('/export/report', [StockMovementController::class, 'exportReport'])
            ->middleware('permission:reports.export')
            ->name('export.report');
    });

    // Inventaires
    Route::resource('inventories', InventoryController::class)
        ->middleware('permission:inventory.view|inventory.create|inventory.edit|inventory.delete');
    
    Route::prefix('inventories')->name('inventories.')->group(function () {
        Route::post('/{inventory}/start', [InventoryController::class, 'start'])
            ->middleware('permission:inventory.edit')
            ->name('start');
        Route::post('/{inventory}/validate', [InventoryController::class, 'validate'])
            ->middleware('permission:inventory.edit')
            ->name('validate');
        Route::post('/{inventory}/apply', [InventoryController::class, 'apply'])
            ->middleware('permission:inventory.edit')
            ->name('apply');
        Route::get('/{inventory}/print', [InventoryController::class, 'print'])
            ->middleware('permission:reports.view')
            ->name('print');
        Route::get('/{inventory}/export', [InventoryController::class, 'export'])
            ->middleware('permission:reports.export')
            ->name('export');
    });

    // Régularisations de stock
    Route::resource('stock-regularizations', StockRegularizationController::class)
        ->middleware('permission:inventory.view|inventory.create|inventory.edit|inventory.delete');
        
    Route::prefix('stock-regularizations')->name('stock-regularizations.')->group(function () {
        Route::post('/{regularization}/validate', [StockRegularizationController::class, 'validate'])
            ->middleware('permission:inventory.edit')
            ->name('validate');
        Route::post('/{regularization}/cancel', [StockRegularizationController::class, 'cancel'])
            ->middleware('permission:inventory.delete')
            ->name('cancel');
    });

    // Entrepôts/Dépôts
    Route::resource('warehouses', WarehouseController::class)
        ->middleware('permission:inventory.view|inventory.create|inventory.edit|inventory.delete');
    
    Route::prefix('warehouses')->name('warehouses.')->group(function () {
        Route::get('/{warehouse}/stock', [WarehouseController::class, 'stock'])
            ->middleware('permission:inventory.view')
            ->name('stock');
        Route::get('/{warehouse}/movements', [WarehouseController::class, 'movements'])
            ->middleware('permission:inventory.view')
            ->name('movements');
        Route::post('/{warehouse}/transfer', [WarehouseController::class, 'transfer'])
            ->middleware('permission:inventory.create')
            ->name('transfer');
    });

    /*
    |--------------------------------------------------------------------------
    | GESTION DES ACHATS & FOURNISSEURS
    |--------------------------------------------------------------------------
    */
    
    Route::resource('purchase-orders', PurchaseOrderController::class)
        ->middleware('permission:purchases.view|purchases.create|purchases.edit|purchases.delete');
    
    Route::prefix('purchase-orders')->name('purchase-orders.')->group(function () {
        Route::post('/{order}/send', [PurchaseOrderController::class, 'send'])
            ->middleware('permission:purchases.edit')
            ->name('send');
        Route::get('/{order}/receive', [PurchaseOrderController::class, 'receiveForm'])
            ->middleware('permission:purchases.edit')
            ->name('receive-form');
        Route::post('/{order}/receive', [PurchaseOrderController::class, 'receive'])
            ->middleware('permission:purchases.edit')
            ->name('receive');
        Route::get('/{order}/print', [PurchaseOrderController::class, 'print'])
            ->middleware('permission:reports.view')
            ->name('print');
        Route::post('/{order}/cancel', [PurchaseOrderController::class, 'cancel'])
            ->middleware('permission:purchases.delete')
            ->name('cancel');
    });

    // Bons de livraison
    Route::resource('delivery-notes', DeliveryNoteController::class)
        ->middleware('permission:purchases.view|purchases.create|purchases.edit|purchases.delete');

    // Bons de retour
    Route::resource('return-notes', ReturnNoteController::class)
        ->middleware('permission:purchases.view|purchases.create|purchases.edit|purchases.delete');

    // Fournisseurs
    Route::resource('suppliers', SupplierController::class)
        ->middleware('permission:suppliers.view|suppliers.create|suppliers.edit|suppliers.delete');
    
    Route::prefix('suppliers')->name('suppliers.')->group(function () {
        Route::get('/export', [SupplierController::class, 'export'])
            ->middleware('permission:reports.export')
            ->name('export');
        Route::post('/import', [SupplierController::class, 'import'])
            ->middleware('permission:suppliers.create')
            ->name('import');
        Route::get('/{supplier}/purchase-history', [SupplierController::class, 'purchaseHistory'])
            ->middleware('permission:purchases.view')
            ->name('purchase-history');
        Route::get('/{supplier}/account-statement', [SupplierController::class, 'accountStatement'])
            ->middleware('permission:purchases.view')
            ->name('account-statement');
    });

    /*
    |--------------------------------------------------------------------------
    | GESTION DES CLIENTS
    |--------------------------------------------------------------------------
    */
    
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
    });

    /*
    |--------------------------------------------------------------------------
    | RAPPORTS ET ANALYSES
    |--------------------------------------------------------------------------
    */
    
    Route::prefix('reports')->name('reports.')->middleware('permission:reports.view')->group(function () {
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
        
        // Configuration rapports
        Route::get('/settings', [ReportController::class, 'settings'])
            ->middleware('permission:settings.edit')
            ->name('settings');
    });

    /*
    |--------------------------------------------------------------------------
    | ALERTES SYSTÈME
    |--------------------------------------------------------------------------
    */
    
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
    });

    /*
    |--------------------------------------------------------------------------
    | ADMINISTRATION UTILISATEURS
    |--------------------------------------------------------------------------
    */
    
    Route::resource('users', UserController::class)
        ->middleware('permission:users.view|users.create|users.edit|users.delete');
    
    Route::prefix('users')->name('users.')->group(function () {
        Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->middleware('permission:users.edit')
            ->name('toggle-status');
        Route::post('/{user}/reset-password', [UserController::class, 'resetPassword'])
            ->middleware('permission:users.edit')
            ->name('reset-password');
    });

    Route::resource('roles', RoleController::class)
        ->middleware('permission:users.view|users.create|users.edit|users.delete');

    Route::prefix('permissions')->name('permissions.')->middleware('permission:users.edit')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::post('/assign', [PermissionController::class, 'assign'])->name('assign');
        Route::post('/revoke', [PermissionController::class, 'revoke'])->name('revoke');
        Route::post('/toggle', [PermissionController::class, 'toggle'])->name('toggle');
        Route::post('/toggle-module', [PermissionController::class, 'toggleModule'])->name('toggle-module');
    });

    /*
    |--------------------------------------------------------------------------
    | PARAMÈTRES & CONFIGURATION SYSTÈME
    |--------------------------------------------------------------------------
    */
    
    Route::prefix('settings')->name('settings.')->middleware('permission:settings.view')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::post('/', [SettingController::class, 'store'])
            ->middleware('permission:settings.edit')
            ->name('store');
        Route::get('/export', [SettingController::class, 'export'])
            ->middleware('permission:settings.edit')
            ->name('export');
        Route::post('/import', [SettingController::class, 'import'])
            ->middleware('permission:settings.edit')
            ->name('import');
        Route::post('/reset', [SettingController::class, 'reset'])
            ->middleware('permission:settings.edit')
            ->name('reset');
    });

    // Paramètres système avancés
    Route::prefix('system-settings')->name('system-settings.')->middleware('permission:settings.view')->group(function () {
        Route::get('/', [SystemSettingController::class, 'index'])->name('index');
        Route::post('/', [SystemSettingController::class, 'store'])
            ->middleware('permission:settings.edit')
            ->name('store');
        Route::post('/optimize-system', [SystemSettingController::class, 'optimizeSystem'])
            ->middleware('permission:settings.edit')
            ->name('optimize-system');
    });

    /*
    |--------------------------------------------------------------------------
    | ACTIVITÉ & LOGS
    |--------------------------------------------------------------------------
    */
    
    Route::prefix('activity')->name('activity.')->group(function () {
        Route::get('/', [ActivityController::class, 'index'])
            ->middleware('permission:settings.view')
            ->name('index');
        Route::get('/logs', [ActivityController::class, 'logs'])
            ->middleware('permission:settings.view')
            ->name('logs');
        Route::get('/user/{user}', [ActivityController::class, 'userActivity'])
            ->middleware('permission:users.view')
            ->name('user');
        Route::delete('/clear/{days?}', [ActivityController::class, 'clear'])
            ->middleware('permission:settings.edit')
            ->name('clear');
    });
    
    Route::prefix('logs')->name('logs.')->middleware('permission:settings.view')->group(function () {
        Route::get('/', [LogController::class, 'index'])->name('index');
        Route::get('/export', [LogController::class, 'export'])
            ->middleware('permission:settings.edit')
            ->name('export');
        Route::delete('/clear-old', [LogController::class, 'clearOld'])
            ->middleware('permission:settings.edit')
            ->name('clear-old');
        Route::get('/download/{file}', [LogController::class, 'download'])
            ->middleware('permission:settings.edit')
            ->name('download');
    });

    /*
    |--------------------------------------------------------------------------
    | MAINTENANCE & SYSTÈME
    |--------------------------------------------------------------------------
    */
    
    Route::prefix('maintenance')->name('maintenance.')->middleware('permission:settings.edit')->group(function () {
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

    Route::prefix('backups')->name('backups.')->middleware('permission:settings.edit')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('index');
        Route::post('/create', [BackupController::class, 'create'])->name('create');
        Route::post('/create-scheduled', [BackupController::class, 'createScheduled'])->name('create-scheduled');
        Route::get('/{backup}/download', [BackupController::class, 'download'])->name('download');
        Route::post('/{backup}/restore', [BackupController::class, 'restore'])->name('restore');
        Route::delete('/{backup}', [BackupController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('performance')->name('performance.')->middleware('permission:settings.view')->group(function () {
        Route::get('/monitor', [PerformanceController::class, 'monitor'])->name('monitor');
        Route::get('/system-info', [PerformanceController::class, 'systemInfo'])->name('system-info');
        Route::get('/database-stats', [PerformanceController::class, 'databaseStats'])->name('database-stats');
        Route::post('/optimize', [PerformanceController::class, 'optimize'])
            ->middleware('permission:settings.edit')
            ->name('optimize');
    });

    /*
    |--------------------------------------------------------------------------
    | NOTIFICATIONS
    |--------------------------------------------------------------------------
    */
    
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
    | IMPORT/EXPORT DE DONNÉES
    |--------------------------------------------------------------------------
    */
    
    Route::prefix('import-export')->name('import-export.')->middleware('permission:settings.edit')->group(function () {
        Route::get('/', [ImportExportController::class, 'index'])->name('index');
        Route::get('/templates', [ImportExportController::class, 'templates'])->name('templates');
        Route::get('/template/{type}', [ImportExportController::class, 'downloadTemplate'])->name('template');
        Route::post('/import/{type}', [ImportExportController::class, 'import'])->name('import');
        Route::get('/export/{type}', [ImportExportController::class, 'export'])->name('export');
        Route::get('/export-all', [ImportExportController::class, 'exportAll'])->name('export-all');
        Route::post('/import-all', [ImportExportController::class, 'importAll'])->name('import-all');
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

});

/*
|--------------------------------------------------------------------------
| ROUTES API (v1)
|--------------------------------------------------------------------------
*/

Route::prefix('api/v1')->middleware(['auth:sanctum'])->name('api.')->group(function () {
    
    // Dashboard API
    Route::get('dashboard/stats', [DashboardController::class, 'apiStats'])->name('dashboard.stats');
    Route::get('dashboard/recent-activities', [DashboardController::class, 'apiRecentActivities'])->name('dashboard.activities');
    
    // Products API
    Route::apiResource('products', ProductController::class);
    Route::get('products/search/{term}', [ProductController::class, 'apiSearch'])->name('products.search');
    Route::get('products/barcode/{barcode}', [ProductController::class, 'apiFindByBarcode'])->name('products.barcode');
    
    // Sales API
    Route::apiResource('sales', SaleController::class);
    Route::get('sales/today/summary', [SaleController::class, 'apiTodaySummary'])->name('sales.today-summary');
    
    // Stock API
    Route::get('stock/alerts', [StockMovementController::class, 'apiAlerts'])->name('stock.alerts');
    Route::get('stock/movements/recent', [StockMovementController::class, 'apiRecentMovements'])->name('stock.recent-movements');
    
    // Customers API
    Route::apiResource('customers', CustomerController::class);
    Route::get('customers/search/{term}', [CustomerController::class, 'apiSearch'])->name('customers.search');
    
    // Suppliers API
    Route::apiResource('suppliers', SupplierController::class);
    
    // Reports API
    Route::get('reports/dashboard-data', [ReportController::class, 'apiDashboardData'])->name('reports.dashboard-data');
    
});

/*
|--------------------------------------------------------------------------
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
        
        Route::get('/clear-all', function () {
            \Artisan::call('cache:clear');
            \Artisan::call('config:clear');
            \Artisan::call('route:clear');
            \Artisan::call('view:clear');
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
        
        Route::get('/phpinfo', function () {
            return phpinfo();
        })->name('phpinfo');
        
    });
}