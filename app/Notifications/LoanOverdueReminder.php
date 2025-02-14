<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Loan;

class LoanOverdueReminder extends Notification
{
    use Queueable;

    protected $loan;
    protected $daysOverdue;

    public function __construct(Loan $loan, $daysOverdue)
    {
        $this->loan = $loan;
        $this->daysOverdue = $daysOverdue;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Rappel : Livre en retard')
            ->line('Ceci est un rappel concernant un livre en retard.')
            ->line('Livre : ' . $this->loan->book->title)
            ->line('Date de retour prévue : ' . $this->loan->to_be_returned_at->format('d/m/Y'))
            ->line('Jours de retard : ' . $this->daysOverdue)
            ->action('Déclarer un retour', url('/loans/return/' . $this->loan->id));
    }
} 