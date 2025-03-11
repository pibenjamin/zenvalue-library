<?php

namespace App\Notifications;

use App\Models\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class BookAddedToCatalogue extends Notification
{
    use Queueable;

    public function __construct(
        public Book $book
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("['".config('app.name')."] 📚 Votre livre a été ajouté au catalogue")
            ->view('emails.books.added-to-catalogue', [
                'book' => $this->book,
                'user' => $this->book->owner
            ]);
    }
} 