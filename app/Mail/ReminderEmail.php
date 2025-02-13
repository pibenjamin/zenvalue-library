<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReminderEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $messageContent;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $template, $messageContent)
    {
        $this->messageContent = $messageContent;
        $this->template = $template;
        $this->subject = '[' . config('app.name') . '] ' . $subject;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->subject)
                    ->view($this->template)
                    ->with([
                        'messageContent' => $this->messageContent,
                        'subject' => $this->subject

                    ]);
    }
}
