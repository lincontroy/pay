<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;
    public $customer_email,$currency,$amount,$description,$refcode;

    public function __construct($customer_email,$refcode,$currency,$amount,$description)
    {
     
        $this->customer_email=$customer_email;
        $this->currency=$currency;
        $this->amount=$amount;
        $this->refcode=$refcode;
        $this->description=$description;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
        ->from('admin@lifegeegs.com','INVOICE SERVICE')
        ->markdown('emails.invoice');
    }
}
