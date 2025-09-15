<?php
// 🎯 VUES FAMILLES DE PRODUITS & PRINCIPES ACTIFS

// ===================================
// 1. VUE INDEX FAMILLES DE PRODUITS
// ===================================
// File: resources/views/product-families/index.blade.php
?>

@extends('layouts.app')

@section('title', 'Familles de Produits')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Familles de Produits</h1>
                    <p class="text-sm text-gray-600 mt-1">Organisez vos produits par catégories</p>
                </div>
                @can('create product families')
                <a href="{{ route('product-families.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Nouvelle famille</span>
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="px-6 py-6">
        <!-- Filtres et recherche -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('product-families.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Nom, code, description..."
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tous</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trier par</label>
                    <select name="sort" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nom</option>
                        <option value="products_count" {{ request('sort') == 'products_count' ? 'selected' : '' }}>Nb produits</option>
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date création</option>
                    </select>
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                        Filtrer
                    </button>
                    <a href="{{ route('product-families.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg transition-colors">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Grille des familles -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($families as $family)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                <!-- Image ou icône -->
                <div class="h-32 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                    @if($family->image)
                    <img src="{{ asset('storage/' . $family->image) }}" 
                         alt="{{ $family->name }}" 
                         class="w-full h-full object-cover">
                    @else
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    @endif
                </div>

                <div class="p-4">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ $family->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $family->code }}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($family->is_active)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Actif
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                Inactif
                            </span>
                            @endif
                        </div>
                    </div>

                    @if($family->description)
                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $family->description }}</p>
                    @endif

                    <!-- Statistiques -->
                    <div class="border-t border-gray-200 pt-3 mb-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Produits :</span>
                            <span class="font-medium text-gray-900">{{ $family->products_count }}</span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span class="text-gray-500">Stock total :</span>
                            <span class="font-medium text-gray-900">{{ number_format($family->total_stock ?? 0) }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between">
                        <a href="{{ route('products.index', ['family' => $family->id]) }}" 
                           class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                            Voir produits
                        </a>
                        <div class="flex space-x-2">
                            @can('view product families')
                            <a href="{{ route('product-families.show', $family) }}" 
                               class="text-gray-600 hover:text-gray-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            @endcan
                            @can('edit product families')
                            <a href="{{ route('product-families.edit', $family) }}" 
                               class="text-blue-600 hover:text-blue-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            @endcan
                            @can('delete product families')
                            <button onclick="confirmDelete({{ $family->id }})" 
                                    class="text-red-600 hover:text-red-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune famille trouvée</h3>
                <p class="mt-1 text-sm text-gray-500">Commencez par créer une nouvelle famille de produits.</p>
                @can('create product families')
                <div class="mt-6">
                    <a href="{{ route('product-families.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Créer une famille
                    </a>
                </div>
                @endcan
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($families->hasPages())
        <div class="mt-6">
            {{ $families->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
