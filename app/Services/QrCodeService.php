<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Book;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
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

        $filename = 'qr-' . $book->id . '.png';
        
        try {
            // Utilisation de Storage facade pour gérer l'écriture du fichier
            Storage::disk('public')->put('qr-codes/' . $filename, $qrCode);
            
            return $filename;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération du QR code : ' . $e->getMessage());
            return null;
        }
    }


}


