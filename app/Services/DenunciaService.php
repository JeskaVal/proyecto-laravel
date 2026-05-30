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
    public function __construct(private ContrasenaAccesoService $contrasenaService) {}

    public function crearDenuncia(array $datos, $usuario_id = null, $ip = null, $navegador = null): array
    {
        return DB::transaction(function () use ($datos, $usuario_id, $ip, $navegador) {
            $folio = Denuncia::generarFolio();
            $contrasena = $this->contrasenaService->generarContrasena();

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
                'contrasena_acceso' => $this->contrasenaService->hashear($contrasena),
            ]);

            DenunciaBitacora::create([
                'denuncia_id' => $denuncia->id,
                'accion' => 'Denuncia creada',
                'descripcion' => 'Denuncia inicial recibida',
                'estado_anterior' => null,
                'estado_nuevo' => 'recibida',
                'usuario_id' => $usuario_id,
                'fecha_accion' => Carbon::now(),
            ]);

            EnviarNotificacionDenuncia::dispatch($denuncia, $contrasena);

            return ['denuncia' => $denuncia, 'contrasena' => $contrasena];
        });
    }

    public function obtenerDenuncia($folio): Denuncia
    {
        return Denuncia::where('folio', $folio)->firstOrFail();
    }

    public function actualizarEstado(Denuncia $denuncia, $estado_nuevo, $descripcion, $usuario_id = null): Denuncia
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

            EnviarActualizacionDenuncia::dispatch($denuncia, "Estado: {$estado_anterior} → {$estado_nuevo}", $descripcion);
        });

        return $denuncia->refresh();
    }
}
