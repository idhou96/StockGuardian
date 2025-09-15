
{{-- resources/views/inventories/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Inventaire - ' . $inventory->reference)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <x-breadcrumb :items="[
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Inventaires', 'url' => route('inventories.index')],
                ['label' => $inventory->reference, 'url' => null]
            ]" />
            <div class="mt-4 flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $inventory->title }}</h1>
                    <p class="mt-1 text-sm text-gray-600">{{ $inventory->reference }}</p>
                </div>
                <div class="flex space-x-3">
                    @can('update', $inventory)
                        @if($inventory->status === 'draft')
                            <a href="{{ route('inventories.edit', $inventory) }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                Modifier
                            </a>
                        @endif
                    @endcan
                    
                    @can('start', $inventory)
                        @if($inventory->status === 'draft')
                            <button onclick="startInventory({{ $inventory->id }})" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                                Démarrer
                            </button>
                        @endif
                    @endcan

                    @can('complete', $inventory)
                        @if($inventory->status === 'in_progress')
                            <button onclick="completeInventory({{ $inventory->id }})" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                Terminer
                            </button>
                        @endif
                    @endcan

                    @can('validate', $inventory)
                        @if($inventory->status === 'completed')
                            <button onclick="validateInventory({{ $inventory->id }})" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700">
                                Valider
                            </button>
                        @endif
                    @endcan

                    <div class="relative inline-block text-left">
                        <button type="button" onclick="toggleDropdown()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Actions
                            <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div id="actionDropdown" class="hidden absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                            <div class="py-1">
                                <a href="{{ route('inventories.export', $inventory) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Exporter Excel</a>
                                <a href="{{ route('inventories.report', $inventory) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Rapport PDF</a>
                                @if($inventory->status === 'in_progress')
                                    <a href="{{ route('inventories.template', $inventory) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Télécharger template</a>
                                    <a href="#" onclick="showImportModal()" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Importer comptage</a>
                                @endif
                                @if(in_array($inventory->status, ['completed', 'validated']))
                                    <a href="{{ route('inventories.discrepancies', $inventory) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Voir les écarts</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Colonne principale --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Informations générales --}}
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Informations générales</h3>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Référence</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $inventory->reference }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Type</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($inventory->type) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Dépôt</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $inventory->warehouse->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date d'inventaire</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $inventory->inventory_date->format('d/m/Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Responsable</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $inventory->responsibleUser->name ?? 'Non assigné' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tolérance</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $inventory->tolerance_percentage }}%</dd>
                            </div>
                            @if($inventory->description)
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $inventory->description }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>
                </div>

                {{-- Progression de l'inventaire --}}
                @if($inventory->status !== 'draft')
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Progression</h3>
                    </div>
                    <div class="px-6 py-4">
                        @php
                            $totalItems = $inventory->details->count();
                            $countedItems = $inventory->details->whereNotNull('physical_quantity')->count();
                            $progressPercentage = $totalItems > 0 ? ($countedItems / $totalItems) * 100 : 0;
                        @endphp
                        
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-700 mb-2">
                                <span>Articles comptés</span>
                                <span>{{ $countedItems }} / {{ $totalItems }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" 
                                     style="width: {{ $progressPercentage }}%"></div>
                            </div>
                            <div class="text-right text-sm text-gray-500 mt-1">{{ number_format($progressPercentage, 1) }}%</div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div>
                                <div class="text-2xl font-bold text-gray-900">{{ $totalItems }}</div>
                                <div class="text-sm text-gray-500">Articles total</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-indigo-600">{{ $countedItems }}</div>
                                <div class="text-sm text-gray-500">Comptés</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-gray-400">{{ $totalItems - $countedItems }}</div>
                                <div class="text-sm text-gray-500">Restants</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Résultats et écarts --}}
                @if(in_array($inventory->status, ['completed', 'validated']))
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Résultats et écarts</h3>
                        <a href="{{ route('inventories.discrepancies', $inventory) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                            Voir tous les écarts
                        </a>
                    </div>
                    <div class="px-6 py-4">
                        @php
                            $discrepancies = $inventory->details->where('has_discrepancy', true);
                            $positiveDiscrepancies = $discrepancies->where('discrepancy_quantity', '>', 0);
                            $negativeDiscrepancies = $discrepancies->where('discrepancy_quantity', '<', 0);
                            $totalDiscrepancyValue = $discrepancies->sum(function($detail) {
                                return abs($detail->discrepancy_quantity * $detail->product->purchase_price);
                            });
                        @endphp
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center mb-6">
                            <div>
                                <div class="text-2xl font-bold text-red-600">{{ $discrepancies->count() }}</div>
                                <div class="text-sm text-gray-500">Écarts trouvés</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-green-600">{{ $positiveDiscrepancies->count() }}</div>
                                <div class="text-sm text-gray-500">Excédents</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-orange-600">{{ $negativeDiscrepancies->count() }}</div>
                                <div class="text-sm text-gray-500">Manquants</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-purple-600">{{ number_format($totalDiscrepancyValue, 0, ',', ' ') }} F</div>
                                <div class="text-sm text-gray-500">Valeur écarts</div>
                            </div>
                        </div>

                        {{-- Top 5 des écarts --}}
                        @if($discrepancies->count() > 0)
                        <div>
                            <h4 class="font-medium text-gray-900 mb-3">Principaux écarts</h4>
                            <div class="space-y-2">
                                @foreach($discrepancies->sortByDesc(function($item) { return abs($item->discrepancy_quantity * $item->product->purchase_price); })->take(5) as $discrepancy)
                                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $discrepancy->product->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $discrepancy->product->code }}</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="font-medium {{ $discrepancy->discrepancy_quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $discrepancy->discrepancy_quantity > 0 ? '+' : '' }}{{ $discrepancy->discrepancy_quantity }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ number_format(abs($discrepancy->discrepancy_quantity * $discrepancy->product->purchase_price), 0, ',', ' ') }} F
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Liste des articles --}}
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Articles de l'inventaire</h3>
                        <div class="flex items-center space-x-4">
                            <select id="statusFilter" onchange="filterItems()" class="text-sm border-gray-300 rounded-md">
                                <option value="">Tous les statuts</option>
                                <option value="pending">En attente</option>
                                <option value="counted">Comptés</option>
                                <option value="discrepancy">Avec écart</option>
                            </select>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock théorique</th>
                                    @if($inventory->status !== 'draft')
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock physique</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Écart</th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                    @if($inventory->status === 'in_progress')
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($inventory->details as $detail)
                                    <tr class="item-row" data-status="{{ $detail->physical_quantity !== null ? ($detail->has_discrepancy ? 'discrepancy' : 'counted') : 'pending' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $detail->product->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $detail->product->code }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-medium text-gray-900">{{ number_format($detail->theoretical_quantity) }}</span>
                                            <span class="text-xs text-gray-500 ml-1">{{ $detail->product->unit }}</span>
                                        </td>
                                        @if($inventory->status !== 'draft')
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($detail->physical_quantity !== null)
                                                    <span class="text-sm font-medium text-gray-900">{{ number_format($detail->physical_quantity) }}</span>
                                                    <span class="text-xs text-gray-500 ml-1">{{ $detail->product->unit }}</span>
                                                @else
                                                    <span class="text-sm text-gray-400">Non compté</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($detail->physical_quantity !== null)
                                                    @if($detail->has_discrepancy)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $detail->discrepancy_quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                            {{ $detail->discrepancy_quantity > 0 ? '+' : '' }}{{ $detail->discrepancy_quantity }}
                                                        </span>
                                                    @else
                                                        <span class="text-sm text-green-600 font-medium">✓ OK</span>
                                                    @endif
                                                @else
                                                    <span class="text-sm text-gray-400">-</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($detail->physical_quantity !== null)
                                                @if($detail->has_discrepancy)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        Écart détecté
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Compté
                                                    </span>
                                                @endif
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    En attente
                                                </span>
                                            @endif
                                        </td>
                                        @if($inventory->status === 'in_progress')
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button onclick="showCountModal({{ $detail->id }}, '{{ $detail->product->name }}', {{ $detail->theoretical_quantity }}, {{ $detail->physical_quantity }})" 
                                                        class="text-indigo-600 hover:text-indigo-900">
                                                    {{ $detail->physical_quantity !== null ? 'Modifier' : 'Compter' }}
                                                </button>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ $inventory->status === 'draft' ? '3' : ($inventory->status === 'in_progress' ? '6' : '5') }}" class="px-6 py-4 text-center text-gray-500">
                                            Aucun article dans cet inventaire
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Colonne latérale --}}
            <div class="space-y-6">
                {{-- Statut et informations rapides --}}
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Statut</h3>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">État actuel</span>
                            @php
                                $statusColors = [
                                    'draft' => 'gray',
                                    'in_progress' => 'blue',
                                    'completed' => 'green',
                                    'validated' => 'purple',
                                    'cancelled' => 'red'
                                ];
                                $statusLabels = [
                                    'draft' => 'Brouillon',
                                    'in_progress' => 'En cours',
                                    'completed' => 'Terminé',
                                    'validated' => 'Validé',
                                    'cancelled' => 'Annulé'
                                ];
                                $color = $statusColors[$inventory->status] ?? 'gray';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800">
                                {{ $statusLabels[$inventory->status] ?? $inventory->status }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Créé le</span>
                            <span class="text-sm text-gray-900">{{ $inventory->created_at->format('d/m/Y H:i') }}</span>
                        </div>

                        @if($inventory->started_at)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Démarré le</span>
                            <span class="text-sm text-gray-900">{{ $inventory->started_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @endif

                        @if($inventory->completed_at)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Terminé le</span>
                            <span class="text-sm text-gray-900">{{ $inventory->completed_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @endif

                        @if($inventory->validated_at)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Validé le</span>
                            <span class="text-sm text-gray-900">{{ $inventory->validated_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @endif

                        @if($inventory->validated_by_user_id)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Validé par</span>
                            <span class="text-sm text-gray-900">{{ $inventory->validatedByUser->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Historique des actions --}}
                @if($inventory->activityLogs->count() > 0)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Historique</h3>
                    </div>
                    <div class="px-6 py-4">
                        <div class="flow-root">
                            <ul class="-mb-8">
                                @foreach($inventory->activityLogs->take(10) as $log)
                                    <li>
                                        <div class="relative pb-8">
                                            @if(!$loop->last)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white">
                                                        <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-sm text-gray-500">{{ $log->action }}</p>
                                                        <p class="text-xs text-gray-400">{{ $log->user->name }}</p>
                                                    </div>
                                                    <div class="text-right text-xs text-gray-400 whitespace-nowrap">
                                                        {{ $log->created_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modals --}}
@include('inventories.partials.count-modal')
@include('inventories.partials.import-modal')

@push('scripts')
<script>
function toggleDropdown() {
    const dropdown = document.getElementById('actionDropdown');
    dropdown.classList.toggle('hidden');
}

function startInventory(inventoryId) {
    if (confirm('Êtes-vous sûr de vouloir démarrer cet inventaire ?')) {
        fetch(`/inventories/${inventoryId}/start`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors du démarrage de l\'inventaire');
            }
        });
    }
}

function completeInventory(inventoryId) {
    if (confirm('Êtes-vous sûr de vouloir terminer cet inventaire ?')) {
        fetch(`/inventories/${inventoryId}/complete`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de la finalisation de l\'inventaire');
            }
        });
    }
}

function validateInventory(inventoryId) {
    if (confirm('Êtes-vous sûr de vouloir valider cet inventaire ? Les ajustements de stock seront appliqués automatiquement.')) {
        fetch(`/inventories/${inventoryId}/validate`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de la validation de l\'inventaire');
            }
        });
    }
}

function filterItems() {
    const filter = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('.item-row');
    
    rows.forEach(row => {
        if (filter === '' || row.dataset.status === filter) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function showCountModal(detailId, productName, theoreticalQty, physicalQty) {
    document.getElementById('countDetailId').value = detailId;
    document.getElementById('countProductName').textContent = productName;
    document.getElementById('countTheoreticalQty').textContent = theoreticalQty;
    document.getElementById('countPhysicalQty').value = physicalQty || '';
    document.getElementById('countModal').classList.remove('hidden');
}

function closeCountModal() {
    document.getElementById('countModal').classList.add('hidden');
}

function showImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
}

function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
}

// Fermer les dropdowns en cliquant à l'extérieur
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('actionDropdown');
    const button = event.target.closest('button');
    
    if (!button || !button.onclick || button.onclick.toString().indexOf('toggleDropdown') === -1) {
        dropdown.classList.add('hidden');
    }
});
</script>
@endpush
@endsection