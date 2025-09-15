<?php
// ===============================================
// VUES SYSTÈME COMPLÉMENTAIRES - ERREURS, LOGS & PERFORMANCE
// ===============================================

// 🎯 PAGE D'ERREUR 500 PERSONNALISÉE
// resources/views/errors/500.blade.php
?>
@extends('layouts.guest')

@section('title', 'Erreur Interne')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
        <div>
            <div class="mx-auto h-24 w-24 text-red-500 mb-6">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.864-.833-2.634 0L4.168 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900 mb-2">
                Erreur Interne du Serveur
            </h2>
            <p class="text-gray-600 mb-8">
                Une erreur inattendue s'est produite. Notre équipe technique a été automatiquement notifiée.
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Que faire maintenant ?</h3>
            <div class="space-y-3 text-left">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-600">Patientez quelques instants et réessayez</p>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-600">Vérifiez votre connexion internet</p>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-600">Contactez le support si le problème persiste</p>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <button onclick="window.history.back()" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour
            </button>
            <a href="{{ route('home') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Accueil
            </a>
        </div>

        @if(app()->environment('local'))
        <div class="mt-8 p-4 bg-red-50 border border-red-200 rounded-lg">
            <h4 class="text-sm font-semibold text-red-800 mb-2">Informations de débogage (mode développement)</h4>
            <div class="text-xs text-red-700 space-y-1">
                <p><strong>Timestamp:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
                <p><strong>User Agent:</strong> {{ request()->userAgent() }}</p>
                <p><strong>IP:</strong> {{ request()->ip() }}</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
