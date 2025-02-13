<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Mail\AdminConfirmReturn;
use App\Models\Book;


class LoanController extends Controller
{
    public function myLoans(Request $request)
    {
        $user = Auth::user();
        $query = $user->loans()
            ->with('book');

        // Filtre par statut
        if ($request->has('status')) {
            switch ($request->status) {
                case 'active':
                    $query->whereNull('returned_at');
                    break;
                case 'returned':
                    $query->whereNotNull('returned_at');
                    break;
                case 'overdue':
                    $query->whereNull('returned_at')
                         ->where('to_be_returned_at', '<', now());
                    break;
            }
        }

        $loans = $query->orderBy('created_at', 'desc')->get();

        return view('vendor.voyager.loans.my-loans', compact('loans'));
    }

//    public function signalReturn(Request $request)
//    {
//        $loan = Loan::findOrFail($request->loan_id);
        
        // Générer un token unique
//        $token = hash_hmac('sha256', $loan->id . $loan->book_id . time(), config('app.key'));
        
        // Marquer comme "retour en cours"
//        $loan->update([
//            'return_signaled_at' => now(),
//            'return_confirmation_token' => $token,
//            'status' => Loan::STATUS_RETURN_IN_PROGRESS  // Utilisation de la constante
//        ]);

//        // Envoyer l'email au gestionnaire
//        $librarians = User::where('role_id', 3)->get();

//        foreach($librarians as $librarian) {
//            Mail::to($librarian->email)->send(new ReturnSignaled($loan));
//        }

//        return response()->json([
//            'message' => 'Retour signalé avec succès. Un bibliothécaire va confirmer le retour.',
//            'success' => true
//        ]);
//    }

    public function validateReturn(Request $request)
    {
        $token = $request->token;

        if(!$loan = Loan::where('return_confirmation_token', $token)->first())
        {
            abort(403, 'Token de confirmation invalide');
        }
                
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

        return $book->title . ' a bien été retourné';
    }

    public function late()
    {
        $loans = Loan::where('to_be_returned_at', '<', now())->get();
        return view('vendor.voyager.loans.late', compact('loans'));
    }
    
    public function borrow(Request $request)
    {
        $user = Auth::user();

        $currentLoans   = $user->loans()->where('returned_at', null)->count();
        $maxLoans       = setting('site.max_loans');

        if (!$user->canBorrow()) {
            if ($request->ajax()) {
                return response()->json([
                    'draw' => $request->input('draw', 1),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0,
                    'data' => [],
                    'error' => "Nombre de prêts autorisés dépassé ({$currentLoans}/{$maxLoans})"
                ]);
            }

            return redirect()->back()->with([
                'message'    => "Nombre de prêts autorisés dépassé ({$currentLoans}/{$maxLoans})",
                'alert-type' => 'error',
                'current_loans' => $currentLoans,
                'max_loans' => $maxLoans
            ]);
        }

        $book = $this->findBook($request->id);
        $this->checkBookAvailability($book);

        $book->update(['is_borrowed' => true]);
        
        $loan = $this->createLoan($book, $user);

        return redirect()->back()->with([
            'message' => "Le livre \"{$book->title}\" a été emprunté avec succès",
            'alert-type' => 'success',
        ]);
    }

}

