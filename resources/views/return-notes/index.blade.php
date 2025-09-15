<?php
// ===============================================
// VUES BONS DE RETOUR - COMPLET
// ===============================================

// 🎯 VUE INDEX DES BONS DE RETOUR
// resources/views/return-notes/index.blade.php
?>

@extends('layouts.app')

@section('title', 'Bons de Retour')

@section('content')
<div class="space-y-6">
    {{-- En-tête avec actions --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Bons de Retour</h1>
                <p class="text-sm text-gray-600 mt-1">
                    Gérez les retours de produits clients et fournisseurs
                </p>
            </div>
            
            @can('return-note.create')
            <div class="flex gap-3">
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" 
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Nouveau Retour
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <div x-show="open" @click.away="open = false" 
                         class="absolute right-0 mt-2 w-56 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                        <div class="py-1">
                            <a href="{{ route('return-notes.create', ['type' => 'customer']) }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Retour Client
                            </a>
                            <a href="{{ route('return-notes.create', ['type' => 'supplier']) }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Retour Fournisseur
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
        </div>
    </div>

    {{-- Filtres et recherche --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="GET" action="{{ route('return-notes.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            {{-- Recherche --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Référence, client/fournisseur..."
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
            </div>

            {{-- Type --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type de Retour</label>
                <select name="type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">Tous les types</option>
                    <option value="customer" {{ request('type') == 'customer' ? 'selected' : '' }}>Retour Client</option>
                    <option value="supplier" {{ request('type') == 'supplier' ? 'selected' : '' }}>Retour Fournisseur</option>
                </select>
            </div>

            {{-- Raison --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Raison</label>
                <select name="reason" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">Toutes les raisons</option>
                    <option value="defective" {{ request('reason') == 'defective' ? 'selected' : '' }}>Produit défectueux</option>
                    <option value="expired" {{ request('reason') == 'expired' ? 'selected' : '' }}>Expiré</option>
                    <option value="damaged" {{ request('reason') == 'damaged' ? 'selected' : '' }}>Endommagé</option>
                    <option value="wrong_product" {{ request('reason') == 'wrong_product' ? 'selected' : '' }}>Mauvais produit</option>
                    <option value="overstocked" {{ request('reason') == 'overstocked' ? 'selected' : '' }}>Surstocké</option>
                    <option value="other" {{ request('reason') == 'other' ? 'selected' : '' }}>Autre</option>
                </select>
            </div>

            {{-- Statut --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">Tous les statuts</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approuvé</option>
                    <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>Traité</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejeté</option>
                </select>
            </div>

            {{-- Actions --}}
            <div class="flex gap-2 items-end">
                <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                    Filtrer
                </button>
                <a href="{{ route('return-notes.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Statistiques --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Retours</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalReturnNotes ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Retours Clients</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $customerReturns ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Retours Fournisseurs</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $supplierReturns ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Valeur Totale</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalReturnValue ?? 0, 0, ',', ' ') }} F</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau des bons de retour --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Référence
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Type & Tiers
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Raison
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Articles
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Montant
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Statut
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($returnNotes ?? [] as $returnNote)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-lg bg-gradient-to-br 
                                    @if($returnNote->type == 'customer') from-blue-500 to-purple-600 
                                    @else from-green-500 to-teal-600 
                                    @endif 
                                    flex items-center justify-center text-white font-bold text-xs">
                                    @if($returnNote->type == 'customer') RC @else RF @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $returnNote->reference }}</div>
                                    <div class="text-sm text-gray-500">{{ $returnNote->internal_reference ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mb-1
                                    @if($returnNote->type == 'customer') bg-blue-100 text-blue-800 
                                    @else bg-green-100 text-green-800 
                                    @endif">
                                    @if($returnNote->type == 'customer') Retour Client @else Retour Fournisseur @endif
                                </span>
                                <div class="text-sm font-medium text-gray-900">
                                    @if($returnNote->type == 'customer')
                                        {{ $returnNote->customer->name ?? 'Client N/A' }}
                                    @else
                                        {{ $returnNote->supplier->name ?? 'Fournisseur N/A' }}
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($returnNote->reason == 'defective') bg-red-100 text-red-800
                                @elseif($returnNote->reason == 'expired') bg-orange-100 text-orange-800
                                @elseif($returnNote->reason == 'damaged') bg-yellow-100 text-yellow-800
                                @elseif($returnNote->reason == 'wrong_product') bg-purple-100 text-purple-800
                                @elseif($returnNote->reason == 'overstocked') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @if($returnNote->reason == 'defective') Défectueux
                                @elseif($returnNote->reason == 'expired') Expiré
                                @elseif($returnNote->reason == 'damaged') Endommagé
                                @elseif($returnNote->reason == 'wrong_product') Mauvais produit
                                @elseif($returnNote->reason == 'overstocked') Surstocké
                                @else {{ $returnNote->reason_label ?? 'Autre' }}
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-900">{{ $returnNote->total_items ?? 0 }}</span>
                                @if($returnNote->total_items > 0)
                                    <span class="ml-2 text-xs text-gray-500">articles</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ number_format($returnNote->total_amount ?? 0, 0, ',', ' ') }} F
                            </div>
                            @if($returnNote->refund_amount > 0)
                                <div class="text-sm text-green-600">Remb: {{ number_format($returnNote->refund_amount, 0, ',', ' ') }} F</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $returnNote->return_date ? $returnNote->return_date->format('d/m/Y') : 'N/A' }}
                            @if($returnNote->created_at)
                                <div class="text-xs text-gray-400">{{ $returnNote->created_at->diffForHumans() }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($returnNote->status == 'draft')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <svg class="w-1.5 h-1.5 mr-1.5" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3"/>
                                    </svg>
                                    Brouillon
                                </span>
                            @elseif($returnNote->status == 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-1.5 h-1.5 mr-1.5" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3"/>
                                    </svg>
                                    En attente
                                </span>
                            @elseif($returnNote->status == 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <svg class="w-1.5 h-1.5 mr-1.5" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3"/>
                                    </svg>
                                    Approuvé
                                </span>
                            @elseif($returnNote->status == 'processed')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-1.5 h-1.5 mr-1.5" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3"/>
                                    </svg>
                                    Traité
                                </span>
                            @elseif($returnNote->status == 'rejected')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <svg class="w-1.5 h-1.5 mr-1.5" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3"/>
                                    </svg>
                                    Rejeté
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center space-x-2">
                                {{-- Voir --}}
                                @can('return-note.show')
                                <a href="{{ route('return-notes.show', $returnNote) }}" 
                                   class="text-blue-600 hover:text-blue-900 p-1 rounded transition-colors duration-200"
                                   title="Voir les détails">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @endcan

                                {{-- Modifier --}}
                                @can('return-note.edit')
                                @if(in_array($returnNote->status, ['draft', 'pending']))
                                <a href="{{ route('return-notes.edit', $returnNote) }}" 
                                   class="text-amber-600 hover:text-amber-900 p-1 rounded transition-colors duration-200"
                                   title="Modifier">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @endif
                                @endcan

                                {{-- Approuver --}}
                                @can('return-note.approve')
                                @if($returnNote->status == 'pending')
                                <form method="POST" action="{{ route('return-notes.approve', $returnNote) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="text-green-600 hover:text-green-900 p-1 rounded transition-colors duration-200"
                                            title="Approuver"
                                            onclick="return confirm('Voulez-vous approuver ce retour ?')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                                @endcan

                                {{-- Traiter --}}
                                @can('return-note.process')
                                @if($returnNote->status == 'approved')
                                <a href="{{ route('return-notes.process', $returnNote) }}" 
                                   class="text-purple-600 hover:text-purple-900 p-1 rounded transition-colors duration-200"
                                   title="Traiter le retour">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </a>
                                @endif
                                @endcan

                                {{-- Supprimer --}}
                                @can('return-note.delete')
                                @if($returnNote->status == 'draft')
                                <form method="POST" action="{{ route('return-notes.destroy', $returnNote) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900 p-1 rounded transition-colors duration-200"
                                            title="Supprimer"
                                            onclick="return confirm('Voulez-vous vraiment supprimer ce bon de retour ?')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3m9 14V5a2 2 0 00-2-2H6a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"/>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">Aucun bon de retour trouvé</h3>
                                <p class="text-gray-500 mb-4">Les bons de retour apparaîtront ici.</p>
                                @can('return-note.create')
                                <div class="flex gap-3">
                                    <a href="{{ route('return-notes.create', ['type' => 'customer']) }}" 
                                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                                        Retour Client
                                    </a>
                                    <a href="{{ route('return-notes.create', ['type' => 'supplier']) }}" 
                                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                                        Retour Fournisseur
                                    </a>
                                </div>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(isset($returnNotes) && method_exists($returnNotes, 'links'))
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $returnNotes->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
