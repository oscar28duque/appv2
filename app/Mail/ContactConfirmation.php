<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Crea una nueva instancia del correo de confirmación al usuario.
     */
    public function __construct(
        public string $name,
        public string $email,
        public string $userMessage
    ) {}

    /**
     * Asunto del correo de confirmación.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Hemos recibido tu mensaje - ' . config('app.name')
        );
    }

    /**
     * Vista Blade de confirmación.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-confirmation',
            with: [
                'name' => $this->name,
                'email' => $this->email,
                'userMessage' => $this->userMessage,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
