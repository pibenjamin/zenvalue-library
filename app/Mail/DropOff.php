<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DropOff extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $bookTitle,
        public readonly \Carbon\Carbon $dropOffAt,
        public readonly string $ownerName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Livre déposé 📚',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.books.drop-off',
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
