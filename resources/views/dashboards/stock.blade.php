
<?php
// ===============================================
// 🎯 DASHBOARD MAGASINIER
// resources/views/dashboard/stock.blade.php
?>
@extends('layouts.app')

@section('title', 'Dashboard Stock')

@section('content')
<div class="space-y-6">
    {{-- Header Stock --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Tableau de Bord Stock</h1>
                <p class="text-blue-100 mt-1">Contrôlez et optimisez vos stocks</p>
            </div>
            <div class="bg-blue-500 bg-opacity-30 rounded-full p-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Alertes Stock --}}
    @if($stockAlerts ?? 0 > 0)
    <div class="bg-red-50 border-l-4 border-red-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700 font-medium">{{ $stockAlerts }} produit(s) nécessitent votre attention</p>
                <a href="{{ route('alerts.stock') }}" class="text-sm text-red-700 underline">Voir les alertes</a>
            </div>
        </div>
    </div>
    @endif

    {{-- Actions rapides Stock --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <a href="{{ route('stock-movements.create-entry') }}" class="bg-green-600 hover:bg-green-700 rounded-lg shadow-lg p-6 text-white transition-all duration-200 transform hover:scale-105">
            <div class="text-center">
                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <h3 class="text-lg font-semibold">Entrée Stock</h3>
                <p class="text-green-100 text-sm">Ajouter du stock</p>
            </div>
        </a>

        <a href="{{ route('stock-movements.create-exit') }}" class="bg-red-600 hover:bg-red-700 rounded-lg shadow-lg p-6 text-white transition-all duration-200 transform hover:scale-105">
            <div class="text-center">
                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                </svg>
                <h3 class="text-lg font-semibold">Sortie Stock</h3>
                <p class="text-red-100 text-sm">Retirer du stock</p>
            </div>
        </a>

        <a href="{{ route('inventories.create') }}" class="bg-purple-600 hover:bg-purple-700 rounded-lg shadow-lg p-6 text-white transition-all duration-200 transform hover:scale-105">
            <div class="text-center">
                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <h3 class="text-lg font-semibold">Inventaire</h3>
                <p class="text-purple-100 text-sm">Lancer un inventaire</p>
            </div>
        </a>

        <a href="{{ route('warehouses.index') }}" class="bg-orange-600 hover:bg-orange-700 rounded-lg shadow-lg p-6 text-white transition-all duration-200 transform hover:scale-105">
            <div class="text-center">
                <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <h3 class="text-lg font-semibold">Entrepôts</h3>
                <p class="text-orange-100 text-sm">Gérer les dépôts</p>
            </div>
        </a>
    </div>

    {{-- Statistiques Stock --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($stockStats['total_value'] ?? 0, 0, ',', ' ') }} F</div>
                <svg class="w-6 h-6 ml-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
            </div>
            <p class="text-gray-600 text-sm">Valeur totale stock</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="text-2xl font-bold text-gray-900">{{ $stockStats['total_products'] ?? 0 }}</div>
                <svg class="w-6 h-6 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <p class="text-gray-600 text-sm">Produits en stock</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="text-2xl font-bold text-gray-900">{{ $stockStats['low_stock'] ?? 0 }}</div>
                <svg class="w-6 h-6 ml-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.864-.833-2.634 0L4.168 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <p class="text-gray-600 text-sm">Stock faible</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="text-2xl font-bold text-gray-900">{{ $stockStats['expired'] ?? 0 }}</div>
                <svg class="w-6 h-6 ml-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-gray-600 text-sm">Produits expirés</p>
        </div>
    </div>

    {{-- Derniers mouvements --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Derniers mouvements de stock</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentMovements ?? [] as $movement)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $movement->product->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $movement->type === 'entree' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($movement->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $movement->quantity }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $movement->created_at->format('d/m/Y H:i') }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Aucun mouvement récent</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection