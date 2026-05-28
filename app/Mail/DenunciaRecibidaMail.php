<?php
namespace App\Mail;

use App\Models\Denuncia;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailable\Content;
use Illuminate\Mail\Mailable\Envelope;
Use Illuminate\Queue\SerializesModels;

class DenunciaRecibidaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Denuncia $denuncia) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Acuse de Recibo - Denuncia {$this->denuncia->folio}", 
        );
    }

    public function content(): content
    {
        return new Content(
            view: 'emails.denuncia-recibida',
            with: [
                'denuncia' => $this->denuncia, 'acuse_url' => config('app.frontend_url') . "/denuncias/consultar?folio= {$this->denuncia->folio}"
            ],
        );
    }
}