<?php
namespace App\Http\Controllers;

class BackupController extends Controller
{
    public function index()
    {
        // Lister les sauvegardes
        return view('backups.index');
    }
    
    public function create()
    {
        // Créer une nouvelle sauvegarde
    }
    
    public function download($backup)
    {
        // Télécharger une sauvegarde
    }
    
    public function destroy($backup)
    {
        // Supprimer une sauvegarde
    }
}
