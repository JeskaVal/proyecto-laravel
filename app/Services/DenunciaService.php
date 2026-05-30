<?php

namespace App\Services;

use App\Models\Denuncia;
use App\Models\DenunciaBitacora;
use App\Jobs\EnviarActualizacionDenuncia;
use App\Jobs\EnviarNotificacionDenuncia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DenunciaService
{
    public function crearDenuncia(array $datos, $usuario_id = null, $ip = null, $navegador = null)
    {
        return DB::transaction(function () use ($datos, $usuario_id, $ip, $navegador) {
            // Generar folio único
            $folio = Denuncia::generarFolio();
            // Crear denuncia
            $denuncia = Denuncia::create([
                'folio' => $folio,
                'tipo_denunciante' => $datos['tipo_denunciante'],
                'nombre_denunciante' => $datos['nombre_denunciante'] ?? null,
                'correo_denunciante' => $datos['correo_denunciante'] ?? null,
                'telefono_denunciante' => $datos['telefono_denunciante'] ?? null,
                'tipo_identificacion' => $datos['tipo_identificacion'] ?? null,
                'numero_identificacion' => $datos['numero_identificacion'] ?? null,
                'titulo_denuncia' => $datos['titulo_denuncia'],
                'descripcion_denuncia' => $datos['descripcion_denuncia'],
                'fecha_hechos' => $datos['fecha_hechos'] ?? null,
                'lugar_hechos' => $datos['lugar_hechos'] ?? null,
                'dependencia_implicada' => $datos['dependencia_implicada'] ?? null,
                'prioridad' => $datos['prioridad'] ?? 'media',
                'estado' => 'recibida',
                'fecha_recibida' => Carbon::now(),
                'usuario_id' => $usuario_id,
                'ip_origen' => $ip,
                'navegador' => $navegador,
            ]);

            // Registrar en bitácora
            DenunciaBitacora::create([
                'denuncia_id' => $denuncia->id,
                'accion' => 'Denuncia creada',
                'descripcion' => 'Denuncia inicial recibida',
                'estado_anterior' => null,
                'estado_nuevo' => 'recibida',
                'usuario_id' => $usuario_id,
                'fecha_accion' => Carbon::now(),
            ]);

            EnviarNotificacionDenuncia::dispatch($denuncia);
            
            return $denuncia;

        });
    }

    public function obtenerDenuncia($folio)
    {
        return Denuncia::where('folio', $folio)->firstOrFail();
    }

    public function actualizarEstado(Denuncia $denuncia, $estado_nuevo, $descripcion, $usuario_id = null)
    {
        $estado_anterior = $denuncia->estado;

        DB::transaction(function () use ($denuncia, $estado_nuevo, $descripcion, $estado_anterior, $usuario_id) {
            $denuncia->update([
                'estado' => $estado_nuevo,
                'fecha_ultimaActualizacion' => Carbon::now(),
            ]);

            DenunciaBitacora::create([
                'denuncia_id' => $denuncia->id,
                'accion' => "Estado cambiado a {$estado_nuevo}",
                'descripcion' => $descripcion,
                'estado_anterior' => $estado_anterior,
                'estado_nuevo' => $estado_nuevo,
                'usuario_id' => $usuario_id,
                'fecha_accion' => Carbon::now(),
            ]);

            // Enviar notificacion de actualización
            EnviarActualizacionDenuncia::dispatch ($denuncia, "Estado: {$estado_anterior} → {$estado_nuevo}", $descripcion);
        });

        return $denuncia->refresh();
    }
}