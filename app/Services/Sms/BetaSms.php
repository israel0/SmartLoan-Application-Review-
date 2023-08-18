<?php

 namespace App\Services\Sms;
 use Illuminate\Support\Facades\Http;
 use  App\Models\Setting;

 class BetaSms 
 {
       public static function betasmsUsername()
       {
          return  Setting::where('setting_key','betasms_email')->first()->setting_value;
       }

       public static function betasmsPassword()
       {
          return  Setting::where('setting_key','betasms_pass')->first()->setting_value;
       }
        
       public static function SendSms($message, $phone)
       {

             $username = Self::betasmsUsername();
             $password = Self::betasmsPassword(); 
             $header_url = "http://login.betasms.com.ng/api/?username=".$username."&password=".$password."&message=".$message."&sender=SmartLoan"."&mobiles=".$phone;
             Http::post($header_url);
          
        }

        public static function balance() {
            $username = Self::betasmsUsername();
            $password = Self::betasmsPassword(); 
            $header_url = "http://login.betasms.com.ng/api/?username=".$username."&password=".$password."&action=balance";
            return Http::get($header_url)->json("balance");
        }

 }