<?php

namespace App\Http\Controllers\V1\Api; 

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Card as CardModel;
use App\Models\{User, Setting};
use App\Http\Resources\CardResource;    
use App\Models\Message as MessageModel;
use Carbon\Carbon; 
use Illuminate\Support\Facades\{Config, Validator }; 

// use App\Traits\{AutoResponderTrait, CommanTrait}; 
use Exception;

class CardController extends Controller
{
    public $stripe;
    // use AutoResponderTrait, CommanTrait;  

    public function __construct() {

        $this->middleware('auth:api', ['except' => ['']]);
        $this->stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

    }

    public function store(Request $request)
    {  
        $validator = Validator::make($request->all(), [
            'token_id' => 'required', 
            'paymentmethod_id' => 'required',  
        ]);

        if($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        }
        // $all = $this->stripe->customers->all();
        // dd($all->toArray());

        $userId = auth()->user()->id;  
        $userEmail = auth()->user()->email;   
        try { 
            $card = CardModel::firstWhere('user_id', $userId); 
            if($card){ 
                $customer = $this->stripe->customers->retrieve(
                    $card->stripe_customer_id,
                    []
                );
            } else { 
                $customer = $this->stripe->customers->create([
                    'id' => $userId, 
                    'email' => $userEmail,
                ]); 
            }

            $source = $this->stripe->customers->createSource(
                $customer->id,
                ['source' => $request->token_id]
            );
             
            $pm = $this->stripe->paymentMethods->attach(
                $request->paymentmethod_id,
                ['customer' => $customer->id]
            );
        } catch (\Exception $e) {
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400);  
        }
        $card = CardModel::where('user_id', $userId)->update(['isDefault' => 0]); 
        $saveCard = CardModel::updateOrCreate(['fingerprint' => $source->fingerprint],[
            'user_id'=>$userId,
            // 'token_id' => $request->token_id,
            'token_id' => $source->id,
            'stripe_customer_id' => $pm->customer,
            'fingerprint' => $source->fingerprint,
            'isDefault'=> 1,
            'last4' => $pm->card->last4,
            'paymentmethod_id'=>$pm->id,

        ]);
        return response()->json([
            'status' => true,
            'card' => $saveCard,
            'message' => 'Card '.Config::get('constants.SUCCESS.CREATE_DONE')
            ,
        ], 200); 
        
