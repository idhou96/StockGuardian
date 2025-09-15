
// ===================================
// 5. VUE PERMISSIONS DÉTAILLÉES
// ===================================
// File: resources/views/permissions/index.blade.php

@extends('layouts.app')

@section('title', 'Gestion des Permissions')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Gestion des Permissions</h1>
                    <p class="text-sm text-gray-600 mt-1">Configurez les permissions par rôle et module</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('users.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                        <span>Utilisateurs</span>
                    </a>
                    <a href="{{ route('roles.index') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>Rôles</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="px-6 py-6">
        <!-- Vue d'ensemble des rôles -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Vue d'ensemble des Rôles</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
                @foreach($roles as $role)
                <div class="text-center">
                    <div class="w-16 h-16 bg-{{ $role->color ?? 'blue' }}-100 rounded-full flex items-center justify-center mx-auto mb-2">
                        <svg class="w-8 h-8 text-{{ $role->color ?? 'blue' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div class="text-sm font-medium text-gray-900">{{ $role->display_name }}</div>
                    <div class="text-xs text-gray-500">{{ $role->users_count }} utilisateurs</div>
                    <div class="text-xs text-gray-500">{{ $role->permissions_count }} permissions</div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Matrice des permissions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Matrice des Permissions</h3>
                <p class="text-sm text-gray-600 mt-1">Gérez les permissions par module pour chaque rôle</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="sticky left-0 bg-gray-50 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-200">
                                Module / Permission
                            </th>
                            @foreach($roles as $role)
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex flex-col items-center">
                                    <span>{{ $role->display_name }}</span>
                                    <span class="text-xs text-gray-400">({{ $role->users_count }})</span>
                                </div>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($permissionsByModule as $module => $permissions)
                        <!-- En-tête de module -->
                        <tr class="bg-gray-100">
                            <td class="sticky left-0 bg-gray-100 px-6 py-3 text-sm font-bold text-gray-900 border-r border-gray-200">
                                📦 {{ ucfirst(str_replace('_', ' ', $module)) }}
                            </td>
                            @foreach($roles as $role)
                            <td class="px-3 py-3 text-center">
                                <button onclick="toggleModulePermissions('{{ $module }}', '{{ $role->id }}')" 
                                        class="text-xs bg-blue-100 hover:bg-blue-200 text-blue-800 px-2 py-1 rounded">
                                    Tout
                                </button>
                            </td>
                            @endforeach
                        </tr>
                        
                        <!-- Permissions du module -->
                        @foreach($permissions as $permission)
                        <tr class="hover:bg-gray-50">
                            <td class="sticky left-0 bg-white hover:bg-gray-50 px-6 py-3 text-sm text-gray-700 border-r border-gray-200">
                                <div class="flex items-center">
                                    <span class="ml-4">{{ $permission->display_name }}</span>
                                    @if($permission->description)
                                    <span class="ml-2 text-xs text-gray-500">({{ $permission->description }})</span>
                                    @endif
                                </div>
                            </td>
                            @foreach($roles as $role)
                            <td class="px-3 py-3 text-center">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" 
                                           {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                           onchange="togglePermission('{{ $role->id }}', '{{ $permission->id }}', this.checked)"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </label>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Permissions spéciales -->
        <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Permissions Spéciales</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="border border-yellow-200 bg-yellow-50 rounded-lg p-4">
                    <h4 class="font-medium text-yellow-800 mb-2">🔒 Permissions Sensibles</h4>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• Supprimer des données</li>
                        <li>• Modifier les paramètres système</li>
                        <li>• Gérer les utilisateurs</li>
                        <li>• Accéder aux logs</li>
                    </ul>
                </div>
                
                <div class="border border-blue-200 bg-blue-50 rounded-lg p-4">
                    <h4 class="font-medium text-blue-800 mb-2">⚡ Permissions Avancées</h4>
                    <ul class="text-sm text-blue-700 space-y-1">
                        <li>• Export/Import de données</li>
                        <li>• Rapports avancés</li>
                        <li>• Configuration POS</li>
                        <li>• Gestion des sauvegardes</li>
                    </ul>
                </div>
                
                <div class="border border-green-200 bg-green-50 rounded-lg p-4">
                    <h4 class="font-medium text-green-800 mb-2">📊 Permissions de Consultation</h4>
                    <ul class="text-sm text-green-700 space-y-1">
                        <li>• Voir les produits</li>
                        <li>• Consulter les ventes</li>
                        <li>• Voir les stocks</li>
                        <li>• Accès aux rapports</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePermission(roleId, permissionId, isChecked) {
    fetch('/permissions/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({
            role_id: roleId,
            permission_id: permissionId,
            action: isChecked ? 'grant' : 'revoke'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            alert('Erreur lors de la modification de la permission');
            // Remettre l'état précédent
            event.target.checked = !isChecked;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur de connexion');
        event.target.checked = !isChecked;
    });
}

function toggleModulePermissions(module, roleId) {
    if (confirm(`Activer/désactiver toutes les permissions du module ${module} pour ce rôle ?`)) {
        fetch('/permissions/toggle-module', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({
                role_id: roleId,
                module: module
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de la modification');
            }
        });
    }
}
</script>
@endsection