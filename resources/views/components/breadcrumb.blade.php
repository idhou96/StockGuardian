@php
    $breadcrumbs = [];
    $routeName = request()->route()->getName();
    $parameters = request()->route()->parameters();
    
    // Configuration des breadcrumbs basée sur les routes
    switch(true) {
        case $routeName === 'dashboard':
            $breadcrumbs = [
                ['name' => 'Tableau de bord', 'url' => route('dashboard'), 'current' => true]
            ];
            break;
            
        // Produits
        case str_starts_with($routeName, 'products.'):
            $breadcrumbs = [
                ['name' => 'Tableau de bord', 'url' => route('dashboard')],
                ['name' => 'Gestion de Stock', 'url' => '#'],
            ];
            
            if ($routeName === 'products.index') {
                $breadcrumbs[] = ['name' => 'Produits', 'url' => route('products.index'), 'current' => true];
            } elseif ($routeName === 'products.create') {
                $breadcrumbs[] = ['name' => 'Produits', 'url' => route('products.index')];
                $breadcrumbs[] = ['name' => 'Nouveau produit', 'url' => route('products.create'), 'current' => true];
            } elseif ($routeName === 'products.show') {
                $product = $parameters['product'] ?? null;
                $breadcrumbs[] = ['name' => 'Produits', 'url' => route('products.index')];
                $breadcrumbs[] = ['name' => $product->name ?? 'Détails', 'url' => route('products.show', $product), 'current' => true];
            } elseif ($routeName === 'products.edit') {
                $product = $parameters['product'] ?? null;
                $breadcrumbs[] = ['name' => 'Produits', 'url' => route('products.index')];
                $breadcrumbs[] = ['name' => $product->name ?? 'Produit', 'url' => route('products.show', $product)];
                $breadcrumbs[] = ['name' => 'Modifier', 'url' => route('products.edit', $product), 'current' => true];
            }
            break;
            
        // Ventes
        case str_starts_with($routeName, 'sales.'):
            $breadcrumbs = [
                ['name' => 'Tableau de bord', 'url' => route('dashboard')],
                ['name' => 'Ventes', 'url' => '#'],
            ];
            
            if ($routeName === 'sales.index') {
                $breadcrumbs[] = ['name' => 'Liste des ventes', 'url' => route('sales.index'), 'current' => true];
            } elseif ($routeName === 'sales.pos') {
                $breadcrumbs[] = ['name' => 'Point de Vente', 'url' => route('sales.pos'), 'current' => true];
            } elseif ($routeName === 'sales.show') {
                $sale = $parameters['sale'] ?? null;
                $breadcrumbs[] = ['name' => 'Liste des ventes', 'url' => route('sales.index')];
                $breadcrumbs[] = ['name' => $sale->reference ?? 'Détails', 'url' => route('sales.show', $sale), 'current' => true];
            }
            break;
            
        // Clients
        case str_starts_with($routeName, 'customers.'):
            $breadcrumbs = [
                ['name' => 'Tableau de bord', 'url' => route('dashboard')],
                ['name' => 'Ventes', 'url' => '#'],
            ];
            
            if ($routeName === 'customers.index') {
                $breadcrumbs[] = ['name' => 'Clients', 'url' => route('customers.index'), 'current' => true];
            } elseif ($routeName === 'customers.create') {
                $breadcrumbs[] = ['name' => 'Clients', 'url' => route('customers.index')];
                $breadcrumbs[] = ['name' => 'Nouveau client', 'url' => route('customers.create'), 'current' => true];
            } elseif ($routeName === 'customers.show') {
                $customer = $parameters['customer'] ?? null;
                $breadcrumbs[] = ['name' => 'Clients', 'url' => route('customers.index')];
                $breadcrumbs[] = ['name' => ($customer->first_name ?? '') . ' ' . ($customer->last_name ?? ''), 'url' => route('customers.show', $customer), 'current' => true];
            }
            break;
            
        // Achats
        case str_starts_with($routeName, 'purchase-orders.'):
            $breadcrumbs = [
                ['name' => 'Tableau de bord', 'url' => route('dashboard')],
                ['name' => 'Achats', 'url' => '#'],
            ];
            
            if ($routeName === 'purchase-orders.index') {
                $breadcrumbs[] = ['name' => 'Commandes', 'url' => route('purchase-orders.index'), 'current' => true];
            } elseif ($routeName === 'purchase-orders.create') {
                $breadcrumbs[] = ['name' => 'Commandes', 'url' => route('purchase-orders.index')];
                $breadcrumbs[] = ['name' => 'Nouvelle commande', 'url' => route('purchase-orders.create'), 'current' => true];
            } elseif ($routeName === 'purchase-orders.show') {
                $order = $parameters['purchaseOrder'] ?? null;
                $breadcrumbs[] = ['name' => 'Commandes', 'url' => route('purchase-orders.index')];
                $breadcrumbs[] = ['name' => $order->reference ?? 'Détails', 'url' => route('purchase-orders.show', $order), 'current' => true];
            }
            break;
            
        // Fournisseurs
        case str_starts_with($routeName, 'suppliers.'):
            $breadcrumbs = [
                ['name' => 'Tableau de bord', 'url' => route('dashboard')],
                ['name' => 'Achats', 'url' => '#'],
            ];
            
            if ($routeName === 'suppliers.index') {
                $breadcrumbs[] = ['name' => 'Fournisseurs', 'url' => route('suppliers.index'), 'current' => true];
            } elseif ($routeName === 'suppliers.create') {
                $breadcrumbs[] = ['name' => 'Fournisseurs', 'url' => route('suppliers.index')];
                $breadcrumbs[] = ['name' => 'Nouveau fournisseur', 'url' => route('suppliers.create'), 'current' => true];
            }
            break;
            
        // Inventaires
        case str_starts_with($routeName, 'inventories.'):
            $breadcrumbs = [
                ['name' => 'Tableau de bord', 'url' => route('dashboard')],
                ['name' => 'Gestion de Stock', 'url' => '#'],
            ];
            
            if ($routeName === 'inventories.index') {
                $breadcrumbs[] = ['name' => 'Inventaires', 'url' => route('inventories.index'), 'current' => true];
            } elseif ($routeName === 'inventories.create') {
                $breadcrumbs[] = ['name' => 'Inventaires', 'url' => route('inventories.index')];
                $breadcrumbs[] = ['name' => 'Nouvel inventaire', 'url' => route('inventories.create'), 'current' => true];
            }
            break;
            
        // Mouvements de stock
        case str_starts_with($routeName, 'stock-movements.'):
            $breadcrumbs = [
                ['name' => 'Tableau de bord', 'url' => route('dashboard')],
                ['name' => 'Gestion de Stock', 'url' => '#'],
            ];
            
            if ($routeName === 'stock-movements.index') {
                $breadcrumbs[] = ['name' => 'Mouvements de stock', 'url' => route('stock-movements.index'), 'current' => true];
            } elseif ($routeName === 'stock-movements.create-entry') {
                $breadcrumbs[] = ['name' => 'Mouvements de stock', 'url' => route('stock-movements.index')];
                $breadcrumbs[] = ['name' => 'Entrée de stock', 'url' => route('stock-movements.create-entry'), 'current' => true];
            } elseif ($routeName === 'stock-movements.create-exit') {
                $breadcrumbs[] = ['name' => 'Mouvements de stock', 'url' => route('stock-movements.index')];
                $breadcrumbs[] = ['name' => 'Sortie de stock', 'url' => route('stock-movements.create-exit'), 'current' => true];
            }
            break;
            
        // Entrepôts
        case str_starts_with($routeName, 'warehouses.'):
            $breadcrumbs = [
                ['name' => 'Tableau de bord', 'url' => route('dashboard')],
                ['name' => 'Gestion de Stock', 'url' => '#'],
            ];
            
            if ($routeName === 'warehouses.index') {
                $breadcrumbs[] = ['name' => 'Entrepôts', 'url' => route('warehouses.index'), 'current' => true];
            } elseif ($routeName === 'warehouses.create') {
                $breadcrumbs[] = ['name' => 'Entrepôts', 'url' => route('warehouses.index')];
                $breadcrumbs[] = ['name' => 'Nouvel entrepôt', 'url' => route('warehouses.create'), 'current' => true];
            }
            break;
            
        // Rapports
        case str_starts_with($routeName, 'reports.'):
            $breadcrumbs = [
                ['name' => 'Tableau de bord', 'url' => route('dashboard')],
                ['name' => 'Rapports', 'url' => '#'],
            ];
            
            if ($routeName === 'reports.index') {
                $breadcrumbs[] = ['name' => 'Vue d\'ensemble', 'url' => route('reports.index'), 'current' => true];
            } elseif ($routeName === 'reports.sales') {
                $breadcrumbs[] = ['name' => 'Rapport des ventes', 'url' => route('reports.sales'), 'current' => true];
            } elseif ($routeName === 'reports.purchase') {
                $breadcrumbs[] = ['name' => 'Rapport des achats', 'url' => route('reports.purchase'), 'current' => true];
            } elseif ($routeName === 'reports.stock') {
                $breadcrumbs[] = ['name' => 'Rapport de stock', 'url' => route('reports.stock'), 'current' => true];
            } elseif ($routeName === 'reports.financial') {
                $breadcrumbs[] = ['name' => 'Rapport financier', 'url' => route('reports.financial'), 'current' => true];
            }
            break;
            
        // Administration
        case str_starts_with($routeName, 'users.'):
            $breadcrumbs = [
                ['name' => 'Tableau de bord', 'url' => route('dashboard')],
                ['name' => 'Administration', 'url' => '#'],
            ];
            
            if ($routeName === 'users.index') {
                $breadcrumbs[] = ['name' => 'Utilisateurs', 'url' => route('users.index'), 'current' => true];
            } elseif ($routeName === 'users.create') {
                $breadcrumbs[] = ['name' => 'Utilisateurs', 'url' => route('users.index')];
                $breadcrumbs[] = ['name' => 'Nouvel utilisateur', 'url' => route('users.create'), 'current' => true];
            }
            break;
            
        case str_starts_with($routeName, 'system-settings.'):
            $breadcrumbs = [
                ['name' => 'Tableau de bord', 'url' => route('dashboard')],
                ['name' => 'Administration', 'url' => '#'],
            ];
            
            if ($routeName === 'system-settings.index') {
                $breadcrumbs[] = ['name' => 'Paramètres système', 'url' => route('system-settings.index'), 'current' => true];
            }
            break;
            
        default:
            $breadcrumbs = [
                ['name' => 'Tableau de bord', 'url' => route('dashboard'), 'current' => true]
            ];
    }
