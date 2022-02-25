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
use Dompdf\Dompdf;
use Dompdf\Options;
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

		$html = '<!DOCTYPE html>
		<html>
		<head><link rel="stylesheet" href="invoice.css"></head>
		<body style="padding: 3rem">
		    <h1>Invoice</h1>

		   <img src="https://lifegeegs.com/logo.png">
		   

		    <h2 style="margin-top: 3rem">Bill to</h2>
		    {{ invoice.customer.name | html.escape }}<br />
		    {{ invoice.customer.address | html.escape }}<br />

		    <div style="margin-top: 3rem">
		        Invoice No: #{{ invoice.id }}<br />
		        Date: #{{ invoice.created_at }}
		    </div>

		    <table class="table">
		        <thead>
		            <tr>
		                <th>Item Code</th>
		                <th>Description</th>
		                <th>Quantity</th>
		                <th>Unit Price</th>
		                <th>Total Price</th>
		            </tr>
		        </thead>

		        {{ for order_line in invoice.order_lines }}
		        <tr>
		            <td>{{ order_line.item_code | html.escape }}</td>
		            <td>{{ order_line.description | html.escape }}</td>
		            <td class="text-end">${{ order_line.quantity }}</td>
		            <td class="text-end">${{ order_line.unit_price | math.format "F2" }}</td>
		            <td class="text-end">${{ order_line.total_price | math.format "F2" }}</td>
		        </tr>
		        {{ end }}

		        <tfoot>
		            <tr>
		                <td class="text-end" colspan="4"><strong>Total:</strong></td>
		                <td class="text-end">${{ invoice.total_price | math.format "F2" }}</td>
		            </tr>
		        </tfoot>
		    </table>
		</body>
		</html>';

		$refcode=$this->generateRandomString(7);

		//insert this data on the invoice model

			

            $options = new Options();

            $options->set('isRemoteEnabled', true);

           
            
            $options->set('defaultFont', 'Courier');

            $dompdf = new Dompdf($options);

            $dompdf->loadHtml($html);

            // (Optional) Setup the paper size and orientation
            $dompdf->setPaper('A4', 'landscape');

            // Render the HTML as PDF
            $dompdf->render();

            // Output the generated PDF to Browser
            $dompdf->stream();



		// dispatch(new InvoiceJob($user_id,$refcode,$customer_email,$currency,$amount,$description))->delay(10);;

		return redirect()->back()->with('success', 'Invoice sent');   ;


	}

}
