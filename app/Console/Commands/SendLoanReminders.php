<?php

namespace App\Console\Commands;

use App\Services\LoanReminderService;
use App\Notifications\LoanDueReminder;
use App\Notifications\LoanOverdueReminder;
use App\Notifications\UrgentOverdueNotification;
use Illuminate\Console\Command;
use App\Models\User;

class SendLoanReminders extends Command
{
    protected $signature = 'loans:send-reminders';
    protected $description = 'Send reminders for due and overdue loans';

    protected $reminderService;

    public function __construct(LoanReminderService $reminderService)
    {
        parent::__construct();
        $this->reminderService = $reminderService;
    }

    public function handle()
    {
        $this->sendFirstReminders();
        $this->sendRecurringReminders();
        $this->sendUrgentNotifications();
    }

    protected function sendFirstReminders()
    {
        $loans = $this->reminderService->getLoansDueForFirstReminder();
        
        foreach ($loans as $loan) {
            $daysUntilDue = now()->diffInDays($loan->to_be_returned_at);
            $loan->borrower->notify(new LoanDueReminder($loan, $daysUntilDue));
            
            // Update the reminder sent timestamp
            $loan->update(['first_reminder_sent_at' => now()]);
            
            // Log the reminder details with:
            // - Loan ID
            // - Book title and ID
            // - Borrower email and ID
            // - Due date in Y-m-d format
            $this->info(sprintf(
                "Sent first reminder for loan #%d | Book: %s (#%d) | User: %s (#%d) | Due: %s",
                $loan->id,
                $loan->book->title,
                $loan->book->id,
                $loan->borrower->email,
                $loan->borrower->id,
                $loan->to_be_returned_at->format('Y-m-d')
            ));
        }
    }

    protected function sendRecurringReminders()
    {
        $loans = $this->reminderService->getLoansNeedingRecurringReminder();
        
        foreach ($loans as $loan) {
            $daysOverdue = $loan->to_be_returned_at->diffInDays(now());
            $loan->borrower->notify(new LoanOverdueReminder($loan, $daysOverdue));
            
            $this->info("Sent recurring reminder for loan #{$loan->id}");
        }
    }

    protected function sendUrgentNotifications()
    {
        $loans = $this->reminderService->getLoansOverdueByMonth();
        $librarians = User::where('role_id', 3)->get();
        
        foreach ($loans as $loan) {
            foreach ($librarians as $librarian) {
                $librarian->notify(new UrgentOverdueNotification($loan));
            }
            
            $this->info("Sent urgent notification for loan #{$loan->id} to librarians");
        }
    }
} 