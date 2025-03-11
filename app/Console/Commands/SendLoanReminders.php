<?php

namespace App\Console\Commands;

use App\Services\LoanReminderService;
use App\Notifications\LoanDueReminder;
use App\Notifications\LoanOverdueReminder;
use App\Notifications\UrgentOverdueNotification;
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Loan;

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

        if($loans->count() === 0) {
            $this->info("No loans due for first reminder");
            return;
        }

        $this->info(" Start sendFirstReminders");

        $this->info("Sending first notification for " . $loans->count() . " loans");
        
        foreach ($loans as $loan) {
            $daysUntilDue = (int) floor(now()->diffInDays($loan->to_be_returned_at));
            $loan->borrower->notify(new LoanDueReminder($loan, $daysUntilDue));
            
            // Update the reminder sent timestamp
            $loan->first_reminder_sent_at = now();
            $loan->save();
            
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

        if($loans->count() === 0) {
            $this->info("No loans due for recurring reminder");
            return;
        }

        $this->info(" Start sendRecurringReminders");
        
        foreach ($loans as $loan) {
            $daysOverdue = (int) floor($loan->to_be_returned_at->diffInDays(now()));
            $loan->borrower->notify(new LoanOverdueReminder($loan, $daysOverdue));

            // Update the recurring_reminder_sent_at timestamp
            $loan->last_recurring_reminder_sent_at = now();
            $loan->status = Loan::STATUS_OVERDUE;
            $loan->save();
            
            $this->info(sprintf(
                "Sent recurring reminder for loan #%d | Book: %s (#%d) | User: %s (#%d) | Overdue: %d days",
                $loan->id,
                $loan->book->title,
                $loan->book->id,
                $loan->borrower->email,
                $loan->borrower->id,
                $daysOverdue
            ));
        }
    }

    protected function sendUrgentNotifications()
    {
        $loans      = $this->reminderService->getLoansOverdueByMonth();

        if($loans->count() === 0) {
            $this->info("No loans due for urgent notification");
            return;
        }

        $this->info(" Start sendUrgentNotifications");

        $librarians = User::whereIn('role_id', [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN])->get();
        
        foreach ($loans as $loan) {
            foreach ($librarians as $librarian) {
                $librarian->notify(new UrgentOverdueNotification($loan));
            }

            // Update the urgent_notification_sent_at timestamp
            $loan->urgent_notification_sent_at = now();
            $loan->save();
            
            $this->info(sprintf(
                "Sent urgent notification for loan #%d | Book: %s (#%d) | User: %s (#%d)",
                $loan->id,
                $loan->book->title,
                $loan->book->id,
                $loan->borrower->email,
                $loan->borrower->id
            ));
        }
    }
} 