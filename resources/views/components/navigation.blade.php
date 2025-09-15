<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between h-16">
        <!-- Left section -->
        <div class="flex items-center">
            <!-- Page Title -->
            <div class="flex-shrink-0">
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                    @yield('page-title', 'Tableau de bord')
                </h1>
            </div>
        </div>

        <!-- Right section -->
        <div class="flex items-center space-x-4">
            <!-- Search -->
            <div class="hidden md:flex items-center">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" 
                           class="block w-80 pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm" 
                           placeholder="Rechercher produits, clients, commandes..."
                           id="global-search">
                </div>
                <!-- Search Results Dropdown -->
                <div class="absolute top-full left-0 right-0 mt-1 bg-white dark:bg-gray-800 shadow-lg rounded-lg border border-gray-200 dark:border-gray-700 hidden z-50" id="search-results">
                    <div class="p-3">
                        <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Résultats</div>
                        <div id="search-content">
                            <!-- Les résultats seront affichés ici -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="flex items-center space-x-2">
                @if(in_array(auth()->user()->role, ['administrateur', 'vendeur', 'caissiere']))
                <!-- Point de Vente -->
                <a href="{{ route('sales.pos') }}" 
                   class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-150">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Vente
                </a>
                @endif

                @if(in_array(auth()->user()->role, ['administrateur', 'responsable_achats']))
                <!-- Nouvelle Commande -->
                <a href="{{ route('purchase-orders.create') }}" 
                   class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-150">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Commande
                </a>
                @endif
            </div>

            <!-- Notifications -->
            <div class="relative">
                <button type="button" 
                        class="p-2 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 rounded-full"
                        id="notifications-button"
                        aria-expanded="false">
                    <span class="sr-only">Voir les notifications</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19H6.5A2.5 2.5 0 014 16.5v-7A2.5 2.5 0 016.5 7H12"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <!-- Badge de notification -->
                    <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400 ring-2 ring-white dark:ring-gray-800"></span>
                </button>

                <!-- Dropdown des notifications -->
                <div class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 hidden" id="notifications-dropdown">
                    <div class="py-1">
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">Notifications</h3>
                        </div>
                        
                        <!-- Stock faible -->
                        <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-2 w-2 bg-orange-400 rounded-full mt-2"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900 dark:text-white">Stock faible détecté</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">5 produits en stock critique</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Il y a 2 heures</p>
                                </div>
                            </div>
                        </div>

                        <!-- Commande en attente -->
                        <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-2 w-2 bg-blue-400 rounded-full mt-2"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900 dark:text-white">Nouvelle commande reçue</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Commande #CMD202501001</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Il y a 1 heure</p>
                                </div>
                            </div>
                        </div>

                        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                            <a href="#" class="text-xs text-primary-600 hover:text-primary-500">Voir toutes les notifications</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dark Mode Toggle -->
            <button type="button" 
                    onclick="toggleDarkMode()"
                    class="p-2 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 rounded-full">
                <span class="sr-only">Basculer le mode sombre</span>
                <svg class="h-5 w-5 hidden dark:block" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                </svg>
                <svg class="h-5 w-5 block dark:hidden" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                </svg>
            </button>

            <!-- Profile dropdown -->
            <div class="relative">
                <div>
                    <button type="button" 
                            class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500" 
                            id="user-menu-button" 
                            aria-expanded="false" 
                            aria-haspopup="true">
                        <span class="sr-only">Ouvrir le menu utilisateur</span>
                        <div class="h-8 w-8 bg-primary-100 rounded-full flex items-center justify-center">
                            <span class="text-primary-600 font-semibold text-xs">
                                {{ strtoupper(substr(auth()->user()->first_name, 0, 1) . substr(auth()->user()->last_name, 0, 1)) }}
                            </span>
                        </div>
                    </button>
                </div>

                <!-- Dropdown menu -->
                <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 hidden" 
                     id="user-dropdown" 
                     role="menu" 
                     aria-orientation="vertical" 
                     aria-labelledby="user-menu-button" 
                     tabindex="-1">
                    <div class="py-1" role="none">
                        <!-- User Info -->
                        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ auth()->user()->email }}
                            </p>
                        </div>

                        <!-- Profile -->
                        <a href="{{ route('profile.edit') }}" 
                           class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" 
                           role="menuitem" 
                           tabindex="-1">
                            <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Mon profil
                        </a>

                        @if(auth()->user()->role === 'administrateur')
                        <!-- Settings -->
                        <a href="{{ route('system-settings.index') }}" 
                           class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" 
                           role="menuitem" 
                           tabindex="-1">
                            <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Paramètres
                        </a>
                        @endif

                        <!-- Help -->
                        <a href="#" 
                           class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" 
                           role="menuitem" 
                           tabindex="-1">
                            <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Aide
                        </a>

                        <div class="border-t border-gray-200 dark:border-gray-700"></div>

                        <!-- Sign out -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700" 
                                    role="menuitem" 
                                    tabindex="-1">
                                <svg class="mr-3 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Se déconnecter
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript pour la navigation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Notifications dropdown
    const notificationsButton = document.getElementById('notifications-button');
    const notificationsDropdown = document.getElementById('notifications-dropdown');
    
    notificationsButton?.addEventListener('click', function() {
        notificationsDropdown.classList.toggle('hidden');
        // Fermer les autres dropdowns
        document.getElementById('user-dropdown')?.classList.add('hidden');
    });

    // User menu dropdown
    const userMenuButton = document.getElementById('user-menu-button');
    const userDropdown = document.getElementById('user-dropdown');
    
    userMenuButton?.addEventListener('click', function() {
        userDropdown.classList.toggle('hidden');
        // Fermer les autres dropdowns
        notificationsDropdown?.classList.add('hidden');
    });

    // Global search
    const searchInput = document.getElementById('global-search');
    const searchResults = document.getElementById('search-results');
    
    searchInput?.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        
        if (query.length > 2) {
            // Simuler une recherche (à remplacer par une vraie API)
            setTimeout(() => {
                document.getElementById('search-content').innerHTML = `
                    <div class="space-y-2">
                        <div class="flex items-center p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg cursor-pointer">
                            <div class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-900 dark:text-white">Paracétamol 500mg</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Produit</p>
                            </div>
                        </div>
                        <div class="flex items-center p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg cursor-pointer">
                            <div class="flex-shrink-0 h-8 w-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M8 11v6a2 2 0 002 2h4a2 2 0 002-2v-6M8 11H6a2 2 0 00-2 2v6a2 2 0 002 2h2M16 11h2a2 2 0 012 2v6a2 2 0 01-2 2h-2"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-gray-900 dark:text-white">CMD202501001</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Commande</p>
                            </div>
                        </div>
                    </div>
                `;
                searchResults.classList.remove('hidden');
            }, 300);
        } else {
            searchResults.classList.add('hidden');
        }
    });

    // Fermer les dropdowns en cliquant à l'extérieur
    document.addEventListener('click', function(e) {
        if (!notificationsButton?.contains(e.target) && !notificationsDropdown?.contains(e.target)) {
            notificationsDropdown?.classList.add('hidden');
        }
        
        if (!userMenuButton?.contains(e.target) && !userDropdown?.contains(e.target)) {
            userDropdown?.classList.add('hidden');
        }
        
        if (!searchInput?.contains(e.target) && !searchResults?.contains(e.target)) {
            searchResults?.classList.add('hidden');
        }
    });
});
</script>