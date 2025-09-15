
<?php
// ===============================================
// VUE MONITORING SYSTÈME
// ===============================================

// 🎯 VUE MONITORING SYSTÈME
// resources/views/system-monitoring/index.blade.php
?>

@extends('layouts.app')

@section('title', 'Monitoring Système')

@section('content')
<div class="space-y-6" x-data="systemMonitoring()" x-init="startMonitoring()">
    {{-- En-tête --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Monitoring Système
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="systemStatus === 'healthy' ? 'bg-green-100 text-green-800' : systemStatus === 'warning' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'"
                          x-text="getStatusLabel(systemStatus)"></span>
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                    Surveillez les performances et la santé de votre système StockGuardian
                </p>
            </div>
            
            <div class="flex gap-3">
                <span class="text-sm text-gray-500">
                    Dernière mise à jour: <span x-text="lastUpdate"></span>
                </span>
                <button @click="refreshData()" 
                        :disabled="loading"
                        class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span x-show="!loading">Actualiser</span>
                    <span x-show="loading">Chargement...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Métriques système --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">CPU</p>
                    <div class="flex items-baseline gap-2">
                        <p class="text-2xl font-bold text-gray-900" x-text="metrics.cpu + '%'"></p>
                        <span class="text-xs px-2 py-1 rounded-full"
                              :class="getCpuStatusColor(metrics.cpu)"
                              x-text="getCpuStatus(metrics.cpu)"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="h-2 rounded-full transition-all duration-500"
                             :class="getCpuBarColor(metrics.cpu)"
                             :style="`width: ${metrics.cpu}%`"></div>
                    </div>
                </div>
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Mémoire</p>
                    <div class="flex items-baseline gap-2">
                        <p class="text-2xl font-bold text-gray-900" x-text="metrics.memory + '%'"></p>
                        <span class="text-xs px-2 py-1 rounded-full"
                              :class="getMemoryStatusColor(metrics.memory)"
                              x-text="getMemoryStatus(metrics.memory)"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="h-2 rounded-full transition-all duration-500"
                             :class="getMemoryBarColor(metrics.memory)"
                             :style="`width: ${metrics.memory}%`"></div>
                    </div>
                </div>
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Stockage</p>
                    <div class="flex items-baseline gap-2">
                        <p class="text-2xl font-bold text-gray-900" x-text="metrics.storage + '%'"></p>
                        <span class="text-xs text-gray-500" x-text="metrics.storageUsed + '/' + metrics.storageTotal"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="h-2 rounded-full transition-all duration-500"
                             :class="getStorageBarColor(metrics.storage)"
                             :style="`width: ${metrics.storage}%`"></div>
                    </div>
                </div>
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Base de Données</p>
                    <div class="flex items-baseline gap-2">
                        <p class="text-2xl font-bold text-gray-900" x-text="metrics.dbConnections"></p>
                        <span class="text-xs text-gray-500">connexions</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Taille: <span x-text="metrics.dbSize"></span>
                    </p>
                </div>
                <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Alertes système --}}
    <div x-show="alerts.length > 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            Alertes Système
        </h3>
        
        <div class="space-y-3">
            <template x-for="alert in alerts" :key="alert.id">
                <div class="p-4 rounded-lg border-l-4"
                     :class="getAlertStyle(alert.severity)">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-medium" x-text="alert.title"></h4>
                            <p class="text-sm mt-1" x-text="alert.message"></p>
                            <p class="text-xs text-gray-500 mt-2" x-text="formatDate(alert.created_at)"></p>
                        </div>
                        <button @click="dismissAlert(alert.id)" 
                                class="text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Services et applications --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">État des Services</h3>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <template x-for="service in services" :key="service.name">
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div class="h-3 w-3 rounded-full"
                                 :class="service.status === 'online' ? 'bg-green-500' : service.status === 'warning' ? 'bg-yellow-500' : 'bg-red-500'"></div>
                            <div>
                                <h4 class="font-medium text-sm text-gray-900" x-text="service.name"></h4>
                                <p class="text-xs text-gray-500" x-text="service.description"></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs px-2 py-1 rounded-full"
                                  :class="service.status === 'online' ? 'bg-green-100 text-green-800' : service.status === 'warning' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'"
                                  x-text="service.status === 'online' ? 'En ligne' : service.status === 'warning' ? 'Attention' : 'Hors ligne'"></span>
                            <p class="text-xs text-gray-400 mt-1" x-text="service.uptime"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Graphiques de performance --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Utilisation CPU (24h)</h3>
            <div id="cpuChart" style="height: 300px;"></div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Utilisation Mémoire (24h)</h3>
            <div id="memoryChart" style="height: 300px;"></div>
        </div>
    </div>
