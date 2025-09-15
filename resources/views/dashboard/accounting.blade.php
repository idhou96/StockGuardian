
<?php
// ===============================================
// 🎯 DASHBOARD COMPTABILITÉ
// resources/views/dashboard/accounting.blade.php
?>
@extends('layouts.app')

@section('title', 'Dashboard Comptabilité')

@section('content')
<div class="space-y-6">
    {{-- Header Comptabilité --}}
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Tableau de Bord Comptabilité</h1>
                <p class="text-indigo-100 mt-1">Suivez les finances et la rentabilité</p>
            </div>
            <div class="bg-indigo-500 bg-opacity-30 rounded-full p-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Indicateurs financiers --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($financialStats['revenue_month'] ?? 0, 0, ',', ' ') }} F</div>
                <svg class="w-6 h-6 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
            </div>
            <p class="text-gray-600 text-sm">Chiffre d'affaires mensuel</p>
            <div class="mt-1">
                <span class="text-xs text-green-600">+{{ $financialStats['revenue_growth'] ?? 0 }}% vs mois dernier</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($financialStats['profit_month'] ?? 0, 0, ',', ' ') }} F</div>
                <svg class="w-6 h-6 ml-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
            <p class="text-gray-600 text-sm">Bénéfice mensuel</p>
            <div class="mt-1">
                <span class="text-xs text-blue-600">Marge: {{ $financialStats['profit_margin'] ?? 0 }}%</span>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($financialStats['pending_payments'] ?? 0, 0, ',', ' ') }} F</div>
                <svg class="w-6 h-6 ml-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-gray-600 text-sm">Paiements en attente</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($financialStats['overdue_amount'] ?? 0, 0, ',', ' ') }} F</div>
                <svg class="w-6 h-6 ml-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-gray-600 text-sm">Créances en retard</p>
        </div>
    </div>

    {{-- Actions rapides Comptabilité --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="{{ route('reports.financial') }}" class="bg-blue-600 hover:bg-blue-700 rounded-lg shadow-lg p-6 text-white transition-all duration-200 transform hover:scale-105">
            <div class="flex items-center">
                <svg class="w-12 h-12 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold">Rapports Financiers</h3>
                    <p class="text-blue-100 text-sm">Bilan, compte de résultat</p>
                </div>
            </div>
        </a>

        <a href="{{ route('reports.profit-loss') }}" class="bg-green-600 hover:bg-green-700 rounded-lg shadow-lg p-6 text-white transition-all duration-200 transform hover:scale-105">
            <div class="flex items-center">
                <svg class="w-12 h-12 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold">Rentabilité</h3>
                    <p class="text-green-100 text-sm">Analyse des profits</p>
                </div>
            </div>
        </a>

        <a href="{{ route('reports.cash-flow') }}" class="bg-purple-600 hover:bg-purple-700 rounded-lg shadow-lg p-6 text-white transition-all duration-200 transform hover:scale-105">
            <div class="flex items-center">
                <svg class="w-12 h-12 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                </svg>
                <div>
                    <h3 class="text-lg font-semibold">Trésorerie</h3>
                    <p class="text-purple-100 text-sm">Flux de trésorerie</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
