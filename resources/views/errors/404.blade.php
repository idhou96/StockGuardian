{{-- resources/views/errors/404.blade.php --}}
@extends('layouts.app')

@section('title', 'Page non trouvée')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <!-- Icône d'erreur animée -->
        <div class="relative">
            <div class="animate-bounce">
                <div class="mx-auto h-32 w-32 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center shadow-lg">
                    <svg class="h-16 w-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
            <!-- Particules flottantes -->
            <div class="absolute top-0 left-0 w-full h-full pointer-events-none">
                <div class="absolute top-2 left-2 w-2 h-2 bg-blue-300 rounded-full animate-pulse"></div>
                <div class="absolute top-6 right-4 w-1 h-1 bg-blue-400 rounded-full animate-ping"></div>
                <div class="absolute bottom-4 left-6 w-3 h-3 bg-blue-200 rounded-full animate-bounce" style="animation-delay: 0.5s;"></div>
                <div class="absolute bottom-8 right-2 w-1 h-1 bg-blue-500 rounded-full animate-pulse" style="animation-delay: 1s;"></div>
            </div>
        </div>

        <!-- Titre d'erreur -->
        <div>
            <h1 class="text-6xl font-bold text-gray-900 mb-2">404</h1>
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Page non trouvée</h2>
            <p class="text-gray-600 mb-8">
                Oups ! Il semble que la page que vous cherchez n'existe pas dans notre stock. 
                Elle a peut-être été déplacée, supprimée ou l'URL est incorrecte.
            </p>
        </div>

        <!-- Suggestions d'actions -->
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <!-- Bouton retour -->
                <button onclick="goBack()" 
                        class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Retour
                </button>

                <!-- Bouton dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    Tableau de bord
                </a>
            </div>

            <!-- Recherche rapide -->
            <div class="max-w-xs mx-auto">
                <div class="relative">
                    <input type="text" 
                           id="quick-search" 
                           placeholder="Rechercher..."
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liens rapides -->
        <div class="border-t border-gray-200 pt-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Liens utiles</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <a href="{{ route('products.index') }}" 
                   class="text-blue-600 hover:text-blue-800 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Produits
                </a>
                <a href="{{ route('sales.index') }}" 
                   class="text-blue-600 hover:text-blue-800 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    Ventes
                </a>
                <a href="{{ route('customers.index') }}" 
                   class="text-blue-600 hover:text-blue-800 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    Clients
                </a>
                <a href="{{ route('suppliers.index') }}" 
                   class="text-blue-600 hover:text-blue-800 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Fournisseurs
                </a>
                <a href="{{ route('inventories.index') }}" 
                   class="text-blue-600 hover:text-blue-800 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Inventaires
                </a>
                <a href="{{ route('reports.index') }}" 
                   class="text-blue-600 hover:text-blue-800 flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Rapports
                </a>
            </div>
        </div>

        <!-- Informations de contact -->
        <div class="border-t border-gray-200 pt-6">
            <p class="text-sm text-gray-500">
                Besoin d'aide ? 
                <a href="mailto:support@stockguardian.com" class="text-blue-600 hover:text-blue-800">
                    Contactez notre support
                </a>
            </p>
        </div>
    </div>
</div>

<script>
function goBack() {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = '{{ route("dashboard") }}';
    }
}

// Recherche rapide
document.getElementById('quick-search').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        const query = this.value.trim();
        if (query) {
            // Rediriger vers la page de recherche globale ou faire une recherche AJAX
            window.location.href = '{{ route("search") }}?q=' + encodeURIComponent(query);
        }
    }
});

// Auto-focus sur le champ de recherche après 2 secondes
setTimeout(() => {
    document.getElementById('quick-search').focus();
}, 2000);

// Animation des particules
function animateParticles() {
    const particles = document.querySelectorAll('.animate-pulse, .animate-ping, .animate-bounce');
    particles.forEach((particle, index) => {
        setTimeout(() => {
            particle.style.animationDelay = `${Math.random() * 2}s`;
        }, index * 200);
    });
}

// Démarrer l'animation des particules
animateParticles();
</script>

<style>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

/* Animation pour les liens au survol */
a:hover svg {
    transform: scale(1.1);
    transition: transform 0.2s ease-in-out;
}

/* Amélioration de l'animation bounce */
@keyframes custom-bounce {
    0%, 20%, 53%, 80%, 100% {
        animation-timing-function: cubic-bezier(0.215, 0.610, 0.355, 1.000);
        transform: translate3d(0,0,0);
    }
    40%, 43% {
        animation-timing-function: cubic-bezier(0.755, 0.050, 0.855, 0.060);
        transform: translate3d(0, -10px, 0);
    }
    70% {
        animation-timing-function: cubic-bezier(0.755, 0.050, 0.855, 0.060);
        transform: translate3d(0, -5px, 0);
    }
    90% {
        transform: translate3d(0,-2px,0);
    }
}

.animate-bounce {
    animation: custom-bounce 2s infinite;
}
</style>
@endsection