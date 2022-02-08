<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\SerializesModels;
use Mail;
use App\Mail\WithdrawOtp;

class MailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $code,$ref,$amt,$email,$name,$currency;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($code,$ref,$amt,$email,$name,$currency)
    {
        //
        $this->code = $code;
        $this->email = $email;
        $this->ref = $ref;
        $this->amt = $amt;
        $this->name = $name;
        $this->currency = $currency;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //

        $m=$this->email;
        $c=$this->code;
        $r=$this->ref;
        $a=$this->amt;
        $n=$this->name;
        $cu=$this->currency;


       $check= Mail::to($m)->send(new WithdrawOtp($c,$r,$a,$n,$cu));



    }
}
