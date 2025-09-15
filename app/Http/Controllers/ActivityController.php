<?php
namespace App\Http\Controllers;

class ActivityController extends Controller
{
    public function index()
    {
        // Récupérer les activités récentes
        return view('activity.index');
    }
    
    public function logs()
    {
        // Afficher les logs d'activité détaillés
        return view('activity.logs');
    }
    
    public function userActivity($user)
    {
        // Afficher l'activité d'un utilisateur spécifique
        return view('activity.user');
    }
}