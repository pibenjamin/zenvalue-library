<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Loan;

class LoanDueReminder extends Notification
{
    use Queueable;

    protected $loan;
    protected $daysUntilDue;

    public function __construct(Loan $loan, $daysUntilDue)
    {
        $this->loan = $loan;
        $this->daysUntilDue = $daysUntilDue;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Rappel de retour de livre')
            ->line('Ceci est un rappel concernant un livre à retourner prochainement.')
            ->line('Livre : ' . $this->loan->book->title)
            ->line('Date de retour : ' . $this->loan->to_be_returned_at->format('d/m/Y'))
            ->line('Jours restants : ' . $this->daysUntilDue)
            ->action('Voir mes emprunts', url('/loans'));
    }
} 