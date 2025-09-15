
<?php
// ===============================================
// 🎯 VUES GESTION DES ALERTES SYSTÈME
// resources/views/alerts/index.blade.php
?>
@extends('layouts.app')

@section('title', 'Alertes Système')

@section('content')
<div class="space-y-6" x-data="alertsManager()">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.864-.833-2.634 0L4.168 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    Centre d'Alertes
                </h1>
                <p class="text-sm text-gray-600 mt-1">
                    Surveillez et gérez les alertes système en temps réel
                </p>
            </div>
            
            <div class="flex gap-3">
                <button @click="markAllAsResolved()" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200"
                        :disabled="!hasUnresolvedAlerts">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Tout résoudre
                </button>
                <button @click="refreshAlerts()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Actualiser
                </button>
            </div>
        </div>
    </div>

    {{-- Filtres par type d'alerte --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtrer les alertes</h3>
        <div class="flex flex-wrap gap-3">
            <button @click="setFilter('all')" 
                    :class="activeFilter === 'all' ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Toutes ({{ $alertCounts['total'] ?? 0 }})
            </button>
            <button @click="setFilter('stock')" 
                    :class="activeFilter === 'stock' ? 'bg-red-600 text-white' : 'bg-red-100 text-red-700 hover:bg-red-200'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Stock faible ({{ $alertCounts['low_stock'] ?? 0 }})
            </button>
            <button @click="setFilter('expiry')" 
                    :class="activeFilter === 'expiry' ? 'bg-orange-600 text-white' : 'bg-orange-100 text-orange-700 hover:bg-orange-200'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Expiration ({{ $alertCounts['expiry'] ?? 0 }})
            </button>
            <button @click="setFilter('system')" 
                    :class="activeFilter === 'system' ? 'bg-purple-600 text-white' : 'bg-purple-100 text-purple-700 hover:bg-purple-200'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                Système ({{ $alertCounts['system'] ?? 0 }})
            </button>
        </div>
    </div>

    {{-- Liste des alertes --}}
    <div class="space-y-4">
        @forelse($alerts ?? [] as $alert)
        <div class="bg-white rounded-lg shadow border-l-4 
                    {{ $alert->priority === 'high' ? 'border-red-500' : 
                       ($alert->priority === 'medium' ? 'border-yellow-500' : 'border-blue-500') }}">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            @if($alert->type === 'stock')
                                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                            @elseif($alert->type === 'expiry')
                                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            @else
                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-gray-900">{{ $alert->title }}</h4>
                            <p class="text-gray-600 mt-1">{{ $alert->message }}</p>
                            <div class="flex items-center space-x-4 mt-3 text-sm text-gray-500">
                                <span>{{ $alert->created_at->diffForHumans() }}</span>
                                @if($alert->product)
                                    <span>Produit: {{ $alert->product->name }}</span>
                                @endif
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $alert->priority === 'high' ? 'bg-red-100 text-red-800' : 
                                       ($alert->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ ucfirst($alert->priority) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($alert->action_url)
                            <a href="{{ $alert->action_url }}" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                Voir détail
                            </a>
                        @endif
                        @if(!$alert->resolved_at)
                            <button @click="markAsResolved({{ $alert->id }})" 
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition-colors">
                                Résoudre
                            </button>
                        @else
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded text-sm">
                                Résolu
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune alerte</h3>
            <p class="text-gray-600">Toutes les alertes ont été résolues ou aucune alerte active.</p>
        </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
function alertsManager() {
    return {
        activeFilter: 'all',
        hasUnresolvedAlerts: {{ $alerts->where('resolved_at', null)->count() > 0 ? 'true' : 'false' }},
        
        setFilter(filter) {
            this.activeFilter = filter;
            // Recharger la page avec le filtre
            window.location.href = `{{ route('alerts.index') }}?filter=${filter}`;
        },
        
        async markAsResolved(alertId) {
            try {
                const response = await fetch(`/alerts/mark-resolved/${alertId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    }
                });
                
                if (response.ok) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        },
        
        async markAllAsResolved() {
            if (!confirm('Êtes-vous sûr de vouloir marquer toutes les alertes comme résolues ?')) {
                return;
            }
            
            try {
                const response = await fetch('/alerts/mark-all-resolved', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                    }
                });
                
                if (response.ok) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        },
        
        refreshAlerts() {
            window.location.reload();
        }
    }
}
</script>
@endpush
@endsection