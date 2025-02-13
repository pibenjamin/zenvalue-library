<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReturnSignaled extends Mailable
{
    use SerializesModels;

    public $loan;

    public function __construct($loan)
    {
        $this->loan = $loan;
    }

    public function build()
    {
        $confirmUrl = route('voyager.loans.confirm-return', ['id' => $this->loan->id]);

        return $this->view('emails.return-signaled')
                    ->subject('[' . config('app.name') . '] Retour de livre signalé - À confirmer')
                    ->with([
                        'loan' => $this->loan,
                        'confirmUrl' => $confirmUrl
                    ]);
    }
} 