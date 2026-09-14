<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPasswordCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $code;
    public string $name;

    public function __construct(string $code, string $name = 'Pengguna')
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode Verifikasi Lupa Password - BookStore',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reset_password_code',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
