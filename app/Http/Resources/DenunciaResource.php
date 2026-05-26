<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DenunciaResource extends JsonResource

{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'folio' => $this->folio,
            'tipo_denunciante' => $this->tipo_denunciante,
            'nombre_denunciante' => $this->nombre_denunciante,
            'correo_denunciante' => $this->correo_denunciante,
            'titulo_denuncia' => $this->titulo_denuncia,
            'descripcion_denuncia' => $this->descripcion_denuncia,
            'estado' => $this->estado,
            'prioridad' => $this->prioridad,
            'fecha_recibida' => $this->fecha_recibida?->format('Y-m-d H:i:s'),
            'fecha_ultimaActualizacion' => $this->fecha_ultimaActualizacion?->format('Y-m-d H:i:s'),
            'dependencia_implicada' => $this->dependencia_implicada,
            'lugar_hechos' => $this->lugar_hechos,
        ];
    }
}