<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\{State }; 

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $exist = DB::table('states')->where('name', 'ACT')->count();
        if($exist == 0){
            $remindData =[
                'name' => 'ACT', 
            ]; 
            $response = State::create($remindData);
            
        }

        $exist = DB::table('states')->where('name', 'NSW')->count();
        if($exist == 0){
            $remindData =[
                'name' => 'NSW', 
            ]; 
            $response = State::create($remindData);
            
        }

        $exist = DB::table('states')->where('name', 'NT')->count();
        if($exist == 0){
            $remindData =[
                'name' => 'NT', 
            ]; 
            $response = State::create($remindData);
            
        }
        
        $exist = DB::table('states')->where('name', 'QLD')->count();
        if($exist == 0){
            $remindData =[
                'name' => 'QLD', 
            ]; 
            $response = State::create($remindData);
            
        }
        
        $exist = DB::table('states')->where('name', 'SA')->count();
        if($exist == 0){
            $remindData =[
                'name' => 'SA', 
            ]; 
            $response = State::create($remindData);
            
        }
        
        $exist = DB::table('states')->where('name', 'WA')->count();
        if($exist == 0){
            $remindData =[
                'name' => 'WA', 
            ]; 
            $response = State::create($remindData);
            
        } 
         
        $exist = DB::table('states')->where('name', 'TAS')->count();
        if($exist == 0){
            $remindData =[
                'name' => 'TAS', 
            ]; 
            $response = State::create($remindData);
            
        }

        $exist = DB::table('states')->where('name', 'VIC')->count();
        if($exist == 0){
            $remindData =[
                'name' => 'VIC', 
            ]; 
            $response = State::create($remindData);
            
        }
    }
}
