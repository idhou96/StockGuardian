@extends('layouts.app')
@section('title', 'Dashboard Comptable')
@section('content')
<div class="space-y-6">
    <div class="bg-gradient-to-r from-purple-600 to-purple-800 rounded-lg shadow-lg p-6 text-white">
        <h1 class="text-2xl font-bold">Dashboard Comptable</h1>
        <p class="text-purple-100 mt-1">Suivi financier et comptabilité</p>
    </div>
    @if(isset($dashboardData['cards']))
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($dashboardData['cards'] as $card)
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-2xl font-bold text-{{ $card['color'] ?? 'purple' }}-600">{{ $card['value'] }}</div>
                    <p class="text-gray-600 text-sm">{{ $card['title'] }}</p>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection