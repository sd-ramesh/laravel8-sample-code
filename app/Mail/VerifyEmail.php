<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;
    // public $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public array $user)
    {

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('no-reply@qgo-app.com')
                    ->subject('Account Verification')
                    ->view('emails.authuser')
                    ->with(['user_id'=> $this->user['id']]);

    }
}
