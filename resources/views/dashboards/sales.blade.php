
<?php
// ===============================================
// 🎯 DASHBOARD VENDEUR/COMMERCIAL
// resources/views/dashboard/sales.blade.php
?>
@extends('layouts.app')

@section('title', 'Dashboard Ventes')

@section('content')
<div class="space-y-6">
    {{-- Header Ventes --}}
    <div class="bg-gradient-to-r from-green-600 to-green-800 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Tableau de Bord Ventes</h1>
                <p class="text-green-100 mt-1">Maximisez vos performances commerciales</p>
            </div>
            <div class="bg-green-500 bg-opacity-30 rounded-full p-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Actions rapides Ventes --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('sales.create') }}" class="bg-blue-600 hover:bg-blue-700 rounded-lg shadow-lg p-6 text-white transition-all duration-200 transform hover:scale-105">
            <div class="flex items-center">
                <svg class="w-12 h-12 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold">Nouvelle Vente</h3>
                    <p class="text-blue-100 text-sm">Créer une nouvelle transaction</p>
                </div>
            </div>
        </a>

        <a href="{{ route('customers.index') }}" class="bg-purple-600 hover:bg-purple-700 rounded-lg shadow-lg p-6 text-white transition-all duration-200 transform hover:scale-105">
            <div class="flex items-center">
                <svg class="w-12 h-12 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-3a3.5 3.5 0 11-7 0 3.5 3.5 0 017 0z"/>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold">Clients</h3>
                    <p class="text-purple-100 text-sm">Gérer la clientèle</p>
                </div>
            </div>
        </a>

        <a href="{{ route('reports.sales') }}" class="bg-orange-600 hover:bg-orange-700 rounded-lg shadow-lg p-6 text-white transition-all duration-200 transform hover:scale-105">
            <div class="flex items-center">
                <svg class="w-12 h-12 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold">Rapports</h3>
                    <p class="text-orange-100 text-sm">Analyses des ventes</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Statistiques Vendeur --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($todayStats['sales'] ?? 0, 0, ',', ' ') }} F</div>
                <svg class="w-6 h-6 ml-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
            </div>
            <p class="text-gray-600 text-sm">Ventes aujourd'hui</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="text-2xl font-bold text-gray-900">{{ $todayStats['transactions'] ?? 0 }}</div>
                <svg class="w-6 h-6 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <p class="text-gray-600 text-sm">Transactions</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($todayStats['average_basket'] ?? 0, 0, ',', ' ') }} F</div>
                <svg class="w-6 h-6 ml-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17M17 13v4a2 2 0 01-2 2H9a2 2 0 01-2-2v-4m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
                </svg>
            </div>
            <p class="text-gray-600 text-sm">Panier moyen</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="text-2xl font-bold text-gray-900">{{ $objectives['progress'] ?? 0 }}%</div>
                <svg class="w-6 h-6 ml-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <p class="text-gray-600 text-sm">Objectif mensuel</p>
        </div>
    </div>

    {{-- Dernières ventes --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Dernières ventes</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentSales ?? [] as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $sale->customer_name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($sale->total_ttc, 0, ',', ' ') }} F</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $sale->sale_date->format('d/m/Y H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Validée
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">Aucune vente récente</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
