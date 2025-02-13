<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use App\User;
use App\Models\Book;
use App\Models\Loan;

class LoanConfirmation extends Mailable
{
    public $user;
    public $book;
    public $loan;
    public $activeLoans;
    public $borrowDate;
    public $returnDate;

    public function __construct(User $user, Book $book, Loan $loan)
    {
        $this->user = $user;
        $this->book = $book;
        $this->loan = $loan;
        $this->borrowDate = $loan->borrowed_at;
        $this->returnDate = $loan->to_be_returned_at;
        $this->activeLoans = $user->loans()->where('returned_at', null)->with('book')->get();
    }

    public function build()
    {
        return $this->subject(config('app.name') . " - Confirmation d'emprunt : " . $this->book->title)
            ->markdown('emails.loan-confirmation')
            ->with([
                'user' => $this->user,
                'book' => $this->book,
                'loan' => $this->loan,
                'borrowDate' => $this->borrowDate,
                'returnDate' => $this->returnDate,
                'activeLoans' => $this->activeLoans,
            ]);
}
} 