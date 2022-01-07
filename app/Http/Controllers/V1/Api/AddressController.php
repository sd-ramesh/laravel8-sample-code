<?php

namespace App\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{Config, Validator};  
use App\Http\Requests\UploadImageRequest;
use Illuminate\Http\Request; 
use App\Models\{User, Vendor, Address, Otp}; 
use Illuminate\Support\Facades\Storage;
use App\Traits\{AutoResponderTrait, CommanTrait}; 
use App\Events\SendVerificationEmail;
use Exception,Event, JWTAuth; 

class AddressController extends Controller
{
    use AutoResponderTrait, CommanTrait;  
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['', '']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request) {
        try {
            $userId = auth()->user()->id;
            $user = auth()->user();
            // dd($user);
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|between:2,100',  
            ]);
    
            if($validator->fails()) {
                return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
            }
            
            if ($request->has('phone_number')) { 
                if(!$request->has('otp')){ 
                    $validator = Validator::make($request->all(), [ 
                        'country_code' => 'required',
                        'phone_number' => 'required|unique:users,id,'.$userId,
                    ]);
            
                    if($validator->fails()){
                        return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
                    }
                    /*Send OTP*/
                    $otp = $this->generateOtp();
                    $phone = $request->country_code.$request->phone_number;
                    $sendOtp = Otp::updateOrCreate(
                        ['user_id' => $userId],
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
                        return response()->json([
                            'status' => true,
                            'type' => 'otp',
                            'message' => Config::get('constants.SUCCESS.OTP_SENT')
                        ], 200);

                    }
                    /* */ 
                } else {
                    $validator = Validator::make($request->all(), [
                        'otp' => 'required|min:6|max:6',
                    ]);
            
                    if($validator->fails()){
                        return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
                    }
                    /*Verify OTP*/ 
                    $otp = $request->otp;

                    $response = Otp::where('user_id', $userId )->where( 'otp', $otp )->first(); 
                    if (!$response) {
                        return response()->json([ 'status' => false, 'type' => 'otp', 'message' => Config::get('constants.ERROR.OTP_INVALID')], 401); 
                    } else { 

                        $response = User::where('id', $userId)->update(['phone_verified' => now(), 'country_code' =>$request->country_code, 'phone_number' => $request->phone_number]); 
                        if($response) { 
                            Otp::where('user_id', $userId )->where( 'otp', $otp )->delete();  
                        }

                    }

                }
            }
            
            if ($request->has('email')) { 
                $validator = Validator::make($request->all(), [ 
                    'email' => 'required|string|email|max:100|unique:users,id,'.$userId,
                ]);
        
                if($validator->fails()){
                    return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
                }
                $responseEmail = User::where('id', $userId)->update(['email' => $request->email]); 
                /*Send Verification Link*/  
                Event::dispatch(new SendVerificationEmail($userId));
                /*end*/ 
            }
            
            if ($request->has('profile_image')) { 
                $validator = Validator::make($request->all(), [
                    'profile_image' => 'required|image|mimes:jpeg,png,jpg,svg|dimensions:min_width=200,min_height=200,max_width=500,max_height=500'
                ]);
        
                if($validator->fails()) {
                    return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
                }  
                    $backendPath = config('app.backend_url');
                    $file = $request->file('profile_image');
                    $fileName = $file->store('images/profile_images', 'public_uploads');
                    $url = url('/').'/'.$fileName;
        
                    $res = User::where('id', $userId )->update(['profile_image' => $url]); 
            }
            if($user->role == 'vendor') { 

                $validator = Validator::make($request->all(), [ 
                    'business_name' => 'string|required',
                    // 'trading_as' => 'string',
                    'abn' => 'required|unique:vendors,user_id,'.$userId, 
                    'address' => 'max:255',
                    'suburb' => 'max:255',
                    'postcode' => 'integer',
                    'state' => 'required', 
                ]);
        
                if($validator->fails()){
                    return response()->json($validator->errors()->toJson(), 422);
                }
               
                $businessData = [
                    'business_name' => $request->business_name,
                    'trading_as' => $request->trading_as,
                    'abn' => $request->abn,
                ];
                $businessDetail = Vendor::updateOrCreate(
                    ['user_id' => $userId], $businessData
                );
                $addressData = [
                    'address' => $request->address,
                    'suburb' => $request->suburb,
                    'postcode' => $request->postcode,
                    'state' => $request->state, 
                ];
                $addressDetail = Address::updateOrCreate( ['user_id' => $userId], $addressData );
            }

            $profile = User::where('id', $userId)->update(['name' => $request->name]);
            
            return response()->json([
                'status' => true, 
                'type' => 'profile',
                'message' => 'User '.Config::get('constants.SUCCESS.UPDATE_DONE')  
            ], 200);
        } catch ( \Exception $e ) { 
            return response()->json( [ 'status' => false, 'message' => $e->getMessage() ], 400);
        } 
    }
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logo' => 'required|image|mimes:jpeg,png,jpg,svg|dimensions:min_width=200,min_height=200,max_width=500,max_height=500'
        ]);

        if($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->all()[0] ], 422); 
        } 
            $user = auth()->user(); 
            
            $backendPath = config('app.backend_url');
            $file = $request->file('logo');
            $fileName = $file->store('images/vendor-logo', 'public_uploads');
            $url = url('/').'/'.$fileName;

            $user = Vendor::where('user_id', $user->id)->update(['logo' => $url]);

            return response([
                'status' => true,
                'url' => $url,
                'message' => 'Logo image '.Config::get('constants.SUCCESS.UPLOAD_DONE')
            ], 200); 
    }  

}