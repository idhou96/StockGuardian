
// ===================================
// 4. VUE CREATE PRINCIPE ACTIF
// ===================================
// File: resources/views/active-principles/create.blade.php

@extends('layouts.app')

@section('title', 'Créer un principe actif')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Créer un principe actif</h1>
                    <p class="text-sm text-gray-600 mt-1">Ajoutez un nouveau principe actif au système</p>
                </div>
                <a href="{{ route('active-principles.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Retour</span>
                </a>
            </div>
        </div>
    </div>

    <div class="px-6 py-6">
        <div class="max-w-2xl mx-auto">
            <form action="{{ route('active-principles.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Informations générales -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informations générales</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nom du principe actif *</label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   required
                                   placeholder="Ex: Paracétamol, Ibuprofène..."
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                            @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="dci_code" class="block text-sm font-medium text-gray-700 mb-2">Code DCI</label>
                            <input type="text" 
                                   id="dci_code" 
                                   name="dci_code" 
                                   value="{{ old('dci_code') }}"
                                   placeholder="Dénomination Commune Internationale"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('dci_code') border-red-500 @enderror">
                            @error('dci_code')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="therapeutic_class" class="block text-sm font-medium text-gray-700 mb-2">Classe thérapeutique</label>
                            <input type="text" 
                                   id="therapeutic_class" 
                                   name="therapeutic_class" 
                                   value="{{ old('therapeutic_class') }}"
                                   placeholder="Ex: Antalgique, Anti-inflammatoire..."
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('therapeutic_class') border-red-500 @enderror">
                            @error('therapeutic_class')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="4"
                                      placeholder="Indications, mécanisme d'action, précautions d'emploi..."
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Principe actif</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('active-principles.index') }}" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        Créer le principe actif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection