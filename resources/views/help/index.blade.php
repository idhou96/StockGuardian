
// ===================================
// 7. VUE AIDE ET DOCUMENTATION
// ===================================
// File: resources/views/help/index.blade.php

@extends('layouts.app')

@section('title', 'Aide et Documentation')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="helpManager()">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Aide et Documentation</h1>
                    <p class="text-sm text-gray-600 mt-1">Guide d'utilisation de StockGuardian</p>
                </div>
                <div class="flex space-x-3">
                    <button @click="showSearchModal = true" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <span>Rechercher</span>
                    </button>
                    <a href="{{ route('help.pdf') }}" 
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Télécharger PDF</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-6">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Sidebar Navigation -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Navigation</h3>
                        <nav class="space-y-2">
                            <a href="#getting-started" 
                               @click="activeSection = 'getting-started'"
                               :class="{'bg-blue-100 text-blue-700 border-blue-200': activeSection === 'getting-started'}"
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 border border-transparent">
                                🚀 Premiers pas
                            </a>
                            <a href="#products" 
                               @click="activeSection = 'products'"
                               :class="{'bg-blue-100 text-blue-700 border-blue-200': activeSection === 'products'}"
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 border border-transparent">
                                📦 Gestion Produits
                            </a>
                            <a href="#sales" 
                               @click="activeSection = 'sales'"
                               :class="{'bg-blue-100 text-blue-700 border-blue-200': activeSection === 'sales'}"
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 border border-transparent">
                                💰 Point de Vente
                            </a>
                            <a href="#stock" 
                               @click="activeSection = 'stock'"
                               :class="{'bg-blue-100 text-blue-700 border-blue-200': activeSection === 'stock'}"
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 border border-transparent">
                                📊 Gestion Stock
                            </a>
                            <a href="#clients" 
                               @click="activeSection = 'clients'"
                               :class="{'bg-blue-100 text-blue-700 border-blue-200': activeSection === 'clients'}"
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 border border-transparent">
                                👥 Clients & Fournisseurs
                            </a>
                            <a href="#reports" 
                               @click="activeSection = 'reports'"
                               :class="{'bg-blue-100 text-blue-700 border-blue-200': activeSection === 'reports'}"
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 border border-transparent">
                                📈 Rapports
                            </a>
                            <a href="#settings" 
                               @click="activeSection = 'settings'"
                               :class="{'bg-blue-100 text-blue-700 border-blue-200': activeSection === 'settings'}"
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 border border-transparent">
                                ⚙️ Paramètres
                            </a>
                            <a href="#troubleshooting" 
                               @click="activeSection = 'troubleshooting'"
                               :class="{'bg-blue-100 text-blue-700 border-blue-200': activeSection === 'troubleshooting'}"
                               class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100 border border-transparent">
                                🔧 Dépannage
                            </a>
                        </nav>

                        <!-- Support rapide -->
                        <div class="mt-8 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <h4 class="text-sm font-medium text-blue-800 mb-2">Besoin d'aide ?</h4>
                            <div class="space-y-2 text-sm">
                                <div class="text-blue-700">📧 support@stockguardian.ci</div>
                                <div class="text-blue-700">📱 +225 XX XX XX XX</div>
                                <div class="text-blue-700">💬 Chat en ligne</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="lg:col-span-3">
                    <!-- Premiers pas -->
                    <div x-show="activeSection === 'getting-started'" x-transition class="space-y-6">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">🚀 Premiers pas avec StockGuardian</h2>
                            
                            <div class="prose max-w-none">
                                <h3>Bienvenue dans StockGuardian !</h3>
                                <p>StockGuardian est votre solution complète de gestion de pharmacie. Voici comment commencer :</p>
                                
                                <h4>1. Configuration initiale</h4>
                                <ol>
                                    <li>Accédez aux <strong>Paramètres système</strong> pour configurer votre entreprise</li>
                                    <li>Créez vos <strong>entrepôts/dépôts</strong></li>
                                    <li>Configurez les <strong>familles de produits</strong></li>
                                    <li>Ajoutez vos <strong>fournisseurs principaux</strong></li>
                                </ol>

                                <h4>2. Première utilisation</h4>
                                <ol>
                                    <li>Importez votre <strong>catalogue produits</strong> (ou créez-les manuellement)</li>
                                    <li>Initialisez vos <strong>stocks</strong> par entrepôt</li>
                                    <li>Créez vos premiers <strong>clients</strong></li>
                                    <li>Effectuez votre <strong>première vente</strong></li>
                                </ol>

                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 my-4">
                                    <h5 class="text-blue-800 font-medium mb-2">💡 Conseil</h5>
                                    <p class="text-blue-700 text-sm">Commencez par un petit nombre de produits pour vous familiariser avec l'interface, puis importez le reste de votre catalogue.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Video tutoriel -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">📹 Tutoriel vidéo</h3>
                            <div class="aspect-w-16 aspect-h-9 bg-gray-100 rounded-lg flex items-center justify-center">
                                <div class="text-center">
                                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293H15M9 10v4a2 2 0 002 2h2a2 2 0 002-2v-4M9 10V9a2 2 0 012-2h2a2 2 0 012 2v1"/>
                                    </svg>
                                    <p class="text-gray-600">Tutoriel vidéo "Premiers pas" - Durée : 10 minutes</p>
                                    <button class="mt-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                                        ▶️ Regarder
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gestion Produits -->
                    <div x-show="activeSection === 'products'" x-transition class="space-y-6">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">📦 Gestion des Produits</h2>
                            
                            <div class="prose max-w-none">
                                <h3>Créer un nouveau produit</h3>
                                <ol>
                                    <li>Allez dans <strong>Produits → Nouveau produit</strong></li>
                                    <li>Remplissez les <strong>informations obligatoires</strong> (nom, code, prix)</li>
                                    <li>Associez une <strong>famille de produits</strong></li>
                                    <li>Ajoutez les <strong>principes actifs</strong> si applicable</li>
                                    <li>Configurez les <strong>seuils d'alerte</strong> par entrepôt</li>
                                </ol>

                                <h3>Import en masse</h3>
                                <p>Pour importer plusieurs produits à la fois :</p>
                                <ol>
                                    <li>Téléchargez le <strong>modèle Excel</strong> depuis Import/Export</li>
                                    <li>Remplissez le fichier avec vos données</li>
                                    <li>Importez le fichier via <strong>Import/Export → Produits</strong></li>
                                </ol>

                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 my-4">
                                    <h5 class="text-yellow-800 font-medium mb-2">⚠️ Important</h5>
                                    <p class="text-yellow-700 text-sm">Les codes produits doivent être uniques. Le système refusera les doublons.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Point de Vente -->
                    <div x-show="activeSection === 'sales'" x-transition class="space-y-6">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">💰 Point de Vente (POS)</h2>
                            
                            <div class="prose max-w-none">
                                <h3>Effectuer une vente</h3>
                                <ol>
                                    <li>Accédez au <strong>Point de Vente</strong> depuis le menu principal</li>
                                    <li><strong>Recherchez et ajoutez</strong> les produits au panier</li>
                                    <li>Sélectionnez le <strong>client</strong> (optionnel)</li>
                                    <li>Vérifiez les <strong>quantités et prix</strong></li>
                                    <li>Choisissez le <strong>mode de paiement</strong></li>
                                    <li>Validez la vente et <strong>imprimez le ticket</strong></li>
                                </ol>

                                <h3>Raccourcis clavier</h3>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div><strong>F1</strong> : Nouvelle vente</div>
                                        <div><strong>F2</strong> : Rechercher produit</div>
                                        <div><strong>F3</strong> : Nouveau client</div>
                                        <div><strong>F4</strong> : Mode paiement</div>
                                        <div><strong>Entrée</strong> : Valider vente</div>
                                        <div><strong>Échap</strong> : Annuler</div>
                                    </div>
                                </div>

                                <h3>Gestion des retours</h3>
                                <p>Pour traiter un retour client :</p>
                                <ol>
                                    <li>Accédez à <strong>Bons de Retour → Nouveau retour</strong></li>
                                    <li>Sélectionnez le type : <strong>Retour client</strong></li>
                                    <li>Ajoutez les produits retournés</li>
                                    <li>Indiquez le motif du retour</li>
                                    <li>Validez pour ajuster automatiquement le stock</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Section -->
                    <div x-show="activeSection === 'troubleshooting'" x-transition class="space-y-6">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4">🔧 Questions Fréquentes</h2>
                            
                            <div class="space-y-4">
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-900 mb-2">❓ Comment réinitialiser mon mot de passe ?</h4>
                                    <p class="text-gray-600 text-sm">Contactez votre administrateur système qui peut réinitialiser votre mot de passe depuis la gestion des utilisateurs.</p>
                                </div>

                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-900 mb-2">❓ L'imprimante ne fonctionne pas</h4>
                                    <p class="text-gray-600 text-sm">Vérifiez que l'imprimante est configurée dans Paramètres → Ventes & POS → Imprimante tickets. Assurez-vous qu'elle est allumée et connectée.</p>
                                </div>

                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-900 mb-2">❓ Stock négatif affiché</h4>
                                    <p class="text-gray-600 text-sm">Effectuez une régularisation de stock depuis Gestion Stock → Régularisations pour corriger les écarts.</p>
                                </div>

                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="font-medium text-gray-900 mb-2">❓ Comment sauvegarder mes données ?</h4>
                                    <p class="text-gray-600 text-sm">Les sauvegardes automatiques sont configurées dans Maintenance → Sauvegarde. Vous pouvez aussi créer une sauvegarde manuelle.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Autres sections... (stock, clients, rapports, etc.) -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de recherche -->
    <div x-show="showSearchModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-2/3 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Rechercher dans l'aide</h3>
                <input x-model="searchQuery" 
                       @input="searchHelp()"
                       type="text" 
                       placeholder="Tapez votre question..."
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                
                <div class="mt-4 max-h-96 overflow-y-auto">
                    <div x-show="searchResults.length === 0 && searchQuery.length > 2" class="text-center py-8 text-gray-500">
                        Aucun résultat trouvé
                    </div>
                    <div class="space-y-2">
                        <template x-for="result in searchResults" :key="result.id">
                            <div class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer"
                                 @click="goToSection(result.section)">
                                <h5 class="font-medium text-gray-900" x-text="result.title"></h5>
                                <p class="text-sm text-gray-600" x-text="result.excerpt"></p>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button @click="showSearchModal = false" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Fermer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function helpManager() {
    return {
        activeSection: 'getting-started',
        showSearchModal: false,
        searchQuery: '',
        searchResults: [],
        
        helpData: [
            {
                id: 1,
                section: 'getting-started',
                title: 'Configuration initiale',
                excerpt: 'Comment configurer StockGuardian pour la première fois',
                content: 'Accédez aux Paramètres système pour configurer votre entreprise...'
            },
            {
                id: 2,
                section: 'products',
                title: 'Créer un produit',
                excerpt: 'Guide étape par étape pour ajouter un nouveau produit',
                content: 'Allez dans Produits → Nouveau produit...'
            },
            {
                id: 3,
                section: 'sales',
                title: 'Effectuer une vente',
                excerpt: 'Comment utiliser le point de vente',
                content: 'Accédez au Point de Vente depuis le menu principal...'
            },
            // Ajouter plus de données d'aide...
        ],
        
        searchHelp() {
            if (this.searchQuery.length < 3) {
                this.searchResults = [];
                return;
            }
            
            this.searchResults = this.helpData.filter(item => 
                item.title.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                item.excerpt.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                item.content.toLowerCase().includes(this.searchQuery.toLowerCase())
            );
        },
        
        goToSection(section) {
            this.activeSection = section;
            this.showSearchModal = false;
            this.searchQuery = '';
            this.searchResults = [];
        }
    }
}
</script>
@endsection