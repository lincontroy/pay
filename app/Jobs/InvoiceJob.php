<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\InvoiceModel;
use App\Mail\InvoiceMail;
use Mail;
use Illuminate\Queue\SerializesModels;

class InvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user_id,$refcode,$customer_email,$currency,$amount,$description;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id,$refcode,$customer_email,$currency,$amount,$description)
    {
        //
        $this->user_id=$user_id;
        $this->refcode=$refcode;
        $this->customer_email=$customer_email;
        $this->currency=$currency;
        $this->amount=$amount;
        $this->description=$description;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //save this data in the database
        $InvoiceModel=new InvoiceModel();

        $InvoiceModel->user_id=$this->user_id;
        $InvoiceModel->invoice_code=$this->refcode;
        $InvoiceModel->c_email=$this->customer_email;
        $InvoiceModel->currency=$this->currency;
        $InvoiceModel->amount=$this->amount;
        $InvoiceModel->description=$this->description;

        if($InvoiceModel->save()){

            //send this document to the cmail

            $mail=new InvoiceMail($this->customer_email,$this->refcode,$this->currency,$this->amount,$this->description);
            Mail::to($this->customer_email)->send($mail);

        }
     

    }
}
