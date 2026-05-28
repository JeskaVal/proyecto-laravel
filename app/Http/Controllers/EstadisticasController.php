<?php

namespace App\Http\Controllers;

use App\Services\EstadisticasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EstadisticasController extends Controller
{
    private EstadisticasSercive $estadisticasService;

    public function __construct(EstadisticasService $estadisticasService)
    {
        $this->estadisticasService = $estadisticasService;
    }

    //GET /api/dashboard - Dashboard para admin
    public function dashboard()
    {
        $this->authorize('viewAdminDashboard', 'Denuncia');

        return response()->json([
            'success' => true,
            'data' => $this->estadisticasService->obtenerDashboardAdmin()
        ]);
    }

    //GET /api/reportes - Obtener reporte
    public function reporte(Request $request)
    {
        $this->authorize('viewReports', 'Denuncia');

        $request->validate([
            'fecha_desde' => 'nullable|date',
            'fecha_hasta' => 'nullable|date|after_or_equal:fecha_desde'
        ]);

        return response()->json([
            'success' => true,
            'data' => $this->estadisticasService->obtenerReporte
            (
                $request->input('fecha_desde'),
                $request->input('fecha_hasta')
            )
        ]);
    }

    // GET /api/exportar-reporte - Exportar reporte en Excel
    public function exportarReporte(Request $request)
    {
        $this->authorize('viewReports', 'Denuncia');

        // Se implementara con Laravel Excel o similar, por el momento retorna JSON

        return response()->json([
            'message' => 'Exportación en desarrollo'
        ]);
    }
}
