<?php

namespace App\Listeners;

use App\Events\VendorCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Setting;

class CreateSettings
{
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
     * @param  VendorCreated  $event
     * @return void
     */
    public function handle(VendorCreated $event)
    {
        $settings = Setting::insert([
            [
                'vendor_id' => $event->vendor->id,
                'key' => 'top-up',
                'name' => 'auto_top-up',
                'value' => '0'
            ],
            [
                'vendor_id' => $event->vendor->id,
                'key' => 'top-up',
                'name' => 'previous-payment',
                'value' => '0'
            ],
            [
                'vendor_id' => $event->vendor->id,
                'key' => 'top-up',
                'name' => 'default-payment',
                'value' => '0'
            ],
            [
                'vendor_id' => $event->vendor->id,
                'key' => 'queue',
                'name' => 'venue_name',
                'value' => 'Sample Store'
            ],
            [
                'vendor_id' => $event->vendor->id,
                'key' => 'queue',
                'name' => 'category',
                'value' => 'Restaurant'
            ],
            [
                'vendor_id' => $event->vendor->id,
                'key' => 'queue',
                'name' => 'message',
                'value' => 'Your order is ready for collection! '
            ],
            [
                'vendor_id' => $event->vendor->id,
                'key' => 'notification',
                'name' => 'sms_messages',
                'value' => '0'
            ],
            [
                'vendor_id' => $event->vendor->id,
                'key' => 'notification',
                'name' => 'push_notifications',
                'value' => '0'
            ],
            [
                'vendor_id' => $event->vendor->id,
                'key' => 'notification',
                'name' => 'email_notifications',
                'value' => '0'
            ],
            [
                'vendor_id' => $event->vendor->id,
                'key' => 'notification',
                'name' => 'frequency_repetition',
                'value' => '0'
            ],
            [
                'vendor_id' => $event->vendor->id,
                'key' => 'notification',
                'name' => 'frequency_interval',
                'value' => '0'
            ]
        ]);
    }
}
