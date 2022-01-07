<?php
declare(strict_types=1);


namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\VendorQueueStatus;
use App\Listeners\QueueStatusUpdate;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
 


    protected $listen = [
        \App\Events\SendVerificationEmail::class => [
            \App\Listeners\SendMailFired::class,
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
        ], 
        VendorQueueStatus::class =>[
            QueueStatusUpdate::class,
        ],
        \App\Events\VendorCreated::class =>[
            \App\Listeners\CreateSettings::class, 
            \App\Listeners\RegisterCard::class,
         ],
        \App\Events\UserCreated::class =>[
            \App\Listeners\SendEmailVerification::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
