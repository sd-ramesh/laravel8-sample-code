<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Queue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;



class Statistics extends Controller
{

   public function getQueue(Request $request, $vendor_id){

    //   $stats = Queue::where('vendor_id', $vendor_id);
      switch($request->stat){
        case 'today':
            $stats = Queue::where('vendor_id', $vendor_id)
            ->whereBetween('created_at', [Carbon::today(), Carbon::now()])->get();
            if($request->export == true){
                $this->exportCsv($stats);
            }
            break;

        case 'last_7days':
            $stats = Queue::where('vendor_id', $vendor_id)
            ->whereBetween('created_at', [ Carbon::now()->subweek(), Carbon::today()])->get();
            if($request->export == true){
                $this->exportCsv($stats);
            }
            break;

        case 'last_30days':
            $stats = Queue::where('vendor_id', $vendor_id)
            ->whereBetween('created_at', [Carbon::now()->subMonth(), Carbon::today()])->get();
            if($request->export == true){
                $this->exportCsv($stats);
            }
            break;

        case 'last_90days':
            $stats = Queue::where('vendor_id', $vendor_id)
            ->whereBetween('created_at', [ Carbon::now()->subMonth(3), Carbon::today()])->get();
            if($request->export == true){
                $this->exportCsv($stats);
            }
            break;

        case 'this_year':
            $stats = Queue::where('vendor_id', $vendor_id)
            ->whereBetween('created_at', [ Carbon::now()->startOfYear(),Carbon::now()->endOfYear()])->get() ;
            if($request->export == true){
                $this->exportCsv($stats);
            }
            break;

        case 'all_stats':
            $stats = Queue::where('vendor_id', $vendor_id)->get();
            if($request->export == true){
                $this->exportCsv($stats);
            }
      }

      $checked_user = null;
      $newuser = 0;
      $returning = 0;
      foreach($stats as $stat){
        $userquees = Queue::where(['user_id' => $stat->user_id, 'vendor_id' => $stat->vendor_id])->get();
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

        return array(
            'queues'=>$stats,
            'user_stats'=>[
                'new'=>$newuser,
                'returning'=>$returning
            ]
        );

   }

    public function exportCsv($stats)
    {
    $fileName = 'tasks.csv';
    $tasks = $stats->toArray();
       // dd($stats);
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
