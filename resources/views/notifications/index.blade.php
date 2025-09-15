
// ===================================
// 5. VUE CENTRE DE NOTIFICATIONS
// ===================================
// File: resources/views/notifications/index.blade.php

@extends('layouts.app')

@section('title', 'Centre de Notifications')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Centre de Notifications</h1>
                    <p class="text-sm text-gray-600 mt-1">Gérez toutes vos notifications système</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="markAllAsRead()" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Tout marquer comme lu</span>
                    </button>
                    <button onclick="clearAllNotifications()" 
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        <span>Tout supprimer</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-6">
        <!-- Filtres -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Filtrer par :</label>
                    <select id="typeFilter" onchange="filterNotifications()" 
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tous types</option>
                        <option value="stock_alert">Alertes stock</option>
                        <option value="expiry_alert">Alertes expiration</option>
                        <option value="sales_notification">Notifications ventes</option>
                        <option value="system_notification">Notifications système</option>
                        <option value="user_action">Actions utilisateur</option>
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Statut :</label>
                    <select id="statusFilter" onchange="filterNotifications()" 
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Tous</option>
                        <option value="unread">Non lues</option>
                        <option value="read">Lues</option>
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Période :</label>
                    <select id="periodFilter" onchange="filterNotifications()" 
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="today">Aujourd'hui</option>
                        <option value="week" selected>Cette semaine</option>
                        <option value="month">Ce mois</option>
                        <option value="all">Toutes</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Liste des notifications -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="divide-y divide-gray-200">
                @forelse($notifications ?? [] as $notification)
                <div class="notification-item p-6 hover:bg-gray-50 {{ $notification['read'] ? '' : 'bg-blue-50' }}" 
                     data-type="{{ $notification['type'] }}" 
                     data-status="{{ $notification['read'] ? 'read' : 'unread' }}">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            @switch($notification['type'])
                                @case('stock_alert')
                                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                    </div>
                                    @break
                                @case('expiry_alert')
                                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    @break
                                @case('sales_notification')
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                        </svg>
                                    </div>
                                    @break
                                @case('system_notification')
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    @break
                                @default
                                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"/>
                                        </svg>
                                    </div>
                            @endswitch
                        </div>
                        
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $notification['title'] }}
                                    @if(!$notification['read'])
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-2">
                                        Nouveau
                                    </span>
                                    @endif
                                </p>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs text-gray-500">{{ $notification['time'] }}</span>
                                    <div class="flex space-x-1">
                                        @if(!$notification['read'])
                                        <button onclick="markAsRead({{ $notification['id'] }})" 
                                                class="text-blue-600 hover:text-blue-900 text-xs">
                                            Marquer comme lu
                                        </button>
                                        @endif
                                        <button onclick="deleteNotification({{ $notification['id'] }})" 
                                                class="text-red-600 hover:text-red-900 text-xs">
                                            Supprimer
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-1 text-sm text-gray-600">{{ $notification['message'] }}</p>
                            @if(isset($notification['action_url']))
                            <div class="mt-2">
                                <a href="{{ $notification['action_url'] }}" 
                                   class="inline-flex items-center text-sm text-blue-600 hover:text-blue-900">
                                    {{ $notification['action_text'] ?? 'Voir détails' }}
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune notification</h3>
                    <p class="mt-1 text-sm text-gray-500">Vous n'avez aucune notification pour le moment.</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination si nécessaire -->
        <div class="mt-6 flex justify-center">
            <nav class="flex items-center space-x-2">
                <button class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 disabled:opacity-50" disabled>
                    Précédent
                </button>
                <span class="px-3 py-2 text-sm bg-blue-600 text-white rounded">1</span>
                <button class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700">2</button>
                <button class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700">3</button>
                <button class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700">
                    Suivant
                </button>
            </nav>
        </div>
    </div>
</div>

<script>
// Données d'exemple pour les notifications
const sampleNotifications = [
    {
        id: 1,
        type: 'stock_alert',
        title: 'Stock faible détecté',
        message: 'Le produit "Paracétamol 500mg" a un stock inférieur au seuil d\'alerte (5 unités restantes).',
        time: 'Il y a 2h',
        read: false,
        action_url: '/products/123',
        action_text: 'Voir le produit'
    },
    {
        id: 2,
        type: 'expiry_alert',
        title: 'Produits bientôt expirés',
        message: '15 produits expireront dans les 30 prochains jours.',
        time: 'Il y a 4h',
        read: false,
        action_url: '/reports/expiry',
        action_text: 'Voir la liste'
    },
    {
        id: 3,
        type: 'sales_notification',
        title: 'Objectif de vente atteint',
        message: 'Félicitations ! L\'objectif mensuel de 500 000 F a été atteint.',
        time: 'Hier',
        read: true,
        action_url: '/reports/sales',
        action_text: 'Voir les détails'
    }
];

function filterNotifications() {
    const typeFilter = document.getElementById('typeFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    const periodFilter = document.getElementById('periodFilter').value;
    
    const items = document.querySelectorAll('.notification-item');
    
    items.forEach(item => {
        let show = true;
        
        if (typeFilter && item.dataset.type !== typeFilter) {
            show = false;
        }
        
        if (statusFilter && item.dataset.status !== statusFilter) {
            show = false;
        }
        
        item.style.display = show ? 'block' : 'none';
    });
}

function markAsRead(id) {
    // Simulation - dans la vraie app, faire un appel API
    const item = document.querySelector(`[data-id="${id}"]`);
    if (item) {
        item.classList.remove('bg-blue-50');
        item.dataset.status = 'read';
        // Supprimer le badge "Nouveau"
        const badge = item.querySelector('.bg-blue-100');
        if (badge) badge.remove();
    }
}

function markAllAsRead() {
    if (confirm('Marquer toutes les notifications comme lues ?')) {
        document.querySelectorAll('.notification-item[data-status="unread"]').forEach(item => {
            item.classList.remove('bg-blue-50');
            item.dataset.status = 'read';
            const badge = item.querySelector('.bg-blue-100');
            if (badge) badge.remove();
        });
        alert('Toutes les notifications ont été marquées comme lues');
    }
}

function deleteNotification(id) {
    if (confirm('Supprimer cette notification ?')) {
        const item = document.querySelector(`[data-id="${id}"]`);
        if (item) {
            item.remove();
        }
    }
}

function clearAllNotifications() {
    if (confirm('Supprimer toutes les notifications ? Cette action est irréversible.')) {
        document.querySelectorAll('.notification-item').forEach(item => item.remove());
        // Afficher le message "Aucune notification"
        location.reload();
    }
}
</script>
@endsection