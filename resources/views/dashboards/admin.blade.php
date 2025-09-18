@extends('layouts.app')

@section('title', 'Dashboard Administrateur')

@push('styles')
<style>
    .metric-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
        transition: left 0.5s ease;
    }
    .metric-card:hover::before {
        left: 100%;
    }
    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .action-card {
        transition: all 0.3s ease;
        background: linear-gradient(145deg, #ffffff, #f8fafc);
    }
    .action-card:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .pulse-dot {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.7; }
        100% { transform: scale(1); opacity: 1; }
    }
    .fade-in {
        animation: fadeIn 0.8s ease-in;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50">
    {{-- Hero Section --}}
    <div class="relative mb-8">
        <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-2xl shadow-2xl p-8 text-white overflow-hidden">
            <div class="absolute inset-0 bg-black opacity-10"></div>
            <div class="absolute -top-4 -right-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
            <div class="absolute -bottom-8 -left-8 w-32 h-32 bg-white opacity-5 rounded-full"></div>
            
            <div class="relative z-10">
                <div class="flex items-center justify-between">
                    <div class="space-y-2">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 bg-green-400 rounded-full pulse-dot"></div>
                            <span class="text-sm font-medium opacity-90">Système opérationnel</span>
                        </div>
                        <h1 class="text-4xl font-bold tracking-tight">Tableau de Bord</h1>
                        <p class="text-xl text-indigo-100">Administration StockGuardian</p>
                        <div class="flex items-center space-x-4 mt-4">
                            <div class="text-sm opacity-75">
                                Dernier accès: {{ now()->format('d/m/Y à H:i') }}
                            </div>
                            <div class="w-1 h-4 bg-white opacity-30"></div>
                            <div class="text-sm opacity-75">
                                {{ auth()->user()->name }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="hidden lg:flex items-center space-x-6">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-2">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div class="text-xs opacity-80">Performance</div>
                        </div>
                        <div class="text-center">
                            <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mb-2">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="text-xs opacity-80">Sécurisé</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Metrics Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 fade-in">
        <div class="metric-card rounded-2xl p-6 text-white relative">
            <div class="absolute top-4 right-4">
                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="space-y-2">
                <h3 class="text-sm font-medium opacity-90">Utilisateurs Actifs</h3>
                <div class="text-3xl font-bold">{{ $stats['total_users'] ?? 127 }}</div>
                <div class="flex items-center space-x-2 text-xs">
                    <span class="px-2 py-1 bg-white bg-opacity-20 rounded-full">+12%</span>
                    <span class="opacity-75">vs mois dernier</span>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-emerald-500 to-green-600 rounded-2xl p-6 text-white relative metric-card">
            <div class="absolute top-4 right-4">
                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            <div class="space-y-2">
                <h3 class="text-sm font-medium opacity-90">Chiffre d'Affaires</h3>
                <div class="text-3xl font-bold">{{ number_format($stats['total_revenue'] ?? 2847560, 0, ',', ' ') }} F</div>
                <div class="flex items-center space-x-2 text-xs">
                    <span class="px-2 py-1 bg-white bg-opacity-20 rounded-full">+8.2%</span>
                    <span class="opacity-75">cette semaine</span>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl p-6 text-white relative metric-card">
            <div class="absolute top-4 right-4">
                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            <div class="space-y-2">
                <h3 class="text-sm font-medium opacity-90">Alertes Stock</h3>
                <div class="text-3xl font-bold">{{ $stats['low_stock_alerts'] ?? 23 }}</div>
                <div class="flex items-center space-x-2 text-xs">
                    <span class="px-2 py-1 bg-white bg-opacity-20 rounded-full">Urgent</span>
                    <span class="opacity-75">nécessite attention</span>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-rose-500 to-pink-600 rounded-2xl p-6 text-white relative metric-card">
            <div class="absolute top-4 right-4">
                <div class="w-8 h-8 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            <div class="space-y-2">
                <h3 class="text-sm font-medium opacity-90">Système</h3>
                <div class="text-3xl font-bold">{{ $stats['system_health'] ?? 'Optimal' }}</div>
                <div class="flex items-center space-x-2 text-xs">
                    <span class="px-2 py-1 bg-white bg-opacity-20 rounded-full">100%</span>
                    <span class="opacity-75">disponibilité</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Users Management --}}
        <div class="glass-card rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Gestion Utilisateurs</h3>
                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-3a3.5 3.5 0 11-7 0 3.5 3.5 0 017 0z"/>
                    </svg>
                </div>
            </div>
            
            <div class="space-y-3">
                <a href="{{ route('users.index') }}" class="action-card rounded-xl p-4 block border border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">Utilisateurs</div>
                            <div class="text-sm text-gray-500">Gérer les comptes utilisateurs</div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
                
                <a href="{{ route('roles.index') }}" class="action-card rounded-xl p-4 block border border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">Rôles & Permissions</div>
                            <div class="text-sm text-gray-500">Configurer les accès</div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            </div>
        </div>

        {{-- System Management --}}
        <div class="glass-card rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Système</h3>
                <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-red-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    </svg>
                </div>
            </div>
            
            <div class="space-y-3">
                <a href="{{ route('maintenance.index') }}" class="action-card rounded-xl p-4 block border border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">Maintenance</div>
                            <div class="text-sm text-gray-500">Optimisation système</div>
                        </div>
                        <div class="w-2 h-2 bg-green-400 rounded-full pulse-dot"></div>
                    </div>
                </a>
                
                <a href="{{ route('backups.index') }}" class="action-card rounded-xl p-4 block border border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">Sauvegardes</div>
                            <div class="text-sm text-gray-500">Protection des données</div>
                        </div>
                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Auto</span>
                    </div>
                </a>
            </div>
        </div>

        {{-- Analytics --}}
        <div class="glass-card rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Analytics</h3>
                <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            
            <div class="space-y-3">
                <a href="{{ route('reports.index') }}" class="action-card rounded-xl p-4 block border border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">Rapports</div>
                            <div class="text-sm text-gray-500">Analyses détaillées</div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
                
                <a href="{{ route('activity.index') }}" class="action-card rounded-xl p-4 block border border-gray-100">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-900">Logs</div>
                            <div class="text-sm text-gray-500">Activité système</div>
                        </div>
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">Live</span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-8">
        <div class="glass-card rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Performance des Ventes</h3>
                <div class="flex space-x-2">
                    <button class="px-3 py-1 text-xs bg-indigo-100 text-indigo-700 rounded-lg font-medium">7j</button>
                    <button class="px-3 py-1 text-xs text-gray-500 rounded-lg">30j</button>
                    <button class="px-3 py-1 text-xs text-gray-500 rounded-lg">90j</button>
                </div>
            </div>
            <div class="h-64 flex items-center justify-center">
                <div class="text-center text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <p class="text-sm">Graphique des ventes</p>
                    <canvas id="salesChart" class="hidden"></canvas>
                </div>
            </div>
        </div>

        <div class="glass-card rounded-2xl p-6 shadow-xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Activité en Temps Réel</h3>
                <span class="flex items-center text-xs text-green-600">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2 pulse-dot"></div>
                    En direct
                </span>
            </div>
            
            <div class="space-y-4">
                @if(isset($dashboardData['recent_activities']))
                    @foreach(array_slice($dashboardData['recent_activities'], 0, 5) as $activity)
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-8 h-8 bg-{{ $activity['color'] ?? 'blue' }}-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-{{ $activity['color'] ?? 'blue' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $activity['user'] }}</p>
                                <p class="text-xs text-gray-500">{{ $activity['action'] }} • {{ $activity['time'] }}</p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-8 text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm">Aucune activité récente</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- System Status --}}
    <div class="glass-card rounded-2xl p-6 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-800">État du Système</h3>
            <div class="flex items-center space-x-2 text-sm text-green-600">
                <div class="w-2 h-2 bg-green-500 rounded-full pulse-dot"></div>
                <span>Tous les services opérationnels</span>
            </div>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-green-50 rounded-xl">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="text-xs font-medium text-gray-600">Base de Données</div>
                <div class="text-xs text-green-600 mt-1">Opérationnelle</div>
            </div>
            
            <div class="text-center p-4 bg-green-50 rounded-xl">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="text-xs font-medium text-gray-600">API</div>
                <div class="text-xs text-green-600 mt-1">Stable</div>
            </div>
            
            <div class="text-center p-4 bg-green-50 rounded-xl">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="text-xs font-medium text-gray-600">Stockage</div>
                <div class="text-xs text-green-600 mt-1">75% Utilisé</div>
            </div>
            
            <div class="text-center p-4 bg-green-50 rounded-xl">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="text-xs font-medium text-gray-600">Sécurité</div>
                <div class="text-xs text-green-600 mt-1">Protégé</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh dashboard data
    setInterval(() => {
        // Refresh metrics via AJAX if needed
        console.log('Dashboard refresh...');
    }, 60000);
    
    // Add smooth scrolling and animations
    const cards = document.querySelectorAll('.metric-card, .action-card, .glass-card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationDelay = Math.random() * 0.5 + 's';
                entry.target.classList.add('fade-in');
            }
        });
    });
    
    cards.forEach(card => observer.observe(card));
});
</script>
@endpush