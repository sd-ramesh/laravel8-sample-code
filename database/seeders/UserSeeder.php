<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\{User, Vendor, Address, Wallet, Setting}; 

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usercount = DB::table('users')->where('email', 'testvendor@yopmail.com')->count();
        if($usercount == 0){
            $userData =[
                'name' => 'Vendor',
                'country_code' => '61',
                'phone_number' => 9999999999,
                'email' => 'testvendor@yopmail.com',
                'profile_image'  => 'https://via.placeholder.com/640x480.png/009a73?text=V',
                'password' => bcrypt(123456), 
                'phone_verified'=> now(),
                'role' => 'vendor', 
            ];

            $user = User::create($userData);
            $vendorData = [
                'user_id'  => $user->id,
                'business_name' => 'Test Business',
                'abn'  => 9795551332090,
                'trading_as'  => 'Test trading',
                'waiting_message'  => 'Your order is preparing please wait',
                'ready_message'  => 'Your order is ready please collect',
                'logo'  => 'https://via.placeholder.com/640x480.png/009a73?text=T',
            ]; 

            $vendor = Vendor::create($vendorData);
            $addressData =[
                'state' => 1,
                'country' => 'Australia', 
                'user_id' => $user->id, 
            ]; 
            $addressResponse = Address::create($addressData);

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
        }
        $usercount = DB::table('users')->where('email', 'testcustomer@yopmail.com')->count();
        if($usercount == 0){
            $userData =[
                'name' => 'User',
                'country_code' => '61',
                'phone_number' => 9999999999,
                'email' => 'testcustomer@yopmail.com',
                'profile_image'  => 'https://via.placeholder.com/640x480.png/009a73?text=U',
                'password' => bcrypt(123456),
                'phone_verified'=> now(), 
                'role' => 'customer', 
            ];

            $user = User::create($userData);
            
        } 
    }
}
