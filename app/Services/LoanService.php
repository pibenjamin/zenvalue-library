<?php

namespace App\Services;

use App\Mail\LoanConfirmed; 
use App\Mail\UserSignaledReturn;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Mail\AdminConfirmReturn;



class LoanService
{
    public function extendLoan(Loan $loan, int $months = 1): void
    {
        $loan->to_be_returned_at = $loan->to_be_returned_at->addMonths(1);
        $loan->extended_for = $months;
        $loan->save();
    }

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

            Log::error('Erreur lors de l\'emprunt : ' . $e->getMessage());
            Notification::make()
                ->title("Erreur lors de l'emprunt du livre")
                ->danger()
                ->send();
        }
    }


    public function validateReturn(Loan $loan)
    {
        $token = $loan->token;
                
        $loan->update([
            'returned_at'                   => now(),
            'return_confirmed_by'           => auth()->id(),
            'return_confirmation_token'     => null,
            'status'                        => Loan::STATUS_RETURNED  // Ajout de cette ligne
        ]);

        $user = User::find($loan->borrower_id);
        $book = Book::find($loan->book_id);

        $book->update([
            'is_borrowed' => false
        ]);

        Mail::to($user->email)->send(new AdminConfirmReturn(
            userName: $user->name,
            bookTitle: $book->title,
            returnedAt: $loan->returned_at
        ));
    }    

    public function userSignaleReturn(Loan $loan): void
    {
        if (!$loan) 
        {
            Notification::make()
                ->title('Nous n\'avons retrouvé ce prêt')
                ->danger()
                ->send();
            return;
        }

        if(!$user = $loan->borrower){
                Notification::make()
                    ->title('Nous n\'avons pas trouvé d\'utilisateur pour ce prêt')
                    ->danger()
                    ->send();
                return;
        }

        if(!$book = $loan->book){
            Notification::make()
                ->title('Nous n\'avons pas trouvé de livre pour ce prêt')
                ->danger()
                ->send();
            return;
        }

        $token = hash('sha256', Str::random(32) . time() . config('app.key'));

        $loan->returned_at = now();
        $loan->status = Loan::STATUS_RETURN_IN_PROGRESS;
        $loan->return_confirmation_token = $token;
        $loan->save();

        Notification::make()
            ->title('Retour de prêt en attente de validation par l\'administrateur')
            ->success()
            ->send();

        $returnedAt = $loan->returned_at;
        $adminEmail = config('app.admin_email');

        Mail::to($adminEmail)->send(new UserSignaledReturn(
            userName: $user->name,
            bookTitle: $book->title,
            returnedAt: $returnedAt,
            validationToken: $token
        ));
    }

    public function getLoanCountsByStatus(?int $borrowerId = null): array
    {
        $query = Loan::query();
        
        if ($borrowerId) {
            $query->where('borrower_id', $borrowerId);
        }

        return [
            'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'returned' => (clone $query)->where('status', 'returned')->count(),
        ];
    }
} 