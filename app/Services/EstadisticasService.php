<?php

namespace App\Services;

use App\Models\Denuncia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EstadisticasService
{
    public function obtenerDashboardAdmin()
    {
        return [
            'totales' => $this->obtenerTotales(),
            'por_estado' => $this->obtenerPorEsstado(),
            'por_prioridad' => $this->obtenerPorPrioridad(),
            'ultimas_7_dias' => $this->obtenerUltimas7Dias(),
            'promedio_tiempo_resolucion' => $this->obtenerPromedioResolucion(),
            'denuncias_recientes' => $this->obtenerDenunciasRecientes(5),
        ];
    }

    private function obtenerTotales()
    {
        return [
            'total_denuncias' => Denuncia::count(),
            'denuncias_recibidas_hoy' => Denuncia::whereDate('created_at', today())->count(),
            'denuncias_resueltas' => Denuncia::where('estado','resuelta')->count(),
            'denuncias_en_proceso' => Denuncia::whereIn('estado', ['recibida', 'en_revision', 'en_proceso'])->count(),
        ];
    }

    private function obtenerPorEstado()
    {
        return Denuncia::groupBy('estado')
        ->select('estado', DB::raw('count(*) as cantidad'))
        ->get()
        ->map(function ($item) {
            return [
                'estado' => $item->estado,
                'cantidad' => $item->cantidad,
                'porcentaje' => round(($item->cantidad / Denuncia::count()) * 100, 2)
            ];
        });
    }

    private function obtenerPorPrioridad()
    {
        return Denuncia::groupBy('prioridad')
        ->select('prioridad', DB::raw('count(*) as cantidad'))
        ->get()
        ->map(function ($item) {
            return [
                'prioridad' => $item->prioridad,
                'cantidad' => $item->cantidad,
            ];
        });
    }

    private function obtenerUltimas7Dias()
    {
        $data = [];
        for ( $i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i)->format('Y-m-d');
            $cantidad = Denuncia::whereDate('created_at', $fecha)
            ->count();
            $data[] = [
                'fecha' => $fecha,
                'cantidad' => $cantidad
            ];
        }
        return $data;
    }

    private function obtenerPromedioResolucion()
    {
        $denunciasResueltas = Denuncia::where('estado', 'resuelta')
        ->whereNotNull('fecha_ultimaActualizacion')
        ->get();

        if ($denunciasResueltas->isEmpty()) {
            return 0;
        }

        $totalDias = $denunciasResueltas->sum(function ($denuncia) {
            return $denuncia->fecha_ultimaActualizacion->diffInDays($denuncia->fecha_recibida);
        });

        return round($totalDias / $denunciasResueltas->count(), 2);
    }

    private function obtenerDenunciasRecientes($limite = 5)
    {
        return Denuncia::with('usuario:id,name,email')
        ->orderBy('created_at', 'desc')
        ->limit($limite)
        ->get()
        ->map(function ($denuncia) {
            return [
                'folio' => $denuncia->folio,
                'titulo' => $denuncia->titulo_denuncia,
                'estado' => $denuncia->estado,
                'prioridad' => $denuncia->prioridad,
                'fecha_recibida' => $denuncia->fecha_recibida,
            ];
        });
    }

    public function obtenerReporte($fecha_desde = null, $fecha_hasta = null)
    {
        $query = Denuncia::query();
        if ($fecha_desde) {
            $query->whereDate('fecha_recibida', '>=', $fecha_desde);
        }

        if ($fecha_hasta) {
            $query->whereDate('fecha_recibida', '<=', $fecha_hasta);
        }

        return [
            'periodo' => [
                'desde' => $fecha_desde ?? 'Inicio',
                'hasta' => $fecha_hasta ?? 'Hoy'
            ],
            'resumen' => [
                'total_denuncias' => $query->count(),
                'denuncias_anonimas' => $query->where('tipo_denunciante', 'anonimo')->count(),
                'denuncias_identificadas' => $query->where('tipo_denunciante', 'identificado')->count(),
                'por_prioridad' => $this->obtenerPorPrioridad(),
                'por_estado' => $this->obtenerPorEstado(),
                'dependencias_principales' => $this->obtenerDependenciasTop($query)
            ]
        ];
    }

    private function obtenerDependenciasTop($query)
    {
        return $query
        ->whereNotNull('dependencia_implicada')
        ->groupBy('dependencia_implicada')
        ->select('dependencia_implicada', DB::raw('count(*) as cantidad'))
        ->orderBy('cantidad', 'desc')
        ->limit(5)
        ->get();
    }
}