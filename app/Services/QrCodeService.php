<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Book;
class QrCodeService
{
    /**
     * Generate a QR code for a book
     * 
     * @param Book $book
     * @param int $size
     * @return string
     */
    public function generateQrCode($book, $size = 300)
    {
        $qrCode = QrCode::size($size)->generate(config('app.url').'/emprunter/'.$book->id);
    
        return $qrCode;
    }

    public function generateAndSaveAsFile(Book $book, $size = 300, $regenerate = false)
    {
        $qrCode = QrCode::format('png')
        ->size($size)
        ->errorCorrection('H')
        ->generate(config('app.url').'/emprunter/'.$book->id);
    
        $path = storage_path('app/public/qr-codes/');
        
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        
        $filename = 'qr-' . $book->id . '.png';
        
        if ($regenerate) {
            file_put_contents($path . $filename, $qrCode);
        }

        return $filename;
    }


}


