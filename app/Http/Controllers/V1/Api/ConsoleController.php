<?php

namespace App\Http\Controllers\V1\Api; 

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;  
use App\Traits\{CommanTrait};
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\{Config, Validator};  
use Carbon\Carbon; 
use App\Models\{Vendor, QrCode, Reminder};  
use Exception;

class ConsoleController extends Controller
{
    
    use CommanTrait;  
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['']]);
    }

    /* Get screen info for the vendor */
    public function getScreen(Request $request) { 
        try {
            $userId = auth()->user()->id; 
            $data = Vendor::where('user_id', $userId)->select('id', 'user_id', 'business_name', 'trading_as', 'waiting_message', 'ready_message')->first();
              
            if($data){   
                return response()->json([
                    'status' => true,
                    'screen' => $data,  
                    'message' => 'List of screen configurations',  
                ], 200);
            }
            
            return response()->json(['status' => false, 'message' => Config::get('constants.ERROR.WRONG_CREDENTIAL')], 401);

        } catch ( \Exception $e ) { 
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400);
        } 
    }

    /* update screen info for the vendor*/
    public function updateScreen(Request $request){ 
        if ($request->has('business_name')) {
            $validator = Validator::make($request->all(), [
                'business_name' => 'required', 
            ]);
        }
        if ($request->has('trading_as')) {
            $validator = Validator::make($request->all(), [ 
                'trading_as' => 'required', 
            ]);
        }
        if ($request->has('waiting_message')) {
            $validator = Validator::make($request->all(), [ 
                'waiting_message' => 'required', 
            ]);
        }
        if ($request->has('ready_message')) { 
            $validator = Validator::make($request->all(), [ 
                'ready_message' => 'required',
            ]);
        } 
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        }
        try { 
            $userId = auth()->user()->id;

            $detail =  Vendor::where('user_id', $userId)
                    ->update($validator->validated()); 
            if($detail) {
                return response()->json(['status' => true, 'message' => 'Screen '.Config::get('constants.SUCCESS.UPDATE_DONE')], 200);
            }   

            return response()->json(['status' => false, 'message' => Config::get('constants.ERROR.WRONG_CREDENTIAL')], 401); 

        } catch ( \Exception $e ) { 
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400);
        } 
    }

    public function getNotification(Request $request) { 
        try {
            $userId = auth()->user()->id; 
            $data = Vendor::where('user_id', $userId)->select('id', 'user_id', 'sms_notification', 'push_notification', 'email_notification', 'reminder_prompt')->first();
            $reminder = Reminder::select('id', 'name', 'value')->where('status', 1)->get();
              
            if($data){   
                return response()->json([
                    'status' => true,
                    'screen' => $data,  
                    'reminder' => $reminder,  
                    'message' => 'List of notification configurations',  
                ], 200);
            }
            
            return response()->json(['status' => false, 'message' => Config::get('constants.ERROR.WRONG_CREDENTIAL')], 401);

        } catch ( \Exception $e ) { 
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400);
        } 
    }

    public function updateNotification(Request $request) { 
        $validator = Validator::make($request->all(), [
            'sms_notification' => 'required',
            'push_notification' => 'required',
            'email_notification' => 'required', 
            'reminder_prompt' => 'required', 
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        }
        try { 
            $userId = auth()->user()->id;

            $detail =  Vendor::where('user_id', $userId)
                    ->update([
                        'sms_notification' => $request->sms_notification,
                        'push_notification' => $request->push_notification,
                        'email_notification' => $request->email_notification, 
                        'reminder_prompt' => $request->reminder_prompt, 
                    ]); 
            if($detail) {
                return response()->json(['status' => true, 'message' => 'Notification '.Config::get('constants.SUCCESS.UPDATE_DONE')], 200);
            }   

            return response()->json(['status' => false, 'message' => Config::get('constants.ERROR.WRONG_CREDENTIAL')], 401); 

        } catch ( \Exception $e ) { 
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400);
        } 
    }
    

    public function getStatics(Request $request) {  
        $validator = Validator::make($request->all(), [
            'require_for' => 'required', 
            'waiting_stats' => 'required|numeric|max:1|min:0', 
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        }
        try {
            $userId = auth()->user()->id;   
            $date ="";
            $raw = [
                'today' => Carbon::today(),
                'last_7days' => Carbon::now()->subweek(),
                'last_30days' => Carbon::now()->subMonth(),
                'last_90days' => Carbon::now()->subMonth(3),
                'this_year' => Carbon::now()->startOfYear(),
                'all_stats' => false,
            ];
            $date = $raw[$request->require_for];    

            $data = QrCode::with('logs:id,order_id,order_status,created_at')
                ->where( 'vendor_id', $userId ) 
                ->whereNotNull('user_id')
                ->when($date , function($query) use ($date){ 
                    $query->whereHas('logs', function($qdate) use ($date){
                        $qdate->where('order_status', 'preparing')
                        ->where('created_at', '>=', $date);
                    });
                })
                ->when((!$request->waiting_stats) , function($query){  
                    $query->whereIn('status',['assigned','completed',]);
                })
                ->when(($request->waiting_stats == 1) , function($query){ 
                    $query->where('order_status', '!=', 'preparing')
                    ->where('status','completed');
                })
                ->get();

 
                
            if($request->export == true){ 
                $data = '';
                $data = QrCode::join('order_logs', 'order_logs.order_id', '=', 'qr_codes.id')
                ->join('users', 'users.id', '=', 'qr_codes.user_id')      
                ->where( 'qr_codes.vendor_id', $userId ) 
                ->whereNotNull('qr_codes.user_id')
                ->when($date , function($query) use ($date){ 
                    $query->whereHas('preparinglog', function($qdate) use ($date){
                        $qdate->where('order_status', 'preparing')
                        ->where('created_at', '>=', $date)->orderBy('id', 'desc');
                    });
                })
                ->when((!$request->waiting_stats) , function($query){  
                    $query->whereIn('status',['assigned','completed',]);
                })
                ->when(($request->waiting_stats == 1) , function($query){ 
                    $query->where('order_status', '!=', 'preparing')
                    ->where('status','completed');
                }) 
                ->select(DB::raw('qgo_qr_codes.id,qgo_users.name,qgo_qr_codes.order_no,qgo_qr_codes.order_status,qgo_order_logs.created_at as createdAt, qgo_qr_codes.updated_at as completedAt'))
                ->groupBy('qr_codes.id')
                ->get();
                
                if($data->isNotEmpty()){
                    return $this->exportCsv($data);
                }
                return response()->json(['status' => false, 'message' =>  Config::get('constants.ERROR.NO_ORDER_YET')], 404);
            }

            $checked_user = null;
            $newuser = 0;
            $returning = 0;
            foreach($data as $stat){
                $userquees = QrCode::where(['user_id' => $stat->user_id, 'vendor_id' => $stat->vendor_id])->get();
                if($checked_user != $stat->user_id)
                {
                    if(count($userquees) > 1){
                    $returning++;
                    }
                    else{
                        $newuser++;
                    }
                    $checked_user = $stat->user_id;
                }
            }

            $prevData =['prev_new'=> 0, 'prev_returning'=> 0];
            $increased_returning = 0;
            $decreased_returning = 0;
            $increased_new = 0;
            $decreased_new = 0;
            if($request->require_for == 'today') {
                $prevData = $this->getPrevStatics( 'yesterday', $request->waiting_stats); 
            }
            if($request->require_for == 'last_7days') {
                $prevData = $this->getPrevStatics( 'prev_week', $request->waiting_stats);
            }
            if($request->require_for == 'last_30days') {
                $prevData = $this->getPrevStatics( 'prev_month', $request->waiting_stats);
            }
            if($request->require_for == 'last_90days') {
                $prevData = $this->getPrevStatics( 'prev_3month', $request->waiting_stats);
            }
            if($request->require_for == 'this_year') {
                $prevData = $this->getPrevStatics( 'prev_year', $request->waiting_stats);
            }
            if($prevData['prev_new'] != $newuser){
                if($newuser != 0 && $prevData['prev_new'] == 0){
                    $increased_new = 100*$newuser;
                } elseif($newuser == 0 && $prevData['prev_new'] != 0){
                    $decreased_new = 100*$prevData['prev_new'];
                }
                elseif($prevData['prev_new'] && $prevData['prev_new']<$newuser) { 
                    $increased_new =  (($newuser-$prevData['prev_new'])/$prevData['prev_new'])*100;
                } else { 
                    $decreased_new =  (($prevData['prev_new']-$newuser)/$newuser)*100;
                }
            }
            
            if($prevData['prev_returning'] != $returning){
                if($prevData['prev_returning'] == 0 && $returning != 0){
                    $increased_returning = 100*$returning;
                } elseif($prevData['prev_returning'] != 0 && $returning == 0){
                    $decreased_returning = 100*$prevData['prev_returning'];
                }
                elseif($prevData['prev_returning'] && $prevData['prev_returning']<$returning) {
                    $increased_returning =  (($returning-$prevData['prev_returning'])/$prevData['prev_returning'])*100;
                } else { 
                    $decreased_returning =  (($prevData['prev_returning']-$returning)/$returning)*100;
                }
            } 
            return response()->json([
                'status' => true,
                'queues' => $data,
                'user_stats'=>[
                    'new'=>$newuser,
                    'returning'=>$returning, 
                    'increased_new'=>$increased_new,
                    'decreased_new'=>$decreased_new,
                    'increased_returning'=>$increased_returning,
                    'decreased_returning'=>$decreased_returning,
                ], 
                'message' => 'List of stats',  
            ], 200);  
        } catch ( \Exception $e ) { 
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400);
        } 
    }

    public function getPrevStatics($require_for, $waiting_stats) {   
        try {
            $userId = auth()->user()->id;  
            $start_date = "";
            $end_date = "";
            if($require_for == 'yesterday'){
                $start_date = Carbon::yesterday();
                $end_date  = Carbon::today();   
            }
            if($require_for == 'prev_week'){
                $start_date = Carbon::now()->subDays(7)->startOfWeek();
                $end_date  = Carbon::now()->startOfWeek();   
            }
            if($require_for == 'prev_month'){
                $start_date = Carbon::now()->subMonth(2);
                $end_date = Carbon::now()->subMonth(1);   
            }
            if($require_for == 'prev_3month'){  
                $start_date =  Carbon::now()->subMonth(6);
                $end_date = Carbon::now()->subMonth(3);  
            }
            if($require_for == 'prev_year'){  
                $start_date =  Carbon::now()->subMonth(24);
                $end_date = Carbon::now()->subMonth(12);  
            } 

            $data = QrCode::with('logs:id,order_id,order_status,created_at')
                ->where( 'vendor_id', $userId ) 
                ->whereNotNull('user_id')
                ->when($start_date , function($query) use ($start_date,$end_date){ 
                    $query->whereBetween('created_at', [$start_date,$end_date]); 
                })
                ->when((!$waiting_stats) , function($query){  
                    $query->whereIn('status',['assigned','completed',]);
                })
                ->when(($waiting_stats == 1) , function($query){ 
                    $query->where('order_status', '!=', 'preparing')
                    ->where('status','completed');
                })
                ->get();

            $checked_user = null;
            $newuser = 0;
            $returning = 0;
            foreach($data as $stat){
                $userquees = QrCode::where(['user_id' => $stat->user_id, 'vendor_id' => $stat->vendor_id])->get();
                if($checked_user != $stat->user_id)
                {
                    if(count($userquees) > 1){
                    $returning++;
                    }
                    else{
                        $newuser++;
                    }
                    $checked_user = $stat->user_id;
                }
            }
            return ['prev_new'=>$newuser, 'prev_returning'=>$returning];  

        } catch ( \Exception $e ) { 
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400);
        } 
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
            array_unshift($tasks, array_keys($tasks[0]));

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

     
}