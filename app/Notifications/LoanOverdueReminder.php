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
            ->subject("Rappel : Retour de livre en retard")
            ->view('emails.loans.overdue-reminder', [
                'loan' => $this->loan,
                'daysOverdue' => $this->daysOverdue
            ]);
    }
} 