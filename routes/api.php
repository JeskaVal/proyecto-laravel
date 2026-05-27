<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Health check - para verificar que funciona
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

// Ruta de prueba
Route::get('/test', function () {
    return response()->json(['message' => 'API está funcionando']);
});

// Rutas de denuncias
Route::post('/denuncias', [\App\Http\Controllers\DenunciaController::class, 'store']);
Route::get('/denuncias/{folio}', [\App\Http\Controllers\DenunciaController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/denuncias', [\App\Http\Controllers\DenunciaController::class, 'index']);
});