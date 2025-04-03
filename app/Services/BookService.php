<?php

namespace App\Services;

use App\Mail\LoanConfirmed; 
use App\Mail\UserSignaledReturn;
use App\Models\Book;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Mail\AdminConfirmReturn;
use App\Mail\DropOff;
use App\Mail\RequestLoan;

class BookService
{
    public function dropOffNotify(Book $book)
    {
        Mail::to(config('app.admin_email'))->send(new DropOff(
            bookTitle: $book->title,
            dropOffAt: now(),
            ownerName: $book->owner->name
        ));
    }    

    public function borrowBookAtHome(Book $book, $user)
    {
        Mail::to($book->owner->email)->send(new RequestLoan(
            book: $book,
            owner: $book->owner,
            user: $user
        ));
    }
} 