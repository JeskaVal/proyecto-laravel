<?php

namespace App\Jobs;

use App\Mail\ActualizacionDenunciaMail;
use App\Models\Denuncia;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EnviarActualizacionDenuncia implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Denuncia $denuncia,
        public string $accion,
        public string $descripcion
        ) {}

    public function handle(): void
    {
        if ($this->denuncia->correo_denunciante && $this->denuncia->tipo_denunciante === 'identificado') {
            Mail::to
            ($this->denuncia->correo_denunciante)->send(new DenunciaRecibidaMail (
                $this->denuncia,
                $this->accion,
                $this->descripcion
                ));
        }
    }
}