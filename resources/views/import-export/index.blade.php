<?php
// 🎯 VUES FINALES ULTIMES POUR COMPLÉTER STOCKGUARDIAN

// ===================================
// 4. VUE IMPORT/EXPORT DE DONNÉES
// ===================================
// File: resources/views/import-export/index.blade.php
?>

@extends('layouts.app')

@section('title', 'Import/Export de Données')

@section('content')
<div class="min-h-screen bg-gray-50" x-data="importExportManager()">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Import/Export de Données</h1>
                    <p class="text-sm text-gray-600 mt-1">Importez et exportez vos données en masse</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('import-export.templates') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Télécharger modèles</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-6">
        <div class="max-w-6xl mx-auto space-y-8">
            <!-- Import de données -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                    </svg>
                    Import de Données
                </h3>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Import Produits -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="text-center mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <h4 class="text-lg font-medium text-gray-900">Produits</h4>
                            <p class="text-sm text-gray-600">Importez vos produits depuis Excel/CSV</p>
                        </div>

                        <form @submit.prevent="importData('products')" class="space-y-4">
                            <div>
                                <input type="file" 
                                       x-ref="productsFile"
                                       accept=".xlsx,.xls,.csv"
                                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       x-model="importOptions.products.updateExisting"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Mettre à jour les produits existants</span>
                            </div>
                            <button type="submit" 
                                    :disabled="isProcessing"
                                    class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                Importer Produits
                            </button>
                        </form>
                        
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <a href="{{ route('import-export.template', 'products') }}" 
                               class="text-sm text-blue-600 hover:text-blue-900">
                                📄 Télécharger le modèle Excel
                            </a>
                        </div>
                    </div>

                    <!-- Import Clients -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="text-center mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                            </div>
                            <h4 class="text-lg font-medium text-gray-900">Clients</h4>
                            <p class="text-sm text-gray-600">Importez votre base clients</p>
                        </div>

                        <form @submit.prevent="importData('clients')" class="space-y-4">
                            <div>
                                <input type="file" 
                                       x-ref="clientsFile"
                                       accept=".xlsx,.xls,.csv"
                                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       x-model="importOptions.clients.updateExisting"
                                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Mettre à jour les clients existants</span>
                            </div>
                            <button type="submit" 
                                    :disabled="isProcessing"
                                    class="w-full bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                Importer Clients
                            </button>
                        </form>

                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <a href="{{ route('import-export.template', 'clients') }}" 
                               class="text-sm text-green-600 hover:text-green-900">
                                📄 Télécharger le modèle Excel
                            </a>
                        </div>
                    </div>

                    <!-- Import Fournisseurs -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <div class="text-center mb-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0h3m2 0h5M9 7h6m-6 4h6m-6 4h6m-6 4h6"/>
                                </svg>
                            </div>
                            <h4 class="text-lg font-medium text-gray-900">Fournisseurs</h4>
                            <p class="text-sm text-gray-600">Importez vos fournisseurs</p>
                        </div>

                        <form @submit.prevent="importData('suppliers')" class="space-y-4">
                            <div>
                                <input type="file" 
                                       x-ref="suppliersFile"
                                       accept=".xlsx,.xls,.csv"
                                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" 
                                       x-model="importOptions.suppliers.updateExisting"
                                       class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Mettre à jour les fournisseurs existants</span>
                            </div>
                            <button type="submit" 
                                    :disabled="isProcessing"
                                    class="w-full bg-purple-600 hover:bg-purple-700 disabled:opacity-50 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                Importer Fournisseurs
                            </button>
                        </form>

                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <a href="{{ route('import-export.template', 'suppliers') }}" 
                               class="text-sm text-purple-600 hover:text-purple-900">
                                📄 Télécharger le modèle Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export de données -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6 flex items-center">
                    <svg class="w-6 h-6 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l3-3m0 0l-3-3m3 3H6"/>
                    </svg>
                    Export de Données
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="border border-gray-200 rounded-lg p-4 text-center">
                        <h4 class="font-medium text-gray-900 mb-2">Produits</h4>
                        <p class="text-sm text-gray-600 mb-4">{{ $exportStats['products'] ?? 0 }} produits</p>
                        <button @click="exportData('products')" 
                                :disabled="isProcessing"
                                class="w-full bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white px-3 py-2 rounded text-sm transition-colors">
                            Exporter
                        </button>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4 text-center">
                        <h4 class="font-medium text-gray-900 mb-2">Clients</h4>
                        <p class="text-sm text-gray-600 mb-4">{{ $exportStats['clients'] ?? 0 }} clients</p>
                        <button @click="exportData('clients')" 
                                :disabled="isProcessing"
                                class="w-full bg-green-600 hover:bg-green-700 disabled:opacity-50 text-white px-3 py-2 rounded text-sm transition-colors">
                            Exporter
                        </button>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4 text-center">
                        <h4 class="font-medium text-gray-900 mb-2">Fournisseurs</h4>
                        <p class="text-sm text-gray-600 mb-4">{{ $exportStats['suppliers'] ?? 0 }} fournisseurs</p>
                        <button @click="exportData('suppliers')" 
                                :disabled="isProcessing"
                                class="w-full bg-purple-600 hover:bg-purple-700 disabled:opacity-50 text-white px-3 py-2 rounded text-sm transition-colors">
                            Exporter
                        </button>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4 text-center">
                        <h4 class="font-medium text-gray-900 mb-2">Ventes</h4>
                        <p class="text-sm text-gray-600 mb-4">{{ $exportStats['sales'] ?? 0 }} ventes</p>
                        <button @click="showSalesExportModal = true" 
                                class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-2 rounded text-sm transition-colors">
                            Exporter
                        </button>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4 text-center">
                        <h4 class="font-medium text-gray-900 mb-2">Stock</h4>
                        <p class="text-sm text-gray-600 mb-4">État actuel</p>
                        <button @click="exportData('stock')" 
                                :disabled="isProcessing"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white px-3 py-2 rounded text-sm transition-colors">
                            Exporter
                        </button>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4 text-center">
                        <h4 class="font-medium text-gray-900 mb-2">Mouvements</h4>
                        <p class="text-sm text-gray-600 mb-4">Historique</p>
                        <button @click="showMovementsExportModal = true" 
                                class="w-full bg-pink-600 hover:bg-pink-700 text-white px-3 py-2 rounded text-sm transition-colors">
                            Exporter
                        </button>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4 text-center">
                        <h4 class="font-medium text-gray-900 mb-2">Inventaires</h4>
                        <p class="text-sm text-gray-600 mb-4">{{ $exportStats['inventories'] ?? 0 }} inventaires</p>
                        <button @click="exportData('inventories')" 
                                :disabled="isProcessing"
                                class="w-full bg-teal-600 hover:bg-teal-700 disabled:opacity-50 text-white px-3 py-2 rounded text-sm transition-colors">
                            Exporter
                        </button>
                    </div>

                    <div class="border border-gray-200 rounded-lg p-4 text-center">
                        <h4 class="font-medium text-gray-900 mb-2">Tout</h4>
                        <p class="text-sm text-gray-600 mb-4">Backup complet</p>
                        <button @click="exportData('all')" 
                                :disabled="isProcessing"
                                class="w-full bg-gray-600 hover:bg-gray-700 disabled:opacity-50 text-white px-3 py-2 rounded text-sm transition-colors">
                            Exporter
                        </button>
                    </div>
                </div>
            </div>

            <!-- Historique des imports/exports -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Historique des Opérations</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Opération</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lignes</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($importExportHistory ?? [] as $operation)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $operation['date'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $operation['operation'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $operation['type'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $operation['lines'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($operation['status'] === 'success')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Réussi
                                        </span>
                                    @elseif($operation['status'] === 'error')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Erreur
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            En cours
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $operation['user'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($operation['log_file'])
                                    <a href="{{ $operation['log_file'] }}" 
                                       class="text-blue-600 hover:text-blue-900 mr-3">Voir log</a>
                                    @endif
                                    @if($operation['file_path'])
                                    <a href="{{ $operation['file_path'] }}" 
                                       class="text-green-600 hover:text-green-900">Télécharger</a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    <p class="text-sm">Aucun historique d'import/export</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Export Ventes -->
    <div x-show="showSalesExportModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Export des Ventes</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Période</label>
                        <select x-model="salesExportOptions.period" 
                                class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="today">Aujourd'hui</option>
                            <option value="week">Cette semaine</option>
                            <option value="month">Ce mois</option>
                            <option value="quarter">Ce trimestre</option>
                            <option value="year">Cette année</option>
                            <option value="custom">Personnalisée</option>
                        </select>
                    </div>
                    <div x-show="salesExportOptions.period === 'custom'" x-transition>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Du</label>
                                <input type="date" x-model="salesExportOptions.startDate" 
                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Au</label>
                                <input type="date" x-model="salesExportOptions.endDate" 
                                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button @click="showSalesExportModal = false" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                        Annuler
                    </button>
                    <button @click="exportSalesData()" 
                            class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                        Exporter
                    </button>
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
                <div class="mt-4" x-show="progress === 100">
                    <button @click="closeProgressModal()" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Terminer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function importExportManager() {
    return {
        isProcessing: false,
        showProgressModal: false,
        showSalesExportModal: false,
        showMovementsExportModal: false,
        progress: 0,
        progressTitle: '',
        progressMessage: '',
        
        importOptions: {
            products: { updateExisting: false },
            clients: { updateExisting: false },
            suppliers: { updateExisting: false }
        },
        
        salesExportOptions: {
            period: 'month',
            startDate: '',
            endDate: ''
        },
        
        async importData(type) {
            const fileInput = this.$refs[type + 'File'];
            if (!fileInput.files[0]) {
                alert('Veuillez sélectionner un fichier');
                return;
            }
            
            this.startProgress(`Import ${type}`, 'Lecture du fichier...');
            
            try {
                // Simulation du processus d'import
                for (let i = 0; i <= 100; i += 20) {
                    this.progress = i;
                    if (i === 20) this.progressMessage = 'Validation des données...';
                    else if (i === 40) this.progressMessage = 'Traitement des lignes...';
                    else if (i === 60) this.progressMessage = 'Sauvegarde en base...';
                    else if (i === 80) this.progressMessage = 'Vérifications finales...';
                    else if (i === 100) this.progressMessage = 'Import terminé !';
                    
                    await new Promise(resolve => setTimeout(resolve, 500));
                }
                
                // Ici vous feriez l'appel API réel
                // const formData = new FormData();
                // formData.append('file', fileInput.files[0]);
                // formData.append('options', JSON.stringify(this.importOptions[type]));
                // await fetch(`/import/${type}`, { method: 'POST', body: formData });
                
            } catch (error) {
                this.progressMessage = 'Erreur lors de l\'import : ' + error.message;
            }
        },
        
        async exportData(type) {
            this.startProgress(`Export ${type}`, 'Préparation des données...');
            
            try {
                for (let i = 0; i <= 100; i += 25) {
                    this.progress = i;
                    if (i === 25) this.progressMessage = 'Extraction des données...';
                    else if (i === 50) this.progressMessage = 'Formatage Excel...';
                    else if (i === 75) this.progressMessage = 'Génération du fichier...';
                    else if (i === 100) this.progressMessage = 'Export terminé !';
                    
                    await new Promise(resolve => setTimeout(resolve, 400));
                }
                
                // Ici vous feriez l'appel API réel
                // window.location.href = `/export/${type}`;
                
                setTimeout(() => {
                    this.progressMessage = 'Téléchargement automatique...';
                }, 1000);
                
            } catch (error) {
                this.progressMessage = 'Erreur lors de l\'export : ' + error.message;
            }
        },
        
        async exportSalesData() {
            this.showSalesExportModal = false;
            await this.exportData('sales');
        },
        
        startProgress(title, message) {
            this.isProcessing = true;
            this.showProgressModal = true;
            this.progress = 0;
            this.progressTitle = title;
            this.progressMessage = message;
        },
        
        closeProgressModal() {
            this.isProcessing = false;
            this.showProgressModal = false;
            this.progress = 0;
        }
    }
}
</script>
@endsection