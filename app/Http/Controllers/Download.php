<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Queue;
use Carbon\Carbon;

class Download extends Controller
{

    /**
     * @param Request $request
     * @param $vendor_id
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function exportCsv(Request $request, $vendor_id){

        switch($request->stat){
            case 'today':
                $stats = Queue::where('vendor_id', $vendor_id)
                ->whereBetween('created_at', [Carbon::today(), Carbon::now()])->get();
                break;
            case 'last_7days':
                $stats = Queue::where('vendor_id', $vendor_id)
                ->whereBetween('created_at', [ Carbon::now()->subweek(), Carbon::today()])->get();
                $email = Queue::where('vendor_id', $vendor_id)->with('user')
                ->whereBetween('created_at', [ Carbon::now()->subweek(), Carbon::today()])->get()->pluck('user.name', 'user.email');
                break;

            case 'last_30days':
                $stats = Queue::where('vendor_id', $vendor_id)
                ->whereBetween('created_at', [Carbon::now()->subMonth(), Carbon::today()])->get();
                $email = Queue::where('vendor_id', $vendor_id)->with('user')
                ->whereBetween('created_at', [Carbon::now()->subMonth(), Carbon::today()])->get()->pluck('user.name', 'user.email');
                break;

            case 'last_90days':
                $stats = Queue::where('vendor_id', $vendor_id)
                ->whereBetween('created_at', [ Carbon::now()->subMonth(3), Carbon::today()])->get();
                $email = Queue::where('vendor_id', $vendor_id)->with('user')
                ->whereBetween('created_at', [ Carbon::now()->subMonth(3), Carbon::today()])->get()->pluck('user.name', 'user.email');
                break;

            case 'this_year':
                $stats = Queue::where('vendor_id', $vendor_id)
                ->whereBetween('created_at', [ Carbon::now()->startOfYear(),Carbon::now()->endOfYear()])->get();
                $email = Queue::where('vendor_id', $vendor_id)->with('user')
                ->whereBetween('created_at', [ Carbon::now()->startOfYear(),Carbon::now()->endOfYear()])->get()->pluck('user.name', 'user.email');
                break;

            case 'all_stats':
              $email = Queue::where('vendor_id', $vendor_id)->with('user')->get()->pluck('user.name', 'user.email');
              $stats = Queue::where('vendor_id', $vendor_id)->get();
                break;
            default:
                throw new \Exception('Unexpected value');
        }

        $fileName = 'tasks.csv';

        if (!empty($request->download)) {
            if($request->download == 'stats'){
                if($stats){
                    $tasks = $stats->toArray();
                }
            }
           else {
               if($email){
                $tasks[] = $email->toArray();
               }
           }
        }
        if($tasks){
            $headers = [
                'Content-Type' => 'application/octet-stream',
                "Content-Description" => "File Transfer",
                "Cache-Control" => "public",
                'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
             ];
                 // array_unshift($tasks, array_keys($tasks[0]));


                    $callback = function() use($tasks) {
                        $file = fopen('php://output', 'w');
                        fputcsv($file, ['Order ID,', 'Order Date,', 'Scan Time', 'Ready Time', 'Collected Time']);

                        foreach($tasks as $key) {
                            fputcsv($file, [$key['ticket_num'], $key['created_at'], $key['created_at'], $key['created_at'], $key['ready_at'], $key['updated_at']]);
                        }


                    fclose($file);
                 };

                return response()->stream($callback, 200, $headers);
                //return response()->download($callback, 'tasks.csv', $headers);

            }
        }


}
