<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class MiddlewareServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Enregistrer des alias supplémentaires si nécessaire
        $router = $this->app->make(Router::class);
        
        // Groupes de middleware personnalisés
        $router->middlewareGroup('admin', [
            'auth:sanctum',
            'role:administrateur',
        ]);

        $router->middlewareGroup('manager', [
            'auth:sanctum', 
            'role:administrateur,responsable_commercial,responsable_achats',
        ]);

        $router->middlewareGroup('stock_manager', [
            'auth:sanctum',
            'role:administrateur,magasinier,responsable_achats',
            'warehouse.access',
        ]);

        $router->middlewareGroup('sales_team', [
            'auth:sanctum',
            'role:administrateur,responsable_commercial,vendeur,caissiere',
        ]);
    }
}
