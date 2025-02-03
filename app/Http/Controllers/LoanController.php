<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

    public function signalReturn(Request $request)
    {
        $loan = Loan::findOrFail($request->loan_id);
        
        // Générer un token unique
        $token = hash_hmac('sha256', $loan->id . $loan->book_id . time(), config('app.key'));
        
        // Marquer comme "retour en cours"
        $loan->update([
            'return_signaled_at' => now(),
            'return_confirmation_token' => $token,
            'status' => Loan::STATUS_RETURN_IN_PROGRESS  // Utilisation de la constante
        ]);

        // Envoyer l'email au gestionnaire
        $librarians = User::where('role_id', 3)->get();

        foreach($librarians as $librarian) {
            Mail::to($librarian->email)->send(new ReturnSignaled($loan));
        }

        return response()->json([
            'message' => 'Retour signalé avec succès. Un bibliothécaire va confirmer le retour.',
            'success' => true
        ]);
    }

    public function confirmReturn(Request $request, $id)
    {
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();
        
        // Set the dataType on the controller
        $this->dataType = $dataType;

        $loan = call_user_func([$dataType->model_name, 'findOrFail'], $id);
        
        // Vérifier le token
        if (!$request->has('token') || $loan->return_confirmation_token !== $request->token) {
            abort(403, 'Token de confirmation invalide');
        }

        // Vérifier que le token n'a pas expiré (48h par exemple)
        if (Carbon::parse($loan->return_signaled_at)->addHours(48)->isPast()) {
            abort(403, 'Le lien de confirmation a expiré');
        }
        
        // Marquer le livre comme retourné
        $loan->update([
            'returned_at' => now(),
            'return_confirmed_by' => auth()->id(),
            'return_confirmation_token' => null,
            'status' => Loan::STATUS_RETURNED  // Ajout de cette ligne
        ]);

        // Marquer le livre comme disponible
        $loan->book->update([
            'status' => 'available',
            'is_borrowed' => false
        ]);

        // Envoyer l'email de confirmation à l'emprunteur
        Mail::to($loan->borrower->email)->send(new ReturnConfirmed($loan));

        return redirect()
            ->route('voyager.loans.index', ['status' => 'returned'])
            ->with([
                'message'    => "Le retour du livre '{$loan->book->title}' a été confirmé avec succès",
                'alert-type' => 'success',
                'highlighted_loan' => $loan->id
            ]);
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

