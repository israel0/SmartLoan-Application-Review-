<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Helpers\RouteSms;
use App\Helpers\Infobip;
use App\Mail\EmailtoBorrower;
use App\Models\Borrower;
use App\Models\Email;
use App\Models\Setting;
use App\Models\Sms;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Clickatell\Rest;
use Illuminate\Http\Request;
use Aloha\Twilio\Twilio;
use App\Http\Requests;
use App\Services\Sms\BetaSms;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;


class CommunicationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['sentinel', 'branch']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexEmail()
    {
        if (!Sentinel::hasAccess('communication')) {
           redirect()->back()->with('warning', 'Permission Denied');  
        }
        $data = Email::where('branch_id', session('branch_id'))->get();
        return view('communication.email', compact('data'));
    }

    public function indexSms()
    {
        if (!Sentinel::hasAccess('communication')) {
           redirect()->back()->with('warning', 'Permission Denied');  
        }
        $data = Sms::where('branch_id', session('branch_id'))->get();
        return view('communication.sms', compact('data'));
    }


    public function createEmail(Request $request)
    {
        if (!Sentinel::hasAccess('communication.create')) {
           redirect()->back()->with('warning', 'Permission Denied');  
        }
        $borrowers = array();
        $borrowers["0"] = "All Borrowers";
        foreach (Borrower::all() as $key) {
            $borrowers[$key->id] = $key->first_name . ' ' . $key->last_name . ' (' . $key->unique_number . ')';
        }
        if (isset($request->borrower_id)) {
            $selected = $request->borrower_id;
        } else {
            $selected = '';
        }
        return view('communication.create_email', compact('borrowers', 'selected'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeEmail(Request $request)
    {
        if (!Sentinel::hasAccess('communication.create')) {
           redirect()->back()->with('warning', 'Permission Denied');  
        }
        $body = "";
        $recipients = 1;
        if ($request->send_to == 0) {
            foreach (Borrower::all() as $borrower) {
                $body = $request->message;
                //lets build and replace available tags
                $body = str_replace('{borrowerTitle}', $borrower->title, $body);
                $body = str_replace('{borrowerFirstName}', $borrower->first_name, $body);
                $body = str_replace('{borrowerLastName}', $borrower->last_name, $body);
                $body = str_replace('{borrowerAddress}', $borrower->address, $body);
                $body = str_replace('{borrowerMobile}', $borrower->mobile, $body);
                $body = str_replace('{borrowerEmail}', $borrower->email, $body);
                $body = str_replace('{borrowerTotalLoansDue}',
                round(GeneralHelper::borrower_loans_total_due($borrower->id), 2), $body);
                $body = str_replace('{borrowerTotalLoansBalance}',
                round((GeneralHelper::borrower_loans_total_due($borrower->id) - GeneralHelper::borrower_loans_total_paid($borrower->id)),
                    2), $body);
                $body = str_replace('{borrowerTotalLoansPaid}', GeneralHelper::borrower_loans_total_paid($borrower->id),
                $body);
                $email = $borrower->email;
                if (!empty($email)) {
                    Mail::raw(substr(strip_tags($body),0,110), function ($message) use ($request, $borrower, $email) {
                        $message->from(Setting::where('setting_key', 'company_email')->first()->setting_value,
                            Setting::where('setting_key', 'company_name')->first()->setting_value);
                        $message->to($email);
//                        $headers = $message->getHeaders();
                        $message->setContentType('text/html');
                        $message->setSubject($request->subject);
                    });
                }
                $recipients = $recipients + 1;
            }
            $mail = new Email();
            $mail->user_id = Sentinel::getUser()->id;
            $mail->message = $body;
            $mail->subject = $request->subject;
            $mail->branch_id = session('branch_id');
            $mail->recipients = $recipients;
            $mail->send_to = 'All Borrowers';
            $mail->save();
            GeneralHelper::audit_trail("Send  email to all borrowers");
            return redirect('communication/email')->with("Email successfully sent");
        } else {
            $body = $request->message;
            $borrower = Borrower::find($request->send_to);
            //lets build and replace available tags
            $body = str_replace('{borrowerTitle}', $borrower->title, $body);
            $body = str_replace('{borrowerFirstName}', $borrower->first_name, $body);
            $body = str_replace('{borrowerLastName}', $borrower->last_name, $body);
            $body = str_replace('{borrowerAddress}', $borrower->address, $body);
            $body = str_replace('{borrowerMobile}', $borrower->mobile, $body);
            $body = str_replace('{borrowerEmail}', $borrower->email, $body);
            $body = str_replace('{borrowerTotalLoansDue}',
                round(GeneralHelper::borrower_loans_total_due($borrower->id), 2), $body);
            $body = str_replace('{borrowerTotalLoansBalance}',
                round((GeneralHelper::borrower_loans_total_due($borrower->id) - GeneralHelper::borrower_loans_total_paid($borrower->id)),
                    2), $body);
            $body = str_replace('{borrowerTotalLoansPaid}', GeneralHelper::borrower_loans_total_paid($borrower->id),
                $body);
            $email = $borrower->email;
            if (!empty($email)) {
//                Mail::raw(strip_tags($body), function ($message) use ($request, $borrower, $email) {
//                    $message->from(Setting::where('setting_key', 'company_email')->first()->setting_value,
//                        Setting::where('setting_key', 'company_name')->first()->setting_value);
//                    $message->to($email);
////                    $headers = $message->getHeaders();
//                    $message->setContentType('text/html');
//                    $message->setSubject($request->subject);
//
//                });

                Mail::to($email)->send(new EmailtoBorrower($body, $request, $borrower));

                $mail = new Email();
                $mail->user_id = Sentinel::getUser()->id;
                $mail->message = $body;
                $mail->subject = $request->subject;
                $mail->branch_id = session('branch_id');
                $mail->recipients = $recipients;
                $mail->send_to = $borrower->first_name . ' ' . $borrower->last_name . '(' . $borrower->unique_number . ')';
                $mail->save();
                GeneralHelper::audit_trail("Sent email to borrower ");
                return redirect('communication/email')->with("success" , "Email successfully sent");
            }

        }
     
        return redirect('communication/email')->with("success" , "Email successfully sent");
    }


    public function deleteEmail($id)
    {
        if (!Sentinel::hasAccess('communication.delete')) {
           redirect()->back()->with('warning', 'Permission Denied');  
        }
        Email::destroy($id);
        GeneralHelper::audit_trail("Deleted email record with id:" . $id);
        return redirect('communication/email')->with("success" , "Email successfully Deleted");
    }

    public function createSms(Request $request)
    {
        if (!Sentinel::hasAccess('communication.create')) {
           redirect()->back()->with('warning', 'Permission Denied');  
        }
        $borrowers = array();
        $borrowers["0"] = "All Borrowers";
        foreach (Borrower::all() as $key) {
            $borrowers[$key->id] = $key->first_name . ' ' . $key->last_name . ' (' . $key->unique_number . ')';
        }
        if (isset($request->borrower_id)) {
            $selected = $request->borrower_id;
        } else {
            $selected = '';
        }
        return view('communication.create_sms', compact('borrowers', 'selected'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeSms(Request $request)
    {
        if (!Sentinel::hasAccess('communication.create')) {
           redirect()->back()->with('warning', 'Permission Denied');  
        }
        $body = "";
        $recipients = 1;
        if (Setting::where('setting_key', 'sms_enabled')->first()->setting_value == 1) {
            if ($request->send_to == 0) {

                $active_sms = Setting::where('setting_key', 'active_sms')->first()->setting_value;
                foreach (Borrower::all() as $borrower) {
                    $body = $request->message;

                    $body = str_replace('{borrowerTitle}', $borrower->title, $body);
                    $body = str_replace('{borrowerFirstName}', $borrower->first_name, $body);
                    $body = str_replace('{borrowerLastName}', $borrower->last_name, $body);
                    $body = str_replace('{borrowerAddress}', $borrower->address, $body);
                    $body = str_replace('{borrowerMobile}', $borrower->mobile, $body);
                    $body = str_replace('{borrowerEmail}', $borrower->email, $body);
                    $body = str_replace('{borrowerTotalLoansDue}',
                        round(GeneralHelper::borrower_loans_total_due($borrower->id), 2), $body);
                    $body = str_replace('{borrowerTotalLoansBalance}',
                        round((GeneralHelper::borrower_loans_total_due($borrower->id) - GeneralHelper::borrower_loans_total_paid($borrower->id)),
                            2), $body);
                    $body = str_replace('{borrowerTotalLoansPaid}',
                        GeneralHelper::borrower_loans_total_paid($borrower->id),
                        $body);
                    $email = $borrower->email;
                    $body = trim(strip_tags($body));
                    if (!empty($borrower->mobile)) {
                        BetaSms::SendSms($body,$borrower->mobile);
                    }
                    $recipients = $recipients + 1;
                }

                $sms = new Sms();
                $sms->user_id = Sentinel::getUser()->id;
                $sms->message = $body;
                $sms->gateway = $active_sms;
                $sms->branch_id = session('branch_id');
                $sms->recipients = $recipients;
                $sms->send_to = 'All borrowers';
                $sms->save();
                GeneralHelper::audit_trail("Sent SMS   to all borrower");
                return redirect('communication/sms')->with("success" , "SMS successfully sent");

            } else {
                $body = $request->message;
                $borrower = Borrower::find($request->send_to);
                //lets build and replace available tags
                $body = str_replace('{borrowerTitle}', $borrower->title, $body);
                $body = str_replace('{borrowerFirstName}', $borrower->first_name, $body);
                $body = str_replace('{borrowerLastName}', $borrower->last_name, $body);
                $body = str_replace('{borrowerAddress}', $borrower->address, $body);
                $body = str_replace('{borrowerMobile}', $borrower->mobile, $body);
                $body = str_replace('{borrowerEmail}', $borrower->email, $body);
                $body = str_replace('{borrowerTotalLoansDue}',
                    round(GeneralHelper::borrower_loans_total_due($borrower->id), 2), $body);
                $body = str_replace('{borrowerTotalLoansBalance}',
                    round((GeneralHelper::borrower_loans_total_due($borrower->id) - GeneralHelper::borrower_loans_total_paid($borrower->id)),
                        2), $body);
                $body = str_replace('{borrowerTotalLoansPaid}', GeneralHelper::borrower_loans_total_paid($borrower->id),
                    $body);
                $body = trim(strip_tags($body));
                if (!empty($borrower->mobile)) {
                    $active_sms = Setting::where('setting_key', 'active_sms')->first()->setting_value;
                    BetaSms::SendSms($body, $borrower->mobile);
                    $sms = new Sms();
                    $sms->user_id = Sentinel::getUser()->id;
                    $sms->message = $body;
                    $sms->gateway = $active_sms;
                    $sms->recipients = $recipients;
                    $sms->branch_id = session('branch_id');
                    $sms->send_to = $borrower->first_name . ' ' . $borrower->last_name . '(' . $borrower->unique_number . ')';
                    $sms->save();
                    return redirect('communication/sms')->with("success" , "SMS successfully sent");
                }

            }
            GeneralHelper::audit_trail("Sent SMS   to borrower");
            return redirect('communication/sms')->with("success" , "SMS successfully sent");
        } else {
            return redirect('setting/data')->with('warning' , 'SMS is disabled, please enable it.');
        }
    }


    public function deleteSms($id)
    {
        if (!Sentinel::hasAccess('communication.delete')) {
           redirect()->back()->with('warning', 'Permission Denied');  
        }
        Sms::destroy($id);
        GeneralHelper::audit_trail("Deleted sms record with id:" . $id);
        return redirect('communication/sms')->with("success" ,"SMS successfully deleted" );
    }

}
