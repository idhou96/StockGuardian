<?php

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
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
})->name('home');

Route::get('/help/public', [HelpController::class, 'public'])->name('help.public');

/*
|--------------------------------------------------------------------------
| Routes Protégées - Middleware Auth Extended
|--------------------------------------------------------------------------
*/
Route::middleware(['auth.extended'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', fn() => redirect()->route('dashboard.general'))->name('dashboard');

    Route::get('/api/dashboard/kpis', [DashboardController::class, 'kpis'])->name('dashboard.kpis');
    Route::get('/api/dashboard/charts', [DashboardController::class, 'charts'])->name('dashboard.charts');

    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])
        ->middleware('role:administrateur')->name('dashboard.admin');

    Route::get('/dashboard/manager', [DashboardController::class, 'manager'])
        ->middleware('role:responsable_commercial')->name('dashboard.manager');

    Route::get('/dashboard/sales', [DashboardController::class, 'sales'])
        ->middleware('role:vendeur,caissiere,responsable_commercial')->name('dashboard.sales');

    Route::get('/dashboard/stock', [DashboardController::class, 'stock'])
        ->middleware('role:magasinier,administrateur')->name('dashboard.stock');

    Route::get('/dashboard/purchases', [DashboardController::class, 'purchases'])
        ->middleware('role:responsable_achats,administrateur')->name('dashboard.purchases');

    Route::get('/dashboard/accounting', [DashboardController::class, 'accounting'])
        ->middleware('role:comptable,administrateur')->name('dashboard.accounting');

    Route::get('/dashboard/general', [DashboardController::class, 'general'])
        ->middleware('role:invite,stagiaire')->name('dashboard.general');

    /*
    |--------------------------------------------------------------------------
    | GESTION DES PRODUITS
    |--------------------------------------------------------------------------
    */
    Route::resource('products', ProductController::class)
        ->middleware('permission:products.view|products.create|products.edit|products.delete');

    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/search', [ProductController::class, 'search'])->name('search');
        Route::get('/search-ajax', [ProductController::class, 'searchAjax'])->name('search-ajax');
        Route::get('/barcode/{barcode}', [ProductController::class, 'findByBarcode'])->name('barcode');
        Route::get('/export', [ProductController::class, 'export'])->middleware('permission:reports.export')->name('export');
        Route::post('/import', [ProductController::class, 'import'])->middleware('permission:products.create')->name('import');
        Route::post('/{product}/duplicate', [ProductController::class, 'duplicate'])->middleware('permission:products.create')->name('duplicate');
        Route::get('/{product}/print-label', [ProductController::class, 'printLabel'])->middleware('permission:reports.view')->name('print-label');
        Route::post('/{product}/toggle-favorite', [ProductController::class, 'toggleFavorite'])->name('toggle-favorite');
        Route::get('/{product}/stock-history', [ProductController::class, 'stockHistory'])->middleware('permission:inventory.view')->name('stock-history');
    });

    Route::resource('product-families', ProductFamilyController::class)
        ->middleware('permission:products.view|products.create|products.edit|products.delete');
    Route::resource('active-principles', ActivePrincipleController::class)
        ->middleware('permission:products.view|products.create|products.edit|products.delete');

    /*
    |--------------------------------------------------------------------------
    | VENTES & POS
    |--------------------------------------------------------------------------
    */
    Route::middleware(['pos'])->group(function () {
        Route::resource('sales', SaleController::class);

        Route::prefix('sales')->name('sales.')->group(function () {
            Route::get('/{sale}/print', [SaleController::class, 'print'])->name('print');
            Route::get('/{sale}/pdf', [SaleController::class, 'pdf'])->name('pdf');
            Route::post('/{sale}/cancel', [SaleController::class, 'cancel'])->name('cancel');
            Route::post('/{sale}/refund', [SaleController::class, 'refund'])->name('refund');
            Route::get('/report/daily', [SaleController::class, 'dailyReport'])->name('report.daily');
            Route::get('/report/monthly', [SaleController::class, 'monthlyReport'])->name('report.monthly');
            Route::get('/export/excel', [SaleController::class, 'exportExcel'])->name('export.excel');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | STOCK & INVENTAIRES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['stock'])->group(function () {
        Route::resource('stock-movements', StockMovementController::class);
        Route::resource('inventories', InventoryController::class);
        Route::resource('stock-regularizations', StockRegularizationController::class);
        Route::resource('warehouses', WarehouseController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | ACHATS & FOURNISSEURS
    |--------------------------------------------------------------------------
    */
    Route::middleware(['purchases'])->group(function () {
        Route::resource('purchase-orders', PurchaseOrderController::class);
        Route::resource('delivery-notes', DeliveryNoteController::class);
        Route::resource('return-notes', ReturnNoteController::class);
        Route::resource('suppliers', SupplierController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | CLIENTS
    |--------------------------------------------------------------------------
    */
    Route::resource('customers', CustomerController::class);

    /*
    |--------------------------------------------------------------------------
    | RAPPORTS & ALERTES
    |--------------------------------------------------------------------------
    */
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
    });
    Route::prefix('alerts')->name('alerts.')->group(function () {
        Route::get('/stock', [AlertController::class, 'stock'])->name('stock');
    });

    /*
    |--------------------------------------------------------------------------
    | ADMINISTRATION, PARAMÈTRES, MAINTENANCE
    |--------------------------------------------------------------------------
    */
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);
        Route::resource('settings', SettingController::class);
        Route::resource('system-settings', SystemSettingController::class);
        Route::resource('activity', ActivityController::class);
        Route::resource('logs', LogController::class);
        Route::resource('maintenance', MaintenanceController::class);
        Route::resource('backups', BackupController::class);
        Route::resource('performance', PerformanceController::class);
        Route::resource('import-export', ImportExportController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | NOTIFICATIONS & PROFIL
    |--------------------------------------------------------------------------
    */
    Route::resource('notifications', NotificationController::class);
 /*
    |--------------------------------------------------------------------------
    | PROFIL UTILISATEUR - ROUTES CORRIGÉES
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('update-password');
        Route::put('/preferences', [ProfileController::class, 'updatePreferences'])->name('update-preferences');
        Route::post('/avatar', [ProfileController::class, 'updateAvatar'])->name('update-avatar');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
    
    /*
    |--------------------------------------------------------------------------
    | AIDE & DOCUMENTATION
    |--------------------------------------------------------------------------
    */
    Route::resource('help', HelpController::class);
});

