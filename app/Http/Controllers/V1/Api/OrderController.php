<?php

namespace App\Http\Controllers\V1\Api; 

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;   
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\{Config, Validator};  
use App\Traits\{AutoResponderTrait, CommanTrait};
use App\Models\{QrCode, OrderLog};  
use Exception;

class OrderController extends Controller
{
    use AutoResponderTrait, CommanTrait;

    public function __construct() {
        $this->middleware('auth:api', ['except' => ['']]);
    }

    /* Get all the orders for the vendor or customer*/
    public function test(Request $request) { 
        try {
            $user = auth()->user();

            $logs = OrderLog::with('qr')
            ->select('created_at','order_id')->where('order_status', 'preparing')->get()->groupBy(function($item) {
                return $item->created_at->format('Y-m-d');
            });  
 
            $data = [];
            $i = 0;
            foreach($logs as $date => $log) {
                $data[$i] = [
                    'date' => $date,
                    'total' => count($log),
                    'collected' => 0,
                    'order' => []
                ];
            
                $collectedCount = 0;
                $orderList = [];
                foreach($log as $order) {
                    if ($order['qr']['order_status'] == 'Collected') {
                        $collectedCount += 1;
                    }
                
                    $orderList[] = $order['qr'];
                }
                
                $data[$i]['collected'] = $collectedCount;
                $data[$i]['order'] = $orderList;
                
                $i++;
            }
 
            return response()->json([ 
                'logs' => $data,
            ], 200);

        } catch ( \Exception $e ) { 
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400);
        } 
    }

    /* Get all the orders for the vendor or customer*/
    public function get(Request $request) { 
        try {
            $user = auth()->user(); 
            $data='';
            if($user->role == 'vendor') {    
                $data = QrCode::with('customer')->where( 'vendor_id', $user->id )->whereNotNull('user_id')->get();
            } else if($user->role == 'customer') {
                $data = QrCode::with('vendor','vendorDetail')->where( 'user_id', $user->id )->get();
            }
            
            if($data->isNotEmpty()){   
                return response()->json([
                    'status' => true,
                    'qrdata' => $data,  
                    'message' => 'List of Orders',  
                ], 200);
            }
            
            return response()->json(['status' => true, 'qrdata' => $data, 'message' => Config::get('constants.ERROR.NO_ORDER')], 200); 

        } catch ( \Exception $e ) { 
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400);
        } 
    }


    /* Get all the orders for the Customer */
    public function updateStatus(Request $request){ 
        
        $validator = Validator::make($request->all(), [
            'order_no' => 'required',
            'customer_id' => 'required',
            'order_status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        }
        try { 
            $userId = auth()->user()->id;

            $qrDetail = QrCode::where( 'vendor_id', $userId )->where('order_no', $request->order_no)->where('user_id', $request->customer_id)->first(); 
            if($qrDetail) {

                DB::beginTransaction(); 
                $data = QrCode::where( 'vendor_id', $userId )
                        ->where('user_id', $request->customer_id)
                        ->where('order_no', $request->order_no)
                        ->update([ 'order_status' => $request->order_status]);

                if($data) {
                    if($request->order_status == 3) { $status = 'ready';  }
                    if($request->order_status == 4) {
                         $status = 'collected'; 
                         $data = QrCode::where( 'vendor_id', $userId )
                         ->where('user_id', $request->customer_id)
                        ->where('order_no', $request->order_no)
                        ->update([ 'status' => 4]);

                    }
                    if($request->order_status == 5) { $status = 'cancelled'; } 
                    $dataLogs = $this->orderData(); 

                    /* Notification */ 
                    
                    $settings = vendorSettings($userId);
                    $userDetail = userDetail($qrDetail->user_id);
                    
                    $title="Order Status";
                    $message = 'Your order is '.$status;
                    if($status == "ready" && isset($settings->ready_message)){ 
                        $message = $settings->ready_message;
                    }
                    if($settings->sms_notification) { 
                        if($userDetail->phone_number){
                            $smsResponse = $this->sendMessage(  $userDetail->country_code.$userDetail->phone_number, $message );
                        } 
                    }
                    if($settings->push_notification) {
                        $deviceToken = $userDetail->device_token;
                        $deviceType = $userDetail->device_type; 
                        // $deviceToken = "ev-Cxo3jQ-mEbyS8NT2tGU:APA91bFLdSEsRIQuzGw0ffcmOpjGatv8Sv-R-ITXke6HiAjUaTCWxK8Pz3ccSvVMxyzGbHYDYmadwkBa3gQYbl8f04PyfVrrswwBkKiccci07_31xxiesPQGpukajD3whC3QRL4tZAVQ";
                        if($deviceToken){
                            $pushResponse = send_notification_FCM( $deviceToken,  $title, $message, "-", '-' ); 
                        }
                    }
                    if($settings->email_notification) {

                        $userEmail = $userDetail->email; 
                        $userName = $userDetail->name; 
                        $template = $this->get_template_by_name('ORDER_STATUS');
                
                        $string_to_replace = [ '{{$name}}', '{{$message}}'];
                        $string_replace_with = [ $userName, $message ]; 
                        $newval = str_replace($string_to_replace, $string_replace_with, $template->template); 
                        if($userEmail){
                            $emailResponse = $this->send_mail( $userEmail, $title, $newval, $cc=null, 'ORDER_STATUS - '.$message); 
                        }    
                    }
                    if($settings->reminder_prompt) {
                        
                    } 
                    /* Notification End */ 
                    
                    $log = OrderLog::updateOrCreate(
                        [
                            'order_id' => $qrDetail->id,
                            'order_status' => $request->order_status, 
                        ],[
                            'message' => $message,
                            'data' => $dataLogs,
                        ]
                    );
                     
                    if($log) { 
                        DB::commit();
                        return response()->json(['status' => true, 'message' => 'Order '.Config::get('constants.SUCCESS.STATUS_UPDATE')], 200);
                    } else {
                        DB::rollBack();
                        return response()->json(['status' => false, 'message' => Config::get('constants.ERROR.FORBIDDEN_ERROR')], 500);
                    }  
                }
            } 
            return response()->json(['status' => false, 'message' => Config::get('constants.ERROR.WRONG_CREDENTIAL')], 401);  
        } catch ( \Exception $e ) { 
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400);
        } 
    }

    /* Get full details of order */
    public function getOrderDetail(Request $request) {

        $validator = Validator::make($request->all(), [
            'order_id' => 'required', 
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        } 
        
        try {
            $user = auth()->user(); 

            if($user->role == 'vendor') {    
                $data = QrCode::with('customer', 'logs')
                ->where( 'id', $request->order_id )
                ->where( 'vendor_id', $user->id )
                ->where('status','assigned')
                ->whereNotNull('user_id')->first();

            } else if($user->role == 'customer') {
                $data = QrCode::with('vendor','vendorDetail', 'logs')
                ->where( 'id', $request->order_id )
                ->where( 'user_id', $user->id )
                ->where('status','assigned')->first();
            }
            if($data) {   
                return response()->json([
                    'status' => true,
                    'qrdata' => $data,  
                    'message' => 'Order Detail',  
                ], 200);
            }
            
            return response()->json(['status' => true, 'qrdata' => $data, 'message' => Config::get('constants.ERROR.NO_ORDER')], 200);

        } catch ( \Exception $e ) { 
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400);
        } 
    }


     /* Get all the orders for the Customer */
     public function SendLoopNotification(Request $request){ 
        
        $validator = Validator::make($request->all(), [
            'order_no' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        }
        try { 
            $userId = auth()->user()->id;

            $qrDetail = QrCode::with('customer')->where( 'vendor_id', $userId )->where( 'order_status', 'ready' )->where('order_no', $request->order_no)->first(); 
            if($qrDetail) {  
                $status = 'ready';  
                $dataLogs = $this->orderData(); 

                /* Notification */ 
                
                $settings = vendorSettings($userId);
                $userDetail = userDetail($qrDetail->user_id);
                
                $title="Order Status";
                $message = 'Your order is '.$status;
                if($status == "ready" && isset($settings->ready_message)){ 
                    $message = $settings->ready_message;
                }
                if($settings->sms_notification) { 
                    if($userDetail->phone_number){
                        $smsResponse = $this->sendMessage( $userDetail->country_code.$userDetail->phone_number, $message );
                    } 
                }
                if($settings->push_notification) {
                    $deviceToken = $userDetail->device_token;
                    $deviceType = $userDetail->device_type; 
                    // $deviceToken = "ev-Cxo3jQ-mEbyS8NT2tGU:APA91bFLdSEsRIQuzGw0ffcmOpjGatv8Sv-R-ITXke6HiAjUaTCWxK8Pz3ccSvVMxyzGbHYDYmadwkBa3gQYbl8f04PyfVrrswwBkKiccci07_31xxiesPQGpukajD3whC3QRL4tZAVQ";
                    if($deviceToken){
                        $pushResponse = send_notification_FCM( $deviceToken,  $title, $message, "-", '-' ); 
                    }
                }
                if($settings->email_notification) {

                    $userEmail = $userDetail->email; 
                    $userName = $userDetail->name; 
                    $template = $this->get_template_by_name('ORDER_STATUS');
            
                    $string_to_replace = [ '{{$name}}', '{{$message}}'];
                    $string_replace_with = [ $userName, $message ]; 
                    $newval = str_replace($string_to_replace, $string_replace_with, $template->template); 
                    if($userEmail){
                        $emailResponse = $this->send_mail( $userEmail, $title, $newval, $cc=null, 'ORDER_STATUS - '.$message); 
                    }    
                } 
                /* Notification End */ 

                return response()->json(['status' => true, 'message' => 'User '.Config::get('constants.SUCCESS.NOTIFY_DONE')], 200);
            }
            return response()->json(['status' => false, 'message' => Config::get('constants.ERROR.WRONG_CREDENTIAL')], 401);  
        } catch ( \Exception $e ) { 
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400);
        } 
    }


     
}
