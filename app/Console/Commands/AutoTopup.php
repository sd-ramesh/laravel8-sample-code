<?php

namespace App\Console\Commands;

use App\Models\Card as CardModel;
use Illuminate\Console\Command;
use App\Models\Wallet;
use App\Models\Setting;

class AutoTopup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'topup:auto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check balance and update if the it is below $5 for those who enabled auto top up feature';
    /**
     * @var
     */
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
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        $users = Setting::where(['name'=> 'auto_top-up', 'value' => "1"])->with('vendor')->get();
        foreach ($users as $user){
            $bal = Wallet::firstWhere('user_id', $user->vendor->user_id);
            if($bal->amount ?? 0 < 5){
                $customer = CardModel::where('user_id', $user->vendor->user_id)->where('isDefault', 1)->first();
                $amount = Setting::firstWhere(['vendor_id' => $user->vendor->id, 'name'=>'previous-payment']);
                try {
                    $paymentSetup = $stripe->paymentIntents->create([
                        'amount' => $amount->value,
                        'currency' => 'usd',
                        'customer' => $customer->stripe_customer_id,
                        'metadata' => ['integration_check' => 'accept_a_payment'],
                    ]);
                    $paymentConfirm = $stripe->paymentIntents->confirm(
                        $paymentSetup->id,['payment_method' => $customer->paymentmethod_id
                    ]);
                }catch (\Exception $e){
                    return $e->getMessage();
                }
            }
        }
    }
}
