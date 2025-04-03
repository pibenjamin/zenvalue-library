<?php

namespace App\Http\Livewire;

use Livewire\Component;

class AutoSubmitCal extends Component
{
    public $message = "Hello, Livewire!";

    public function mettreAJourMessage()
    {
        $this->message = "Message mis à jour !";
    }

    public function render()
    {
        return view('livewire.auto-submit-cal');
    }
}
