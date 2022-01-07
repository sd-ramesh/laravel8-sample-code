<?php

namespace App\Http\Controllers\V1\Api; 

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB; 
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\{Config, Validator};  
use App\Traits\{AutoResponderTrait, CommanTrait};
use App\Models\{QrCode, OrderLog, Vendor};  
use Exception;

class QrCodeController extends Controller
{
    use AutoResponderTrait, CommanTrait;  

    public function __construct() {
        $this->middleware('auth:api', ['except' => ['getQrDetail']]);
    }

    /* get QR detail */ 
    public function getQrDetail(Request $request){ 

        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required',
            'order_no' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        }   
        try { 
            $vendorId = $request->vendor_id ;   
            $qrDetail = QrCode::where( 'vendor_id', $vendorId)
            ->where('order_no', $request->order_no)
            ->first();   
            $logo = Vendor::where('user_id', $vendorId)->first('logo');

            if(!$qrDetail){ 
                return response()->json(['status' => false, 'message' => 'QR code '.Config::get('constants.ERROR.NOT_EXIST')], 403);
            }   
            $data = array_merge($qrDetail->toArray(),$logo->toArray()); 

            return response()->json(['status' => true, 'data' =>  $data , 'message' => 'QR code details'], 200);  

        } catch ( \Exception $e ) { 
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400);
        } 
    }
    /* get Current QR code data and all the orders of the vandor*/ 
    public function get(Request $request){ 
        try {
            $userId = auth()->user()->id; 

            $orderNumber = 1;
            $data = QrCode::where( 'vendor_id', $userId )->latest('order_no')->first();
            if(!$data || $data->status != 'free') {
                if($data){
                    $orderNumber = ($data->order_no + 1);
                }

                $data = [
                    'vendor_id' => $userId,
                    'order_no' => $orderNumber,
                ]; 
                $data = QrCode::create($data); 
                if(!$data){ 
                    return response()->json(['status' => false, 'message' => Config::get('constants.ERROR.NOT_SUFFICIENT_TOKEN')], 400);
                }
            }
            $orders = QrCode::with('customer')->where( 'vendor_id', $userId )->whereIn('status',['assigned','completed','skipped'])->orderByDesc('id')->get();  
            
            return response()->json(['status' => true, 'data' => $data, 'orders' => $orders, 'message' => 'QR codes '.Config::get('constants.SUCCESS.CREATE_DONE')], 200);  

        } catch ( \Exception $e ) { 
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400);
        } 
    }

    public function skip(Request $request) {
    	$validator = Validator::make($request->all(), [
            'order_no' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        }   
        try { 
            $userId = auth()->user()->id;   
            $data = QrCode::where( 'vendor_id', $userId )->where('order_no', $request->order_no)->update([ 'status' => 3 ]);
            if ($data) { 
                $orderNumber = 1;
                $exist = QrCode::where( 'vendor_id', $userId )->latest('order_no')->first();
                if($exist){     
                   $orderNumber = ($exist->order_no + 1);
                }   
                $data = [
                    'vendor_id' => $userId,
                    'order_no' => $orderNumber,
                ]; 
                $order = QrCode::create($data); 
                if(!$order){ 
                    return response()->json(['status' => false, 'message' => 'QR codes '.Config::get('constants.ERROR.FORBIDDEN_ERROR')], 403);
                } 
            }
 
            return response()->json(['status' => true, 'message' => 'QR codes '.Config::get('constants.SUCCESS.SKIPPED_DONE')], 200);

        } catch ( \Exception $e ) { 
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400);
        } 
    }

    public function assign(Request $request){
    	$validator = Validator::make($request->all(), [
            'vendor_id' => 'required',
            'order_no' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        }   
        try { 
            $vendorId = $request->vendor_id ;
            $userId = auth()->user()->id;   
            $qrDetail = QrCode::where( 'vendor_id', $vendorId)->where('order_no', $request->order_no)->first(); 
            if($qrDetail && $qrDetail->status != 'assigned') {
                
                DB::beginTransaction(); 
                $lastQueueDetail = QrCode::where( 'vendor_id', $vendorId)->orderBy('id', 'ASC')->first();
                $queueNumber  = ($lastQueueDetail->queue_no + 1);
                $data = QrCode::where( 'vendor_id', $vendorId)
                ->where( 'order_no', $request->order_no )
                ->update([ 'status' => 2, 'user_id' =>  $userId, 'order_status' => 2, 'queue_no' => $queueNumber ]); 

                $orderHit = $this->orderData();                
                if($data){ 
                    
                    /* Notification */ 
                    
                    $settings = vendorSettings($vendorId);
                    $userDetail = userDetail($userId);
                    
                    $title = Config::get('constants.SUCCESS.ORDER_STATUS_TITLE'); 
                    $message = ($settings->wait_message) ? $settings->wait_message : Config::get('constants.SUCCESS.DEFAULT_WAIT_MSG');

                    if($settings->sms_notification) { 
                        if($userDetail->phone_number){
                            $smsResponse = $this->sendMessage( $userDetail->country_code.$userDetail->phone_number, $message );
                        }
                    }
                    if($settings->push_notification) {
                        $deviceToken = $userDetail->device_token;
                        $deviceType = $userDetail->device_type;
                        // $deviceToken = "ev-Cxo3jQ-mEbyS8NT2tGU:APA91bFLdSEsRIQuzGw0ffcmOpjGatv8Sv-R-ITXke6HiAjUaTCWxK8Pz3ccSvVMxyzGbHYDYmadwkBa3gQYbl8f04PyfVrrswwBkKiccci07_31xxiesPQGpukajD3whC3QRL4tZAVQ";
                        if($deviceToken) {
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

                    $data = [
                        'order_id' => $qrDetail->id,
                        'order_status' => 2,
                        'message' => Config::get('constants.SUCCESS.PLACED_DONE'),
                        'data' => $orderHit, 
                    ];
                    $log = OrderLog::create($data);
                    if($log) { 
                        DB::commit();
                        return response()->json(['status' => true, 'message' => 'QR codes '.Config::get('constants.SUCCESS.ASSIGN_DONE')], 200);
                    } else {
                        DB::rollBack();
                        return response()->json(['status' => false, 'message' => Config::get('constants.ERROR.FORBIDDEN_ERROR')], 403);
                    }  
                }
            }  
            return response()->json(['status' => false, 'message' => 'QR codes '.Config::get('constants.ERROR.ALREADY_ASSIGN')], 403);
        } catch ( \Exception $e ) { 
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400);
        } 
    }
    /* Check QR status*/
    public function checkQR(Request $request) { 
        try {
            $validator = Validator::make($request->all(), [
                'order_no' => 'required', 
            ]);
    
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
            }

            $userId = auth()->user()->id;    
            $data = QrCode::select('id', 'order_no', 'status')->where( 'vendor_id', $userId )->where( 'order_no', $request->order_no )->first(); 

            if($data){   
                return response()->json([
                    'status' => true,
                    'qrdata' => $data,  
                    'message' => 'Qr Details',  
                ], 200);
            }  
            
            return response()->json(['status' => false, 'message' => Config::get('constants.ERROR.WRONG_CREDENTIAL')], 401);

        } catch ( \Exception $e ) { 
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400);
        } 
    }

}
