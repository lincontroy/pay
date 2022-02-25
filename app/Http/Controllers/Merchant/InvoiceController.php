<?php

namespace App\Http\Controllers\Merchant;

use DB;
use PDF;
use Validator;
use Carbon\Carbon;
use App\Models\PizzaModel;
use App\Jobs\InvoiceJob;
use App\Models\User;
use App\Models\Getway;
use App\Models\Payment;
use App\Models\Userplan;
use App\Models\Usermeta;
use App\Mail\PaymentMail;
use App\Jobs\SendEmailJob;
use App\Models\Usergetway;
use App\Models\Paymentmeta;
use App\Models\Requestmeta;
use Illuminate\Http\Request;
use App\Models\Currencygetway;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;
use App\Models\Request as PaymentRequest;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\Sessions;
use Illuminate\Support\Facades\Http;

class InvoiceController extends Controller
{
    //
   private function generateRandomString($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
	public function showform(){
		return view('merchant.invoice.create');
	}

	public function postinvoice(Request $request){

		$validated = $request->validate([
        'c_email' => 'required',
        'amount' => 'required',
        'description' => 'required',
    	]);


		$customer_email=$request->c_email;
		$currency=$request->currency;
		$user_id=Auth::user()->id;
		$amount=$request->amount;
		$description=$request->description;

		$refcode=$this->generateRandomString(7);

		//insert this data on the invoice model



		dispatch(new InvoiceJob($user_id,$refcode,$customer_email,$currency,$amount,$description))->delay(10);;

		return redirect()->back()->with('success', 'Invoice sent');   ;


	}

}
