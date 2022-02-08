<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WithdrawOtp extends Mailable
{
    use Queueable, SerializesModels;
    
    public $code,$ref,$amt,$link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($code,$ref,$amt)
    {
        //
        
        $this->code=$code;
        $this->ref=$ref;
        $this->amt=$amt;
        
        
        
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $r=$this->ref;
        $link="https://pay.lifegeegs.com/confirm/$r";
        return $this
        ->from('admin@lifegeegs.com','Lifegeegs Withdrawals')
        ->markdown('email.withdraw');
    }
}
