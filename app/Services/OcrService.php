<?php

namespace App\Services;

use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Http\UploadedFile;


class OcrService
{
    public function processImage(UploadedFile $image): string
    {

        $imagePath = $image->getRealPath();

        $text = $this->extractISBNFromImage($image->getRealPath()); 


        return $text;
    }


    public function extractISBNFromImage($imagePath)
    {
        $text = (new TesseractOCR($imagePath))->run();

        dd($text);
    
        // Chercher un numéro ISBN (10 ou 13 chiffres consécutifs)
        preg_match('/\b\d{10,13}\b/', $text, $matches);
    
        if (!empty($matches)) {
            return $matches[0]; // Retourne l'ISBN extrait
        }
    
        return null; // Aucun ISBN trouvé
    }


}


