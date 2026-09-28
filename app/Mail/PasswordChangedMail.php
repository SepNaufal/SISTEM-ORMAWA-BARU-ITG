<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordChangedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $sumber = 'permintaan Anda sendiri'
    ) {
    }

    public function envelope(): Envelope
    {
        $fromAddress = config('mail.from.address', 'noreply@itg.ac.id');
        $fromName = config('mail.from.name', 'SKIN ITG - Sistem Informasi Kemahasiswaan');

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            subject: 'Password Akun SKIN ITG Anda Telah Diubah',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password_changed',
        );
    }
}
