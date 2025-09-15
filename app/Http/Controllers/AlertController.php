<?php
namespace App\Http\Controllers;

class AlertController extends Controller
{
    public function stock()
    {
        // Récupérer les produits en rupture/stock faible
        $lowStockProducts = Product::whereRaw('stock_quantity <= minimum_stock')->get();
        $outOfStockProducts = Product::where('stock_quantity', 0)->get();
        
        return view('alerts.stock', compact('lowStockProducts', 'outOfStockProducts'));
    }
    
    public function expiry()
    {
        // Récupérer les produits proche de l'expiration
        return view('alerts.expiry');
    }
    
    public function markResolved($alert)
    {
        // Marquer une alerte comme résolue
    }
}
