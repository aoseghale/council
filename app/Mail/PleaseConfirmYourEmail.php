<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PleaseConfirmYourEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * NOTE: In Mailables, all public properties are available to the view
     * @var
     */
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        //
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.confirm-email');
    }
}
