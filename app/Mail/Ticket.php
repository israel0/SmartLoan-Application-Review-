<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Ticket extends Mailable
{
    use Queueable, SerializesModels;

    protected $request;
    protected $senderEmail;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($request, $senderEmail)
    {
        $this->request = $request;
        $this->senderEmail = $senderEmail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.ticket.general')
                    ->priority(1)
                    ->subject("Smart Loan Support Ticket | ".$this->request->priority)
//                    ->replyTo(['somoyetosin@gmail.com', 'oyeludeomotola@gmail.com', 'seyeakinsola@gmail.com'])
                    ->replyTo(['somoyetosin@gmail.com'])
                    ->with([
                        'request' => $this->request,
                        'senderEmail' => $this->senderEmail
                    ]);
    }
}
