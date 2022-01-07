<?php

namespace App\Listeners;

use App\Events\SendVerificationEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue; 
use App\Traits\{AutoResponderTrait, CommanTrait};
use App\Models\{User, PasswordReset, NotificationLogs};
use Illuminate\Support\Str;

class SendMailFired
{ 
    use AutoResponderTrait, CommanTrait;  
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\SendVerificationEmail  $event
     * @return void
     */
    public function handle(SendVerificationEmail $event)
    { 
        
        $user = User::find($event->userId)->toArray();
        $passwordReset = PasswordReset::updateOrCreate(['email' => $user['email']], ['email' => $user['email'], 'token' => Str::random(12) ]);
        $logtoken = Str::random(12); 
        $link = config('app.front_url').'/verify-email?token='.$passwordReset->token;
        $template = $this->get_template_by_name('VERIFY_EMAIL');

        $string_to_replace = [ '{{$name}}', '{{$token}}'];
        $string_replace_with = [ $user['name'], $link ];

        $newval = str_replace($string_to_replace, $string_replace_with, $template->template); 

        $result = $this->send_mail( $user['email'], $template->subject, $newval, $cc=null, 'VERIFY_EMAIL');
        
    }
}
