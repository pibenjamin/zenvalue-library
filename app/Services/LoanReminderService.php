<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Loan;
use Illuminate\Database\Eloquent\Collection;

class LoanReminderService
{
    /**
     * Get loans that need their first reminder
     * 
     * Selection criteria:
     * - Loan is in progress ('in_progress')
     * - No first reminder has been sent (first_reminder_sent_at is NULL)
     * - Return date is exactly 3 days from now
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of loans needing first reminder
     */
    public function getLoansDueForFirstReminder(): Collection
    {
        return Loan::query()
            // Select only loans in progress
            ->where('status', 'in_progress')
            // Check that no first reminder has been sent
            ->whereNull('first_reminder_sent_at')
            // Select loans that need to be returned in exactly 3 days
            ->whereDate('to_be_returned_at', '=', now()->addDays(3))
            ->get();
    }

    /**
     * Get loans that need recurring reminders
     * 
     * Selection criteria:
     * - Loan is overdue (return date is in the past)
     * - Loan has not been returned yet
     * - Days overdue is divisible by the configured reminder interval
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of loans needing recurring reminder
     */
    public function getLoansNeedingRecurringReminder()
    {
        return Loan::query()
            // Select overdue loans
            ->where('to_be_returned_at', '<', Carbon::now())
            // Not returned yet
            ->whereNull('returned_at')
            // Check if days overdue matches reminder interval
            ->whereRaw('DATEDIFF(NOW(), to_be_returned_at) % ' . config('app.recurring_late_loan_reminder_days') . ' = 0')
            // Eager load related models
            ->with(['book', 'borrower'])
            ->get();
    }

    /**
     * Get loans that are severely overdue
     * 
     * Selection criteria:
     * - Loan is overdue by more than a month
     * - Loan has not been returned yet
     * 
     * Used for urgent notifications to librarians
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of severely overdue loans
     */
    public function getLoansOverdueByMonth()
    {
        return Loan::query()
            // Select loans overdue by more than a month
            ->where('to_be_returned_at', '<', Carbon::now()->subMonth())
            // Not returned yet
            ->whereNull('returned_at')
            // Eager load related models
            ->with(['book', 'borrower'])
            ->get();
    }
} 