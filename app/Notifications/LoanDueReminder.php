<?php

namespace App\Notifications;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class LoanDueReminder extends Notification
{
    use Queueable;

    public function __construct(
        public Loan $loan,
        public int $daysUntilDue
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Rappel : Retour de livre dans {$this->daysUntilDue} jours")
            ->view('emails.loans.due-reminder', [
                'loan' => $this->loan,
                'daysUntilDue' => $this->daysUntilDue
            ]);
    }
} 