<?php

namespace App\Livewire\Layout;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Navigation extends Component
{
    public $showingNavigationDropdown = false;

    /**
     * Logout the current user.
     */
    public function logout()
    {
        Auth::logout();
        
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        
        return redirect('/');
    }

    /**
     * Get navigation items based on user role.
     */
    public function getNavigationItems()
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }

        $role = $user->role ?? 'guest';
        
        $items = [
            [
                'name' => 'Dashboard',
                'route' => 'dashboard',
                'active' => request()->routeIs('dashboard*')
            ],
        ];

        // Items selon les rôles
        $roleItems = [
            'products' => [
                'name' => 'Produits',
                'route' => 'products.index',
                'active' => request()->routeIs('products*'),
                'roles' => ['administrateur', 'magasinier', 'vendeur', 'responsable_commercial']
            ],
            'sales' => [
                'name' => 'Ventes',
                'route' => 'sales.index', 
                'active' => request()->routeIs('sales*'),
                'roles' => ['administrateur', 'vendeur', 'caissiere', 'responsable_commercial', 'comptable']
            ],
            'customers' => [
                'name' => 'Clients',
                'route' => 'customers.index',
                'active' => request()->routeIs('customers*'),
                'roles' => ['administrateur', 'vendeur', 'caissiere', 'responsable_commercial', 'comptable']
            ],
            'stock' => [
                'name' => 'Stock',
                'route' => 'stock-movements.index',
                'active' => request()->routeIs('stock*', 'inventories*', 'warehouses*'),
                'roles' => ['administrateur', 'magasinier']
            ],
            'purchases' => [
                'name' => 'Achats',
                'route' => 'purchase-orders.index',
                'active' => request()->routeIs('purchase*', 'suppliers*'),
                'roles' => ['administrateur', 'responsable_achats']
            ],
            'reports' => [
                'name' => 'Rapports',
                'route' => 'reports.index',
                'active' => request()->routeIs('reports*'),
                'roles' => ['administrateur', 'responsable_commercial', 'comptable']
            ]
        ];

        // Filtrer les items selon le rôle
        foreach ($roleItems as $key => $item) {
            if (in_array($role, $item['roles'])) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * Get user's role display name.
     */
    public function getUserRoleDisplay()
    {
        $user = Auth::user();
        if (!$user || !$user->role) {
            return 'Utilisateur';
        }

        $roles = [
            'administrateur' => 'Administrateur',
            'responsable_commercial' => 'Resp. Commercial',
            'vendeur' => 'Vendeur',
            'magasinier' => 'Magasinier',
            'responsable_achats' => 'Resp. Achats',
            'comptable' => 'Comptable',
            'caissiere' => 'Caissière',
            'invite' => 'Invité',
            'stagiaire' => 'Stagiaire'
        ];

        return $roles[$user->role] ?? ucfirst($user->role);
    }

    /**
     * Render the component.
     */
    public function render()
    {
        return view('livewire.layout.navigation');
    }
}