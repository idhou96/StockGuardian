{{-- resources/views/inventories/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Nouvel Inventaire')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="mb-6">
            <x-breadcrumb :items="[
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Inventaires', 'url' => route('inventories.index')],
                ['label' => 'Nouvel Inventaire', 'url' => null]
            ]" />
            <div class="mt-4">
                <h1 class="text-2xl font-bold text-gray-900">Nouvel Inventaire</h1>
                <p class="mt-1 text-sm text-gray-600">Créez un nouvel inventaire physique des stocks</p>
            </div>
        </div>

        <form action="{{ route('inventories.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Informations générales --}}
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Informations générales</h3>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Titre de l'inventaire *</label>
                        <input type="text" name="title" id="title" required
                               value="{{ old('title') }}" 
                               placeholder="Ex: Inventaire mensuel Octobre 2024"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('title') border-red-300 @enderror">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="warehouse_id" class="block text-sm font-medium text-gray-700">Dépôt *</label>
                        <select name="warehouse_id" id="warehouse_id" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('warehouse_id') border-red-300 @enderror">
                            <option value="">Sélectionner un dépôt</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }} ({{ $warehouse->type }})
                                </option>
                            @endforeach
                        </select>
                        @error('warehouse_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="inventory_date" class="block text-sm font-medium text-gray-700">Date d'inventaire *</label>
                        <input type="date" name="inventory_date" id="inventory_date" required
                               value="{{ old('inventory_date', date('Y-m-d')) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('inventory_date') border-red-300 @enderror">
                        @error('inventory_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type d'inventaire *</label>
                        <select name="type" id="type" required 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('type') border-red-300 @enderror">
                            <option value="">Sélectionner un type</option>
                            <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>Inventaire général</option>
                            <option value="partial" {{ old('type') == 'partial' ? 'selected' : '' }}>Inventaire partiel</option>
                            <option value="cyclical" {{ old('type') == 'cyclical' ? 'selected' : '' }}>Inventaire cyclique</option>
                            <option value="emergency" {{ old('type') == 'emergency' ? 'selected' : '' }}>Inventaire d'urgence</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3" 
                                  placeholder="Description de l'inventaire et objectifs..."
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Filtres de sélection des produits --}}
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Sélection des produits</h3>
                    <p class="mt-1 text-sm text-gray-600">Définissez les critères pour sélectionner les produits à inventorier</p>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="family_ids" class="block text-sm font-medium text-gray-700">Familles de produits</label>
                        <select name="family_ids[]" id="family_ids" multiple 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($families as $family)
                                <option value="{{ $family->id }}" 
                                    {{ in_array($family->id, old('family_ids', [])) ? 'selected' : '' }}>
                                    {{ $family->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Laissez vide pour inclure toutes les familles</p>
                    </div>

                    <div>
                        <label for="product_type" class="block text-sm font-medium text-gray-700">Type de produit</label>
                        <select name="product_type" id="product_type" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Tous les types</option>
                            <option value="medicament" {{ old('product_type') == 'medicament' ? 'selected' : '' }}>Médicaments</option>
                            <option value="dispositif_medical" {{ old('product_type') == 'dispositif_medical' ? 'selected' : '' }}>Dispositifs médicaux</option>
                            <option value="parapharmacie" {{ old('product_type') == 'parapharmacie' ? 'selected' : '' }}>Parapharmacie</option>
                            <option value="complement_alimentaire" {{ old('product_type') == 'complement_alimentaire' ? 'selected' : '' }}>Compléments alimentaires</option>
                        </select>
                    </div>

                    <div class="flex items-center space-x-6">
                        <div class="flex items-center">
                            <input type="checkbox" name="include_zero_stock" id="include_zero_stock" value="1" 
                                   {{ old('include_zero_stock') ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="include_zero_stock" class="ml-2 block text-sm text-gray-900">
                                Inclure les produits à stock zéro
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center space-x-6">
                        <div class="flex items-center">
                            <input type="checkbox" name="include_inactive" id="include_inactive" value="1" 
                                   {{ old('include_inactive') ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="include_inactive" class="ml-2 block text-sm text-gray-900">
                                Inclure les produits inactifs
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Paramètres d'inventaire --}}
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Paramètres</h3>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tolerance_percentage" class="block text-sm font-medium text-gray-700">Tolérance d'écart (%)</label>
                        <input type="number" name="tolerance_percentage" id="tolerance_percentage"
                               value="{{ old('tolerance_percentage', 5) }}" 
                               min="0" max="100" step="0.1"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p class="mt-1 text-sm text-gray-500">Écart acceptable avant signalement d'anomalie</p>
                    </div>

                    <div>
                        <label for="responsible_user_id" class="block text-sm font-medium text-gray-700">Responsable</label>
                        <select name="responsible_user_id" id="responsible_user_id" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Sélectionner un responsable</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('responsible_user_id', auth()->id()) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ ucfirst($user->role) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center space-x-6">
                        <div class="flex items-center">
                            <input type="checkbox" name="auto_generate_adjustments" id="auto_generate_adjustments" value="1" 
                                   {{ old('auto_generate_adjustments', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="auto_generate_adjustments" class="ml-2 block text-sm text-gray-900">
                                Générer automatiquement les ajustements
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center space-x-6">
                        <div class="flex items-center">
                            <input type="checkbox" name="freeze_stock_movements" id="freeze_stock_movements" value="1" 
                                   {{ old('freeze_stock_movements') ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="freeze_stock_movements" class="ml-2 block text-sm text-gray-900">
                                Geler les mouvements de stock pendant l'inventaire
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end space-x-4">
                <a href="{{ route('inventories.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" name="action" value="save_draft"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700">
                    Enregistrer en brouillon
                </button>
                <button type="submit" name="action" value="create_and_start"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Créer et démarrer l'inventaire
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Sélection multiple améliorée
document.addEventListener('DOMContentLoaded', function() {
    const familySelect = document.getElementById('family_ids');
    if (familySelect) {
        familySelect.addEventListener('change', function() {
            updateProductPreview();
        });
    }
    
    const warehouseSelect = document.getElementById('warehouse_id');
    if (warehouseSelect) {
        warehouseSelect.addEventListener('change', function() {
            updateProductPreview();
        });
    }
});

function updateProductPreview() {
    // Ici vous pouvez ajouter une logique pour prévisualiser le nombre de produits
    // qui seront inclus dans l'inventaire basé sur les filtres sélectionnés
}
</script>
@endpush
@endsection