@endphp

@if(count($breadcrumbs) > 0)
<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        @foreach($breadcrumbs as $index => $breadcrumb)
            <li class="inline-flex items-center">
                @if($index > 0)
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                @endif
                
                @if(isset($breadcrumb['current']) && $breadcrumb['current'])
                    <!-- Élément actuel -->
                    <span class="ml-1 text-sm font-medium text-gray-700 dark:text-gray-300 md:ml-2">
                        {{ $breadcrumb['name'] }}
                    </span>
                @else
                    <!-- Lien -->
                    <a href="{{ $breadcrumb['url'] }}" 
                       class="inline-flex items-center ml-1 text-sm font-medium text-gray-500 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 md:ml-2">
                        @if($index === 0)
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                            </svg>
                        @endif
                        {{ $breadcrumb['name'] }}
                    </a>
                @endif
            </li>
        @endforeach
    </ol>
</nav>

<!-- Actions rapides contextuelles (optionnel) -->
@if(isset($breadcrumb_actions) && count($breadcrumb_actions) > 0)
<div class="flex items-center space-x-2 ml-auto">
    @foreach($breadcrumb_actions as $action)
        <a href="{{ $action['url'] }}" 
           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
            @if(isset($action['icon']))
                {!! $action['icon'] !!}
            @endif
            {{ $action['label'] }}
        </a>
    @endforeach
</div>
@endif
@endif

@push('styles')
<style>
/* Style personnalisé pour les breadcrumbs */
.breadcrumb-item {
    @apply inline-flex items-center;
}

.breadcrumb-item:not(:last-child)::after {
    content: '/';
    @apply mx-2 text-gray-400;
}

.breadcrumb-link {
    @apply text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-200 transition-colors duration-150;
}

.breadcrumb-current {
    @apply text-gray-600 dark:text-gray-300;
}
</style>
@endpush