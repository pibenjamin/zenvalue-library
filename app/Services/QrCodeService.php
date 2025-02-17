<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    public function generateQrCode($book)
    {
        $qrCode = QrCode::size(300)->generate(config('app.url').'/emprunter/'.$book->id);
    
        return $qrCode;
    }


}


