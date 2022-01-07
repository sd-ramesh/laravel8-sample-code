<?php

namespace App\Console\Commands;

use App\Events\QueueStatus;
use App\Events\VendorQueueStatus;
use App\Models\Notification;
use App\Models\Queue;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resend queue notifications ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
       $notification = Notification::where('status', 'ready')->get();
       if($notification){
           foreach ($notification as $notif) {

               $setting_repetition = Setting::firstWhere([
                   'vendor_id' => $notif->vendor_id,
                   'name' => 'frequency_repetition']);
               $setting_interval = Setting::firstWhere([
                   'vendor_id' => $notif->vendor_id,
                   'name' => 'frequency_interval']);
               $queue = Queue::find($notif->ticket_id);

               if($notif->times_notified <= (int)$setting_repetition->value)
               {
                   $additional_minutes = $notif->times_notified * (int) $setting_interval->value;
                   if(now() >=  Carbon::parse($queue->ready_at)->addMinutes($additional_minutes)){
                       event(new VendorQueueStatus($queue));
                       event(new QueueStatus($queue, false));
                       return $queue;
                   }
               }
           }
       }
    }
}
