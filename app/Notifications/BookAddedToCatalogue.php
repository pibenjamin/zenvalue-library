<?php

namespace App\Notifications;

use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
class BookAddedToCatalogue extends Notification
{
    use Queueable;

    public function __construct(
        public Book $book,
        public ?string $qrCodeFile = null
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject("[".config('app.name')."] 📚 Votre livre a été ajouté au catalogue")
            ->view('emails.books.added-to-catalogue', [
                'book' => $this->book,  
                'user' => $this->book->owner,
                'qrCodeUrl' => $this->qrCodeFile ? asset('storage/qr-codes/' . $this->qrCodeFile) : null
            ]);

        if ($this->qrCodeFile) {
            $message->attach(Attachment::fromPath(storage_path('app/public/qr-codes/' . $this->qrCodeFile))
                ->as('qr-code.png')
                ->withMime('image/png'));
        }

        return $message;
    }

    /**
    * Get the attachments for the message.
    *
    * @return array<int, \Illuminate\Mail\Mailables\Attachment>
    */
    public function attachments(): array
    {
        return [
            Attachment::fromStorage('qr-codes/' . $this->qrCodeFile)
                ->as('qr-code.png')
                ->withMime('image/png'),
        ];
    }




} 