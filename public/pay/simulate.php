<?php
    $url = 'https://api.safaricom.co.ke/mpesa/c2b/v1/simulate';
    $consumerKey = 'PnEXMzLuCUwAUJGUGG4WfCe2icHL1GfA'; //Fill with your app Consumer Key
	$consumerSecret = 'PiF6jyVkUboqqmuV'; // Fill with your app Secret

	$headers = ['Content-Type:application/json; charset=utf8'];

	//$url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    $url='https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($curl, CURLOPT_HEADER, FALSE);
	curl_setopt($curl, CURLOPT_USERPWD, $consumerKey.':'.$consumerSecret);
	$result = curl_exec($curl);
	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$result = json_decode($result);

	$access_token = $result->access_token;
	//echo $access_token;
    
      
    //$ShortCode  = '3012059';
    $shortCode = '4019891'; 
    // Shortcode. Same as the one on register_url.php
    $amount     = '1'; // amount the client/we are paying to the paybill
    $msisdn     = '254704800563'; // phone number paying 
    $billRef    = 'v12'; // This is anything that helps identify the specific transaction. Can be a clients ID, Account Number, Invoice amount, cart no.. etc

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$access_token));


    $curl_post_data = array(
           'ShortCode' => $shortCode,
           'CommandID' => 'CustomerPayBillOnline',
           'Amount' => $amount,
           'Msisdn' => $msisdn,
           'BillRefNumber' => $billRef
    );
    //print_r(json_encode($curl_post_data));

    $data_string = json_encode($curl_post_data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
    $curl_response = curl_exec($curl);
   // print_r($curl_response);

    echo $curl_response;
?>
