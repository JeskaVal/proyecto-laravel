<?php

namespace App\Mail;

use App\Models\Denuncia;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ActualizacionDenunciaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Denuncia $denuncia,
        public string $accion,
        public string $descripcion
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Actualización en tu Denuncia - {$this->denuncia->folio}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.actualizacion-denuncia', with: [
                'denuncia' => $this->denuncia,
                'accion' => $this->accion,
                'descripcion' => $this->descripcion,
                'consulta_url' => config('app.frontend_url') . "/denuncias/consultar?folio= {$this->denuncia->folio}"
            ],
        );
    }
}