        // return $saveCard;
    }
    public function show()
    { 
        $userId = auth()->user()->id;
        $card = CardModel::where(['user_id' => $userId, 'isDefault' => 1])->first();
        $data="";
        if($card){
            try {
                $card_details = $this->stripe->paymentMethods->retrieve(
                    $card->paymentmethod_id,
                    []);
                } catch (\Exception $e) {
                    return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400); 
                }
                $data = new CardResource($card_details);
                
                return response()->json([
                    'status' => true,
                    'card' => $data,
                    'message' => 'Card detaiil',
                ], 200); 
            } 
       
        return response()->json([
            'status' => true, 
            'message' => Config::get('constants.SUCCESS.CARD_NOT_EXIST'),
        ], 200); 

    }
    
    public function showAll()
    { 
        $userId = auth()->user()->id;
        $cards = CardModel::where(['user_id' => $userId])->get();
        $data = [];
        $cardDetail = [];
        $default = 0;
        if($cards){ 
            foreach($cards as $key => $card) { 
                $cardDetail[] = $this->stripe->customers->retrieveSource(
                    $card->stripe_customer_id,
                    $card->token_id,
                    []
                  ); 
                  $cardDetail[$key]['isDefault'] =  $card->isDefault; 
                  $cardDetail[$key]['card_update_id'] =  $card->id; 
            }
            return response()->json([
                'status' => true, 
                'card' => $cardDetail,
                'message' => 'Card list',
            ], 200); 
        } 
        return response()->json([
            'status' => true, 
            'message' => Config::get('constants.SUCCESS.NO_CARD'),
        ], 200); 

    }

    public function setDefault(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'id' => 'required',  
        ]);

        if($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        }

        $userId = auth()->user()->id;
        $card = CardModel::where(['user_id' => $userId])->update(['isDefault' => 0]);
        $card = CardModel::where(['user_id' => $userId,'id' => $request->id])->update(['isDefault' => 1]);
        if($card){ 
            return response()->json([
                'status' => true, 
                'message' => 'Updated',
            ], 200); 
        } 
        return response()->json([
            'status' => true, 
            'message' => 'Card not Exist',
        ], 200); 

    }

    public function update(Request $request)
    {  
        $validator = Validator::make($request->all(), [
            'token_id' => 'required', 
            'paymentmethod_id' => 'required',  
        ]);

        if($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        }

        $userId = auth()->user()->id;
        $card = CardModel::where(['user_id' => $userId, 'isDefault' => 1])->first();
        $card->update(['isDefault' => 0]);

        try {
            $customer = $this->stripe->customers->create([
                'source' => $request->token_id,
            ]);
            $pm = $this->stripe->paymentMethods->attach(
                $request->paymentmethod_id,
                ['customer' => $customer->id]
            );
        } catch (\Exception $e) {
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400); 
        }
        $saveCard = CardModel::create([
            'user_id'=>$userId,
            'token_id' => $request->token_id,
            'stripe_customer_id' => $pm->customer,
            'isDefault'=> 1,
            'last4' => $pm->card->last4,
            'paymentmethod_id'=>$pm->id,

        ]);
        
        return response()->json([
            'status' => true,
            'message' => 'Card '.Config::get('constants.SUCCESS.UPDATE_DONE')
        ], 200);  
        
        // return $saveCard;
    }
    
    public function getVendorDetails()
    { 
        $userId = auth()->user()->id;
        $settings = Setting::where('vendor_id', $userId)->get(); 
        $data = [];
        if($settings) { 
            foreach($settings as $key => $setting ){
                $data[$setting->name] =  $setting->value;
            }
            /*Average count*/ 
            $filter = [ Carbon::now()->subMonth(), Carbon::today() ];
            $response = MessageModel::where('vendor_id', $userId)->where('type', 'marketing')->where('marketing', 'sms')->whereBetween('created_at', $filter)->sum('count');
            $smsPrice = config('app.sms_price');
            $total = $response*$smsPrice ;
            $data['average30days'] = number_format((float)$total, 2, '.', ''); 
            /*End average*/ 
            return response()->json([
                'status' => true,
                'vendor' => $data,
                'message' => 'Vendor setting detaiils',
            ], 200);
        }
        return response()->json([
            'status' => true,  
            'vendor' => $user,
            'message' => 'Vendor not exist',
        ], 200); 

    }
    public function updateVendorDetails(Request $request)
    {   
        
        $userId = auth()->user()->id;
        
        if($request->has('default_payment')) {
            $validator = Validator::make($request->all(), [
                'default_payment' => 'required',    
            ]);
    
            if($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
            } 
            $SettingUpdate = Setting::where('vendor_id', $userId)->where('name','default_payment')->update(['value' => $request->default_payment]);  
        } else {
            $validator = Validator::make($request->all(), [
                'auto_top_up' => 'required',  
                'previous_payment' => 'required',  
            ]);
    
            if($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
            } 
            $SettingUpdate = Setting::where('vendor_id', $userId)->where('name','auto_top_up')->update(['value' => $request->auto_top_up]);  
            $SettingUpdate = Setting::where('vendor_id', $userId)->where('name','previous_payment')->update(['value' => $request->previous_payment]);  
        }
        $settings = Setting::where('vendor_id', $userId)->get(); 
        $data = []; 
        if($settings) { 
            foreach($settings as $key => $setting ){
                $data[$setting->name] =  $setting->value;
            }
            return response()->json([
                'status' => true,
                'vendor' => $data,
                'message' => 'Vendor setting detaiils '.Config::get('constants.SUCCESS.UPDATE_DONE'),
            ], 200);
        }
        return response()->json([
            'status' => true,  
            'vendor' => $data,
            'message' => 'Vendor '.Config::get('constants.ERROR.NOT_EXIST'),
        ], 200); 

    }
}
