<?php

use App\Http\Controllers\DenunciaController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    // DENUNCIAS - Rutas públicas
    Route::post('/denuncias', [DenunciaController::class, 'store']); // Crear denuncia (anonima o identificada)
    Route::get('/denuncias/{folio}', [DenunciaController::class, 'show']);// Consultar denuncia por folio

    // DENUNCIAS - Rutas protegidas (autenticación requerida)
    Route::middleware('auth:sanctum')->group(function () {
    Route::get('/denuncias', [DenunciaController::class, 'index']); // Listar denuncias (usuario autenticado)
    });
    
});