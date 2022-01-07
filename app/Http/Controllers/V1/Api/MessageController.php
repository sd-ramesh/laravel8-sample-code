<?php

namespace App\Http\Controllers\V1\Api; 

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Message as MessageModel;
use App\Models\{User, Vendor, QrCode, Wallet}; 
use Aws\Sns\SnsClient; 
use Aws\Exception\AwsException;
use App\Events\SendNotification;
use Carbon\Carbon; 
use Illuminate\Support\Facades\{Config, Validator };  
use App\Traits\{AutoResponderTrait, CommanTrait};
use Exception;

class MessageController extends Controller
{ 
    use AutoResponderTrait, CommanTrait;  
    public $stripe;  
    public $SnSclient;
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['']]);
        $this->stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        $this->SnSclient = new SnsClient([
            'region' => config('services.sns.region'),
            'version' => config('services.sns.version'),
            'credentials' => [
                'key' =>  config('services.sns.key'),
                'secret' =>  config('services.sns.secret')
            ]
        ]);
    }
    /**
     * Display a listing of message
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = MessageModel::paginate();
        // if ($data) {
            return response()->json(['status' => true, 'data' => $data, 'message' => 'List of messages'], 200);  
        // } 
        // return response()->json(['status' => true, 'data' => $data, 'message' => 'List of messages'], 200); 
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message = MessageModel::create($request->all());  

        return response()->json(['status' => true, 'data' => $message, 'message' => 'List of messages'], 201); 
    }

    /**
     * Display the specified resource.
     *
     * @param  int 
     * @return \Illuminate\Http\Response
     */
    public function show()
    { 
        $userId = auth()->user()->id;
        $response = MessageModel::where('vendor_id', $userId)->where('type', 'marketing')->get();
        
        $smsPrice = config('app.sms_price');
        return response()->json(['status' => true, 'data' => $response, 'sms_price' => $smsPrice, 'message' => 'List of messages'], 201); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request )
    {  
        $validator = Validator::make($request->all(), [
            'id' => 'required',  
            'content' => 'required',  
            'marketing' => 'required',  
        ]);

        if($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        }

        $addData = ['subject' => 'Marketting', 'type' => 'marketing' ];
        $request->merge($addData);
        $userId = auth()->user()->id;
        MessageModel::where('vendor_id', $userId)->where('id', $request->id)->update($request->all());

        return response()->json([
            'status' => true,
            'message' => Config::get('constants.SUCCESS.UPDATE_DONE')
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'message_id' => 'required',  
        ]);

        if($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        }
 
        $userId = auth()->user()->id;
        MessageModel::where('vendor_id',$userId)->where('id', $request->message_id)->delete();

        return response()->json([
            'status' => true,
            'message' => "Message ". Config::get('constants.SUCCESS.DELETE_DONE')
        ], 200); 
    }
    public function sendBulkMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',  
            'customer_of' => 'required',  
            'message' => 'required',  
        ]);

        if($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        }

        $status =""; 
        $userId = auth()->user()->id;
         
 
        // $user = Vendor::firstWhere('id', $request->vendor_id);
        $bal = Wallet::firstWhere('user_id', $userId);
        if ($request->amount < $bal->amount){
            $message = $request->message;
            $count = 0;
            $customerList = $this->getUsersDetail($request->customer_of, 'phone_number');
            // dd($customerList);
            // dd($request->customer->toArray());

            foreach( $customerList as $number )
            {
                try {
                    
                    $smsResponse = $this->sendMessage( $number, $message );
                    // dd($smsResponse->toArray());
                    // $result = $this->SnSclient->publish([
                    //     'Message' => $message,
                    //     'PhoneNumber' => $number,
                    // ]);
                    // if($smsResponse){
                        $count++;
                    // } 
                    $status = "Success";
                } catch (AwsException $e) { 
                    error_log($e->getMessage());
                    $status = "Failed";
                    return response()->json([
                        'status' => false,
                        'message' => $e->getMessage(),
                    ], 400); 
                }
            }
            $bal->update([
                'amount' => $bal->amount - $request->amount
            ]);

            MessageModel::create([
                'type' => 'marketing',
                'vendor_id' => $userId,
                //'recipient' => $user->name,
                'content' => $message,
                'status' => $status,
                'marketing' => 'sms',
                'count' => $count
            ]);

            $response = MessageModel::where('vendor_id', $userId)->where('type', 'marketing')->get();
        
            $smsPrice = config('app.sms_price');
            return response()->json(['status' => true, 'data' => $response, 'sms_price' => $smsPrice, 'message' =>  'Message Sent to ' .$count. ' users (' .$status. ')'], 200); 
 
        } else { 
            return response()->json([
                'status' => false,
                'message' => Config::get('constants.SUCCESS.NO_BALANCE'),
            ], 402);  
        }
      
    }
    
    public function getCustomers(Request $request)
    {  
        $validator = Validator::make($request->all(), [
            'customer_of' => 'required',
            'marketing' => 'required',  
        ]);

        if($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        }

        $colunm = "phone_number"; 
        if($request->marketing == 'push'){
            $colunm = "device_token";
        }
        $userId = auth()->user()->id;
        $customer = [];
        $email = [];
        $stats = [];  
        $filter = false;
        switch($request->customer_of){
            case 'last_7days':
                $filter = [ Carbon::now()->subweek(), Carbon::today() ];
                break;
            
            case 'last_30days':
                $filter = [ Carbon::now()->subMonth(), Carbon::today() ];
                break;
            
            case 'last_90days':
                $filter = [ Carbon::now()->subMonth(3), Carbon::today() ];
                break;
            
            case 'this_year':
                $filter = [ Carbon::now()->startOfYear(),Carbon::now()->endOfYear() ];
                break; 
            }
 
            $stats = QrCode::where('vendor_id', $userId)
                ->when($filter, function($query) use($filter){
                    $query->whereHas('preparinglog', function($quee) use($filter){
                        $quee->where('order_status', 'preparing')
                        ->whereBetween('created_at', $filter);
                    });
                })
                ->whereHas('customer', function($que) use($colunm){
                    $que->whereNotNull($colunm);
                })->get();

            
            $recorded_user = null; 
 
            foreach ($stats as $key => $stat){ 
                if($recorded_user != $stat->user_id){
                    $customer[] = $stat->customer->$colunm;
                    $recorded_user = $stat->customer->id;  
                }
            }  
            
            return response()->json([
                'status' => true,
                'data' => count($customer),
                'message' => Config::get('constants.SUCCESS.CUSTOMER_COUNT'),
            ], 200);
            
        //    return $customer;

    }

    public function sendPushNotifications(Request $request) 
    {
         
        $validator = Validator::make($request->all(), [
            'customer_of' => 'required',   
            'message' => 'required',   
        ]);

        if($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        } 
        
        $userId = auth()->user()->id;
 
        $message = $request->input('message');
        $count = 0;
        $status;
        
        $customerList = $this->getUsersDetail($request->customer_of, 'device_token');
        foreach($customerList as $token) {
            $title = "Marketing";
            // $data = [
            //     'user_id' => $user,
            //     'message' => $message,
            // ];

            $pushResponse = send_notification_FCM( $token,  $title, $message, "-", '-' ); 

            // event(new SendNotification($data));
            $count++;
        }

        MessageModel::create([
            'type' => 'marketing',
            'vendor_id' => $userId,
            //'recipient' => $user->name,
            'content' => $message,
            'status' => "Success",
            'marketing' => 'push',
            'count'=> $count
        ]);
        $response = MessageModel::where('vendor_id', $userId)->where('type', 'marketing')->get();
            
        return response()->json([
            'status' => true,
            'data' => $response,
            'message' => 'Message sent to ' .$count. ' users',
        ], 200); 
    }

    public function exportCsv($data)
    {
    $fileName = 'tasks.csv';
    $tasks = $data->toArray();
    //    dd($tasks);
            $headers = array(
            "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            );
            //array_unshift($tasks, array_keys($tasks[0]));

            $callback = function() use($tasks) {
                $file = fopen('php://output', 'w');
               // fputcsv($file, $columns);

                foreach ($tasks as $task) {

                    fputcsv($file, $task);
                }

                fclose($file);
            };

        return response()->stream($callback, 200, $headers);
    }

    public function getUsersDetail($customer_of, $colunm )
    {
        $userId = auth()->user()->id; 
        $customer = [];
        $email = [];
        $stats = [];  

        $filter = false;
        switch( $customer_of){
            case 'last_7days':
                $filter = [ Carbon::now()->subweek(), Carbon::today() ];
                break;
            
            case 'last_30days':
                $filter = [ Carbon::now()->subMonth(), Carbon::today() ];
                break;
            
            case 'last_90days':
                $filter = [ Carbon::now()->subMonth(3), Carbon::today() ];
                break;
            
            case 'this_year':
                $filter = [ Carbon::now()->startOfYear(),Carbon::now()->endOfYear() ];
                break; 
            }
 
            $stats = QrCode::where('vendor_id', $userId)
                ->when($filter, function($query) use($filter){
                    $query->whereHas('preparinglog', function($quee) use($filter){
                        $quee->where('order_status', 'preparing')
                        ->whereBetween('created_at', $filter);
                    });
                })
                ->whereHas('customer', function($que) use($colunm){
                    $que->whereNotNull($colunm);
                })->get();

            
            $recorded_user = null; 
 
            foreach ($stats as $key => $stat){ 
                if($recorded_user != $stat->user_id){
                    if($colunm == 'phone_number' ){
                        $customer[] = $stat->customer->country_code.$stat->customer->$colunm;   
                    } else {
                        $customer[] = $stat->customer->$colunm;  
                    }
                }
            }  
            $customer = array_unique($customer);
            return $customer;
    }

    public function exportEmails(Request $request)
    {          
        $validator = Validator::make($request->all(), [
            'customer_of' => 'required', 
        ]);

        if($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        }
 
        $userId = auth()->user()->id; 
        $email = [];
        $stats = [];  
        $filter = false;
        switch($request->customer_of){
            case 'last_7days':
                $filter = [ Carbon::now()->subweek(), Carbon::today() ];
                break;
            
            case 'last_30days':
                $filter = [ Carbon::now()->subMonth(), Carbon::today() ];
                break;
            
            case 'last_90days':
                $filter = [ Carbon::now()->subMonth(3), Carbon::today() ];
                break;
            
            case 'this_year':
                $filter = [ Carbon::now()->startOfYear(),Carbon::now()->endOfYear() ];
                break; 
            }
 
            $stats = QrCode::where('vendor_id', $userId)
                ->when($filter, function($query) use($filter){
                    $query->whereHas('preparinglog', function($quee) use($filter){
                        $quee->where('order_status', 'preparing')
                        ->whereBetween('created_at', $filter);
                    });
                })
                ->whereHas('customer', function($que){
                    $que->whereNotNull('email');
                })->get(); 

            $recorded_user = null; 
 
            foreach ($stats as $key => $stat){ 
                if($recorded_user != $stat->user_id){ 
                    $recorded_user = $stat->customer->id;
                    $email[] = collect([++$key,$stat->customer->email]);  
                }
            }    
            
            if(count($email)){
                return $this->exportCsv( collect($email) );
            }
            return response()->json(['status' => false, 'message' =>  Config::get('constants.SUCCESS.NO_EMAIL_EXPORT')], 404);
            
    } 

}
