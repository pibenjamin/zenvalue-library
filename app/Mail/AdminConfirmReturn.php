<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminConfirmReturn extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $userName,
        public readonly string $bookTitle,
        public readonly \Carbon\Carbon $returnedAt,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Retour de livre validé 📚',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.loans.return-validated',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
