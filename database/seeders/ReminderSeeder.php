<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\{Reminder }; 

class ReminderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $exist = DB::table('reminders')->where('name', 'Remind once')->count();
        if($exist == 0){
            $remindData =[
                'name' => 'Remind once', 
                'value' => 1, 
            ]; 
            $response = Reminder::create($remindData);
            
        }
        $exist = DB::table('reminders')->where('name', 'Remind 2 times in 2 minutes')->count();
        if($exist == 0){
            $remindData =[
                'name' => 'Remind 2 times in 2 minutes', 
                'value' => 2, 
            ]; 
            $response = Reminder::create($remindData);
            
        }
        $exist = DB::table('reminders')->where('name', 'Remind 5 times in 5 minutes')->count();
        if($exist == 0){
            $remindData =[
                'name' => 'Remind 5 times in 5 minutes', 
                'value' => 5, 
            ]; 
            $response = Reminder::create($remindData);
            
        }
        $exist = DB::table('reminders')->where('name', 'Remind 10 times in 10 minutes')->count();
        if($exist == 0){
            $remindData =[
                'name' => 'Remind 10 times in 10 minutes', 
                'value' => 10, 
            ]; 
            $response = Reminder::create($remindData);
            
        }
    }
}
 

