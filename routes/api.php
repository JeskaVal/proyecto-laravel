<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    // DENUNCIAS - Rutas públicas
    Route::post('/denuncias', [DenunciaController::class, 'store']);
    Route::get('/denuncias/{folio}', [DenunciaController::class, 'show']);
    Route::get('/denuncias/{folio}/archivos', [DenunciaController::class, 'obtenerArchivos']);
    Route::get('/denuncias/{folio}/bitacora', [DenunciaController::class, 'obtenerBitacora']);

    // DENUNCIAS - Rutas protegidas
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/denuncias', [DenunciaController::class, 'index']);
        Route::post('/denuncias/{folio}/archivos', [DenunciaController::class, 'subirArchivo']);
        Route::get('/denuncias/buscar', [DenunciaController::class, 'buscar']);
        Route::get('/denuncias/archivos/{archivo}/descargar', [DenunciaController::class, 'descargarArchivo']);
        
        // Solo para admins
        Route::middleware('can:update,App\Models\Denuncia')->group(function () {
            Route::put('/denuncias/{folio}/estado', [DenunciaController::class, 'cambiarEstado']);
            Route::get('/dashboard', [EstadisticasController::class, 'dashboard']);
            Route::get('/reportes', [EstadisticasController::class, 'reporte']);
            Route::get('/exportar-reporte', [EstadisticasController::class, 'exportarReporte']);
        });
    });
});