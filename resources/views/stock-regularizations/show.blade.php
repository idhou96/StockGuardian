{{-- resources/views/stock-regularizations/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Régularisation #' . $regularization->reference)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <nav class="flex mb-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <a href="{{ route('stock-regularizations.index') }}" class="ml-1 md:ml-2 text-sm font-medium text-gray-700 hover:text-blue-600">Régularisations</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 md:ml-2 text-sm font-medium text-gray-500">{{ $regularization->reference }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header avec statut -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start mb-6 space-y-4 lg:space-y-0">
        <div class="flex items-center space-x-4">
            <div class="bg-blue-100 rounded-full p-3">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <div class="flex items-center space-x-3">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $regularization->reference }}</h1>
                    @php
                    $statusConfig = [
                        'draft' => ['bg-gray-100', 'text-gray-800', 'Brouillon'],
                        'pending' => ['bg-yellow-100', 'text-yellow-800', 'En attente'],
                        'approved' => ['bg-blue-100', 'text-blue-800', 'Approuvée'],
                        'applied' => ['bg-green-100', 'text-green-800', 'Appliquée'],
                        'rejected' => ['bg-red-100', 'text-red-800', 'Rejetée']
                    ];
                    $config = $statusConfig[$regularization->status] ?? ['bg-gray-100', 'text-gray-800', ucfirst($regularization->status)];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $config[0] }} {{ $config[1] }}">
                        {{ $config[2] }}
                    </span>
                    @php
                    $priorityConfig = [
                        'low' => ['bg-gray-100', 'text-gray-800'],
                        'medium' => ['bg-blue-100', 'text-blue-800'],
                        'high' => ['bg-orange-100', 'text-orange-800'],
                        'urgent' => ['bg-red-100', 'text-red-800']
                    ];
                    $priorityStyles = $priorityConfig[$regularization->priority] ?? ['bg-gray-100', 'text-gray-800'];
                    @endphp
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $priorityStyles[0] }} {{ $priorityStyles[1] }}">
                        {{ ucfirst($regularization->priority) }}
                    </span>
                </div>
                <p class="text-gray-600 mt-1">
                    {{ $regularization->title }} - {{ $regularization->created_at->format('d/m/Y à H:i') }}
                </p>
            </div>
        </div>
        
        <div class="flex flex-wrap gap-3">
            @can('edit stock_regularizations')
            @if(in_array($regularization->status, ['draft', 'pending']))
            <a href="{{ route('stock-regularizations.edit', $regularization) }}" 
               class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Modifier</span>
            </a>
            @endif
            @endcan

            @can('approve stock_regularizations')
            @if($regularization->status === 'pending')
            <button onclick="approveRegularization({{ $regularization->id }})" 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Approuver</span>
            </button>
            @endif
            @endcan

            @can('apply stock_regularizations')
            @if($regularization->status === 'approved')
            <button onclick="applyRegularization({{ $regularization->id }})" 
                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Appliquer</span>
            </button>
            @endif
            @endcan

            <a href="{{ route('stock-regularizations.pdf', $regularization) }}" target="_blank"
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>PDF</span>
            </a>

            <a href="{{ route('stock-regularizations.index') }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                </svg>
                <span>Retour</span>
            </a>
        </div>
    </div>

    <!-- Alertes de statut -->
    @if($regularization->status === 'rejected')
    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700 font-medium">Régularisation rejetée</p>
                <p class="text-sm text-red-700 mt-1">
                    Rejetée le {{ $regularization->rejected_at->format('d/m/Y à H:i') }}
                    @if($regularization->rejection_reason)
                    <br>Raison: {{ $regularization->rejection_reason }}
                    @endif
                </p>
            </div>
        </div>
    </div>
    @endif

    @if($regularization->status === 'applied')
    <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700 font-medium">Régularisation appliquée</p>
                <p class="text-sm text-green-700 mt-1">
                    Appliquée le {{ $regularization->applied_at->format('d/m/Y à H:i') }}
                    - Les stocks ont été mis à jour automatiquement
                </p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informations générales -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Informations générales
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type de régularisation</label>
                        @php
                        $typeConfig = [
                            'inventory_adjustment' => 'Ajustement inventaire',
                            'loss' => 'Perte',
                            'damage' => 'Détérioration',
                            'expiry' => 'Péremption',
                            'theft' => 'Vol',
                            'correction' => 'Correction d\'erreur'
                        ];
                        @endphp
                        <p class="text-gray-900">{{ $typeConfig[$regularization->type] ?? ucfirst($regularization->type) }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Entrepôt concerné</label>
                        <p class="text-gray-900">{{ $regularization->warehouse->name }}</p>
                        <p class="text-sm text-gray-500">{{ $regularization->warehouse->location }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Priorité</label>
                        <p class="text-gray-900 capitalize">{{ $regularization->priority }}</p>
                    </div>

                    @if($regularization->planned_date)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date planifiée</label>
                        <p class="text-gray-900">{{ $regularization->planned_date->format('d/m/Y à H:i') }}</p>
                        @if($regularization->planned_date->isPast() && $regularization->status !== 'applied')
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1">
                            En retard
                        </span>
                        @endif
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Créée par</label>
                        <p class="text-gray-900">{{ $regularization->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $regularization->created_at->format('d/m/Y à H:i') }}</p>
                    </div>

                    @if($regularization->approved_by_user_id)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Approuvée par</label>
                        <p class="text-gray-900">{{ $regularization->approvedBy->name }}</p>
                        <p class="text-sm text-gray-500">{{ $regularization->approved_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    @endif
                </div>

                <!-- Description -->
                @if($regularization->description)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-gray-900">{{ $regularization->description }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Articles concernés -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Articles concernés ({{ $regularization->items->count() }})
                    </h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock théorique</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock physique</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Écart</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Impact valeur</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($regularization->items as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($item->product->image_url)
                                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" 
                                             class="h-10 w-10 rounded-md object-cover mr-3">
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $item->product->barcode }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $item->theoretical_quantity }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $item->physical_quantity }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold {{ $item->variance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $item->variance >= 0 ? '+' : '' }}{{ $item->variance }}
                                    </span>
                                    <div class="w-20 bg-gray-200 rounded-full h-1 mt-1">
                                        @php
                                        $maxVariance = $regularization->items->max('variance');
                                        $minVariance = abs($regularization->items->min('variance'));
                                        $maxAbsVariance = max($maxVariance, $minVariance);
                                        $percentage = $maxAbsVariance > 0 ? (abs($item->variance) / $maxAbsVariance) * 100 : 0;
                                        @endphp
                                        <div class="{{ $item->variance >= 0 ? 'bg-green-500' : 'bg-red-500' }} h-1 rounded-full" 
                                             style="width: {{ $percentage }}%"></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ number_format($item->unit_price, 2, ',', ' ') }} €</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold {{ $item->value_impact >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $item->value_impact >= 0 ? '+' : '' }}{{ number_format($item->value_impact, 2, ',', ' ') }} €
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Justifications -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                    Justifications et actions correctives
                </h2>
                
                @if($regularization->justification)
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Justification</label>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $regularization->justification }}</p>
                    </div>
                </div>
                @endif

                @if($regularization->corrective_actions)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Actions correctives</label>
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $regularization->corrective_actions }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Historique des actions -->
            @if($regularization->activities->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Historique des actions
                </h2>
                
                <div class="space-y-4">
                    @foreach($regularization->activities->sortByDesc('created_at') as $activity)
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            @php
                            $activityConfig = [
                                'created' => ['bg-blue-100', 'text-blue-600'],
                                'updated' => ['bg-yellow-100', 'text-yellow-600'],
                                'approved' => ['bg-green-100', 'text-green-600'],
                                'rejected' => ['bg-red-100', 'text-red-600'],
                                'applied' => ['bg-purple-100', 'text-purple-600']
                            ];
                            $activityStyles = $activityConfig[$activity->type] ?? ['bg-gray-100', 'text-gray-600'];
                            @endphp
                            <div class="w-8 h-8 rounded-full {{ $activityStyles[0] }} flex items-center justify-center">
                                <svg class="w-4 h-4 {{ $activityStyles[1] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    @if($activity->type === 'created')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    @elseif($activity->type === 'approved')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    @elseif($activity->type === 'rejected')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    @elseif($activity->type === 'applied')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    @endif
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">
                                {{ $activity->description }}
                            </p>
                            <p class="text-sm text-gray-500">
                                Par {{ $activity->user->name }} - {{ $activity->created_at->format('d/m/Y à H:i') }}
                            </p>
                            @if($activity->comment)
                            <p class="text-sm text-gray-600 mt-1 bg-gray-50 p-2 rounded">
                                {{ $activity->comment }}
                            </p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Colonne latérale -->
        <div class="space-y-6">
            <!-- Résumé de l'impact -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Résumé de l'impact
            </h2>
                
                <div class="space-y-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-2xl font-bold text-blue-600">{{ $regularization->items->count() }}</p>
                                <p class="text-sm text-blue-700">Articles concernés</p>
                            </div>
                            <div class="text-blue-500">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-red-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                @php $negativeVariance = $regularization->items->where('variance', '<', 0)->sum('variance'); @endphp
                                <p class="text-2xl font-bold text-red-600">{{ abs($negativeVariance) }}</p>
                                <p class="text-sm text-red-700">Écarts négatifs</p>
                            </div>
                            <div class="text-red-500">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                @php $positiveVariance = $regularization->items->where('variance', '>', 0)->sum('variance'); @endphp
                                <p class="text-2xl font-bold text-green-600">{{ $positiveVariance }}</p>
                                <p class="text-sm text-green-700">Écarts positifs</p>
                            </div>
                            <div class="text-green-500">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-purple-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                @php $totalValueImpact = $regularization->items->sum('value_impact'); @endphp
                                <p class="text-2xl font-bold {{ $totalValueImpact >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $totalValueImpact >= 0 ? '+' : '' }}{{ number_format($totalValueImpact, 2, ',', ' ') }} €
                                </p>
                                <p class="text-sm text-purple-700">Impact financier</p>
                            </div>
                            <div class="text-purple-500">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Entrepôt -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Entrepôt concerné
                </h2>
                
                <div class="text-center">
                    <h3 class="font-semibold text-gray-900">{{ $regularization->warehouse->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $regularization->warehouse->location }}</p>
                    @if($regularization->warehouse->manager)
                    <p class="text-sm text-gray-600 mt-2">
                        <span class="font-medium">Responsable:</span> {{ $regularization->warehouse->manager }}
                    </p>
                    @endif
                    
                    <a href="{{ route('warehouses.show', $regularization->warehouse) }}" 
                       class="inline-flex items-center px-3 py-2 mt-3 text-sm font-medium text-blue-600 bg-blue-100 rounded-lg hover:bg-blue-200 transition duration-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Voir l'entrepôt
                    </a>
                </div>
            </div>

            <!-- Timeline des statuts -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Suivi du processus
                </h2>
                
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-3 h-3 rounded-full bg-blue-500"></div>
                        <div class="text-sm">
                            <p class="font-medium text-gray-900">Régularisation créée</p>
                            <p class="text-gray-500">{{ $regularization->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    
                    @if($regularization->status !== 'draft')
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-3 h-3 rounded-full bg-yellow-500"></div>
                        <div class="text-sm">
                            <p class="font-medium text-gray-900">Soumise pour approbation</p>
                            <p class="text-gray-500">{{ $regularization->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($regularization->approved_at)
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-3 h-3 rounded-full bg-green-500"></div>
                        <div class="text-sm">
                            <p class="font-medium text-gray-900">Approuvée</p>
                            <p class="text-gray-500">{{ $regularization->approved_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($regularization->applied_at)
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-3 h-3 rounded-full bg-purple-500"></div>
                        <div class="text-sm">
                            <p class="font-medium text-gray-900">Appliquée au stock</p>
                            <p class="text-gray-500">{{ $regularization->applied_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($regularization->rejected_at)
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-3 h-3 rounded-full bg-red-500"></div>
                        <div class="text-sm">
                            <p class="font-medium text-gray-900">Rejetée</p>
                            <p class="text-gray-500">{{ $regularization->rejected_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Actions rapides
                </h2>
                
                <div class="space-y-3">
                    <a href="{{ route('stock-regularizations.index', ['warehouse_id' => $regularization->warehouse->id]) }}" 
                       class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 py-2 px-4 rounded-lg text-sm flex items-center transition duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Autres régularisations entrepôt
                    </a>
                    
                    <a href="{{ route('stock-movements.index', ['warehouse_id' => $regularization->warehouse->id]) }}" 
                       class="w-full bg-purple-50 hover:bg-purple-100 text-purple-700 py-2 px-4 rounded-lg text-sm flex items-center transition duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        Mouvements de stock
                    </a>
                    
                    <a href="{{ route('stock-regularizations.create') }}?warehouse_id={{ $regularization->warehouse->id }}&type={{ $regularization->type }}" 
                       class="w-full bg-green-50 hover:bg-green-100 text-green-700 py-2 px-4 rounded-lg text-sm flex items-center transition duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Nouvelle régularisation similaire
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals de confirmation -->
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

@push('scripts')
<script>
function approveRegularization(regularizationId) {
    document.getElementById('approveModal').classList.remove('hidden');
    
    document.getElementById('confirmApprove').onclick = function() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/stock-regularizations/${regularizationId}/approve`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    };
}

function applyRegularization(regularizationId) {
    document.getElementById('applyModal').classList.remove('hidden');
    
    document.getElementById('confirmApply').onclick = function() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/stock-regularizations/${regularizationId}/apply`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    };
}

// Fermer les modals
document.getElementById('cancelApprove').addEventListener('click', function() {
    document.getElementById('approveModal').classList.add('hidden');
});

document.getElementById('cancelApply').addEventListener('click', function() {
    document.getElementById('applyModal').classList.add('hidden');
});

// Fermer modals en cliquant en dehors
document.getElementById('approveModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});

document.getElementById('applyModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});
</script>
@endpush
@endsection