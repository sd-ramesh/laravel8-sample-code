<?php
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt; 
use App\Models\{User, Vendor, NotificationLogs}; 
use App\Traits\{AutoResponderTrait};

if (!function_exists('vendorSettings'))
{
    function vendorSettings($id) {
        $detail = Vendor::where('user_id', $id)->first();
        return $detail;
    }
    
}
if (!function_exists('getUserDetail'))
{
    function getUserDetail($id) {
        $detail = User::where('device_token', $id)->first();
        return $detail->id;
    }
    
}
if (!function_exists('userDetail'))
{
    function userDetail($id) {
        $detail = User::where('id', $id)->first();
        return $detail;
    }
    
}
if (!function_exists('send_notification_FCM'))
{
    function send_notification_FCM($device_token, $title, $message, $id,$type) {
        if(is_array($device_token)){
            foreach($device_token as $token){
                fcm_curl($token,$title,$message, $id,$type);
            }
        }else{
           fcm_curl($device_token,$title,$message, $id,$type);
        }
    }
    
}
if (!function_exists('fcm_curl'))
{
    function fcm_curl($device_token, $title, $message, $id,$type){
        $accesstoken = config('app.fcm_key');
        // $accesstoken = 'AAAA1pIO1DU:APA91bHgqrUzKB7OHznSN6nCmTazOkxz3NQlUYNlYysKSqQGDIHlEplnfJTDmoX6fNDKUivsAj23ONbSBXz77QHPQdKgUk35BgeuVhwQyG5fexfzwUEQESjSRpF1tV8wS-U7jHzOGVX4';
        $URL = 'https://fcm.googleapis.com/fcm/send';
        $post_data = '{
            "to" : "' . $device_token . '",
            "data" : {
              "body" : "' . $message . '",
              "title" : "' . $title . '",
              "type" : "' . $type . '",
              "id" : "' . $id . '",
              "message" : "' . $message . '",
            },
            "notification" : {
                 "body" : "' . $message . '",
                 "title" : "' . $title . '",
                 "id" : "' . $id . '",
                 "message" : "' . $message . '",
                "icon" : "new",
                "sound" : "default"
                },
 
          }'; 
        $header = array("authorization: key=" .  $accesstoken . "","content-type: application/json");

 
        $crl = curl_init();
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);
 
        curl_setopt($crl, CURLOPT_URL, $URL);
        curl_setopt($crl, CURLOPT_HTTPHEADER, $header);
 
        curl_setopt($crl, CURLOPT_POST, true);
        curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
    
        $result = curl_exec($crl);  

        if($result === false) {
            $status = 0;
        } else {
            $userId = getUserDetail($device_token);
            $notificationdata = [
                'user_id' => $userId,
                'type' => 'push',
                'send_to' => $device_token,
                'message' => $message, 
            ];
    
            NotificationLogs::create($notificationdata);
            $status = 1;
        }
        return $status;
        
    }
} 
   


