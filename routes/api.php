<?php

use Illuminate\Support\Facades\Route;

// Exemple : route test API
Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});
