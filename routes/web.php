<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\LoanController;
use App\Models\Loan;



Route::get('/test-loan', function () {


    $loan = new Loan();
    $loan->book_id = 1;
    $loan->borrower_id = 1;
    $loan->borrowed_at = now();
    $loan->to_be_returned_at = now()->addMonths(1);
    $loan->status = Loan::STATUS_IN_PROGRESS;
    $loan->save();

});



Route::get('/test-email', function () {

    $user = App\Models\User::find(1);
    $book = App\Models\Book::find(1);
    $loan = App\Models\Loan::find(2);

    Mail::to('benjaminpiscart@gmail.com')->send(new App\Mail\LoanConfirmed($user, $book, $loan));

//    Mail::raw('Test de l\'envoi d\'un e-mail dans les logs.', function ($message) {
//        $message->to('test@example.com')
//                ->subject('Test Mail Log tadda');
//    });

    return 'E-mail envoyé et logué';

    exit;
});

Route::get('/loans/late', [LoanController::class],'late')->name('loans.late');
Route::get('/my-loans', [LoanController::class],'myLoans')->name('my_loans');
Route::get('/return-book/{id}', [LoanController::class],'returnBook')->name('return_book');
Route::post('/loans/signal-return', [LoanController::class],'signalReturn')->name('loans.signal-return');
Route::get('/loans/confirm-return/{id}', [LoanController::class],'confirmReturn')
    ->name('loans.confirm-return');
//oute::get('/book/borrow/{id}', [VoyagerLibraryController::class],'borrow')->name('borrow');
//Route::post('/books/mass-update-theme', [VoyagerBookController::class],'massUpdateTheme')->name('books.mass-update-theme');

Route::get('/', function () {
    return view('welcome');
});
