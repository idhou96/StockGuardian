<div class="flex flex-col h-full">
    <!-- Logo -->
    <div class="flex items-center justify-center h-16 bg-primary-600 text-white">
        <div class="flex items-center space-x-2">
            <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4 3a2 2 0 00-2 2v1.816a2 2 0 00.554 1.387l.104.111a2 2 0 00.894.486L4 8.868V19a1 1 0 11-2 0V9.5a1 1 0 10-2 0V19a3 3 0 003 3h12a3 3 0 003-3V9.5a1 1 0 10-2 0V19a1 1 0 11-2 0V8.868l.448-.068a2 2 0 00.894-.486l.104-.111A2 2 0 0018 6.816V5a2 2 0 00-2-2H4zM6 5h8a1 1 0 110 2H6a1 1 0 110-2z"/>
            </svg>
            <span class="text-xl font-bold">StockGuardian</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <!-- Dashboard - Accessible à tous -->
        <a href="{{ route('dashboard') }}" 
           class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h2a2 2 0 012 2v6H8V5z"/>
            </svg>
            <span>Tableau de bord</span>
        </a>

        @php
            $userRole = auth()->user()->role;
            $canAccessProducts = in_array($userRole, ['administrateur', 'magasinier', 'responsable_achats', 'vendeur']);
            $canAccessSales = in_array($userRole, ['administrateur', 'responsable_commercial', 'vendeur', 'caissiere']);
            $canAccessPurchases = in_array($userRole, ['administrateur', 'responsable_achats', 'magasinier']);
            $canAccessInventory = in_array($userRole, ['administrateur', 'magasinier', 'responsable_achats']);
            $canAccessReports = in_array($userRole, ['administrateur', 'responsable_commercial', 'responsable_achats', 'comptable']);
            $canAccessSettings = in_array($userRole, ['administrateur']);
        @endphp

        <!-- Section Gestion de Stock -->
        @if($canAccessProducts || $canAccessInventory)
        <div class="pt-4">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                Gestion de Stock
            </h3>

            @if($canAccessProducts)
            <!-- Produits -->
            <div class="nav-group">
                <button class="nav-group-button" data-target="products-menu">
                    <div class="flex items-center">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span>Produits</span>
                    </div>
                    <svg class="h-4 w-4 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="nav-submenu hidden" id="products-menu">
                    <a href="{{ route('products.index') }}" class="nav-submenu-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        Liste des produits
                    </a>
                    <a href="{{ route('products.create') }}" class="nav-submenu-link">
                        Nouveau produit
                    </a>
                    <a href="{{ route('families.index') }}" class="nav-submenu-link {{ request()->routeIs('families.*') ? 'active' : '' }}">
                        Familles
                    </a>
                </div>
            </div>
            @endif

            @if($canAccessInventory)
            <!-- Inventaires -->
            <div class="nav-group">
                <button class="nav-group-button" data-target="inventory-menu">
                    <div class="flex items-center">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <span>Inventaires</span>
                    </div>
                    <svg class="h-4 w-4 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="nav-submenu hidden" id="inventory-menu">
                    <a href="{{ route('inventories.index') }}" class="nav-submenu-link {{ request()->routeIs('inventories.*') ? 'active' : '' }}">
                        Liste des inventaires
                    </a>
                    <a href="{{ route('inventories.create') }}" class="nav-submenu-link">
                        Nouvel inventaire
                    </a>
                    <a href="{{ route('regularizations.index') }}" class="nav-submenu-link {{ request()->routeIs('regularizations.*') ? 'active' : '' }}">
                        Régularisations
                    </a>
                    <a href="{{ route('stock-movements.index') }}" class="nav-submenu-link {{ request()->routeIs('stock-movements.*') ? 'active' : '' }}">
                        Mouvements de stock
                    </a>
                </div>
            </div>
            @endif

            <!-- Entrepôts -->
            <a href="{{ route('warehouses.index') }}" 
               class="sidebar-link {{ request()->routeIs('warehouses.*') ? 'active' : '' }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>Entrepôts</span>
            </a>
        </div>
        @endif

        <!-- Section Ventes -->
        @if($canAccessSales)
        <div class="pt-4">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                Ventes
            </h3>

            <!-- Point de Vente -->
            <a href="{{ route('sales.pos') }}" 
               class="sidebar-link {{ request()->routeIs('sales.pos') ? 'active' : '' }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Point de Vente</span>
            </a>

            <!-- Ventes -->
            <div class="nav-group">
                <button class="nav-group-button" data-target="sales-menu">
                    <div class="flex items-center">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11H6a2 2 0 00-2 2v6a2 2 0 002 2h2M16 11h2a2 2 0 012 2v6a2 2 0 01-2 2h-2"/>
                        </svg>
                        <span>Ventes</span>
                    </div>
                    <svg class="h-4 w-4 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="nav-submenu hidden" id="sales-menu">
                    <a href="{{ route('sales.index') }}" class="nav-submenu-link {{ request()->routeIs('sales.index') ? 'active' : '' }}">
                        Liste des ventes
                    </a>
                    <a href="{{ route('invoices.index') }}" class="nav-submenu-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                        Factures
                    </a>
                    <a href="{{ route('payments.index') }}" class="nav-submenu-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                        Paiements
                    </a>
                    <a href="{{ route('return-notes.index') }}?type=client" class="nav-submenu-link">
                        Retours clients
                    </a>
                </div>
            </div>

            <!-- Clients -->
            <a href="{{ route('customers.index') }}" 
               class="sidebar-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
                <span>Clients</span>
            </a>
        </div>
        @endif

        <!-- Section Achats -->
        @if($canAccessPurchases)
        <div class="pt-4">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                Achats
            </h3>

            <!-- Commandes -->
            <div class="nav-group">
                <button class="nav-group-button" data-target="purchases-menu">
                    <div class="flex items-center">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11H6a2 2 0 00-2 2v6a2 2 0 002 2h2M16 11h2a2 2 0 012 2v6a2 2 0 01-2 2h-2"/>
                        </svg>
                        <span>Commandes</span>
                    </div>
                    <svg class="h-4 w-4 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="nav-submenu hidden" id="purchases-menu">
                    <a href="{{ route('purchase-orders.index') }}" class="nav-submenu-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}">
                        Liste des commandes
                    </a>
                    <a href="{{ route('purchase-orders.create') }}" class="nav-submenu-link">
                        Nouvelle commande
                    </a>
                    <a href="{{ route('delivery-notes.index') }}" class="nav-submenu-link {{ request()->routeIs('delivery-notes.*') ? 'active' : '' }}">
                        Bons de livraison
                    </a>
                    <a href="{{ route('return-notes.index') }}?type=fournisseur" class="nav-submenu-link">
                        Retours fournisseurs
                    </a>
                </div>
            </div>

            <!-- Fournisseurs -->
            <a href="{{ route('suppliers.index') }}" 
               class="sidebar-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>Fournisseurs</span>
            </a>
        </div>
        @endif

        <!-- Section Rapports -->
        @if($canAccessReports)
        <div class="pt-4">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                Rapports
            </h3>

            <div class="nav-group">
                <button class="nav-group-button" data-target="reports-menu">
                    <div class="flex items-center">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span>Rapports</span>
                    </div>
                    <svg class="h-4 w-4 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="nav-submenu hidden" id="reports-menu">
                    <a href="{{ route('reports.index') }}" class="nav-submenu-link {{ request()->routeIs('reports.index') ? 'active' : '' }}">
                        Vue d'ensemble
                    </a>
                    <a href="{{ route('reports.sales') }}" class="nav-submenu-link {{ request()->routeIs('reports.sales') ? 'active' : '' }}">
                        Rapport des ventes
                    </a>
                    <a href="{{ route('reports.purchase') }}" class="nav-submenu-link {{ request()->routeIs('reports.purchase') ? 'active' : '' }}">
                        Rapport des achats
                    </a>
                    <a href="{{ route('reports.stock') }}" class="nav-submenu-link {{ request()->routeIs('reports.stock') ? 'active' : '' }}">
                        Rapport de stock
                    </a>
                    <a href="{{ route('reports.financial') }}" class="nav-submenu-link {{ request()->routeIs('reports.financial') ? 'active' : '' }}">
                        Rapport financier
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- Section Administration -->
        @if($canAccessSettings)
        <div class="pt-4">
            <h3 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                Administration
            </h3>

            <!-- Utilisateurs -->
            <a href="{{ route('users.index') }}" 
               class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
                <span>Utilisateurs</span>
            </a>

            <!-- Paramètres -->
            <a href="{{ route('system-settings.index') }}" 
               class="sidebar-link {{ request()->routeIs('system-settings.*') ? 'active' : '' }}">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Paramètres</span>
            </a>
        </div>
        @endif
    </nav>

    <!-- User Profile Section -->
    <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center space-x-3">
            <div class="h-10 w-10 bg-primary-100 rounded-full flex items-center justify-center">
                <span class="text-primary-600 font-semibold text-sm">
                    {{ strtoupper(substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1)) }}
                </span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                    {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                    {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Styles CSS pour la sidebar -->
<style>
.sidebar-link {
    @apply flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150 ease-in-out;
}

.sidebar-link.active {
    @apply bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 border-r-2 border-primary-500;
}

.sidebar-link svg {
    @apply mr-3 flex-shrink-0;
}

.nav-group-button {
    @apply flex items-center justify-between w-full px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150 ease-in-out;
}

.nav-group-button:hover svg:last-child {
    @apply rotate-180;
}

.nav-group-button svg:last-child {
    @apply transition-transform duration-200;
}

.nav-submenu {
    @apply mt-2 ml-6 space-y-1;
}

.nav-submenu-link {
    @apply flex items-center px-3 py-2 text-sm text-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150 ease-in-out;
}

.nav-submenu-link.active {
    @apply bg-primary-50 dark:bg-primary-900 text-primary-600 dark:text-primary-300;
}
</style>

<!-- JavaScript pour la navigation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des menus déroulants
    const navButtons = document.querySelectorAll('.nav-group-button');
    
    navButtons.forEach(button => {
        button.addEventListener('click', function() {
            const target = document.getElementById(this.dataset.target);
            const arrow = this.querySelector('svg:last-child');
            
            if (target) {
                target.classList.toggle('hidden');
                arrow.classList.toggle('rotate-180');
            }
        });
    });

    // Garder les menus actifs ouverts
    document.querySelectorAll('.nav-submenu').forEach(submenu => {
        if (submenu.querySelector('.active')) {
            submenu.classList.remove('hidden');
            const button = submenu.parentElement.querySelector('.nav-group-button');
            if (button) {
                button.querySelector('svg:last-child').classList.add('rotate-180');
            }
        }
    });
});
</script>