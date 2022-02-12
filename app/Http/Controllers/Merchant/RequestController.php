<?php

namespace App\Http\Controllers\Merchant;

use DB;
use PDF;
use Validator;
use Carbon\Carbon;
use App\Models\PizzaModel;
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

class RequestController extends Controller
{
    
    
    
    private function postdata($data){
        
        $url = "https://www.staging.travelinnovators.biz/v1/api/post-whatsapp-ride";
                           
        return Http::accept('application/json')->post($url, $data);
    }

    private function getlocationname($lat,$long){

        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$lat.",".$long."&sensor=true&key=AIzaSyAP3QJBgrKkqoeR6flV30GUVSRd9B-Em6c";

        $response=Http::get($url);

        return $response['plus_code']['compound_code'];


    }
    
   
    private function checkassigned($data){
        
        $url = "https://www.staging.travelinnovators.biz/v1/api/ride-info";
                           
        return Http::accept('application/json')->post($url, $data);
    }
    
    
    //delete any record that is older than 2min
    
    public function deleterecord(){
        
        $checks = Sessions::where('status', 0)
        ->get();
        
        foreach($checks as $check){
            if((now()->diffInMinutes($check->updated_at))>3){
                //delete the id
                $res=Sessions::find($check->id);
                $res->delete(); //returns true/false
                
                if($res){
                    $message="Oops No driver was found for this ride, Please try again.";
                    $this->sendtext($check->mobile,$message);
                    return response('Record deleted');
                }
   
            }
        }
    }
    
    public function checkdriver(){
        $check = Sessions::whereNotNull('ride_id')
        ->where('status',0)
        ->latest()
        ->first();
        
        if(!$check){
            return;
        }
            
        //get the ride id
        $ride_id = $check->ride_id;
        
        //check if the ride is assigned to a driver
        $data = array("ride_id"=>$ride_id);
        
        $results = $this->checkassigned($data)->json();
        
        if(isset($results['status']) && $results['status'] === 200 && isset($results['response']['driver_id'])){
            //this means a driver accepted the ride
            // return $results['response']['driver_id'];
             $driver_id = $results['response']['driver_id'];
             $drivername = $results['response']['driver_name'];
             $driver_phone = $results['response']['driver_phone'];
             $driver_profile_photo = $results['response']['profile_picture'];
             $exterior_front = $results['response']['exterior_front'];
             $exterior_back = $results['response']['exterior_back'];
             $exterior_right = $results['response']['exterior_right'];
             $exterior_left = $results['response']['exterior_left'];
             
             
             
             $check->update(['status' => 1, 'driver_id' => $driver_id]);
             
             //update the user that we found a driver.
             $message="Congratulations,\nA driver has accepted your request\nDriver name:$drivername\nDriver mobile:$driver_phone\nThe driver will be right here to pick you up.";
             $this->sendtext($check->mobile,$message);
             $type="driver_images";
             $message2="Below are the car images";
             $this->sendtext($check->mobile,$message2);
             
             $url="https://www.staging.travelinnovators.biz/v1/storage/app/public/".$driver_profile_photo;
             $this->sendimage($check->mobile, $url,$type);
             
             $type="vehicle_image_front";
             $url="https://www.staging.travelinnovators.biz/v1/storage/app/public/".$exterior_front;
             $this->sendimage($check->mobile, $url,$type);
             $type="vehicle_image_front";
             $url="https://www.staging.travelinnovators.biz/v1/storage/app/public/".$exterior_back;
             $this->sendimage($check->mobile, $url,$type);
             $type="vehicle_image_front";
             $url="https://www.staging.travelinnovators.biz/v1/storage/app/public/".$exterior_right;
             $this->sendimage($check->mobile, $url,$type);
             $type="vehicle_image_front";
             $url="https://www.staging.travelinnovators.biz/v1/storage/app/public/".$exterior_left;
             $this->sendimage($check->mobile, $url,$type);
             
             
        }
    }
    
    private function sendtext($mobile, $message){
        // $url = "https://waba-sandbox.360dialog.io/v1/messages";
        
        $url = "https://waba.360dialog.io/v1/messages";
    
        $payload = array(
            "recipient_type"=>"individual",
            "to"=>$mobile,
            "type"=>"text",
            "text"=>array("body" => $message)
        );
        
        
        // return Http::withHeaders([
        //     "Content-Type"=>" application/json",
        //     "D360-Api-Key"=> "0btUAH_sandbox",
        // ])->post($url, $payload);
        
        return Http::withHeaders([
            "Content-Type"=>" application/json",
            "D360-Api-Key"=> "LliemRtEEpS0lOaztn1c4htsAK",
        ])->post($url, $payload);
    }
   
    private function sendimage($mobile, $image,$type){
        // $url = "https://waba-sandbox.360dialog.io/v1/messages";
        
        $url = "https://waba.360dialog.io/v1/messages";
    
        $payload = array(
                        "to"=> $mobile,
                        "type"=> "template",
                        "template"=>array(
                        "namespace"=> "bc76c587_82dc_4b12_9e17_2e84d00e8b28",
                        "language"=>array(
                                    "policy"=> "deterministic",
                                    "code"=> "en_us"
                                ), 
                         "name"=>$type,
                         "components"=> array(array(
                                        "type"=>"header",
                                        "parameters"=>array(array(
                                            
                                                "type"=> "image",
                                               
                                                "image"=> array("link"=> $image)
                                              
                                            )
                                        
                                      
                                        ) 
                                    )),
                                    ) 
                    
        );
        
        
        // return Http::withHeaders([
        //     "Content-Type"=>" application/json",
        //     "D360-Api-Key"=> "0btUAH_sandbox",
        // ])->post($url, $payload);
        
        return Http::withHeaders([
            "Content-Type"=>" application/json",
            "D360-Api-Key"=> "LliemRtEEpS0lOaztn1c4htsAK",
        ])->post($url, $payload);
    }
    
