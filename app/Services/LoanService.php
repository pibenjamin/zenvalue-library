<?php

namespace App\Services;

use App\Mail\LoanConfirmed;
use App\Models\Book;
use App\Models\Loan;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


class LoanService
{
    public function borrowBook(Book $book): void
    {
        if ($book->is_borrowed) 
        {
            Notification::make()
                ->title('Ouvrage déjà emprunté')
                ->danger()
                ->send();
            return;
        }

        $user = Auth::user();

        $currentLoans = $user->loans()->where('returned_at', null)->count();
        $maxLoans = config('app.max_loans');

        if (!$user->canBorrow()) 
        {
            Notification::make()
                ->title("Nombre de prêts autorisés dépassé ($currentLoans/$maxLoans)")
                ->danger()
                ->send();
            return;
        }

        try {
            $loan = Loan::create([
                'book_id' => $book->id,
                'borrower_id' => $user->id,
                'borrowed_at' => now(),
                'to_be_returned_at' => now()->addMonths(1),
                'status' => Loan::STATUS_IN_PROGRESS,
            ]);

            $book->update(['is_borrowed' => true]);

            // Envoi de l'email de confirmation
            Mail::to($user->email)->send(new LoanConfirmed($user, $book, $loan));

            Notification::make()
                ->title("Le livre \"{$book->title}\" a été emprunté avec succès")
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title("Erreur lors de l'emprunt du livre")
                ->danger()
                ->send();
        }
    }
} 