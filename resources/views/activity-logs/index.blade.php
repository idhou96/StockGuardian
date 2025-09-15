<?php
// ===============================================
// VUES LOGS D'ACTIVITÉ & MONITORING - COMPLET
// ===============================================

// 🎯 VUE LOGS D'ACTIVITÉ
// resources/views/activity-logs/index.blade.php
?>

@extends('layouts.app')

@section('title', 'Logs d\'Activité')

@section('content')
<div class="space-y-6" x-data="activityLogs()">
    {{-- En-tête --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Logs d'Activité
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                    Consultez l'historique complet des actions utilisateurs
                </p>
            </div>
            
            <div class="flex gap-3">
                <button @click="exportLogs()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Exporter
                </button>
                <button @click="cleanupOldLogs()" 
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Nettoyer
                </button>
            </div>
        </div>
    </div>

    {{-- Filtres avancés --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Utilisateur</label>
                <select x-model="filters.user_id" @change="applyFilters()" 
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Tous les utilisateurs</option>
                    @foreach($users ?? [] as $user)
                        <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                <select x-model="filters.event" @change="applyFilters()" 
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Toutes les actions</option>
                    <option value="created">Création</option>
                    <option value="updated">Modification</option>
                    <option value="deleted">Suppression</option>
                    <option value="login">Connexion</option>
                    <option value="logout">Déconnexion</option>
                    <option value="viewed">Consultation</option>
                    <option value="exported">Export</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Module</label>
                <select x-model="filters.subject_type" @change="applyFilters()" 
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Tous les modules</option>
                    <option value="Product">Produits</option>
                    <option value="Sale">Ventes</option>
                    <option value="Purchase">Achats</option>
                    <option value="Inventory">Inventaires</option>
                    <option value="Customer">Clients</option>
                    <option value="Supplier">Fournisseurs</option>
                    <option value="User">Utilisateurs</option>
                    <option value="System">Système</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date début</label>
                <input type="date" 
                       x-model="filters.date_from" 
                       @change="applyFilters()"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date fin</label>
                <input type="date" 
                       x-model="filters.date_to" 
                       @change="applyFilters()"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            
            <div class="flex items-end">
                <button @click="resetFilters()" 
                        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                    Réinitialiser
                </button>
            </div>
        </div>
    </div>

    {{-- Statistiques --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-sm p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Actions Aujourd'hui</p>
                    <p class="text-2xl font-bold" x-text="stats.today">{{ $stats['today'] ?? 0 }}</p>
                </div>
                <svg class="w-8 h-8 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-sm p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Utilisateurs Actifs</p>
                    <p class="text-2xl font-bold" x-text="stats.activeUsers">{{ $stats['active_users'] ?? 0 }}</p>
                </div>
                <svg class="w-8 h-8 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-sm p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Modifications</p>
                    <p class="text-2xl font-bold" x-text="stats.updates">{{ $stats['updates'] ?? 0 }}</p>
                </div>
                <svg class="w-8 h-8 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg shadow-sm p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm">Connexions</p>
                    <p class="text-2xl font-bold" x-text="stats.logins">{{ $stats['logins'] ?? 0 }}</p>
                </div>
                <svg class="w-8 h-8 text-yellow-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg shadow-sm p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm">Erreurs</p>
                    <p class="text-2xl font-bold" x-text="stats.errors">{{ $stats['errors'] ?? 0 }}</p>
                </div>
                <svg class="w-8 h-8 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Timeline des activités --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">
                Historique d'Activité (<span x-text="filteredLogs.length">{{ $logs->count() ?? 0 }}</span>)
            </h3>
            <div class="flex gap-2">
                <button @click="toggleAutoRefresh()" 
                        :class="autoRefresh ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600'"
                        class="px-3 py-1 rounded text-sm font-medium transition-colors duration-200">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span x-text="autoRefresh ? 'Auto ON' : 'Auto OFF'"></span>
                </button>
                <button @click="refreshLogs()" 
                        class="text-gray-500 hover:text-gray-700 p-2 rounded transition-colors duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="max-h-96 overflow-y-auto">
            <div class="divide-y divide-gray-200">
                <template x-for="(log, index) in filteredLogs" :key="log.id">
                    <div class="p-6 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex gap-4">
                            {{-- Avatar et indicateur --}}
                            <div class="flex-shrink-0 relative">
                                <div class="h-10 w-10 rounded-full flex items-center justify-center text-white text-sm font-medium"
                                     :class="getEventColor(log.event)">
                                    <span x-text="getUserInitials(log.causer)"></span>
                                </div>
                                <div class="absolute -right-1 -top-1 h-4 w-4 rounded-full border-2 border-white"
                                     :class="getEventIndicator(log.event)">
                                </div>
                            </div>

                            {{-- Contenu --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="text-sm font-semibold text-gray-900" x-text="log.causer?.name || 'Système'"></h4>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                                  :class="getEventBadge(log.event)"
                                                  x-text="getEventLabel(log.event)"></span>
                                        </div>
                                        
                                        <p class="text-sm text-gray-600 mb-2" x-text="log.description"></p>
                                        
                                        <div class="flex items-center gap-4 text-xs text-gray-500">
                                            <span x-text="formatDate(log.created_at)"></span>
                                            <span x-text="log.subject_type" class="capitalize"></span>
                                            <span x-show="log.properties?.ip" x-text="'IP: ' + log.properties.ip"></span>
                                            <span x-show="log.properties?.user_agent" x-text="getBrowser(log.properties.user_agent)"></span>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center gap-2 ml-4">
                                        <button @click="viewLogDetails(log)" 
                                                class="text-blue-600 hover:text-blue-800 p-1 rounded transition-colors duration-200"
                                                title="Voir détails">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Détails supplémentaires --}}
                                <div x-show="log.properties && (log.properties.old || log.properties.attributes)" 
                                     class="mt-3 p-3 bg-gray-50 rounded-lg">
                                    <div class="text-xs text-gray-700">
                                        <template x-if="log.properties?.old">
                                            <div class="mb-2">
                                                <strong>Anciennes valeurs :</strong>
                                                <pre class="text-xs text-gray-600 mt-1" x-text="JSON.stringify(log.properties.old, null, 2)"></pre>
                                            </div>
                                        </template>
                                        <template x-if="log.properties?.attributes">
                                            <div>
                                                <strong>Nouvelles valeurs :</strong>
                                                <pre class="text-xs text-gray-600 mt-1" x-text="JSON.stringify(log.properties.attributes, null, 2)"></pre>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="filteredLogs.length === 0" class="p-12 text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Aucun log trouvé</h3>
                    <p class="text-gray-500">Aucune activité ne correspond aux filtres sélectionnés.</p>
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-200 flex justify-between items-center">
            <div class="text-sm text-gray-700">
                Affichage de <span x-text="((currentPage - 1) * perPage) + 1"></span> à 
                <span x-text="Math.min(currentPage * perPage, totalLogs)"></span> 
                sur <span x-text="totalLogs"></span> entrées
            </div>
            <div class="flex gap-2">
                <button @click="previousPage()" 
                        :disabled="currentPage === 1"
                        class="px-3 py-1 border border-gray-300 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                    Précédent
                </button>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded" x-text="currentPage"></span>
                <button @click="nextPage()" 
                        :disabled="currentPage >= Math.ceil(totalLogs / perPage)"
                        class="px-3 py-1 border border-gray-300 rounded disabled:opacity-50 disabled:cursor-not-allowed">
                    Suivant
                </button>
            </div>
        </div>
    </div>

    {{-- Modal détails --}}
    <div x-show="showDetailsModal" 
         class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
         @click="showDetailsModal = false">
        
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-96 overflow-y-auto" @click.stop>
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Détails de l'Activité</h3>
                    <button @click="showDetailsModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="px-6 py-4">
                <div class="space-y-4" x-show="selectedLog">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Utilisateur</label>
                            <p class="text-sm text-gray-900" x-text="selectedLog?.causer?.name || 'Système'"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Action</label>
                            <p class="text-sm text-gray-900" x-text="getEventLabel(selectedLog?.event)"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Module</label>
                            <p class="text-sm text-gray-900" x-text="selectedLog?.subject_type"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date & Heure</label>
                            <p class="text-sm text-gray-900" x-text="formatDate(selectedLog?.created_at, true)"></p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <p class="text-sm text-gray-900" x-text="selectedLog?.description"></p>
                    </div>
                    
                    <div x-show="selectedLog?.properties">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Propriétés</label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <pre class="text-xs text-gray-700 whitespace-pre-wrap" x-text="JSON.stringify(selectedLog?.properties, null, 2)"></pre>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <button @click="showDetailsModal = false" 
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                    Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function activityLogs() {
    return {
        logs: @json($logs ?? []),
        filteredLogs: [],
        stats: @json($stats ?? []),
        
        filters: {
            user_id: '',
            event: '',
            subject_type: '',
            date_from: '',
            date_to: ''
        },
        
        currentPage: 1,
        perPage: 20,
        totalLogs: 0,
        
        autoRefresh: false,
        refreshInterval: null,
        
        showDetailsModal: false,
        selectedLog: null,
        
        init() {
            this.filteredLogs = [...this.logs];
            this.totalLogs = this.logs.length;
            this.applyFilters();
        },
        
        applyFilters() {
            this.filteredLogs = this.logs.filter(log => {
                let matches = true;
                
                if (this.filters.user_id && log.causer_id != this.filters.user_id) {
                    matches = false;
                }
                
                if (this.filters.event && log.event !== this.filters.event) {
                    matches = false;
                }
                
                if (this.filters.subject_type && log.subject_type !== this.filters.subject_type) {
                    matches = false;
                }
                
                if (this.filters.date_from && new Date(log.created_at) < new Date(this.filters.date_from)) {
                    matches = false;
                }
                
                if (this.filters.date_to && new Date(log.created_at) > new Date(this.filters.date_to + ' 23:59:59')) {
                    matches = false;
                }
                
                return matches;
            });
            
            this.totalLogs = this.filteredLogs.length;
            this.currentPage = 1;
        },
        
        resetFilters() {
            this.filters = {
                user_id: '',
                event: '',
                subject_type: '',
                date_from: '',
                date_to: ''
            };
            this.applyFilters();
        },
        
        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },
        
        nextPage() {
            if (this.currentPage < Math.ceil(this.totalLogs / this.perPage)) {
                this.currentPage++;
            }
        },
        
        async refreshLogs() {
            try {
                const response = await fetch('/api/activity-logs');
                const data = await response.json();
                this.logs = data.logs;
                this.stats = data.stats;
                this.applyFilters();
            } catch (error) {
                console.error('Erreur lors du rafraîchissement:', error);
            }
        },
        
        toggleAutoRefresh() {
            this.autoRefresh = !this.autoRefresh;
            
            if (this.autoRefresh) {
                this.refreshInterval = setInterval(() => {
                    this.refreshLogs();
                }, 10000); // Rafraîchir toutes les 10 secondes
            } else {
                clearInterval(this.refreshInterval);
            }
        },
        
        viewLogDetails(log) {
            this.selectedLog = log;
            this.showDetailsModal = true;
        },
        
        async exportLogs() {
            try {
                const params = new URLSearchParams(this.filters);
                const response = await fetch(`/api/activity-logs/export?${params}`);
                const blob = await response.blob();
                
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `activity-logs-${new Date().toISOString().split('T')[0]}.csv`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
            } catch (error) {
                console.error('Erreur lors de l\'export:', error);
            }
        },
        
        async cleanupOldLogs() {
            if (confirm('Supprimer les logs de plus de 90 jours ?')) {
                try {
                    await fetch('/api/activity-logs/cleanup', {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    
                    await this.refreshLogs();
                    alert('Nettoyage effectué');
                } catch (error) {
                    console.error('Erreur lors du nettoyage:', error);
                }
            }
        },
        
        getEventColor(event) {
            const colors = {
                'created': 'bg-green-500',
                'updated': 'bg-blue-500',
                'deleted': 'bg-red-500',
                'login': 'bg-purple-500',
                'logout': 'bg-gray-500',
                'viewed': 'bg-yellow-500',
                'exported': 'bg-orange-500'
            };
            return colors[event] || 'bg-gray-500';
        },
        
        getEventIndicator(event) {
            const indicators = {
                'created': 'bg-green-400',
                'updated': 'bg-blue-400',
                'deleted': 'bg-red-400',
                'login': 'bg-purple-400',
                'logout': 'bg-gray-400',
                'viewed': 'bg-yellow-400',
                'exported': 'bg-orange-400'
            };
            return indicators[event] || 'bg-gray-400';
        },
        
        getEventBadge(event) {
            const badges = {
                'created': 'bg-green-100 text-green-800',
                'updated': 'bg-blue-100 text-blue-800',
                'deleted': 'bg-red-100 text-red-800',
                'login': 'bg-purple-100 text-purple-800',
                'logout': 'bg-gray-100 text-gray-800',
                'viewed': 'bg-yellow-100 text-yellow-800',
                'exported': 'bg-orange-100 text-orange-800'
            };
            return badges[event] || 'bg-gray-100 text-gray-800';
        },
        
        getEventLabel(event) {
            const labels = {
                'created': 'Création',
                'updated': 'Modification',
                'deleted': 'Suppression',
                'login': 'Connexion',
                'logout': 'Déconnexion',
                'viewed': 'Consultation',
                'exported': 'Export'
            };
            return labels[event] || event;
        },
        
        getUserInitials(user) {
            if (!user) return 'SYS';
            return (user.first_name?.charAt(0) || '') + (user.last_name?.charAt(0) || '');
        },
        
        getBrowser(userAgent) {
            if (!userAgent) return '';
            if (userAgent.includes('Chrome')) return 'Chrome';
            if (userAgent.includes('Firefox')) return 'Firefox';
            if (userAgent.includes('Safari')) return 'Safari';
            if (userAgent.includes('Edge')) return 'Edge';
            return 'Autre';
        },
        
        formatDate(dateString, includeTime = false) {
            const date = new Date(dateString);
            const options = {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            };
            
            if (includeTime) {
                options.hour = '2-digit';
                options.minute = '2-digit';
                options.second = '2-digit';
            }
            
            return date.toLocaleDateString('fr-FR', options);
        }
    }
}
</script>
@endsection
