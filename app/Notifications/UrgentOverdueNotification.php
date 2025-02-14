<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Loan;

class UrgentOverdueNotification extends Notification
{
    use Queueable;

    protected $loan;

    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('URGENT : Livre très en retard')
            ->line('Un livre est très en retard (plus d\'un mois) :')
            ->line('Livre : ' . $this->loan->book->title)
            ->line('Emprunteur : ' . $this->loan->borrower->name)
            ->line('Date de retour prévue : ' . $this->loan->to_be_returned_at->format('d/m/Y'))
            ->line('Jours de retard : ' . $this->loan->to_be_returned_at->diffInDays(now()))
            ->action('Voir les détails', url('/admin/loans/' . $this->loan->id));
    }
} 