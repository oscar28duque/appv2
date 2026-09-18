<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Crea una nueva instancia del mensaje con los datos del contacto.
     */
    public function __construct(
        public string $name,
        public string $email,
        public string $userMessage
    ) {}

    /**
     * Configuración del sobre del correo (asunto).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuevo mensaje de contacto - ' . config('app.name'),
            replyTo: [$this->email]
        );
    }

    /**
     * Definición del contenido y la vista Blade asociada.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-message',
            with: [
                'name' => $this->name,
                'email' => $this->email,
                'userMessage' => $this->userMessage,
            ]
        );
    }

    /**
     * Adjuntos (en caso de requerirse).
     */
    public function attachments(): array
    {
        return [];
    }
}
