<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReturnConfirmed extends Mailable
{
    use SerializesModels;

    public $loan;

    public function __construct($loan)
    {
        $this->loan = $loan;
    }

    public function build()
    {
        return $this->view('emails.return-confirmed')
                    ->subject('[' . config('app.name') . '] Retour de livre confirmé');
    }
} 