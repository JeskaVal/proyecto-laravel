<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BienvenidaMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;

    public function __construct()
    {
        $this->user = $user;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '¡Bienvenido(a) al Sistema de Denuncias Electrónico!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.bienvenida',
            with: [
                'nombre' => $this->user->name,
                'correo' => $this->user->email,
                'login_url' => config('app.frontend_url') . '/login',
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
