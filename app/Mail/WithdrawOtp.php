<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;

class WithdrawOtp extends Mailable
{
    use Queueable, SerializesModels;
    
    public $code,$ref,$amt,$link,$name,$currency;

   
    public function __construct($code,$ref,$amt,$name,$currency)
    {
       
        
        $this->code=$code;
        $this->ref=$ref;
        $this->amt=$amt;
        $this->name=$name;
        $this->currency=$currency;
       
 
    }

    public function build()
    {
        $r=$this->ref;
        $link="https://pay.lifegeegs.com/confirm/$r";
        return $this
        ->from('admin@lifegeegs.com','Lifegeegs Withdrawals')
        ->markdown('email.withdraw');
    }
}
