<?php
// 🎯 VUES ESSENTIELLES RESTANTES

// ===================================
// 1. VUE DÉTAILLÉE BON DE RETOUR
// ===================================
// File: resources/views/return-notes/show.blade.php
?>

@extends('layouts.app')

@section('title', 'Bon de Retour - ' . $returnNote->reference)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Bon de Retour {{ $returnNote->reference }}</h1>
                    <p class="text-sm text-gray-600 mt-1">
                        Créé le {{ $returnNote->created_at->format('d/m/Y à H:i') }} par {{ $returnNote->user->first_name }} {{ $returnNote->user->last_name }}
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('return-notes.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span>Retour liste</span>
                    </a>
                    @can('edit return notes')
                    @if(in_array($returnNote->status, ['draft', 'pending']))
                    <a href="{{ route('return-notes.edit', $returnNote) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Modifier</span>
                    </a>
                    @endif
                    @endcan
                    @can('print return notes')
                    <a href="{{ route('return-notes.print', $returnNote) }}" 
                       class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Imprimer</span>
                    </a>
                    @endcan
                    @can('approve return notes')
                    @if($returnNote->status === 'pending')
                    <button onclick="approveReturn()" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Approuver</span>
                    </button>
                    @endif
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-6">
        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Informations générales -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations générales</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Référence :</span>
                                <div class="text-sm text-gray-900">{{ $returnNote->reference }}</div>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Date de retour :</span>
                                <div class="text-sm text-gray-900">{{ $returnNote->return_date->format('d/m/Y') }}</div>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Type :</span>
                                <div class="text-sm">
                                    @switch($returnNote->type)
                                        @case('client_return')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Retour client
                                            </span>
                                            @break
                                        @case('supplier_return')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                Retour fournisseur
                                            </span>
                                            @break
                                        @case('damaged_goods')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Marchandise endommagée
                                            </span>
                                            @break
                                        @case('expired_goods')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                Produits expirés
                                            </span>
                                            @break
                                    @endswitch
                                </div>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Statut :</span>
                                <div class="text-sm">
                                    @switch($returnNote->status)
                                        @case('draft')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Brouillon
                                            </span>
                                            @break
                                        @case('pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                En attente
                                            </span>
                                            @break
                                        @case('approved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Approuvé
                                            </span>
                                            @break
                                        @case('processed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Traité
                                            </span>
                                            @break
                                        @case('cancelled')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Annulé
                                            </span>
                                            @break
                                    @endswitch
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Partenaire</h3>
                        <div class="space-y-3">
                            @if($returnNote->client)
                            <div>
                                <span class="text-sm font-medium text-gray-500">Client :</span>
                                <div class="text-sm text-gray-900">
                                    {{ $returnNote->client->company_name ?: $returnNote->client->first_name . ' ' . $returnNote->client->last_name }}
                                </div>
                                @if($returnNote->client->email)
                                <div class="text-xs text-gray-500">{{ $returnNote->client->email }}</div>
                                @endif
                                @if($returnNote->client->phone)
                                <div class="text-xs text-gray-500">{{ $returnNote->client->phone }}</div>
                                @endif
                            </div>
                            @elseif($returnNote->supplier)
                            <div>
                                <span class="text-sm font-medium text-gray-500">Fournisseur :</span>
                                <div class="text-sm text-gray-900">{{ $returnNote->supplier->company_name }}</div>
                                @if($returnNote->supplier->email)
                                <div class="text-xs text-gray-500">{{ $returnNote->supplier->email }}</div>
                                @endif
                                @if($returnNote->supplier->phone)
                                <div class="text-xs text-gray-500">{{ $returnNote->supplier->phone }}</div>
                                @endif
                            </div>
                            @else
                            <div class="text-sm text-gray-500">Aucun partenaire défini</div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Entrepôt</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Entrepôt :</span>
                                <div class="text-sm text-gray-900">{{ $returnNote->warehouse->name }}</div>
                                <div class="text-xs text-gray-500">{{ $returnNote->warehouse->address }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($returnNote->reason)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Motif du retour</h4>
                    <p class="text-sm text-gray-600">{{ $returnNote->reason }}</p>
                </div>
                @endif
            </div>

            <!-- Articles retournés -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Articles retournés</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Produit
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Quantité
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Prix unitaire
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total HT
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    TVA
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total TTC
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($returnNote->items as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if($item->product->image)
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded object-cover" src="{{ asset('storage/' . $item->product->image) }}" alt="">
                                        </div>
                                        @endif
                                        <div class="{{ $item->product->image ? 'ml-4' : '' }}">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $item->product->code }}</div>
                                            @if($item->product->barcode)
                                            <div class="text-xs text-gray-400">{{ $item->product->barcode }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-gray-900">
                                    {{ number_format($item->quantity) }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-gray-900">
                                    {{ number_format($item->unit_price, 0, ',', ' ') }} F
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-gray-900">
                                    {{ number_format($item->total_amount_ht, 0, ',', ' ') }} F
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-gray-900">
                                    {{ number_format($item->tax_amount, 0, ',', ' ') }} F
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                                    {{ number_format($item->total_amount, 0, ',', ' ') }} F
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                                    Total
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                                    {{ number_format($returnNote->subtotal, 0, ',', ' ') }} F
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium text-gray-900">
                                    {{ number_format($returnNote->tax_amount, 0, ',', ' ') }} F
                                </td>
                                <td class="px-6 py-4 text-right text-lg font-bold text-gray-900">
                                    {{ number_format($returnNote->total_amount, 0, ',', ' ') }} F
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Historique des actions -->
            @if($returnNote->logs->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Historique des actions</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="flow-root">
                        <ul role="list" class="-mb-8">
                            @foreach($returnNote->logs->sortByDesc('created_at') as $log)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <p class="text-sm text-gray-900">
                                                    <span class="font-medium">{{ $log->user->first_name }} {{ $log->user->last_name }}</span>
                                                    {{ $log->action }}
                                                </p>
                                                <p class="mt-0.5 text-sm text-gray-500">
                                                    {{ $log->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                            @if($log->details)
                                            <div class="mt-2 text-sm text-gray-700">
                                                {{ $log->details }}
                                            </div>
                                            @endif
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

<script>
function approveReturn() {
    if (confirm('Êtes-vous sûr de vouloir approuver ce bon de retour ?')) {
        fetch(`/return-notes/{{ $returnNote->id }}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de l\'approbation');
            }
        });
    }
}
</script>
@endsection