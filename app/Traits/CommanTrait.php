<?php
 
namespace App\Traits; 
use hisorange\BrowserDetect\Parser as Browser;
 
use App\Models\User ;  
 
trait CommanTrait { 
 
    public function updateLastLogin($id = NULL) {
        if($id){
            $lastLoginData = serialize([
                'ip_address' => request()->ip(),
                'Browser Name' => Browser::browserName(),
                'Operating System' => Browser::platformName(),
                'Agent' => Browser::userAgent(),
            ]);
            $services = User::where('id', $id)
            ->update(['last_login' =>  date('Y-m-d H:i:s'), 'last_login_data' => $lastLoginData]);
        }
    }

    public function updateLastLogindd($id = NULL) {
        if($id){
            $lastLoginData = serialize([
                'ip_address' => request()->ip(),
                'Browser Name' => Browser::browserName(),
                'Operating System' => Browser::platformName(),
                'Agent' => Browser::userAgent(),
            ]);
            $services = User::where('id', $id)
            ->update(['last_login' =>  date('Y-m-d H:i:s'), 'last_login_data' => $lastLoginData]);
        }
    } 
    
    public function orderData() { 
        $lastLoginData = serialize([
            'ip_address' => request()->ip(),
            'Browser Name' => Browser::browserName(),
            'Operating System' => Browser::platformName(),
            'Agent' => Browser::userAgent(),
        ]);
        return $lastLoginData;
    }
}