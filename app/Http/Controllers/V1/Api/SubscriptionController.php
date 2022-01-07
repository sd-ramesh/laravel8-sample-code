<?php

namespace App\Http\Controllers\V1\Api; 

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use App\Models\{Invoice, Card} ; 
use App\Models\Subscription as SubscriptionModel;
use Stripe\Exception\ApiErrorException;
use Carbon\Carbon;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Illuminate\Support\Facades\{Config, Validator };  
use Exception;

class SubscriptionController extends Controller
{
    public $stripe; 

    public function __construct() {

        $this->middleware('auth:api', ['except' => ['']]);
        $this->stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

    }
    public function create(Request $request)
    {
        $userId = auth()->user()->id;
        $card =  Card::where('user_id', $userId)->where('isDefault', 1)->first(); 
        $stripe_id = $card->stripe_customer_id;
        $customer = SubscriptionModel::where(['stripe_customer_id' => $stripe_id, 'subscription_status' => 'active'])->first();
        if ($customer) { 
            return response()->json(['status' => true, 'message' => Config::get('constants.SUCCESS.EXISTING_SUBSCRIPTION') ], 403);  
        } else {
            $subscription = SubscriptionModel::where('stripe_customer_id', $stripe_id)->first();
            if($subscription){
                try {
                    return $this->stripe->subscriptions->create([
                        'customer' => $card->stripe_customer_id,
                        'items' => [
                            ['price' => 'price_1K6Ui8SGxi1zoMtkRvYBDcAQ'],
                        ]
                    ]);
                } catch (ApiErrorException $e) {
                    return $e->getMessage();
                }
            } else {
                $start_trial = date('Y-m-d');
                $end_trial = date_create($start_trial);
                date_add($end_trial, date_interval_create_from_date_string("14 days"));
                $end_trial = date_format($end_trial, "Y-m-d");

                try {
                    return $this->stripe->subscriptions->create([
                        'customer' => $card->stripe_customer_id,
                        'items' => [
                            ['price' => 'price_1K6Ui8SGxi1zoMtkRvYBDcAQ'],
                        ], 'trial_end' => strtotime($end_trial)
                    ]);
                } catch (ApiErrorException $e) {
                    return $e->getMessage();
                }
            }
        }

    }

    public function cancel(Request $request){
        
        $userId = auth()->user()->id;
        $card =  Card::where('user_id', $userId)->where('isDefault', 1)->first();
        $stripe_id = $card->stripe_customer_id;
        $customer = SubscriptionModel::where(['stripe_customer_id' => $stripe_id, 'subscription_status' => 'active']);
        if($customer){
            $subscription = $this->stripe->subscriptions->cancel(
                $customer->subscription_id,
                []
              );

              SubscriptionModel::firstWhere('subscription_id',$customer->subscription_id)->update(['subscription_status'=> 'inactive']);

            return $subscription;
        } else {
            return response([
                'message' => Config::get('constants.SUCCESS.NO_SUBSCRIPTION') ,
                'status' => 403
            ]);
        }

    }

    public function invoice(){
        
        $userId = auth()->user()->id;

        $customer = Card::firstWhere('user_id', $userId); 
        $invoice = "";
        if($customer){
            $stripe_id = $customer->stripe_customer_id;
            $invoice = Invoice::where('stripe_customer_id', $stripe_id)->get();
        }

        return response()->json(['status' => true,
        'data' => $invoice,
        'message' => Config::get('constants.SUCCESS.LIST_INVOICE'),
        ],200);
    }

    public function download(){ 
        $userId = auth()->user()->id;
        $customer = Card::firstWhere('user_id', $userId); 
        if(!$customer){ 
            return response()->json(['status' => false,
            'message' => Config::get('constants.SUCCESS.NO_INVOICE') , 
            ],400);
        }
        $stripe_id = $customer->stripe_customer_id;
        $invoice = Invoice::where('stripe_customer_id', $stripe_id)->get()->toArray();
        //dd($invoice);

        $fileName = 'invoice.csv';
        $headers = [
            'Content-Type' => 'application/octet-stream',
            "Content-Description" => "File Transfer",
            "Cache-Control" => "public",
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
         ];
              // dd($tasks);
                array_unshift($invoice, array_keys($invoice[0]));


                $callback = function() use($invoice) {
                    $file = fopen('php://output', 'w');
                    $data = [];
                    foreach($invoice as $index => $key) { 
                        if($index == 0){
                            foreach($key as $da){
                                $data[] = ucwords(str_replace('_', '-', $da));
                            }
                            fputcsv($file,$data); 
                        } else {
                            fputcsv($file,$key);
                        }
                    }


                fclose($file);
             };

            return response()->stream($callback, 200, $headers);
            //return response()->download($callback, 'tasks.csv', $headers);

    }

    public function pdfstatement(Request $request){
         
        $validator = Validator::make($request->all(), [
            'invoice_id' => 'required',   
        ]);

        if($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        } 

        $invoice = Invoice::where('id', $request->invoice_id)->first();
        //  dd($invoice);
        if($invoice && $invoice->event == 'invoice'){

            return response(['status' => true,
                'url' => $invoice->invoice_url,
              //  'status' => 200,
            ]);
          //  return $invoice->invoice_url;
        }
        else{
            
            return response(['status' => true,
                'url' => $invoice->receipt_url,
              //  'status' => 200,
            ]);
        }
        //dd($invoice);

    }

    public function getStatus($id){
        $customer = Card::firstWhere('user_id', $id);
        $stripe_id =  $customer->stripe_customer_id;
        $subscription = SubscriptionModel::where('stripe_customer_id', $stripe_id)->first();

        return response([
            'status'=> $subscription->status,
            'renew_date' => date("Y-m-d",$subscription->current_period_end)
        ]);
    }
}
