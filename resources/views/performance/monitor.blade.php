<?php
// ===============================================
// 🎯 VUE MONITORING PERFORMANCE
// resources/views/performance/monitor.blade.php
?>
@extends('layouts.app')

@section('title', 'Monitoring Performance')

@section('content')
<div class="space-y-6" x-data="performanceMonitor()">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Monitoring Performance
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                    Surveillez les performances système en temps réel
                </p>
            </div>
            
            <div class="flex gap-3">
                <button @click="refreshMetrics()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Actualiser
                </button>
                <button @click="generateReport()" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Rapport
                </button>
            </div>
        </div>
    </div>

    {{-- Métriques en temps réel --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-gray-900" x-text="metrics.cpu_usage + '%'">
                        {{ $performanceMetrics['cpu_usage'] ?? '0%' }}
                    </div>
                    <p class="text-gray-600 text-sm">CPU</p>
                </div>
                <div class="relative w-16 h-16">
                    <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
                        <path class="text-gray-200" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                        <path class="text-blue-500" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" 
                              :stroke-dasharray="`${metrics.cpu_usage}, 100`" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-gray-900" x-text="metrics.memory_usage + '%'">
                        {{ $performanceMetrics['memory_usage'] ?? '0%' }}
                    </div>
                    <p class="text-gray-600 text-sm">Mémoire</p>
                </div>
                <div class="relative w-16 h-16">
                    <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
                        <path class="text-gray-200" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                        <path class="text-green-500" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" 
                              :stroke-dasharray="`${metrics.memory_usage}, 100`" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold text-gray-900" x-text="metrics.disk_usage + '%'">
                        {{ $performanceMetrics['disk_usage'] ?? '0%' }}
                    </div>
                    <p class="text-gray-600 text-sm">Disque</p>
                </div>
                <div class="relative w-16 h-16">
                    <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
                        <path class="text-gray-200" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                        <path class="text-yellow-500" stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" 
                              :stroke-dasharray="`${metrics.disk_usage}, 100`" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="text-2xl font-bold text-gray-900" x-text="metrics.response_time + 'ms'">
                    {{ $performanceMetrics['response_time'] ?? '0ms' }}
                </div>
                <svg class="w-6 h-6 ml-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <p class="text-gray-600 text-sm">Temps réponse</p>
        </div>
    </div>

    {{-- Graphiques de performance --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Utilisation CPU (24h)</h3>
            <div class="h-64">
                <canvas id="cpuChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Mémoire & Disque (24h)</h3>
            <div class="h-64">
                <canvas id="memoryChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Informations système détaillées --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Informations Système</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">Serveur</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">OS:</span>
                            <span class="font-medium">{{ $systemInfo['os'] ?? 'Linux' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">PHP:</span>
                            <span class="font-medium">{{ $systemInfo['php_version'] ?? PHP_VERSION }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Laravel:</span>
                            <span class="font-medium">{{ $systemInfo['laravel_version'] ?? app()->version() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Uptime:</span>
                            <span class="font-medium">{{ $systemInfo['uptime'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="font-medium text-gray-900 mb-3">Base de données</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Type:</span>
                            <span class="font-medium">{{ $dbInfo['type'] ?? 'MySQL' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Version:</span>
                            <span class="font-medium">{{ $dbInfo['version'] ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Taille:</span>
                            <span class="font-medium">{{ $dbInfo['size'] ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Connexions:</span>
                            <span class="font-medium">{{ $dbInfo['connections'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="font-medium text-gray-900 mb-3">Application</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Environnement:</span>
                            <span class="font-medium">{{ $appInfo['env'] ?? app()->environment() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Debug:</span>
                            <span class="font-medium">{{ $appInfo['debug'] ?? (config('app.debug') ? 'Activé' : 'Désactivé') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Cache:</span>
                            <span class="font-medium">{{ $appInfo['cache_driver'] ?? config('cache.default') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Queue:</span>
                            <span class="font-medium">{{ $appInfo['queue_driver'] ?? config('queue.default') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
function performanceMonitor() {
    return {
        metrics: {
            cpu_usage: {{ $performanceMetrics['cpu_usage'] ?? 0 }},
            memory_usage: {{ $performanceMetrics['memory_usage'] ?? 0 }},
            disk_usage: {{ $performanceMetrics['disk_usage'] ?? 0 }},
            response_time: {{ $performanceMetrics['response_time'] ?? 0 }}
        },
        
        init() {
            this.initCharts();
            this.startRealTimeUpdates();
        },
        
        initCharts() {
            // Graphique CPU
            const cpuCtx = document.getElementById('cpuChart').getContext('2d');
            new Chart(cpuCtx, {
                type: 'line',
                data: {
                    labels: @json($chartData['cpu']['labels'] ?? []),
                    datasets: [{
                        label: 'CPU Usage (%)',
                        data: @json($chartData['cpu']['data'] ?? []),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
            
            // Graphique Mémoire & Disque
            const memoryCtx = document.getElementById('memoryChart').getContext('2d');
            new Chart(memoryCtx, {
                type: 'line',
                data: {
                    labels: @json($chartData['memory']['labels'] ?? []),
                    datasets: [{
                        label: 'Mémoire (%)',
                        data: @json($chartData['memory']['data'] ?? []),
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4
                    }, {
                        label: 'Disque (%)',
                        data: @json($chartData['disk']['data'] ?? []),
                        borderColor: 'rgb(251, 191, 36)',
                        backgroundColor: 'rgba(251, 191, 36, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        },
        
        startRealTimeUpdates() {
            setInterval(() => {
                this.refreshMetrics();
            }, 30000); // Actualiser toutes les 30 secondes
        },
        
        async refreshMetrics() {
            try {
                const response = await fetch('/performance/monitor/metrics');
                const data = await response.json();
                this.metrics = data;
            } catch (error) {
                console.error('Erreur lors de l\'actualisation:', error);
            }
        },
        
        async generateReport() {
            try {
                const response = await fetch('/performance/generate-report', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });
                
                if (response.ok) {
                    const blob = await response.blob();
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'performance-report-' + new Date().toISOString().split('T')[0] + '.pdf';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);
                }
            } catch (error) {
                alert('Erreur lors de la génération du rapport: ' + error.message);
            }
        }
    }
}
</script>
@endpush
@endsection