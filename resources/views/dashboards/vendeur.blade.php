@extends('layouts.app')

@section('title', 'Dashboard Vendeur')

@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-green-600 to-green-800 rounded-lg shadow-lg p-6 text-white">
        <h1 class="text-2xl font-bold">Dashboard Vendeur</h1>
        <p class="text-green-100 mt-1">Vos ventes et objectifs</p>
    </div>

    @if(isset($dashboardData['cards']))
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($dashboardData['cards'] as $card)
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-2xl font-bold text-{{ $card['color'] ?? 'blue' }}-600">{{ $card['value'] }}</div>
                    <p class="text-gray-600 text-sm">{{ $card['title'] }}</p>
                    <p class="text-xs text-gray-500">{{ $card['unit'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Actions Rapides</h3>
        <div class="grid grid-cols-2 gap-4">
            <a href="{{ route('pos.index') }}" class="bg-green-600 text-white p-4 rounded-lg text-center hover:bg-green-700">
                Point de Vente
            </a>
            <a href="{{ route('customers.create') }}" class="bg-blue-600 text-white p-4 rounded-lg text-center hover:bg-blue-700">
                Nouveau Client
            </a>
        </div>
    </div>
</div>
@endsection