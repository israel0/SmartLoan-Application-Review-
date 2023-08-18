<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Setting;

class Notification extends Mailable
{
    use Queueable, SerializesModels;

    protected $body;
    protected $request;
    protected $borrower;
    /**
     * Create a new message instance.
     *
     * @return void
     */


    public function __construct($body)
    {
        $this->body = $body;
      
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.notification')
                    ->priority(1)
                    ->subject("Notification From Smartloan")
                    ->replyTo(Setting::where('setting_key', 'company_email')->first()->setting_value)
                    ->with([
                        'body' => $this->body
                    ]);
    }
}
