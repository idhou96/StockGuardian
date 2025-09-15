{{-- resources/views/stock-regularizations/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Régularisations de Stock')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Régularisations de Stock</h1>
            <p class="text-gray-600 mt-1">Gérez les ajustements et corrections d'inventaire</p>
        </div>
        
        @can('create stock_regularizations')
        <a href="{{ route('stock-regularizations.create') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            <span>Nouvelle Régularisation</span>
        </a>
        @endcan
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('stock-regularizations.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Statut -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                <select name="status" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tous les statuts</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approuvée</option>
                    <option value="applied" {{ request('status') == 'applied' ? 'selected' : '' }}>Appliquée</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejetée</option>
                </select>
            </div>

            <!-- Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="type" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tous les types</option>
                    <option value="inventory_adjustment" {{ request('type') == 'inventory_adjustment' ? 'selected' : '' }}>Ajustement inventaire</option>
                    <option value="loss" {{ request('type') == 'loss' ? 'selected' : '' }}>Perte</option>
                    <option value="damage" {{ request('type') == 'damage' ? 'selected' : '' }}>Détérioration</option>
                    <option value="expiry" {{ request('type') == 'expiry' ? 'selected' : '' }}>Péremption</option>
                    <option value="theft" {{ request('type') == 'theft' ? 'selected' : '' }}>Vol</option>
                    <option value="correction" {{ request('type') == 'correction' ? 'selected' : '' }}>Correction</option>
                </select>
            </div>

            <!-- Entrepôt -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Entrepôt</label>
                <select name="warehouse_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tous les entrepôts</option>
                    @foreach($warehouses as $warehouse)
                    <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                        {{ $warehouse->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Date début -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date début</label>
                <input type="date" name="date_start" value="{{ request('date_start') }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Boutons -->
            <div class="flex space-x-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex-1">
                    Filtrer
                </button>
                <a href="{{ route('stock-regularizations.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg text-center">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">En attente</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $stats['pending'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Appliquées</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $stats['applied'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="bg-red-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Pertes ce mois</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $stats['monthly_losses'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center">
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Impact financier</p>
                    <p class="text-xl font-semibold text-gray-900">{{ number_format($stats['total_value_impact'] ?? 0, 0, ',', ' ') }} €</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table des régularisations -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entrepôt</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Articles</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Impact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsable</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($regularizations as $regularization)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">{{ $regularization->reference }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ $regularization->created_at->format('d/m/Y H:i') }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                            $typeConfig = [
                                'inventory_adjustment' => ['bg-blue-100', 'text-blue-800', 'Ajustement inventaire'],
                                'loss' => ['bg-red-100', 'text-red-800', 'Perte'],
                                'damage' => ['bg-orange-100', 'text-orange-800', 'Détérioration'],
                                'expiry' => ['bg-yellow-100', 'text-yellow-800', 'Péremption'],
                                'theft' => ['bg-purple-100', 'text-purple-800', 'Vol'],
                                'correction' => ['bg-green-100', 'text-green-800', 'Correction']
                            ];
                            $typeStyles = $typeConfig[$regularization->type] ?? ['bg-gray-100', 'text-gray-800', ucfirst($regularization->type)];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeStyles[0] }} {{ $typeStyles[1] }}">
                                {{ $typeStyles[2] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ $regularization->warehouse->name }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ $regularization->items->count() }} article(s)</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php $totalImpact = $regularization->items->sum('value_impact'); @endphp
                            <span class="text-sm font-semibold {{ $totalImpact >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $totalImpact >= 0 ? '+' : '' }}{{ number_format($totalImpact, 2, ',', ' ') }} €
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                            $statusConfig = [
                                'draft' => ['bg-gray-100', 'text-gray-800', 'Brouillon'],
                                'pending' => ['bg-yellow-100', 'text-yellow-800', 'En attente'],
                                'approved' => ['bg-blue-100', 'text-blue-800', 'Approuvée'],
                                'applied' => ['bg-green-100', 'text-green-800', 'Appliquée'],
                                'rejected' => ['bg-red-100', 'text-red-800', 'Rejetée']
                            ];
                            $statusStyles = $statusConfig[$regularization->status] ?? ['bg-gray-100', 'text-gray-800', ucfirst($regularization->status)];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusStyles[0] }} {{ $statusStyles[1] }}">
                                {{ $statusStyles[2] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-900">{{ $regularization->user->name }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                @can('view stock_regularizations')
                                <a href="{{ route('stock-regularizations.show', $regularization) }}" 
                                   class="text-blue-600 hover:text-blue-900 transition duration-200" title="Voir les détails">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @endcan

                                @can('edit stock_regularizations')
                                @if(in_array($regularization->status, ['draft', 'pending']))
                                <a href="{{ route('stock-regularizations.edit', $regularization) }}" 
                                   class="text-yellow-600 hover:text-yellow-900 transition duration-200" title="Modifier">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @endif
                                @endcan

                                @can('approve stock_regularizations')
                                @if($regularization->status === 'pending')
                                <button onclick="approveRegularization({{ $regularization->id }})" 
                                        class="text-green-600 hover:text-green-900 transition duration-200" title="Approuver">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                                @endif
                                @endcan

                                @can('apply stock_regularizations')
                                @if($regularization->status === 'approved')
                                <button onclick="applyRegularization({{ $regularization->id }})" 
                                        class="text-purple-600 hover:text-purple-900 transition duration-200" title="Appliquer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                                @endif
                                @endcan

                                <!-- Dropdown pour plus d'actions -->
                                <div class="relative inline-block text-left" x-data="{ open: false }">
                                    <button @click="open = !open" class="text-gray-600 hover:text-gray-900 transition duration-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                        </svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" 
                                         class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                        <div class="py-1">
                                            <a href="{{ route('stock-regularizations.pdf', $regularization) }}" target="_blank"
                                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                Télécharger PDF
                                            </a>
                                            @if(in_array($regularization->status, ['pending', 'approved']) && auth()->user()->can('reject stock_regularizations'))
                                            <button onclick="rejectRegularization({{ $regularization->id }})"
                                                    class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                Rejeter
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-lg font-medium text-gray-900 mb-2">Aucune régularisation</p>
                            <p class="text-gray-500">Aucune régularisation ne correspond à vos critères de recherche.</p>
                            @can('create stock_regularizations')
                            <a href="{{ route('stock-regularizations.create') }}" 
                               class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Créer ma première régularisation
                            </a>
                            @endcan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($regularizations->hasPages())
    <div class="mt-6">
        {{ $regularizations->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- Modal de confirmation d'approbation -->
<div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <svg class="mx-auto mb-4 w-14 h-14 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Approuver la régularisation</h3>
            <p class="text-sm text-gray-500 mb-4">
                Êtes-vous sûr de vouloir approuver cette régularisation ? 
                Elle pourra ensuite être appliquée au stock.
            </p>
            <div class="flex justify-center space-x-4">
                <button id="confirmApprove" type="button" 
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Oui, approuver
                </button>
                <button id="cancelApprove" type="button" 
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation d'application -->
<div id="applyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <svg class="mx-auto mb-4 w-14 h-14 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Appliquer la régularisation</h3>
            <p class="text-sm text-gray-500 mb-4">
                Êtes-vous sûr de vouloir appliquer cette régularisation au stock ? 
                Cette action modifiera définitivement les quantités en stock.
            </p>
            <div class="flex justify-center space-x-4">
                <button id="confirmApply" type="button" 
                        class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                    Oui, appliquer
                </button>
                <button id="cancelApply" type="button" 
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de rejet -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <svg class="mx-auto mb-4 w-14 h-14 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Rejeter la régularisation</h3>
            <div class="mb-4">
                <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">Raison du rejet</label>
                <textarea id="rejection_reason" rows="3" 
                          placeholder="Expliquez pourquoi cette régularisation est rejetée..."
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
            </div>
            <div class="flex justify-center space-x-4">
                <button id="confirmReject" type="button" 
                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Confirmer le rejet
                </button>
                <button id="cancelReject" type="button" 
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let regularizationToApprove = null;
let regularizationToApply = null;
let regularizationToReject = null;

function approveRegularization(regularizationId) {
    regularizationToApprove = regularizationId;
    document.getElementById('approveModal').classList.remove('hidden');
}

function applyRegularization(regularizationId) {
    regularizationToApply = regularizationId;
    document.getElementById('applyModal').classList.remove('hidden');
}

function rejectRegularization(regularizationId) {
    regularizationToReject = regularizationId;
    document.getElementById('rejectModal').classList.remove('hidden');
}

// Confirmation d'approbation
document.getElementById('confirmApprove').addEventListener('click', function() {
    if (regularizationToApprove) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/stock-regularizations/${regularizationToApprove}/approve`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
});

// Confirmation d'application
document.getElementById('confirmApply').addEventListener('click', function() {
    if (regularizationToApply) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/stock-regularizations/${regularizationToApply}/apply`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
});

// Confirmation de rejet
document.getElementById('confirmReject').addEventListener('click', function() {
    if (regularizationToReject) {
        const reason = document.getElementById('rejection_reason').value;
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/stock-regularizations/${regularizationToReject}/reject`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const reasonField = document.createElement('input');
        reasonField.type = 'hidden';
        reasonField.name = 'reason';
        reasonField.value = reason;
        
        form.appendChild(csrfToken);
        form.appendChild(reasonField);
        document.body.appendChild(form);
        form.submit();
    }
});

// Annulations
document.getElementById('cancelApprove').addEventListener('click', function() {
    document.getElementById('approveModal').classList.add('hidden');
    regularizationToApprove = null;
});

document.getElementById('cancelApply').addEventListener('click', function() {
    document.getElementById('applyModal').classList.add('hidden');
    regularizationToApply = null;
});

document.getElementById('cancelReject').addEventListener('click', function() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejection_reason').value = '';
    regularizationToReject = null;
});

// Fermer modals en cliquant en dehors
document.getElementById('approveModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
        regularizationToApprove = null;
    }
});

document.getElementById('applyModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
        regularizationToApply = null;
    }
});

document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
        document.getElementById('rejection_reason').value = '';
        regularizationToReject = null;
    }
});
</script>
@endpush
@endsection