</div>

<script>
function systemMonitoring() {
    return {
        loading: false,
        lastUpdate: '',
        systemStatus: 'healthy',
        
        metrics: {
            cpu: 0,
            memory: 0,
            storage: 0,
            storageUsed: '0 GB',
            storageTotal: '0 GB',
            dbConnections: 0,
            dbSize: '0 MB'
        },
        
        alerts: [],
        services: [],
        
        init() {
            this.loadData();
            this.initCharts();
        },
        
        startMonitoring() {
            // Actualisation automatique toutes les 30 secondes
            setInterval(() => {
                this.refreshData();
            }, 30000);
        },
        
        async loadData() {
            this.loading = true;
            try {
                const response = await fetch('/api/system-monitoring');
                const data = await response.json();
                
                this.metrics = data.metrics;
                this.alerts = data.alerts;
                this.services = data.services;
                this.systemStatus = data.systemStatus;
                this.lastUpdate = new Date().toLocaleTimeString('fr-FR');
                
                this.updateCharts(data.charts);
            } catch (error) {
                console.error('Erreur lors du chargement:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async refreshData() {
            await this.loadData();
        },
        
        initCharts() {
            // Initialisation des graphiques Chart.js
            // Code d'initialisation des graphiques
        },
        
        updateCharts(chartData) {
            // Mise à jour des graphiques avec les nouvelles données
        },
        
        async dismissAlert(alertId) {
            try {
                await fetch(`/api/system-monitoring/alerts/${alertId}/dismiss`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                this.alerts = this.alerts.filter(alert => alert.id !== alertId);
            } catch (error) {
                console.error('Erreur lors de la suppression de l\'alerte:', error);
            }
        },
        
        getStatusLabel(status) {
            const labels = {
                'healthy': 'Système Sain',
                'warning': 'Attention',
                'critical': 'Critique'
            };
            return labels[status] || status;
        },
        
        getCpuStatus(cpu) {
            if (cpu < 60) return 'Normal';
            if (cpu < 80) return 'Élevé';
            return 'Critique';
        },
        
        getCpuStatusColor(cpu) {
            if (cpu < 60) return 'bg-green-100 text-green-800';
            if (cpu < 80) return 'bg-yellow-100 text-yellow-800';
            return 'bg-red-100 text-red-800';
        },
        
        getCpuBarColor(cpu) {
            if (cpu < 60) return 'bg-green-500';
            if (cpu < 80) return 'bg-yellow-500';
            return 'bg-red-500';
        },
        
        getMemoryStatus(memory) {
            if (memory < 70) return 'Normal';
            if (memory < 85) return 'Élevé';
            return 'Critique';
        },
        
        getMemoryStatusColor(memory) {
            if (memory < 70) return 'bg-green-100 text-green-800';
            if (memory < 85) return 'bg-yellow-100 text-yellow-800';
            return 'bg-red-100 text-red-800';
        },
        
        getMemoryBarColor(memory) {
            if (memory < 70) return 'bg-green-500';
            if (memory < 85) return 'bg-yellow-500';
            return 'bg-red-500';
        },
        
        getStorageBarColor(storage) {
            if (storage < 80) return 'bg-green-500';
            if (storage < 90) return 'bg-yellow-500';
            return 'bg-red-500';
        },
        
        getAlertStyle(severity) {
            const styles = {
                'info': 'bg-blue-50 border-l-blue-400 text-blue-800',
                'warning': 'bg-yellow-50 border-l-yellow-400 text-yellow-800',
                'error': 'bg-red-50 border-l-red-400 text-red-800',
                'critical': 'bg-red-100 border-l-red-600 text-red-900'
            };
            return styles[severity] || styles.info;
        },
        
        formatDate(dateString) {
            return new Date(dateString).toLocaleString('fr-FR');
        }
    }
}
</script>
@endsection