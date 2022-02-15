<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\EmailNotification;
use Mail;

class EmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $email,$name,$subject,$body;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email,$name,$subject,$body)
    {
        //
        $this->email=$email;
        $this->name=$name;
        $this->subject=$subject;
        $this->body=$body;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        Mail::to($this->email)->send(new EmailNotification($this->name,$this->subject,$this->body));

    }
}
