<?php

namespace App\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{Config, Validator,Hash,DB}; 
use SimpleSoftwareIO\QrCode\Facades\QrCode; 
use App\Traits\{AutoResponderTrait, CommanTrait};
use Illuminate\Validation\Rule; 
use Illuminate\Support\Str;
use Illuminate\Http\Request; 
use App\Events\SendVerificationEmail;

use Illuminate\Support\Facades\Auth;
use App\Models\{User, Vendor, PasswordReset, Otp, Address, State, Wallet, Setting}; 
use Exception,Event, JWTAuth;

class AuthController extends Controller
{ 
    use AutoResponderTrait, CommanTrait;  
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'socialLogin', 'miniRegister', 'verifyEmail', 'resendOtp', 'passwordResetLink', 'updateNewPassword', 'verifyOtp']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */ 

    public function login(Request $request){

        if(is_numeric($request->get('email'))){  
            $validator = Validator::make($request->all(), [
                'email' => 'required|numeric',
                'password' => 'required|string|min:6',
            ]);
            $cred = ['phone_number' => $request->email, 'password' => $request->password];
        }
        elseif (filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) { 
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);
            $cred = ['email' => $request->email, 'password' => $request->password];
        } 

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        } 

        $usersDetail = User::where('email', $request->email)->orWhere('phone_number', $request->email)->first();
        if($usersDetail && Hash::check($request->password, $usersDetail->password)){
            if($usersDetail->phone_verified == null && $usersDetail->social_type == null) {
                return response()->json([ 'status' => false, 'message' => 'Phone number is not verified','user_id' => $usersDetail->id], 400);
            } 
        } else { 
            return response()->json([ 'status' => false, 'message' => Config::get('constants.ERROR.WRONG_CREDENTIAL')], 401);
        }  
        if(! $token = auth()->attempt($cred)) { 
            return response()->json([ 'status' => false, 'message' => Config::get('constants.ERROR.WRONG_CREDENTIAL')], 401);
        } 
        $lastdata = $this->updateLastLogin(auth()->user()->id);

        $tokendata = $this->createNewToken($token);

        $user = User::where('id', auth()->user()->id)->first();
        $logo = '';
        if($user->role == 'vendor') {
            $logoDetail = Vendor::where('user_id',  $user->id)->first();
            $logo = isset($logoDetail->logo) ? $logoDetail->logo : ''; 
        }
        $data = array_merge($tokendata, ['logo' => $logo]);
        $user = User::where('id', auth()->user()->id)->update( [ 'last_login' => now() ]);

        return response()->json($data, 200); 

    }

    /*
    Social Login
    */ 
    public function socialLogin(Request $request){
    	$validator = Validator::make($request->all(), [
            'social_id' => 'required',
            'social_type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        } 

        $usersDetail = User::where('social_type', $request->social_type)->where('social_id', $request->social_id)->first(); 
        if(!$usersDetail) { 
            $userData =[
                'social_type' => $request->social_type,
                'social_id' => $request->social_id,
                'role' => 'customer',
            ]; 
            if($request->social_type == 'google') {
                $userData['name'] = $request->name;
                $userData['email'] = $request->email;  
            }
            $usersDetail = User::create($userData);
        } 

        if(! $token = JWTAuth::fromUser($usersDetail)) {
            return response()->json([ 'status' => false, 'message' => Config::get('constants.ERROR.WRONG_CREDENTIAL')], 401);
        } 
        JWTAuth::setToken($token)->toUser();
        $lastdata = $this->updateLastLogin(auth()->user()->id);
        $tokendata = $this->createNewToken($token);
        $data = array_merge($tokendata);
        $user = User::where('id', auth()->user()->id)->update( [ 'last_login' => now() ]);

        return response()->json($data, 200); 

    } 

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'country_code' => 'required',
            'phone_number' => 'required|unique:users',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'password_confirmation' => 'required|same:password', 
            'role' => 'required',
            'business_name' => Rule::requiredIf($request->role == 2),
            'abn' => Rule::requiredIf($request->role == 2).'|unique:vendors', 
        ]);

        if($validator->fails()){    
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        }

        DB::beginTransaction();
        $user = User::create(array_merge( $validator->validated(), ['password' => bcrypt($request->password), 'profile_image' =>'https://via.placeholder.com/640x480.png/009a73?text='.ucfirst(substr($request->name, 0, 1))] ));

        if($user && $request->role == 2) {
            
            $data = [
                'business_name' => $request->business_name,
                'abn' => $request->abn,
                'waiting_message' => Config::get('constants.SUCCESS.DEFAULT_WAIT_MSG'),
                'ready_message' => Config::get('constants.SUCCESS.DEFAULT_READY_MSG'),
                'user_id' => $user->id,  
                'logo' => 'https://via.placeholder.com/640x480.png/009a73?text='.ucfirst(substr($request->business_name, 0, 1)),
            ];
            $response = Vendor::create($data);
            $addressData = [
                'country' => 'Australia', 
                'user_id' => $user->id, 
            ];
            
            $addressDetail = Address::create( $addressData );
            $walletDetail = Wallet::create([ 'user_id' => $user->id ]);
            $settings = Setting::insert([ 
                [
                    'vendor_id' => $user->id,
                    'key' => 'top-up',
                    'name' => 'previous_payment',
                    'value' => 0,
                ],
                [
                    'vendor_id' => $user->id,
                    'key' => 'top-up',
                    'name' => 'auto_top_up',
                    'value' => false,
                ],
                [
                    'vendor_id' => $user->id,
                    'key' => 'top-up',
                    'name' => 'default_payment',
                    'value' => false,
                ],
            ]);

            // $qr = QrCode::size(50)->color(0,154,116)->generate('https://qgo-app.com/?vendor_id='.$user->id);
            // $vendor = Vendor::where('id',$response->id)->update(['qrcode' => base64_encode($qr)]);

            if (!$response) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => Config::get('constants.ERROR.FORBIDDEN_ERROR')
                ], 400);
            }
        }
        //working
        if($user) {            
            /*Send OTP*/
            $otp = $this->generateOtp();
            $phone = $request->country_code.$request->phone_number;
            $sendOtp = Otp::updateOrCreate(
                ['user_id' => $user->id],
                ['phone_number' => $phone, 'otp' => $otp]
            );
            if($sendOtp) {
                $result = $this->sendMessage($phone, "Your Q & Go OTP is ".$otp);  
                if(!$result)
                {
                    return response()->json([
                        'status' => false,
                        'message' => Config::get('constants.ERROR.FORBIDDEN_ERROR')
                    ], 403);
                }
            }
            /* */ 
        } else {
            return response()->json([
                'status' => false,
                'message' => Config::get('constants.ERROR.FORBIDDEN_ERROR')
            ], 403);
        }
        
        /*Send Verification Link*/  
            Event::dispatch(new SendVerificationEmail($user->id));
        /*end*/

        DB::commit();
        return response()->json([
            'status' => true,
            'message' => Config::get('constants.SUCCESS.ACCOUNT_CREATED'),
            'user' => $user
        ], 201);
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();

        return response()->json(['message' => Config::get('constants.SUCCESS.LOG_OUT')], 200);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        $user = auth()->user();
        $states="";
        if($user->role == 'vendor'){ 
            $user = Vendor::where('user_id',  $user->id)->with(['address.state', 'user'])->first();
            $states = State::select('id','name')->where('status',1)->get(); 
        } 
        return response()->json([ 'status' => true, 'data' => $user, 'states' => $states ], 200);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){ 
        $data =  [
            'status' => true,
            'access_token' => $token,
            'token_type' => 'bearer', 
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]; 
        return $data;
    }

    public function verifyEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',  
        ]);

        if($validator->fails()){
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        }

        $passwordReset = PasswordReset::where('token', $request->token)->first();
        if (!$passwordReset) {
            return response()->json([ 'status' => false, 'message' => Config::get('constants.ERROR.VERIFY_TOKEN_INVALID')], 403); 
        } else {
            $users = User::where('email', $passwordReset->email)->update(['verified_at' => now()]);
            $usersDetail = User::where('email', $passwordReset->email)->first();
            if ($users) {
                $passwordReset->delete();
                return response()->json([
                    'status' => true,
                    'role' => $usersDetail->role, 
                    'message' => Config::get('constants.SUCCESS.WELCOME_LOGIN')
                ], 200); 
            } 
        }
    }
 
    public function passwordResetLink(Request $request )
    { 
        
        $validator = Validator::make($request->all(), [ 'email' => 'required|email' ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422);            
        } 
        try {
            $user = User::where('email', $request->email)->first(); 

            if (!$user) {
                return response()->json([
                    'status' => false,   
                    'message' => Config::get('constants.ERROR.WRONG_CREDENTIAL')
                ], 400);
            } else { 
                $passwordReset = PasswordReset::updateOrCreate(['email' => $user->email], ['email' => $user->email, 'token' => Str::random(12) ]);

                $link = config('app.front_url')."/reset-password?token=".$passwordReset->token ; 

                $template = $this->get_template_by_name('FORGOT_PASSWORD');

                $string_to_replace = [ '{{$name}}', '{{$token}}' ];
                $string_replace_with = [ $user->name, $link ];

                $newval = str_replace($string_to_replace, $string_replace_with, $template->template);
 
                $result = $this->send_mail($user->email, $template->subject, $newval, $cc=null, 'FORGOT_PASSWORD');
            
                if ($result) { 
                    return response()->json([
                        'status' => true,   
                        'message' => Config::get('constants.SUCCESS.RESET_LINK_MAIL')
                    ], 200);    
                }
                return response()->json([
                    'status' => false,   
                    'message' => Config::get('constants.ERROR.OOPS_ERROR')
                ], 400);  
            } 
        } catch ( \Exception $e ) { 
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400);
        }
    }

    /* End Method passwordResetLink */

    public function verifyOtp(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'otp' => 'required|min:6|max:6',
        ]);

        if($validator->fails()){
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        }
        $userId = $request->user_id;
        $otp = $request->otp;
        $response = Otp::where('user_id', $userId )->where( 'otp', $otp )->first(); 
        if (!$response) {
            return response()->json([ 'status' => false, 'message' => Config::get('constants.ERROR.OTP_INVALID')], 401); 
        } else {
            $response = User::where('id', $userId )->update(['last_login' => null, 'phone_verified' => now()]);
            if($response) { 
                Otp::where('user_id', $userId )->where( 'otp', $otp )->delete(); 
                $data = User::where('id', $userId )->first(); 
                return response()->json([
                    'status' => true,
                    'userData' => $data,
                    'message' => 'Phone number '.Config::get('constants.SUCCESS.OTP_VERIFIED')
                    ,
                ], 200); 
            } else {
                return response()->json([
                    'status' => false,
                    'message' => Config::get('constants.ERROR.FORBIDDEN_ERROR')
                ], 403);
            } 
        }
    }

    
    public function resendOtp(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'user_id' => 'required', 
        ]);

        if($validator->fails()){
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        }
        $userId = $request->user_id; 
        $response = Otp::where('user_id', $userId )->first();
        
        if (!$response) {
            return response()->json([ 'status' => false, 'message' => Config::get('constants.ERROR.FORBIDDEN_ERROR')], 401); 
        } else { 
            $resend = $this->sendOtp($response->phone_number, $response->otp);  
            return response()->json([
                'status' => true, 
                'message' => 'OTP '.Config::get('constants.SUCCESS.RESEND_DONE'),
            ], 200); 
            
        }
    }

    public function updateNewPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'token' => 'required', 
            'password' => 'required|string|confirmed|min:6',
            'password_confirmation' => 'required|same:password',
        ]);

        if($validator->fails()){
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        }

        $isRequested = PasswordReset::where('token', $request->token)->first(); 

        if (!$isRequested){ 
            return response()->json([
                'status' => false,   
                'message' => Config::get('constants.ERROR.TOKEN_EXPIRED')
            ], 401);  
        } 

        try {
            $email = $isRequested->email; 
            $data = [
                'password' => bcrypt($request->password),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $record = User::where('email', $email)->update($data);
            if($record) {
            PasswordReset::where('email', $email)->delete();  
            $role = User::where('email', $email)->first();
            $role = $role->role; 
                return response()->json([
                    'status' => true,
                    'role' => $role,
                    'message' => 'Password '.Config::get('constants.SUCCESS.UPDATE_DONE')
                ], 200); 
            } 
            return response()->json([
                'status' => false,   
                'message' => Config::get('constants.ERROR.OOPS_ERROR')
            ], 400); 
            
        } catch(\Exception $e) { 
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400); 
        }

    }
    /* End Method updateNewPassword */
    
    public function updatePassword(Request $request){
        $validator = Validator::make($request->all(), [ 
            'current_password' => 'required', 
            'password' => 'required', 
            'confirm_password' => 'required|same:password', 
            ]); 
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422);            
            }  
            
        try {
            $id = Auth::user()->id; 
            $record = User::where(['id' => $id])->first(); 
            if (Hash::check($request->current_password,$record->password)) { 
                $data = [
                    'password' => bcrypt($request->password),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $record = User::where('id', $id)->update($data);

                return response()->json([
                    'status' => true,
                    'message' => 'User password '.Config::get('constants.SUCCESS.UPDATE_DONE')
                ], 200);  
 
            } else { 
                return response()->json([
                    'status' => false,   
                    'message' => 'Current password is wrong',
                ], 400); 
            } 
        } catch ( \Exception $e ) { 
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400); 
        }
    }
    /* End Method updatePassword */    

    /* Store Device ID For push notification */ 
    public function storeDeviceId(Request $request){
        $validator = Validator::make($request->all(), [ 
            'device_type' => 'required', 
            'device_token' => 'required',  
            ]); 
            if ($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422);            
            }  
            
        try {
            $userId = Auth::user()->id; 
            $record = User::where(['id' => $userId])->first(); 
            if ($record) { 
                $data = [
                    'device_type' => $request->device_type, 
                    'device_token' => $request->device_token,  
                ];
                $record = User::where('id', $userId)->update($data);

                return response()->json([
                    'status' => true,
                    'message' => 'User device id  '.Config::get('constants.SUCCESS.STORE_DONE')
                ], 200);  
 
            } else { 
                return response()->json([
                    'status' => false,   
                    'message' => Config::get('constants.ERROR.WRONG_CREDENTIAL')
                ], 400); 
            } 
        } catch ( \Exception $e ) { 
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400); 
        }
    }
    /* End Method storeDeviceId */

    /* Mini registration form*/ 
    public function miniRegister(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'country_code' => 'required',
            'phone_number' => 'required|unique:users',
            'email' => 'required|string|email|max:100|unique:users',  
        ]);

        if($validator->fails()){    
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        }

        DB::beginTransaction();
        $user = User::create(array_merge( $validator->validated(), ['role' => 1, 'password' => bcrypt($request->phone_number), 'profile_image' =>'https://via.placeholder.com/640x480.png/009a73?text='.ucfirst(substr($request->name, 0, 1))] )); 
        //working
        if($user) {

            /*Send OTP*/
            $otp = $this->generateOtp();
            $phone = $request->country_code.$request->phone_number;
            $sendOtp = Otp::updateOrCreate(
                ['user_id' => $user->id],
                ['phone_number' => $phone, 'otp' => $otp]
            );
            if($sendOtp) {
                $result = $this->sendMessage($phone, "Your Q & Go OTP is ".$otp);
                if(!$result)
                {
                    return response()->json([
                        'status' => false,
                        'message' => Config::get('constants.ERROR.FORBIDDEN_ERROR')
                    ], 403);
                }
            }
            /* */ 
        } else {
            return response()->json([
                'status' => false,
                'message' => Config::get('constants.ERROR.FORBIDDEN_ERROR')
            ], 403);
        }
        
        /*Send Verification Link*/  
            $passwordReset = PasswordReset::updateOrCreate(['email' => $user->email], ['email' => $user->email, 'token' => Str::random(12) ]);

            $link = config('app.front_url')."/reset-password?token=".$passwordReset->token ; 

            $template = $this->get_template_by_name('SET_PASSWORD');

            $string_to_replace = [ '{{$name}}', '{{$token}}' ];
            $string_replace_with = [ $user->name, $link ];

            $newval = str_replace($string_to_replace, $string_replace_with, $template->template);

            $result = $this->send_mail($user->email, $template->subject, $newval, $cc=null, 'SET_PASSWORD');
        /*end*/
        $auth = ['email' => $user->email, 'password' => $request->phone_number];
        if(! $token =  auth()->attempt($auth)) {
            return response()->json([ 'status' => false, 'message' => Config::get('constants.ERROR.WRONG_CREDENTIAL')], 401);
        }  

        DB::commit(); 
        $data = array_merge($this->createNewToken($token),['otp' => $otp, 'message' => Config::get('constants.SUCCESS.ACCOUNT_CREATED'),]);

        return response()->json($data, 200);
    }


       /* Get all states */ 
       public function getStates(Request $request){ 
            
        try { 
            $response = State::select('id','name')->where('status',1)->get(); 
            if ($response) {  
                return response()->json([
                    'status' => true,
                    'data' => $response,
                    'message' => 'List Of all the states',
                ], 200);  
 
            }  
                return response()->json([
                    'status' => false,   
                    'message' => 'States '.Config::get('constants.ERROR.NOT_EXIST')
                ], 400);  
        } catch ( \Exception $e ) { 
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400); 
        }
    }
    /* End Method states*/

}