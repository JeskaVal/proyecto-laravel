<?php

namespace App\Mail;

use App\Models\Denuncia;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DenunciaRecibidaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Denuncia $denuncia,
        public string $contrasenaPlana
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Acuse de Recibo - Denuncia {$this->denuncia->folio}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.denuncia-recibida',
            with: [
                'denuncia' => $this->denuncia,
                'contrasena' => $this->contrasenaPlana,
                'acuse_url' => config('app.frontend_url') . "/denuncias/consultar?folio={$this->denuncia->folio}",
            ],
        );
    }
}
