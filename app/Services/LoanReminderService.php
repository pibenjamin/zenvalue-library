<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Loan;

class LoanReminderService
{
    /**
     * Get loans that need their first reminder (due in next 3 days)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLoansDueForFirstReminder()
    {
        return Loan::query()
            ->whereBetween('to_be_returned_at', [Carbon::now(), Carbon::now()->addDays(3)])
            ->whereNull('returned_at')
            ->with(['book', 'borrower']) // Eager load relationships
            ->get();
    }

    /**
     * Get loans that need recurring reminders (overdue and divisible by 3 days)
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLoansNeedingRecurringReminder()
    {
        return Loan::query()
            ->where('to_be_returned_at', '<', Carbon::now())
            ->whereNull('returned_at')
            ->whereRaw('DATEDIFF(NOW(), to_be_returned_at) % 3 = 0')
            ->with(['book', 'borrower']) // Eager load relationships
            ->get();
    }

    /**
     * Get loans that are overdue by more than a month
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLoansOverdueByMonth()
    {
        return Loan::query()
            ->where('to_be_returned_at', '<', Carbon::now()->subMonth())
            ->whereNull('returned_at')
            ->with(['book', 'borrower']) // Eager load relationships
            ->get();
    }
} 