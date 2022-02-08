<?php


header("Content-Type:application/json");
$shortcode='4019891';
$consumerkey    ="";
$consumersecret ="";
$validationurl="";
$confirmationurl="";
/* testing environment, comment the below two lines if on production */
$authenticationurl='https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$registerurl = 'https://api.safaricom.co.ke/mpesa/c2b/v1/registerurl';
/* production un-comment the below two lines if you are in production */
//$authenticationurl='https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
//$registerurl = 'https://api.safaricom.co.ke/mpesa/c2b/v1/registerurl';
$credentials= base64_encode($consumerkey.':'.$consumersecret);
$username=$consumerkey ;
$password=$consumersecret;
  // Request headers
  $headers = array(  
    'Content-Type: application/json; charset=utf-8'
  );
  // Request
  

  $ch = curl_init($authenticationurl);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  //curl_setopt($ch, CURLOPT_HEADER, TRUE); // Includes the header in the output
  curl_setopt($ch, CURLOPT_HEADER, FALSE); // excludes the header in the output
  curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password); // HTTP Basic Authentication
  $result = curl_exec($ch);  
  $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
$result = json_decode($result);
$access_token=$result->access_token;
curl_close($ch);

//Register urls
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $registerurl);
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$access_token)); 
$curl_post_data = array(
  //Fill in the request parameters with valid values
  'ShortCode' => $shortcode,
  'ResponseType' => 'Cancelled',
  'ConfirmationURL' => $confirmationurl,
  'ValidationURL' => $validationurl
);
$data_string = json_encode($curl_post_data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
$curl_response = curl_exec($curl);
echo $curl_response;

// 	$url = 'https://api.safaricom.co.ke/mpesa/c2b/v1/registerurl';
	
// 	$consumerKey = 'PnEXMzLuCUwAUJGUGG4WfCe2icHL1GfA'; //Fill with your app Consumer Key
// 	$consumerSecret = 'PiF6jyVkUboqqmuV'; // Fill with your app Secret

// 	$headers = ['Content-Type:application/json; charset=utf8'];
//     $credentials = base64_encode($consumerKey. ':' .$consumerSecret);
    
// 	//$url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
//     $url='https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
// 	$curl = curl_init($url);
// 	curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
// 	curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
// 	curl_setopt($curl, CURLOPT_HEADER, FALSE);
// 	curl_setopt($curl, CURLOPT_USERPWD, $consumerKey.':'.$consumerSecret);
// 	$result = curl_exec($curl);
// 	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
// 	$result = json_decode($result);

// 	$access_token = $result->access_token;

// 	//echo $access_token;
// 	//print_r($result);
// 	curl_close($curl);

// 	//$access_token = ''; // check the mpesa_accesstoken.php file for this. No need to writing a new file here, just combine the code as in the tutorial.
// 	$shortCode = '4019891'; 
// 	//$shortCode = '3012059'; 
// 	// provide the short code obtained from your test credentials

// 	/* This two files are provided in the project. */
// 	$confirmationUrl = 'https://ygmlimited.com/admin/payments/confirmation_url.php'; 
// 	// path to your confirmation url. can be IP address that is publicly accessible or a url
// 	$validationUrl = 'https://ygmlimited.com/admin/payments/validation_url.php'; // path to your validation url. can be IP address that is publicly accessible or a url



// 	$curl = curl_init();
// 	curl_setopt($curl, CURLOPT_URL, $url);
// 	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$access_token)); //setting custom header


// 	$curl_post_data = array(
// 	  //Fill in the request parameters with valid values
// 	  'ShortCode' => $shortCode,
// 	  'ResponseType' => '[Cancelled/Completed]',
// 	  'ConfirmationURL' => $confirmationUrl,
// 	  'ValidationURL' => $validationUrl
// 	);

// 	$data_string = json_encode($curl_post_data);

// 	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
// 	curl_setopt($curl, CURLOPT_POST, true);
// 	curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

// 	$curl_response = curl_exec($curl);
// 	print_r($curl_response);

// 	echo $curl_response;
?>