    public function drupp(Request $request){
        
        $logFile = "responsee.txt";
    
        $log = fopen($logFile, "a");
        
        fwrite($log, $request);
        
        fclose($log);
        
        
        //ask the rider for his location
        
        //check if this user has an active session from sessions table
        if(!isset($request->messages)){
            return response('', 201);
        }
        
        $re = $request->messages[0];
        $re2 = $request->contacts[0];
        $name= $re2['profile']['name'];
        $ride_type = $re['text']['body'] ?? 0;
        
        $payment_type = $re['text']['body'] ?? 1;
        $email = $re['text']['body'] ?? 0;
        
        if(!isset($re['from'])){
            return response('', 201);
        }
        
        $mobile=$re['from'];
        
        //check if there is an active session for this user with status of 0
        $check = Sessions::where('conversation', $mobile)
                    ->where('status', 0)
                    ->latest()
                    ->first();
        
        if(!$check){
            $message="
            Hello $name,\n\nWelcome to Drupp Direct, your instant messaging ride service.\n\nPlease select:.\n\n1.To Book a Ride\n2.Book Ride in Advance \n3.Report a Driver\n4.Contact Customer Service\n5.Become a Driver\n6.Other issues";
            $this->sendtext($mobile,$message);
            
            $session=new Sessions();
            $session->conversation=$mobile;
            $session->mobile=$mobile;
            $session->save();
            
            return response('', 201);
        }
        
        if(isset($check->ride_id)){
            $this->sendtext($mobile, "Your Number has requested for a ride.\nPlease retry in 2 minutes.");
            return response('', 201);
        }
        
        if(!$check->ride_type){
             switch($ride_type){
                 case 1:
                    //update the check with ridetype 1
                    //update the check id with ride type 1 and proceed to wait for response of current location
                    $check->update(['ride_type'=>$ride_type]);
               
                    //ask the rider for the details of the current location
                    $message="Thank you for your selection $name,\n\n\nPlease share your current location by attaching it and sending it here.\n(Click on the Pin, Select Location, search for or select  your location and send)";
                    $this->sendtext($mobile,$message);
                    return response('', 201);
                    
                 case 4:
                    
                    $check->update(['ride_type'=>$ride_type,'status'=>1]);
                    $message="Hello $name,\n\nTo contact customer care please write an email to info@mydrupp.com\n\nCall: 080-5555-7676/080-7777-8876\n\nVisit Us at: 13B Onikanga Road, GRA, Ilorin Kwara State \n\nhttps://mydrupp.com/contactUs.html";
                    $this->sendtext($mobile,$message);
                    
                    break; 
                  
                case 5:
                    $check->update(['ride_type'=>$ride_type,'status'=>1]);
                    $message="Hello $name,\n\nTo become a driver please download our drupp driver app from playstore using the link below \n\nbit.ly/earnwithdrupp";
                    $this->sendtext($mobile,$message);
                    
                    break;
                    
                default:
                    $message="Hello $name,\n\nYou entered an invalid option.Please Try again";
                    
                    $this->sendtext($mobile,$message);
                
                    $message="
                    Hello $name,\n\nWelcome to Drupp Direct, your instant messaging ride service.\n\nPlease select:.\n\n1.To Book a Ride\n2.Book Ride in Advance \n3.Report a Driver\n4.Contact Customer Service\n5.Become a Driver\n6.Other issues";
                    $this->sendtext($mobile,$message);
                    return response('', 201);
             }
        }
        
        // Good
        switch($check->ride_type){
           case 1:
                if(!isset($re['location']) && !$check->received_source && !$check->received_destination){
                    //update the check with ridetype 1
                   //update the check id with ride type 1 and proceed to wait for response of current location
                   $check->update(['ride_type'=>$ride_type]);
                   
                    //ask the rider for the details of the current location
                    $message="Thank you for your selection $name,\n\n\nPlease share your current location by attaching it and sending it here.\n(Click on the Pin, Select Location, search for or select  your location and send)";
                    $this->sendtext($mobile,$message);
                    return response('', 201);
                }
                
                //check if the curreent location is set
                if(!$check->received_source){
                    
                    $user_current_lat= $re['location']['latitude'];
                    $user_current_long= $re['location']['longitude'];

                    $user_current_location_name=$this->getlocationname($user_current_lat,$user_current_long);
                    // $user_current_location_name = $re['location']['address'] ?? 'Undefined Location';
  
                    $check->update([
                            'source_latitude' => $user_current_lat,
                            'source_longitude' => $user_current_long,
                            'source_name' => $user_current_location_name,
                            'received_source' => 1
                        ]);
                        
                    $message="Thank you $name for sharing your location.\n\nNow, please share your destination by attaching it in this chat.";
                    $this->sendtext($mobile,$message); 
                    return response('', 201);
                }
                
                if(!$check->received_destination){
                    $user_dest_lat= $re['location']['latitude'];
                    $user_dest_long= $re['location']['longitude'];
                
                    if(isset($re['location']['address'])){
                        $user_destination_name = $re['location']['address'];
                    } else {
                        $user_destination_name =$this->getlocationname($user_dest_lat,$user_dest_long);
                    }
                
                    $check->update([
                            'destination_latitude'=>$user_dest_lat,
                            'destination_longitude'=>$user_dest_long,
                            'destination_name'=>$user_destination_name,
                            'received_destination' => 1,
                        ]);
                        
                    $message="Thank you $name for sharing your destination location.\n\nNow, please select the payment type\n\n2.Credit/Debit card\n3.Wallet\n4.Net Banking\n5.Cash";
                    $this->sendtext($mobile,$message); 
                    $check->update(['paynot'=>1]);
                    return response('', 201);
                }
                
                //check if payment option is set
                if(!$check->payment_type){
                    switch($payment_type){
                        case 2: 
                            $check->update(['payment_type'=>2]);
                            $message="Please provide us with your email address";
                            $this->sendtext($mobile,$message); 
                            return response('', 201);
                            
                        case 3: 
                            $check->update(['payment_type'=>3]);
                            
                            break;
                            
                         case 4: 

                            $check->update(['payment_type'=>4]);
                            
                            break;
                            
                        case 5: 

                            $check->update(['payment_type'=>5]);
                            
                            break;
                                
                        default:

                            $message="Invalid selection";
                            $this->sendtext($mobile,$message);
                            return response('', 201);
                            
                    }
                }
                
                if(!$email && !$check->email){
                    $message="Invalid email, retry.";
                    $this->sendtext($mobile,$message);
                    return response('', 201);
                } else {
                    $check->update(['email'=>$email]);
                }
                break;
            default:
                return response('', 201);
        }
        
        $country_code = substr($mobile, 0, 3);
    
        $length=strlen($mobile);
        
        if($length==12){
            $mobile_number = substr($mobile, 3, 9);
        }else{
            $mobile_number = substr($mobile, 3, 10);
        }
    
        
    

        $requestpayload=array(
            "source_latitude"=> $check->source_latitude,
            "source_longitude"=> $check->source_longitude,
            "destination_latitude"=>$check->destination_latitude,
            "destination_longitude"=> $check->destination_longitude,
            "payment_option"=> $check->payment_type,
            "destination"=> $check->destination_name,
            "vehicle_type"=> "1",
            "passengers_preference"=> "0",
            "source"=> $check->source_name,
            "ride_type"=> "1",
            "ride_option"=> "1",
            "phone"=>$mobile_number,
            "email"=>$check->email,
            "country_code"=>"+".$country_code,
            "first_name"=>"Mr/Mrs",
            "last_name"=>$name
        );
        
        

        $results = $this->postdata($requestpayload)->json();
        
        // $datass=json_encode($requestpayload);

        if($results['status'] === 200){
            
            
            
            
             $ride_id = $results['response']['ride_id'];
             
            $message="Thank you for your sharing your destination,\n\nNow that we have your request, we will now fetch you a nearby driver. Please hold on while we fix you up\n\nMeanwhile checkout our Drupp rider app using the link https://shorturl.at/cgnFS";
             
            $update=Sessions::where('id',$check->id)
             ->update(['ride_id'=>$ride_id]);
             
             $this->sendtext($mobile, $message);
             
            return response('', 201);
            
        }
}
    
       
    
    
     public function druppp(Request $request){
        
        
        
        
        function sendtext ($mobile, $message){
            $url = "https://waba-sandbox.360dialog.io/v1/messages";
        
            $payload = array(
    
            "recipient_type"=>"individual",
            "to"=>$mobile,
            "type"=>"text",
            "text"=>array("body"=>$message));
                        
            $payload=json_encode($payload);
            $headers = array(
                "Content-Type"=>" application/json",
                "D360-Api-Key"=> "0btUAH_sandbox",
            );
                            
                            
            $ch = curl_init();
            
            // $payload=http_build_query(array($payload));

            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_POST, 1);
            // curl_setopt($ch, CURLOPT_POSTFIELDS,
            //             $payload);
            
            // In real life you should use something like:
            curl_setopt($ch, CURLOPT_POSTFIELDS, 
                     $payload);
                     
             curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'D360-Api-Key:0btUAH_sandbox'));
            
            // Receive server response ...
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            
            $server_output = curl_exec($ch);

            curl_close ($ch);
            
            return $server_output;
                           
        }
    
        
        
        foreach($request as $req){
            
            foreach($req as $res){
                
                foreach($res as $re){
                        
                        //check if this user has an active session from sessions table
                        
                        
                    
                        if( (isset($re['from']))) {
                        
                        $mobile=$re['from'];
                        
                        //check if there is an active session for this user with status of 0
                        $checks=Sessions::where('conversation',$mobile)
                        ->where('status',0)
                        ->where('received_source',1)
                        ->get();
                        
                        
                        $convos=count($checks);
                        
                        
                        
                        
                        if(($convos>0)){
                            
                             //check if the destination_latitude and destination_longitude are null ask the user for the details
                            $message="Please provide drupp with Destination Location";
                            
                                $this->sendtext($mobile,$message);
                            
                            if(isset($re['location'])){
                            
                            $user_dest_lat= $re['location']['latitude'];
                            
                            $user_dest_long= $re['location']['longitude'];
                            
                            foreach($checks as $check){
                               
                                    
                                    
                                    //update the new incoming data
                                    
                                    $update=Sessions::where('id',$check->id)->update(['destination_latitude'=>$user_dest_lat,'destination_longitude'=>$user_dest_long]);
                                break;
                                
                              
                                }
                            
                            }else{
                                //let the user enter a valid destination
                                
                                $message="Please provide drupp with a valid destination";
                            
                                $this->sendtext($mobile,$message);
                            }
                            
                            
                            
                            
                        }else{
                            //create the session
                            
                            //check that there is no conversation
                            
                                
                            $session=new Sessions();
                            
                            
                            
                            $message="Please provide drupp with Your current location";
                            
                            $this->sendtext($mobile,$message);
                            
                            if(isset($re['location'])){
                            
                            
                            $session->conversation=$mobile;
                            $session->mobile=$mobile;
                            
                            
                            
                            $user_current_lat= $re['location']['latitude'];
                            
                            $user_current_long= $re['location']['longitude'];
                            
                            $session->source_latitude=$user_current_lat;
                            $session->source_longitude=$user_current_long;
                            
                            
                            //ask the user for source lat and long
                            
                            $session->received_source=1;
                            
                            $session->save();
                            
                            //call the next function to save the destination
                            
                            
                          break;
                            
                            }
                            
                            
        
                        }
                        
                        
                        
                        }else{
                            
                            
                            return "Null";
                        }
                    
                }
            
            }
            
        }
        
        
        exit;
        if (property_exists($request, 'messages')) 
          {
              
              
              return "true";
        
         //ask for the destination
         foreach($request as $json){
            
            
            // print_r($json);
            foreach($json as $jso){
                
                $mobile=$jso->from;
                
                
                
                $message="Please provide drupp with destination location";
                
                $response=$this->sendtext($mobile,$message);
                
                $logFile = "response.txt";
    
                $log = fopen($logFile, "a");
                
                fwrite($log, $response);
                
                fclose($log);
                
                
            }
             
         }
         
         
          }else{
              return "false";
              //ask for the source destination
              $mobile=$jso->from;
                
                $message="Please provide drupp with Your current location";
                
                $response=$this->sendtext($mobile,$message);
                
                $logFile = "response.txt";
    
                $log = fopen($logFile, "a");
                
                fwrite($log, $response);
                
                fclose($log);
                
                
            //   $message="Hello "
          }
        
        
        $logFile = "whatsapp.txt";
    
        $log = fopen($logFile, "a");
        
        fwrite($log, $request);
        
        fclose($log);
        
        exit();
        
        //check if an array has Latitude or Longitude 
        
        //if it has 
        
                    $pizza_count=[];
        
                    $url = "https://waba-sandbox.360dialog.io/v1/messages";
        
                        $payload = array(
                
                        "recipient_type"=>"individual",
                        "to"=>"254704800563",
                        "type"=>"text",
                        "text"=>array("body"=>"new"));
                        
                    $payload=json_encode($payload);
                    $headers = array(
                        "Content-Type"=>" application/json",
                        "D360-Api-Key"=> "0btUAH_sandbox",
                    );
                            
                            
                            $ch = curl_init();
                            
                            // $payload=http_build_query(array($payload));

                            curl_setopt($ch, CURLOPT_URL,$url);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            // curl_setopt($ch, CURLOPT_POSTFIELDS,
                            //             $payload);
                            
                            // In real life you should use something like:
                            curl_setopt($ch, CURLOPT_POSTFIELDS, 
                                     $payload);
                                     
                             curl_setopt($ch,CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'D360-Api-Key:0btUAH_sandbox'));
                            
                            // Receive server response ...
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            
                            
                            // $server_output = curl_exec($ch);

                            curl_close ($ch);
                            // curl_close($curl);
                            
                            
                            
                           
                      
        $jsons=json_decode($request);
        
        
        foreach($request as $json){
            
            
            // print_r($json);
            foreach($json as $jso){
                
                // return $jso;
                
                
                    
                    $message= "Hi";
                    
                    //if message is not integer
                    
                    if(!is_int($message)){
                        
                        $pizza_counts=PizzaModel::all();
                        
                        $count=count($pizza_counts);
                        
                        foreach($pizza_counts as $pizza_count){
                            
                            
                           
                            
                            $pizza_name = $pizza_count->name;
                            
                            $pizza_id=$pizza_count->id;
                            
                            
                            
                            
                            
                            // echo $message;
                                
                               
                        }
                        
                        // $this->sendtext($jso->from,$pizza_count);
                        
                        $url = "https://waba-sandbox.360dialog.io/v1/messages";
        
                                        $payload = [
                                            "to"=> "254704800563",
                                            "type"=> "text",
                                            "text"=> [
                                            	"body"=> $pizza_count
                                            ],
                                        ];
                            
                            $headers = [
                                "Content-Type: application/json",
                                "D360-Api-Key: 0btUAH_sandbox",
                            ];
                            
                            $curl = curl_init();
                            
                            curl_setopt_array($curl, [
                                CURLOPT_URL => $url,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_CUSTOMREQUEST => "POST",
                                CURLOPT_POSTFIELDS => $payload,
                                CURLOPT_HTTPHEADER => $headers,
                            ]);
                            
                            $response = curl_exec($curl);
                            $err = curl_error($curl);
                            
                            curl_close($curl);
                            
                            if ($err) {
                                echo "curl error: " . $err;
                            } else {
                                echo $response;
                            }
                        
                        //get the number of pizza
                    }else{
                        return null;
                    }

    
            }
        }
        
        
        
        $logFile = "whatsapp.txt";
    
        $log = fopen($logFile, "a");
        
        fwrite($log, $request);
        
        fclose($log);
        
        // return response()->json($json->statuses, 404);
        
    }
    
  
    
    public function invoicePdf($id)
    {
        $data = PaymentRequest::where('user_id',Auth::id())->with('requestmeta')->findOrFail($id);
        $jsonData = json_decode($data->requestmeta->value);
        $pdf = PDF::loadView('merchant.request.invoice-pdf', compact('data', 'jsonData'));
        return $pdf->download('request-invoice.pdf');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $requests = PaymentRequest::where('user_id',Auth::id())->with('requestmeta')->latest()->paginate(20);
        return view('merchant.request.index', compact('requests'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(getplandata('menual_req') == 0, 404);
        return view('merchant.request.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate
        $request->validate([
            'purpose'        => 'required',
            'amount'         => 'required',
            'amount'         => 'required',
            'captcha_status' => 'required',
            'is_test'        => 'required',
            'status'         => 'required'
        ]);

        $user_plan = Userplan::where('user_id', Auth::id())->select('id', 'daily_req', 'monthly_req')->first();
        $daily_request = PaymentRequest::where('user_id', Auth::id())->whereDate('created_at', Carbon::today())->count();
        $monthly_request = PaymentRequest::where('user_id', Auth::id())->whereMonth('created_at', Carbon::now()->month)->count();

        // if ($user_plan->daily_req <= $daily_request) {
        //     $msg['errors']['error'] = "Daily Request Limited Exceeded!";
        //     return response()->json($msg, 404);
        // }

        // if ($user_plan->monthly_req <= $monthly_request) {
        //     $msg['errors']['error'] = "Monthly Request Limited Exceeded!";
        //     return response()->json($msg, 404);
        // }

        $data = [
            'purpose' => $request->purpose,
        ];

        $requestObj = new PaymentRequest;
        $requestObj->amount = $request->amount;
        $requestObj->is_fallback = 0;
        $requestObj->user_id = Auth::id();
        $requestObj->captcha_status = $request->captcha_status;
        $requestObj->currency = $request->currency;
        $requestObj->status = $request->status;
        $requestObj->is_test = $request->is_test;
        $requestObj->save();

        $meta = new Requestmeta;
        $meta->key = 'request_info';
        $meta->request_id = $requestObj->id;
        $meta->value = json_encode($data);
        $meta->save();

        return response()->json(encrypt($requestObj->id));
    }

    public function checkoutUrl($param)
    {
        $decrypted = decrypt($param);
        $paymentRequest = PaymentRequest::where('id', $decrypted)->where('status', 1)->first();
        if (!$paymentRequest) {
            return 'Invalid URL';
        }
        Session::put('requestData', $paymentRequest);
        return redirect()->route('checkout.view');
    }

    public function checkoutView()
    {
        $requestData = Session::has('requestData') ? Session::get('requestData') : [];
        abort_if(!$requestData, 403);

        $plan = Userplan::where('user_id', $requestData['user_id'])->first();
        $usergetways = Usergetway::with('getway', 'user')->where('user_id', $requestData['user_id'])->where('status',1)->get();
        $request = PaymentRequest::with('requestmeta')->findOrFail($requestData->id);
        return view('payment.checkout', compact('requestData', 'usergetways', 'plan','request'));
    }

    public function paymentView(Request $request)
    {

        if ($request->phone_required == 1) {
            $request->validate([
                'phone' => 'required',
            ]);
        }
        if($request->image_accept == 1){
            $request->validate([
                'screenshot' => 'required|image|max:1000|mimes:jpeg,bmp,png,jpg',
                'comment' => 'required|max:200'
            ]);
        }

        $test_mode = $request->session()->has('test_mode') ? $request->session()->has('test_mode') : 1;
        // Google recaptcha validation
        if ($request->has('g-recaptcha-response')) {
            if(env('NOCAPTCHA_SECRET') != null){
                $messages = [
                    'g-recaptcha-response.required' => 'You must check the reCAPTCHA.',
                    'g-recaptcha-response.captcha' => 'Captcha error! try again later or contact site admin.',
                ];
                
                $validator = Validator::make($request->all(), [
                    'g-recaptcha-response' => 'required|captcha'
                ], $messages);
                
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }
            }
        }

        $user_id = $request->session()->get('requestData')['user_id'];

        $storage_limit=Userplan::where('user_id',$user_id)->pluck('storage_limit')->first();
        $storage_used = folderSize('uploads/'.$user_id);
        if ($request->hasFile('screenshot') && $storage_limit > $storage_used) {
            $logo      = $request->file('screenshot');
            $logo_name = hexdec(uniqid()) . '.' . $logo->getClientOriginalExtension();
            $logo_path = 'uploads/'.$user_id.'/'.date('y/m/');
            $logo->move($logo_path, $logo_name);
            $payment_data['screenshot'] = $logo_path . $logo_name;
        }

        $usergateway = Usergetway::with('getway', 'user', 'currencygetway')->where([
            ['getway_id', $request->gateway_id],
            ['status', 1],
            ['user_id',$user_id],
        ])->first();

        $paymentRequest = PaymentRequest::findOrFail($request->request_id);
        $paymentRequest->status = 0; //Inactive
        $paymentRequest->ip = getIp(); //Ip Address
        $paymentRequest->save();
        $payment_data['currency'] = $usergateway->currency_name ?? $usergateway->getway->currency_name ?? 'USD';
        $payment_data['email'] = $usergateway->user->email ?? 'demo@mail.com';
        $payment_data['name'] = $usergateway->user->name ?? 'customer';
        $payment_data['phone'] = $request->phone ?? '';
        $payment_data['billName'] = 'customer payment';
        $payment_data['amount'] = $paymentRequest->amount;
        $payment_data['test_mode'] = $request->is_test ?? $test_mode ?? 1;
        $payment_data['charge'] = $usergateway->charge ?? 0;
        $payment_data['pay_amount'] = $paymentRequest->amount * $usergateway->rate + $usergateway->charge;
        $payment_data['getway_id'] = $usergateway->getway_id;
        $payment_data['user_id'] = $usergateway->user_id;
        $payment_data['request_from'] = 'customer';
        $payment_data['request_id'] = $request->request_id;
        $payment_data['payment_type'] = 1;
        $payment_data['comment'] = $request->comment ?? '';

        if ($request->is_test == 1) {
            $gateway_info = json_decode($usergateway->sandbox);
        } else {
            $gateway_info = json_decode($usergateway->production);
        }

        if (!empty($gateway_info)) {
            foreach ($gateway_info as $key => $info) {
                $payment_data[$key] = $info;
            };
        }

        return $usergateway->getway->namespace::make_payment($payment_data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = PaymentRequest::with('requestmeta')->findOrFail($id);
        $jsonData = json_decode($data->requestmeta->value);
        return view('merchant.request.show', compact('data', 'jsonData'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $currencygetway = Currencygetway::with('usergetway', 'currency')->get();
        $request = PaymentRequest::where('user_id',Auth::id())->findOrFail($id);
        $request->id;
        $param = encrypt($request->id);
        return view('merchant.request.edit', compact('currencygetway', 'request', 'param'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate
        $request->validate([
            'purpose'        => 'required',
            'amount'         => 'required',
            'amount'         => 'required',
            'captcha_status' => 'required',
            'is_test'        => 'required',
            'status'         => 'required'
        ]);

        $data = [
            'purpose' => $request->purpose,
        ];

        $requestObj = PaymentRequest::where('user_id',Auth::id())->findOrFail($id);
        $requestObj->amount = $request->amount;
        $requestObj->captcha_status = $request->captcha_status;
        $requestObj->status = $request->status;
        $requestObj->is_test = $request->is_test;
        $requestObj->save();

        $meta = Requestmeta::where('request_id', $id)->first();
        $meta->value = json_encode($data);
        $meta->save();
        return response()->json('Added Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = PaymentRequest::where('user_id',Auth::id())->findOrFail($id);
        $data->delete();
        return redirect()->back()->with('success', 'Payment Request Deleted Successfully');
    }

    public function success(Request $request)
    {
        if (!session()->has('payment_info') && session()->get('payment_info')['payment_status'] != 1) {
            abort(403);
        }
        
        $screenshot = Session::get('payment_info')['screenshot'] ?? '';
        $comment = Session::get('payment_info')['comment'] ?? '';
        $data = $request->session()->get('payment_info');
        $getway = Getway::findOrFail($data['getway_id']);
        $payment = new Payment;
        $payment->request_id = $data['request_id'];
        $payment->user_id = PaymentRequest::findOrFail($data['request_id'])->user_id;
        $payment->getway_id = $data['getway_id'];
        $payment->amount = $data['amount'];
        $payment->main_amount = $data['main_amount'];
        $payment->currency = $data['currency'];
        $payment->trx_id = $data['payment_id'];
        $payment->status = ($getway->is_auto == 1 ? 1 : $data['payment_status']) ?? 2;
        $payment->save();

        if($screenshot != ''){
            $paymentmeta = new Paymentmeta;
            $paymentmeta->payment_id = $payment->id;
            $paymentmeta->key = 'payment_meta';
            $paymentmeta->value = json_encode(['screenshot' => $screenshot,'comment'=> $comment]);
            $paymentmeta->save();
        }
        Session::flash('message', 'Payment Successfull!!!');
        Session::flash('type', 'success');
        return redirect()->route('customer.payment.status');
    }

    public function failed()
    {
        Session::flash('message', 'Transaction Error Occured!!');
        Session::flash('type', 'danger');
        return redirect()->route('customer.payment.status');
    }

    public function status()
    {
        abort_if(!Session::has('payment_info') && !Session::has('customer_session'),403);
       
        if(Session::has('payment_info')){

            $payment_info=session()->get('payment_info');
            $amount = $payment_info['main_amount'] ?? 0;
            $customer_session['status']=$payment_info['status'] ?? '';
            $customer_session['request_id']=session()->get('requestData')['id'] ?? $payment_info['request_id'] ?? session()->get('api_request')['request_id'];
            $customer_session['payment_id']=$payment_info['payment_id'] ?? '';
            $customer_session['getway_id']=$payment_info['getway_id'];
            $customer_session['is_fallback']=$payment_info['is_fallback'] ?? 0; 
            $customer_session['user_id']=$payment_info['user_id'];
            $customer_session['payment_method']=$payment_info['payment_method'];
            $payment_id = $customer_session['payment_id'];
            $getway_id = $customer_session['getway_id'];
            $payment_status = $customer_session['status'];
            $status = $customer_session['status'];
            $payment_status = $status == 1 ? 'success' : ($status == 0 ? 'failed' : 'pending');
            $getway = Getway::findOrFail($getway_id) ?? '';

            if($status == 0){
                $url = session()->has('api_request') ? session()->get('api_request')['fallbackurl'] . "?getway=" . $getway->name . "&&status=" . $payment_status : '';
                if ($payment_info['is_fallback'] == 1) {
                    return redirect($url);
                }else{
                    Session::flash('message', 'Transaction Error Occured!!');
                    Session::flash('type', 'danger');
                    return view('payment.status', compact('status'));
                }
            }
            $customer_session['url']=session()->has('api_request') ? session()->get('api_request')['fallbackurl'] . "?trxid=" . $payment_id . "&&getway=" . $getway->name . "&&status=" . $payment_status : '';

            Session::put('customer_session',$customer_session);
        }
        else{
            $payment_info=session()->get('customer_session');
            $customer_session['request_id']=session()->get('requestData')['id'] ?? $payment_info['request_id'];
            $customer_session['payment_id']=$payment_info['payment_id'] ?? '';
            $customer_session['status']=$payment_info['status'] ?? '';
            $customer_session['getway_id']=$payment_info['getway_id'];
            $customer_session['is_fallback']=$payment_info['is_fallback'] ?? 0;
            $customer_session['user_id']=$payment_info['user_id'];
            $getway_id = $customer_session['getway_id'];
            $status = $customer_session['status'];
            $getway = Getway::findOrFail($getway_id) ?? '';
            $customer_session['url']=$payment_info['url'];
            $payment_id = $customer_session['payment_id'];
            $user_id = $customer_session['user_id'];
            $amount = $payment_info['main_amount'] ?? 0;
        }
        

        $req_id = $customer_session['request_id'];
        $request_meta = Requestmeta::where('request_id', $req_id)->pluck('value')->first();
        $req_info = json_decode($request_meta, true);
        
        $status = $customer_session['status'];
        $payment_status = $status == 1 ? 'success' : ($status == 0 ? 'failed' : 'pending');

        $fallback = $customer_session['is_fallback'];
        
        $payment = Payment::with('getway', 'user')->where('trx_id', $payment_id)->first() ?? '';
        $url = $customer_session['url'];

       

        // send mail
        $mailcheck = Userplan::where('user_id',$customer_session['user_id'])->first();
       
        $user = User::findOrFail($customer_session['user_id']);
        if (Session::has('payment_info')) {
            if($status == 1 && $mailcheck->mail_activity == 1){
               
                $data = [
                    'type'    => 'payment',
                    'email' => $user->email,
                    'message' => "Successfully payment " . round($amount, 2) . " (".$user->currency.") by Customer via ". strtoupper($payment_info['payment_method']) . " Transaction ID : ". $payment_info['payment_id']
                ];

                if (env('QUEUE_MAIL') == 'on') {
                    dispatch(new SendEmailJob($data));
                } else {
                    Mail::to($user->email)->send(new PaymentMail($data));
                }
            }
        }
        session()->has('payment_info') ? Session::forget('payment_info') : '';
        session()->has('api_request') ? Session::forget('api_request') : '';
        session()->has('requestData') ? Session::forget('requestData') : '';

        if ($status == 2) {
            Session::flash('message', 'Payment Pending: The request is now in pending for verification...!!');
            Session::flash('type', 'warning');
        }

        if ($fallback == 1) {
            return redirect($url);
        }

        return view('payment.status', compact('payment', 'url', 'fallback','req_info','status'));
    }

    // ==============================  Api routes =============================  //

    public function apirequest(Request $request)
    {

        $validated = $request->validate([
            'private_key' => 'required|min:50|max:50',
            'currency' => 'required|max:50',
            'is_fallback' => 'required',
            'is_test' => 'required',
            'account'=>'required',
            'ho' => 'required',
            'purpose' => 'required|max:500',
            'amount' => 'required|max:100',
            'mobile' => '|max:100',
        ]);

        if($request->is_fallback == 1){
            $validated = $request->validate([
            'fallback_url' => 'required|max:100',
          ]);
        }

        if($request->amount <= 0){
            return response()->json('Invalid Amount',401);
        }

        $private_key = $request->private_key;
        $currency = $request->currency;


       $user = User::where('private_key', $private_key)->where('status', 1)->first();

        if (!$user) {
            return response()->json('Invalid Request!',401);
            
        }

        //Check if request limit exceeeded
        $user_plan = Userplan::where('user_id', $user->id)->select('id', 'daily_req', 'monthly_req')->first();
        $daily_request = PaymentRequest::where('user_id', $user->id)->whereDate('created_at', Carbon::today())->count();
        $monthly_request = PaymentRequest::where('user_id', $user->id)->whereMonth('created_at', Carbon::now()->month)->count();

        $daily_req = $user_plan->daily_req ?? 0;
        // if ($daily_req <= $daily_request) {
        //     $msg['errors']['error'] = "Daily Request Limited Exceeded!";
        //     return response()->json($msg, 401);
        // }

        $monthly_req = $user_plan->monthly_req ?? 0;
        // if ($monthly_req <= $monthly_request) {
        //     $msg['errors']['error'] = "Monthly Request Limited Exceeded!";
        //     return response()->json($msg, 401);
        // }

        DB::beginTransaction();

        try {
        $paymentRequest = new PaymentRequest;
        $paymentRequest->user_id = $user->id;
        $paymentRequest->amount = $request->amount;
        $paymentRequest->currency = $request->currency;
        $paymentRequest->is_fallback = $request->is_fallback;
        $paymentRequest->is_test = $request->is_test;
        $paymentRequest->ip = $request->ip();
        $paymentRequest->status = 1; //pending
        $paymentRequest->save();

        $requestMeta = new Requestmeta;
        $requestMeta->key = 'request_info';
        $requestMeta->value = json_encode(['fallback' => $request->fallback_url,'purpose'=>$request->purpose ?? '']);
        $requestMeta->request_id = $paymentRequest->id;
        $requestMeta->save();

        DB::commit();

        

        if(($request->currency)=='KES'){
            //do the stk push here
            //check if mobile number is there
            if(($request->mobile)!=''){

                //check if amount is more than 50
                if(($request->amount)>=50){
                    //process the user request
                    //check the availability of the Head office
                    $count=Usermeta::where('ho',$request->ho)->count();
                    if($count>0){
                        //head office exists
    
                      $consumerKey = env('consumerkey'); //Fill with your app Consumer Key
                      $consumerSecret = env('consumersecret'); // Fill with your app Secret
                    
                      # define the variales
                      # provide the following details, this part is found on your test credentials on the developer account
                      $BusinessShortCode = env('shortcode');
                      $Passkey = env('passkey');
                      
                      
                      
                      $PartyA = $request->mobile; // This is your phone number, 
                      $AccountReference = $request->ho;
                      $TransactionDesc = $request->account;
                      $Amount = $request->amount;
                     
                      # Get the timestamp, format YYYYmmddhms -> 20181004151020
                      $Timestamp = date('YmdHis');    
                      
                      # Get the base64 encoded string -> $password. The passkey is the M-PESA Public Key
                      $Password = base64_encode($BusinessShortCode.$Passkey.$Timestamp);
                    
                      # header for access token
                      $headers = ['Content-Type:application/json; charset=utf8'];
                    
                        # M-PESA endpoint urls
                      $access_token_url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
                      $initiate_url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
                    
                      # callback url
                      $CallBackURL = env('confirmation_url');  
                    
                      $curl = curl_init($access_token_url);
                      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                      curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
                      curl_setopt($curl, CURLOPT_HEADER, FALSE);
                      curl_setopt($curl, CURLOPT_USERPWD, $consumerKey.':'.$consumerSecret);
                      $result = curl_exec($curl);
                      $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                      $result = json_decode($result);
                      $access_token = $result->access_token;  
                      curl_close($curl);
                    
                      # header for stk push
                      $stkheader = ['Content-Type:application/json','Authorization:Bearer '.$access_token];
                    
                      # initiating the transaction
                      $curl = curl_init();
                      curl_setopt($curl, CURLOPT_URL, $initiate_url);
                      curl_setopt($curl, CURLOPT_HTTPHEADER, $stkheader); //setting custom header
                    
                      $curl_post_data = array(
                        //Fill in the request parameters with valid values
                        'BusinessShortCode' => $BusinessShortCode,
                        'Password' => $Password,
                        'Timestamp' => $Timestamp,
                        'TransactionType' => 'CustomerPayBillOnline',
                        'Amount' => $Amount,
                        'PartyA' => $PartyA,
                        'PartyB' => $BusinessShortCode,
                        'InvoiceNumber'=>'https://lifegeegs.com/status',
                        'PhoneNumber' => $PartyA,
                        'CallBackURL' => $CallBackURL,
                        'AccountReference' => $AccountReference,
                        'TransactionDesc' => $TransactionDesc
                      );
                    
                      $data_string = json_encode($curl_post_data);
                      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                      curl_setopt($curl, CURLOPT_POST, true);
                      curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
                      $curl_response = curl_exec($curl);
                    //   print_r($curl_response);
                    
                    //   echo $curl_response;
                    $response=json_decode($curl_response);

                    return response()->json($response,200);

                    }else{
                        //salam alaikum
                        return response()->json('Head office Not found',404);
                    }
                
                }
                
                else{
                    return response()->json('Error on amount',403);
                }

            }else{
                return response()->json('Mobile Number is required',404);
            }
            
            
            

        }
        } catch (Exception $e) {
          DB::rollback();

          return response()->json('Opps something wrong',403);
        }

        $encrtypted_token = encrypt($paymentRequest->id);
        return response()->json(['checkout_url' => url('/customer/checkout/' . $encrtypted_token)]);
    }

    public function apiCheckoutUrl($token)
    {
        try {
            $id = decrypt($token);
        } catch (DecryptException $e) {
            return $e;
        }
       $paymentRequest = PaymentRequest::with('requestmeta')->where('status', 1)->findOrFail($id);
       // abort_if($paymentRequest->ip != getIp(), 403);
       $paymentRequest->status = 0;
       $paymentRequest->save();

        if ($paymentRequest === null) {
            return 'Invalid URL';
        }

        $info = json_decode($paymentRequest->requestmeta->value);

        $data = [
            'data'           => $info,
            'request_id'     => $paymentRequest->id,
            'amount'         => $paymentRequest->amount,
            'user_id'        => $paymentRequest->user_id,
            'is_fallback'    => $paymentRequest->is_fallback,
            'is_test'        => $paymentRequest->is_test,
            'ip'             => $paymentRequest->ip,
            'captcha_status' => $paymentRequest->captcha_status,
            'fallbackurl'    => $info->fallback,
        ];


        Session::put('api_request', $data);
        
        return redirect()->route('customer.checkout.view');
    }

    public function apiCheckoutView()
    {
        $requestData = Session::has('api_request') ? json_decode(json_encode(Session::get('api_request')), false) : '';
        abort_if(!$requestData, 403);
        $phone = Session::get('api_request')['phone'] ?? '';
        $plan = Userplan::with('user')->where('user_id', $requestData->user_id)->first();
        $usergetways = Usergetway::with('getway', 'user', 'currencygetway')->where('user_id', $requestData->user_id)->get();

        return view('api.checkout', compact('requestData', 'usergetways', 'phone', 'plan'));
    }

    public function apisuccess(Request $request)
    {

        if (!session()->has('payment_info') && session()->get('payment_info')['payment_status'] != 1) {
            abort(403);
        }
        Session::forget('customer_session');

        $screenshot = Session::get('payment_info')['screenshot'] ?? '';
        $comment = Session::get('payment_info')['comment'] ?? '';

        $data = $request->session()->get('payment_info');
        $getway = Getway::findOrFail($data['getway_id']);
        $payment = new Payment;
        $payment->request_id = $data['request_id'];
        $payment->user_id = $data['user_id'];
        $payment->getway_id = $data['getway_id'];
        $payment->amount = $data['amount'];
        $payment->main_amount = $data['main_amount'];
        $payment->currency = $data['currency'];
        $payment->trx_id = $data['payment_id'];
        $payment->status = ($getway->is_auto == 1 ? 1 : $data['payment_status']) ?? 2;
        $payment->save();

        $paymentmeta = new Paymentmeta;
        $paymentmeta->payment_id = $payment->id;
        $paymentmeta->key = 'payment_meta';
        $paymentmeta->value = json_encode(['screenshot' => $screenshot, 'comment' => $comment]);
        $paymentmeta->save();

        Session::flash('message', 'Payment Successfull!!!');
        Session::flash('type', 'success');
        return redirect()->route('customer.payment.status');
    }

    public function apifailed()
    {
        Session::flash('message', 'Transaction Error Occured!!');
        Session::flash('type', 'danger');
        return redirect()->route('customer.payment.status');
    }

    public function apipayment(Request $request)
    {
        if ($request->phone_required == 1) {
            $request->validate([
                'phone' => 'required',
            ]);
        }
        if ($request->image_accept == 1) {
            $request->validate([
                'screenshot' => 'required|image|max:1000|mimes:jpeg,bmp,png,jpg',
                'comment' => 'required|max:200'
            ]);
        }

        // Google recaptcha validation
        if ($request->has('g-recaptcha-response')) {
            if(env('NOCAPTCHA_SECRET') != null){
                $messages = [
                    'g-recaptcha-response.required' => 'You must check the reCAPTCHA.',
                    'g-recaptcha-response.captcha' => 'Captcha error! try again later or contact site admin.',
                ];
                
                $validator = Validator::make($request->all(), [
                    'g-recaptcha-response' => 'required|captcha'
                ], $messages);
                
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput();
                }
            }
        }

        $user_info = Session::has('api_request') ? Session::get('api_request') : "";

        $usergateway = Usergetway::with('getway', 'user', 'currencygetway')->where([
            ['getway_id', $request->gateway_id],
            ['status', 1],
            ['user_id', $user_info['user_id']],
        ])->first();
      
        $user = User::where([['role_id',2],['status',1]])->findOrFail($user_info['user_id']);

        $paymentRequest = PaymentRequest::with('requestmeta')->whereHas('requestmeta')->findOrFail($request->request_id);
        $paymentRequest->status = 0; //Inactive
        $paymentRequest->ip = getIp(); //Ip Address
        $info = json_decode($paymentRequest->requestmeta->value);
        $paymentRequest->save();

        $storage_limit=Userplan::where('user_id',$user->id)->pluck('storage_limit')->first();
        $storage_used = folderSize('uploads/'.$user->id);
        if ($request->hasFile('screenshot') && $storage_limit > $storage_used) {
            $logo      = $request->file('screenshot');
            $logo_name = hexdec(uniqid()) . '.' . $logo->getClientOriginalExtension();
            $logo_path = 'uploads/'.$user->id.'/'.date('y/m/');
            $logo->move($logo_path, $logo_name);
            $payment_data['screenshot'] = $logo_path . $logo_name;
        }

       $payment_data['currency'] = $usergateway->currency_name ?? $usergateway->getway->currency_name ?? 'USD';
        $payment_data['email'] = $user->email ?? $user_info->email ?? "";
        $payment_data['name'] = $user->name ?? $user_info->name ?? "";
        $payment_data['phone'] = $user_info->phone ?? $request->phone ?? "";
        $payment_data['billName'] = $info->purpose ?? 'External Payment';
        $payment_data['amount'] = $paymentRequest->amount;
        $payment_data['test_mode'] = $request->is_test ?? $test_mode ?? 1;
        $payment_data['charge'] = $usergateway->currencygetway->charge ?? 0;
        $payment_data['pay_amount'] = $paymentRequest->amount * $usergateway->rate + $usergateway->charge;
        $payment_data['getway_id'] = $usergateway->getway_id;
        $payment_data['user_id'] = $usergateway->user_id;
        $payment_data['request_from'] = 'api';
        $payment_data['request_id'] = $request->request_id;
        $payment_data['is_fallback'] = $paymentRequest->is_fallback;
        $payment_data['payment_type'] = 1;
        $payment_data['comment'] = $request->comment ?? '';

        if ($request->is_test == 1) {
            $gateway_info = json_decode($usergateway->sandbox);
        } else {
            $gateway_info = json_decode($usergateway->production);
        }

        if (!empty($gateway_info)) {
            foreach ($gateway_info as $key => $info) {
                $payment_data[$key] = $info;
            };
        }
        return $usergateway->getway->namespace::make_payment($payment_data);
    }


    //Request From external form
    public function requestform(Request $request)
    {
        if ($request->public_key == "") {
            return response()->json('No Public key found!');
        }
        if ($request->amount == "") {
            return response()->json('Amount field is required!');
        }
        if ($request->phone == "") {
            return response()->json('Phone field is required!');
        }
        if ($request->currency == "") {
            return response()->json('Currency field is required!');
        }
        if ($request->email == "") {
            return response()->json('Email field is required!');
        }

        $public_key = $request->public_key;
        $currency = $request->currency;

       $user = User::where('private_key', $public_key)->where('status', 1)->first();
        if (!$user) {
            abort(403, "Invalid Request!");
        }

        //Check if request limit exceeded
        $user_plan = Userplan::where('user_id', $user->id)->select('id', 'daily_req', 'monthly_req')->first();
        $daily_request = PaymentRequest::where('user_id', $user->id)->whereDate('created_at', Carbon::today())->count();
        $monthly_request = PaymentRequest::where('user_id', $user->id)->whereMonth('created_at', Carbon::now()->month)->count();

        // if ($user_plan->daily_req <= $daily_request) {
        //     abort(403, "Daily Request Limited Exceeded!");
        // }

        // if ($user_plan->monthly_req <= $monthly_request) {
        //     abort(403, "Monthly Request Limited Exceeded!");
        // }

        if ($request->is_fallback == '1' && $request->fallback_url == "") {
            abort(403, "Fallback url is required!");
        }

        $data['phone'] = $phone = $request->phone ?? "";
        $data['email'] = $request->email ?? "";
        $data['name'] = $request->name ?? "";
        $data['purpose'] = $request->purpose ?? "";
        $fallbackurl = $data['fallback'] = rtrim($request->fallback_url, '/') ?? "";

        $paymentRequest = new PaymentRequest;
        $paymentRequest->user_id = $user->id;
        $paymentRequest->amount = $request->amount;
        $paymentRequest->currency = $request->currency;
        $paymentRequest->is_fallback = $request->is_fallback ?? 0;
        $paymentRequest->is_test = $is_test = $request->is_test;
        $paymentRequest->ip = $request->ip();
        $paymentRequest->status = 0; //inactive
        $paymentRequest->save();

        $requestMeta = new Requestmeta;
        $requestMeta->key = 'request_info';
        $requestMeta->value = json_encode($data);
        $requestMeta->request_id = $paymentRequest->id;
        $requestMeta->save();

        $data = [
            'data'           => json_decode($paymentRequest->requestmeta->value),
            'request_id'     => $paymentRequest->id,
            'amount'         => $paymentRequest->amount,
            'user_id'        => $paymentRequest->user_id,
            'is_fallback'    => $paymentRequest->is_fallback,
            'is_test'        => $is_test,
            'ip'             => $paymentRequest->ip,
            'phone'          => $phone,
            'captcha_status' => $paymentRequest->captcha_status,
            'fallbackurl'    => $fallbackurl,
        ];

        Session::put('api_request', $data);
        return redirect()->route('customer.checkout.view');
    }
}
