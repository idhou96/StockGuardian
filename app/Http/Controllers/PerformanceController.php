<?php
namespace App\Http\Controllers;

class PerformanceController extends Controller
{
    public function monitor()
    {
        // Afficher les métriques de performance
        return view('performance.monitor');
    }
    
    public function systemInfo()
    {
        // Informations système
        return view('performance.system-info');
    }
    
    public function optimize()
    {
        // Optimiser le système
    }
}
