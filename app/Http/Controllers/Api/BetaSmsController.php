<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Borrower;
use Illuminate\Http\Request;
use App\Services\Sms\BetaSms;

class BetaSmsController extends Controller
{
       public function betasms_api(Request $request) {
            
             $message = $request->message;
             $phone = $request->phone;


            $response = BetaSms::sendsms($message,$phone);
             return response()->json([
                  "success" => true,
                  "message" => $response
             ]);
       }


            public function getbalance()
            {
                $balance = BetaSms::balance();  
                return $balance;
            }
}
