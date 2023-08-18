<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Mail\Ticket;
use App\Models\OpenTicket;
use App\Models\Setting;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Support\Facades\Mail;

class OpenticketController extends Controller
{
     public function store(Request $request)
    {
         $body = $request->message;
         $ticket = '#SL-'.strtoupper(GeneralHelper::getrandomstr());
        // //lets build and replace available tags
         $body = "You have a new Ticket From the Smartloan Appliation with the Ticket ID". $ticket;

         $email = ["somoyetosin@gmail.com", "seyeakinsola@gmail.com", "oyeludeomotola@gmail.com"];

        $mail = new OpenTicket();
        $mail->user_id = Sentinel::getUser()->id;
        $mail->ticket_id =  $ticket;
        $mail->message = $request->comment;
        $mail->department = $request->department;
        $mail->subject = $request->subject;
        $mail->branch_id = session('branch_id');
        $mail->priority = $request->priority;
        $mail->sender_email = Sentinel::getUser()->email;
        $mail->save();

         Mail::to($email)->send(new Ticket($request, $mail->sender_email));

        return redirect("openticket/data")->with("success" ,"Ticket Sent Successfully");//redirect('open_ticket/data')->with("Ticket successfully sent");
    }

    public function index()
    {
         $data =  Openticket::all();
         return view("openticket.data" , compact("data"));
    }
}
