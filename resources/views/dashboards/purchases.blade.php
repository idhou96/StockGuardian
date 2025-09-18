<?php
// ===============================================
// VUES SYSTÈME, MAINTENANCE & ALERTES - COMPLÉMENTAIRES
// ===============================================

// 🎯 DASHBOARD ACHATS
// resources/views/dashboard/purchases.blade.php
?>
@extends('layouts.app')

@section('title', 'Dashboard Achats')

@section('content')
<div class="space-y-6">
    {{-- Header Achats --}}
    <div class="bg-gradient-to-r from-orange-600 to-orange-800 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Tableau de Bord Achats</h1>
                <p class="text-orange-100 mt-1">Optimisez vos approvisionnements</p>
            </div>
            <div class="bg-orange-500 bg-opacity-30 rounded-full p-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Actions rapides Achats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('purchase-orders.create') }}" class="bg-blue-600 hover:bg-blue-700 rounded-lg shadow-lg p-6 text-white transition-all duration-200 transform hover:scale-105">
            <div class="flex items-center">
                <svg class="w-12 h-12 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold">Nouvelle Commande</h3>
                    <p class="text-blue-100 text-sm">Commander auprès des fournisseurs</p>
                </div>
            </div>
        </a>

        <a href="{{ route('suppliers.index') }}" class="bg-purple-600 hover:bg-purple-700 rounded-lg shadow-lg p-6 text-white transition-all duration-200 transform hover:scale-105">
            <div class="flex items-center">
                <svg class="w-12 h-12 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold">Fournisseurs</h3>
                    <p class="text-purple-100 text-sm">Gérer les partenaires</p>
                </div>
            </div>
        </a>

        <a href="{{ route('reports.purchases') }}" class="bg-green-600 hover:bg-green-700 rounded-lg shadow-lg p-6 text-white transition-all duration-200 transform hover:scale-105">
            <div class="flex items-center">
                <svg class="w-12 h-12 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold">Rapports</h3>
                    <p class="text-green-100 text-sm">Analyses des achats</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Statistiques Achats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($purchaseStats['total_month'] ?? 0, 0, ',', ' ') }} F</div>
                <svg class="w-6 h-6 ml-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
            </div>
            <p class="text-gray-600 text-sm">Achats ce mois</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="text-2xl font-bold text-gray-900">{{ $purchaseStats['pending_orders'] ?? 0 }}</div>
                <svg class="w-6 h-6 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-gray-600 text-sm">Commandes en attente</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="text-2xl font-bold text-gray-900">{{ $purchaseStats['suppliers_count'] ?? 0 }}</div>
                <svg class="w-6 h-6 ml-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <p class="text-gray-600 text-sm">Fournisseurs actifs</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($purchaseStats['average_order'] ?? 0, 0, ',', ' ') }} F</div>
                <svg class="w-6 h-6 ml-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <p class="text-gray-600 text-sm">Commande moyenne</p>
        </div>
    </div>

    {{-- Dernières commandes --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Dernières commandes fournisseurs</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fournisseur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentOrders ?? [] as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $order->supplier->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($order->total_ttc, 0, ',', ' ') }} F</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $order->order_date->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $order->status === 'validated' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Aucune commande récente</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection