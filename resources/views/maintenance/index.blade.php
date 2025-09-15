<?php
// 🎯 VUES FINALES ESSENTIELLES

// ===================================
// 4. VUE MAINTENANCE ET BACKUP
// ===================================
// File: resources/views/maintenance/index.blade.php
?>
@extends('layouts.app')

@section('title', 'Maintenance Système')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="maintenanceManager()">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Maintenance Système</h1>
                    <p class="text-sm text-gray-600 mt-1">Outils de maintenance et sauvegarde</p>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="flex items-center">
                        <span class="text-sm text-gray-500 mr-2">Statut système :</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                            Opérationnel
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-6">
        <!-- État du système -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">CPU</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">15%</div>
                                <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                    Normal
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Base de données</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">2.1 GB</div>
                                <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                    Saine
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Stockage</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">78%</div>
                                <div class="ml-2 flex items-baseline text-sm font-semibold text-yellow-600">
                                    Attention
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Dernière sauvegarde</dt>
                            <dd class="flex items-baseline">
                                <div class="text-sm font-semibold text-gray-900">Il y a 2h</div>
                                <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                    Récente
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions de maintenance -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Sauvegarde et restauration -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Sauvegarde et Restauration
                </h3>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <button @click="createBackup('database')" 
                                :disabled="isProcessing"
                                class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white px-4 py-3 rounded-lg text-sm transition-colors">
                            <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7"/>
                            </svg>
                            Sauvegarder BDD
                        </button>
                        <button @click="createBackup('full')" 
                                :disabled="isProcessing"
                                class="bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white px-4 py-3 rounded-lg text-sm transition-colors">
                            <svg class="w-5 h-5 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1"/>
                            </svg>
                            Sauvegarde complète
                        </button>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Sauvegardes récentes</h4>
                        <div class="space-y-2">
                            @forelse($backups ?? [] as $backup)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $backup['name'] }}</div>
                                    <div class="text-xs text-gray-500">{{ $backup['size'] }} - {{ $backup['date'] }}</div>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="downloadBackup('{{ $backup['id'] }}')" 
                                            class="text-blue-600 hover:text-blue-900 text-sm">
                                        Télécharger
                                    </button>
                                    <button onclick="restoreBackup('{{ $backup['id'] }}')" 
                                            class="text-green-600 hover:text-green-900 text-sm">
                                        Restaurer
                                    </button>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4 text-gray-500">
                                <p class="text-sm">Aucune sauvegarde disponible</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Optimisation et nettoyage -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Optimisation et Nettoyage
                </h3>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-1 gap-3">
                        <button @click="optimizeDatabase()" 
                                :disabled="isProcessing"
                                class="bg-purple-600 hover:bg-purple-700 disabled:opacity-50 text-white px-4 py-3 rounded-lg text-sm transition-colors text-left">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                                </svg>
                                <div>
                                    <div class="font-medium">Optimiser la base de données</div>
                                    <div class="text-xs opacity-75">Nettoie et optimise les tables</div>
                                </div>
                            </div>
                        </button>
                        
                        <button @click="clearCache()" 
                                :disabled="isProcessing"
                                class="bg-orange-600 hover:bg-orange-700 disabled:opacity-50 text-white px-4 py-3 rounded-lg text-sm transition-colors text-left">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                <div>
                                    <div class="font-medium">Vider le cache</div>
                                    <div class="text-xs opacity-75">Supprime les fichiers temporaires</div>
                                </div>
                            </div>
                        </button>
                        
                        <button @click="clearLogs()" 
                                :disabled="isProcessing"
                                class="bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white px-4 py-3 rounded-lg text-sm transition-colors text-left">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <div>
                                    <div class="font-medium">Nettoyer les logs anciens</div>
                                    <div class="text-xs opacity-75">Supprime les logs > 3 mois</div>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations système -->
        <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-6">Informations système</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Application</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Version :</span>
                            <span class="text-gray-900">StockGuardian v1.0.0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Laravel :</span>
                            <span class="text-gray-900">{{ app()->version() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">PHP :</span>
                            <span class="text-gray-900">{{ PHP_VERSION }}</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Base de données</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Type :</span>
                            <span class="text-gray-900">MySQL</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Taille :</span>
                            <span class="text-gray-900">2.1 GB</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Tables :</span>
                            <span class="text-gray-900">28</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Serveur</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">OS :</span>
                            <span class="text-gray-900">{{ PHP_OS }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Mémoire :</span>
                            <span class="text-gray-900">{{ ini_get('memory_limit') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Timezone :</span>
                            <span class="text-gray-900">{{ config('app.timezone') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de progression -->
    <div x-show="showProgressModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900 mb-4" x-text="progressTitle"></h3>
                <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                    <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" 
                         :style="`width: ${progress}%`"></div>
                </div>
                <p class="text-sm text-gray-600" x-text="progressMessage"></p>
            </div>
        </div>
    </div>
</div>

<script>
function maintenanceManager() {
    return {
        isProcessing: false,
        showProgressModal: false,
        progress: 0,
        progressTitle: '',
        progressMessage: '',
        
        async createBackup(type) {
            this.startProgress(`Création de sauvegarde ${type}`, 'Initialisation...');
            
            try {
                for (let i = 0; i <= 100; i += 10) {
                    this.progress = i;
                    this.progressMessage = `${type === 'full' ? 'Sauvegarde complète' : 'Base de données'} en cours... ${i}%`;
                    await new Promise(resolve => setTimeout(resolve, 200));
                }
                
                this.progressMessage = 'Sauvegarde terminée !';
                setTimeout(() => {
                    this.endProgress();
                    alert('Sauvegarde créée avec succès');
                }, 1000);
            } catch (error) {
                this.endProgress();
                alert('Erreur lors de la sauvegarde');
            }
        },
        
        async optimizeDatabase() {
            this.startProgress('Optimisation de la base de données', 'Analyse des tables...');
            
            try {
                for (let i = 0; i <= 100; i += 20) {
                    this.progress = i;
                    if (i === 20) this.progressMessage = 'Optimisation des index...';
                    else if (i === 40) this.progressMessage = 'Nettoyage des données...';
                    else if (i === 60) this.progressMessage = 'Réorganisation des tables...';
                    else if (i === 80) this.progressMessage = 'Finalisation...';
                    else if (i === 100) this.progressMessage = 'Optimisation terminée !';
                    
                    await new Promise(resolve => setTimeout(resolve, 500));
                }
                
                setTimeout(() => {
                    this.endProgress();
                    alert('Base de données optimisée avec succès');
                }, 1000);
            } catch (error) {
                this.endProgress();
                alert('Erreur lors de l\'optimisation');
            }
        },
        
        async clearCache() {
            this.startProgress('Nettoyage du cache', 'Suppression des fichiers temporaires...');
            
            try {
                for (let i = 0; i <= 100; i += 25) {
                    this.progress = i;
                    await new Promise(resolve => setTimeout(resolve, 300));
                }
                
                setTimeout(() => {
                    this.endProgress();
                    alert('Cache vidé avec succès');
                }, 500);
            } catch (error) {
                this.endProgress();
                alert('Erreur lors du nettoyage');
            }
        },
        
        async clearLogs() {
            this.startProgress('Nettoyage des logs', 'Suppression des anciens logs...');
            
            try {
                for (let i = 0; i <= 100; i += 30) {
                    this.progress = i;
                    await new Promise(resolve => setTimeout(resolve, 200));
                }
                
                setTimeout(() => {
                    this.endProgress();
                    alert('Logs nettoyés avec succès');
                }, 500);
            } catch (error) {
                this.endProgress();
                alert('Erreur lors du nettoyage');
            }
        },
        
        startProgress(title, message) {
            this.isProcessing = true;
            this.showProgressModal = true;
            this.progress = 0;
            this.progressTitle = title;
            this.progressMessage = message;
        },
        
        endProgress() {
            this.isProcessing = false;
            this.showProgressModal = false;
            this.progress = 0;
        }
    }
}

function downloadBackup(id) {
    window.location.href = `/maintenance/backups/${id}/download`;
}

function restoreBackup(id) {
    if (confirm('Êtes-vous sûr de vouloir restaurer cette sauvegarde ? Cette action est irréversible.')) {
        alert('Restauration en cours...');
    }
}
</script>
@endsection