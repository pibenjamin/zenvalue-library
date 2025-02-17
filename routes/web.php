<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\LoanController;
use App\Models\Role;

Route::get('/test-email', function () {

    Mail::raw('Test de l\'envoi d\'un e-mail dans les logs.', function ($message) {
        $message->to('bpiscart@zenvalue.fr')->to('benjaminpiscart@gmail.com')
                ->subject('Test Mail Log tadda');
    });

    return 'E-mail envoyé et logué';
});


Route::get('/loans/late', [LoanController::class],'late')->name('loans.late');
Route::get('/my-loans', [LoanController::class],'myLoans')->name('my_loans');
Route::get('/return-book/{id}', [LoanController::class],'returnBook')->name('return_book');
Route::post('/loans/signal-return', [LoanController::class],'signalReturn')->name('loans.signal-return');


//Route::get('/loans/confirm-return/{id}', [LoanController::class],'confirmReturn')

Route::get('/admin/validate-return/{token}', [LoanController::class,'validateReturn'])->name('admin.validate-return');

Route::get('/admin/books/custom-filter/{filter}', function ($filter) {
    $books = Book::all();
    return view('admin.books.custom-filter', compact('books'));
});

//oute::get('/book/borrow/{id}', [VoyagerLibraryController::class],'borrow')->name('borrow');
//Route::post('/books/mass-update-theme', [VoyagerBookController::class],'massUpdateTheme')->name('books.mass-update-theme');
Route::get('/', function () {
    return redirect()->to('admin/login');
//    return view('welcome');
});




Route::get('/emprunter/{book_id}', function () {

    $book_id = request()->book_id;
    $session = session();
    $session->put('url.intended', url('admin/books?tableFilters[id][value]='.$book_id));
    // http://zbv.local/admin/books?tableFilters[id][value]=1

    return redirect()->to('admin/login');
//    return view('welcome');
});



Route::post('/process-image', [LoanController::class, 'processImage'])->name('process-image');
Route::post('/extract-isbn', [LoanController::class, 'extractISBNFromImage'])->name('extract-isbn');

