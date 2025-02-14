<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Loan;
use App\Models\User;
use App\Models\Book;
use App\Services\LoanReminderService;
use App\Notifications\LoanDueReminder;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\Test;
use SolutionForest\FilamentAccessManagement\Support\Utils;

class LoanRemindersTest extends TestCase
{
    use RefreshDatabase;

    private LoanReminderService $reminderService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reminderService = app(LoanReminderService::class);
        Notification::fake();
        Log::spy();
    }

    #[Test]
    public function test_it_sends_first_reminder_when_loan_is_due_in_three_days()
    {
        // Given a loan is due in 3 days and was created 27 days ago
        $borrower = User::factory()->create();
        $book = Book::factory()->create();
        $loan = Loan::factory()->create([
            'borrower_id' => $borrower->id,
            'book_id' => $book->id,
            'created_at' => now()->subDays(27),
            'to_be_returned_at' => now()->addDays(3),
            'status' => 'in_progress'
        ]);

        // When the reminder system runs
        $this->artisan('loans:send-reminders');

        // Then a first reminder should be sent to the borrower
        Notification::assertSentTo(
            $borrower,
            LoanDueReminder::class,
            function ($notification) use ($loan) {
                return $notification->loan->id === $loan->id
                    && $notification->daysUntilDue === 3;
            }
        );

        // And the system should log the reminder
        Log::shouldHaveReceived('info')
            ->with("Sent first reminder for loan #{$loan->id}")
            ->once();
    }
} 