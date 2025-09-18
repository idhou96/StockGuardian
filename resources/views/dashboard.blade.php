<<<<<<< HEAD
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
=======
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-6">
            <h1 class="text-2xl font-bold text-gray-900">
                Bienvenue dans StockGuardian
            </h1>
            <p class="mt-1 text-gray-600">
                Tableau de bord général - {{ auth()->user()->name ?? 'Utilisateur' }}
            </p>
        </div>
    </div>

    @if(isset($dashboardData['cards']))
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($dashboardData['cards'] as $card)
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="text-2xl font-bold text-{{ $card['color'] ?? 'blue' }}-600">
                                    {{ $card['value'] }}
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        {{ $card['title'] }}
                                    </dt>
                                    @if(isset($card['unit']))
                                        <dd class="text-sm text-gray-900">{{ $card['unit'] }}</dd>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Accès rapide</h2>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <a href="{{ route('products.index') }}" class="bg-blue-50 p-4 rounded-lg hover:bg-blue-100 transition-colors">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <span class="text-sm font-medium">Produits</span>
                    </div>
                </a>
                <a href="{{ route('customers.index') }}" class="bg-green-50 p-4 rounded-lg hover:bg-green-100 transition-colors">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-3a3.5 3.5 0 11-7 0 3.5 3.5 0 017 0z"/>
                        </svg>
                        <span class="text-sm font-medium">Clients</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
>>>>>>> 2022c95 (essaie commit)
