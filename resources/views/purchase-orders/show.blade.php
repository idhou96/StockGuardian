{{-- resources/views/purchase-orders/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Commande #' . $order->reference)

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
                    <a href="{{ route('purchase-orders.index') }}" class="ml-1 md:ml-2 text-sm font-medium text-gray-700 hover:text-blue-600">Commandes</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="ml-1 md:ml-2 text-sm font-medium text-gray-500">{{ $order->reference }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header avec statut -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start mb-6 space-y-4 lg:space-y-0">
        <div class="flex items-center space-x-4">
            <div class="bg-blue-100 rounded-full p-3">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <div class="flex items-center space-x-3">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $order->reference }}</h1>
                    @php
                    $statusConfig = [
                        'draft' => ['bg-gray-100', 'text-gray-800', 'Brouillon'],
                        'sent' => ['bg-blue-100', 'text-blue-800', 'Envoyée'],
                        'confirmed' => ['bg-yellow-100', 'text-yellow-800', 'Confirmée'],
                        'partially_received' => ['bg-orange-100', 'text-orange-800', 'Partiellement reçue'],
                        'received' => ['bg-green-100', 'text-green-800', 'Reçue'],
                        'cancelled' => ['bg-red-100', 'text-red-800', 'Annulée']
                    ];
                    $config = $statusConfig[$order->status] ?? ['bg-gray-100', 'text-gray-800', ucfirst($order->status)];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $config[0] }} {{ $config[1] }}">
                        {{ $config[2] }}
                    </span>
                </div>
                <p class="text-gray-600 mt-1">
                    Commande du {{ $order->order_date->format('d/m/Y') }} - 
                    {{ $order->supplier->name }}
                </p>
            </div>
        </div>
        
        <div class="flex flex-wrap gap-3">
            @can('edit purchase_orders')
            @if($order->status === 'draft')
            <a href="{{ route('purchase-orders.edit', $order) }}" 
               class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Modifier</span>
            </a>
            @endif
            @endcan

            @can('send purchase_orders')
            @if($order->status === 'draft')
            <button onclick="sendOrder({{ $order->id }})" 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
                <span>Envoyer</span>
            </button>
            @endif
            @endcan

            @can('receive purchase_orders')
            @if(in_array($order->status, ['confirmed', 'partially_received']))
            <a href="{{ route('purchase-orders.receive', $order) }}" 
               class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span>Réceptionner</span>
            </a>
            @endif
            @endcan

            <a href="{{ route('purchase-orders.pdf', $order) }}" target="_blank"
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>PDF</span>
            </a>

            <a href="{{ route('purchase-orders.index') }}" 
               class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg flex items-center space-x-2 transition duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                </svg>
                <span>Retour</span>
            </a>
        </div>
    </div>

    <!-- Alertes -->
    @if($order->expected_delivery_date && $order->expected_delivery_date->isPast() && !in_array($order->status, ['received', 'cancelled']))
    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700 font-medium">Commande en retard</p>
                <p class="text-sm text-red-700 mt-1">
                    Livraison prévue le {{ $order->expected_delivery_date->format('d/m/Y') }}
                    ({{ $order->expected_delivery_date->diffForHumans() }})
                </p>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne principale -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informations de la commande -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Informations de la commande
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date de commande</label>
                        <p class="text-gray-900">{{ $order->order_date->format('d/m/Y') }}</p>
                    </div>

                    @if($order->expected_delivery_date)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Livraison prévue</label>
                        <p class="text-gray-900">{{ $order->expected_delivery_date->format('d/m/Y') }}</p>
                        @if($order->expected_delivery_date->isPast() && !in_array($order->status, ['received', 'cancelled']))
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-1">
                            En retard
                        </span>
                        @endif
                    </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Entrepôt de livraison</label>
                        <p class="text-gray-900">{{ $order->warehouse->name }}</p>
                        <p class="text-sm text-gray-500">{{ $order->warehouse->location }}</p>
                    </div>

                    @if($order->supplier_reference)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Référence fournisseur</label>
                        <p class="text-gray-900 font-mono">{{ $order->supplier_reference }}</p>
                    </div>
                    @endif

                    @if($order->sent_at)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date d'envoi</label>
                        <p class="text-gray-900">{{ $order->sent_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    @endif

                    @if($order->confirmed_at)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date de confirmation</label>
                        <p class="text-gray-900">{{ $order->confirmed_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    @endif
                </div>

                @if($order->notes)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes et instructions</label>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-gray-900">{{ $order->notes }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Articles commandés -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Articles commandés ({{ $order->details->count() }})
                    </h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qté commandée</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qté reçue</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($order->details as $detail)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($detail->product->image_url)
                                        <img src="{{ $detail->product->image_url }}" alt="{{ $detail->product->name }}" 
                                             class="h-10 w-10 rounded-md object-cover mr-3">
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $detail->product->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $detail->product->barcode }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $detail->quantity }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ number_format($detail->unit_price, 2, ',', ' ') }} €</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-900">{{ number_format($detail->total_price, 2, ',', ' ') }} €</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $detail->received_quantity ?? 0 }}</span>
                                    @if($detail->received_quantity > 0)
                                    <div class="w-full bg-gray-200 rounded-full h-1 mt-1">
                                        @php $percentage = min(100, ($detail->received_quantity / $detail->quantity) * 100); @endphp
                                        <div class="bg-green-500 h-1 rounded-full" style="width: {{ $percentage }}%"></div>
                                    </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($detail->received_quantity >= $detail->quantity)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Reçu complet
                                    </span>
                                    @elseif($detail->received_quantity > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Partiel ({{ number_format(($detail->received_quantity / $detail->quantity) * 100, 0) }}%)
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        En attente
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Historique des réceptions -->
            @if($order->deliveryNotes->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                    Historique des réceptions
                </h2>
                
                <div class="space-y-4">
                    @foreach($order->deliveryNotes as $deliveryNote)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="bg-green-100 rounded-full p-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $deliveryNote->reference }}</p>
                                <p class="text-sm text-gray-500">{{ $deliveryNote->delivery_date->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-900">{{ $deliveryNote->details->count() }} article(s)</p>
                            <a href="{{ route('delivery-notes.show', $deliveryNote) }}" 
                               class="text-sm text-blue-600 hover:text-blue-800">Voir détails</a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Colonne latérale -->
        <div class="space-y-6">
            <!-- Résumé financier -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                    Résumé financier
                </h2>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Sous-total HT:</span>
                        <span class="font-medium">{{ number_format($order->subtotal_amount, 2, ',', ' ') }} €</span>
                    </div>
                    @if($order->tax_amount > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">TVA:</span>
                        <span class="font-medium">{{ number_format($order->tax_amount, 2, ',', ' ') }} €</span>
                    </div>
                    @endif
                    @if($order->shipping_cost > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Frais de port:</span>
                        <span class="font-medium">{{ number_format($order->shipping_cost, 2, ',', ' ') }} €</span>
                    </div>
                    @endif
                    @if($order->discount_amount > 0)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Remise:</span>
                        <span class="font-medium text-green-600">-{{ number_format($order->discount_amount, 2, ',', ' ') }} €</span>
                    </div>
                    @endif
                    <hr class="border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-900">Total TTC:</span>
                        <span class="text-xl font-bold text-blue-600">{{ number_format($order->total_amount, 2, ',', ' ') }} €</span>
                    </div>
                </div>
            </div>

            <!-- Informations fournisseur -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Fournisseur
                </h2>
                
                <div class="text-center">
                    <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mx-auto mb-3">
                        <span class="text-xl font-bold text-blue-600">
                            {{ strtoupper(substr($order->supplier->name, 0, 2)) }}
                        </span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">{{ $order->supplier->name }}</h3>
                    
                    <div class="space-y-2 text-sm text-gray-600">
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ $order->supplier->email }}</span>
                        </div>
                        @if($order->supplier->phone)
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span>{{ $order->supplier->phone }}</span>
                        </div>
                        @endif
                        @if($order->supplier->address)
                        <div class="flex items-start justify-center space-x-2 mt-3">
                            <svg class="w-4 h-4 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-left">{{ $order->supplier->address }}</span>
                        </div>
                        @endif
                    </div>
                    
                    <a href="{{ route('suppliers.show', $order->supplier) }}" 
                       class="inline-flex items-center px-3 py-2 mt-4 text-sm font-medium text-blue-600 bg-blue-100 rounded-lg hover:bg-blue-200 transition duration-200">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Voir le fournisseur
                    </a>
                </div>
            </div>

            <!-- Progression de la commande -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Progression
                </h2>
                
                <div class="space-y-4">
                    @php
                    $totalOrdered = $order->details->sum('quantity');
                    $totalReceived = $order->details->sum('received_quantity');
                    $progressPercentage = $totalOrdered > 0 ? ($totalReceived / $totalOrdered) * 100 : 0;
                    @endphp
                    
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">Articles reçus</span>
                            <span class="font-medium">{{ $totalReceived }} / {{ $totalOrdered }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full transition-all duration-300" 
                                 style="width: {{ $progressPercentage }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ number_format($progressPercentage, 1) }}% complété</p>
                    </div>

                    <!-- Timeline -->
                    <div class="mt-6">
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-3 h-3 rounded-full bg-blue-500"></div>
                                <div class="text-sm">
                                    <p class="font-medium text-gray-900">Commande créée</p>
                                    <p class="text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            
                            @if($order->sent_at)
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-3 h-3 rounded-full bg-green-500"></div>
                                <div class="text-sm">
                                    <p class="font-medium text-gray-900">Commande envoyée</p>
                                    <p class="text-gray-500">{{ $order->sent_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            @endif
                            
                            @if($order->confirmed_at)
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-3 h-3 rounded-full bg-yellow-500"></div>
                                <div class="text-sm">
                                    <p class="font-medium text-gray-900">Commande confirmée</p>
                                    <p class="text-gray-500">{{ $order->confirmed_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            @endif
                            
                            @if($order->status === 'received')
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-3 h-3 rounded-full bg-purple-500"></div>
                                <div class="text-sm">
                                    <p class="font-medium text-gray-900">Commande reçue</p>
                                    <p class="text-gray-500">{{ $order->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            @elseif($order->status === 'cancelled')
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-3 h-3 rounded-full bg-red-500"></div>
                                <div class="text-sm">
                                    <p class="font-medium text-gray-900">Commande annulée</p>
                                    <p class="text-gray-500">{{ $order->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
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
                    <a href="{{ route('purchase-orders.duplicate', $order) }}" 
                       class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 py-2 px-4 rounded-lg text-sm flex items-center transition duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Dupliquer la commande
                    </a>
                    
                    <a href="{{ route('purchase-orders.index', ['supplier_id' => $order->supplier->id]) }}" 
                       class="w-full bg-purple-50 hover:bg-purple-100 text-purple-700 py-2 px-4 rounded-lg text-sm flex items-center transition duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Autres commandes fournisseur
                    </a>
                    
                    @if($order->status !== 'cancelled')
                    <button onclick="cancelOrder({{ $order->id }})"
                            class="w-full bg-red-50 hover:bg-red-100 text-red-700 py-2 px-4 rounded-lg text-sm flex items-center transition duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Annuler la commande
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation d'envoi -->
<div id="sendModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <svg class="mx-auto mb-4 w-14 h-14 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Envoyer la commande</h3>
            <p class="text-sm text-gray-500 mb-4">
                Êtes-vous sûr de vouloir envoyer cette commande au fournisseur ? 
                Elle ne pourra plus être modifiée par la suite.
            </p>
            <div class="flex justify-center space-x-4">
                <button id="confirmSend" type="button" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Oui, envoyer
                </button>
                <button id="cancelSend" type="button" 
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Annuler
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation d'annulation -->
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <svg class="mx-auto mb-4 w-14 h-14 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Annuler la commande</h3>
            <p class="text-sm text-gray-500 mb-4">
                Êtes-vous sûr de vouloir annuler cette commande ? 
                Cette action est irréversible.
            </p>
            <div class="flex justify-center space-x-4">
                <button id="confirmCancel" type="button" 
                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Oui, annuler
                </button>
                <button id="cancelCancel" type="button" 
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Non, garder
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function sendOrder(orderId) {
    document.getElementById('sendModal').classList.remove('hidden');
    
    document.getElementById('confirmSend').onclick = function() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/purchase-orders/${orderId}/send`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    };
}

function cancelOrder(orderId) {
    document.getElementById('cancelModal').classList.remove('hidden');
    
    document.getElementById('confirmCancel').onclick = function() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/purchase-orders/${orderId}/cancel`;
        
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
document.getElementById('cancelSend').addEventListener('click', function() {
    document.getElementById('sendModal').classList.add('hidden');
});

document.getElementById('cancelCancel').addEventListener('click', function() {
    document.getElementById('cancelModal').classList.add('hidden');
});

// Fermer modals en cliquant en dehors
document.getElementById('sendModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});

document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});
</script>
@endpush
@endsection