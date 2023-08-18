<?php

namespace App\Mail;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailtoBorrower extends Mailable
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


    public $details;

    public function __construct($body)
    {
        $this->body = $body;
        $this->request = $request;
        $this->borrower = $borrower;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.borrower.general')
                    ->priority(1)
                    ->subject($this->request->subject)
                    ->replyTo(Setting::where('setting_key', 'company_email')->first()->setting_value)
                    ->with([
                        'body' => $this->body
                    ]);
    }
}
