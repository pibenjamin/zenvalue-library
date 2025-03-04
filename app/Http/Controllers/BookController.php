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
use App\Services\LoanService;
use App\Services\QrCodeService;
use Illuminate\Http\UploadedFile;

use Illuminate\Support\Facades\FileUpload;
use Illuminate\Support\Facades\Form;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use DOMDocument;
use DOMXPath;




class BookController extends Controller
{

    public function extractBookInfo(Request $request)
    {

    }

    public function printQrCodes(Request $request)
    {
        $this->canAccess();

        $books      = Book::all();
        $qrCodes    = [];
        $printSize  = $request->print_size ?? 300;
        $regenerate = $request->regenerate ?? false;

        $qrCodeService = new QrCodeService();

        $i = 0;
        foreach ($books as $book) {

            $qrCodes[] = [
                'qrCode'    => $qrCodeService->generateAndSaveAsFile($book, 300, $regenerate),
                'title'     => $book->title,
                'isbn'      => str_replace('-', '', $book->isbn),
                'owner'     => $book->owner->name == 'Admin' ? 'Zen Value, Propriétaire : ' : 'Zen Value, Propriétaire : ' . $book->owner->name,
            ];

            $i++;
        }

        return view('print-qr-codes', compact('qrCodes', 'printSize'));
    }

    protected function canAccess()
    {
        if (!Auth::check()) {

            $user = auth()->user();
            if(!$user) {
                abort(403, 'Unauthorized');
            }

            dd($user);
            if($user->role->name != 'super-admin') {
                abort(403, 'Unauthorized');
            }

        }
    }

}

