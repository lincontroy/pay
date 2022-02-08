<?php
/* access token */
  $consumerKey = ''; //Fill with your app Consumer Key
  $consumerSecret = ''; // Fill with your app Secret
  
  $headers = ['Content-Type:application/json; charset=utf8'];
  $access_token_url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
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
   

  /* main request */
  $bal_url = 'https://api.safaricom.co.ke/mpesa/accountbalance/v1/query';

  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $bal_url);
  curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$access_token)); //setting custom header

  $curl_post_data = array(
    //Fill in the request parameters with valid values
    'Initiator'           => 'LINCOLN MUNENE',                      # initiator name -> For test, use Initiator name(Shortcode 1)
    'SecurityCredential'  => 'HOpw3DASwN5hRtpHx4XHf9WvP0FOrHqYOP4atbSHXCIlSSZ5qYI2Nri/NIKy+oxJecXbq18K7EFVSZF4oC+FAPz18WDm+DHfpbRDKb83pQy1oWL402171CRMHayrtcaIICbpY5ZJ4BxC3H4ErSxlXopzJEoy7a2C3P54cTYU/MOb4DV+D9X74d+4c665xEgcuzarevGJpA8wCJ1av9SIu8LlmU7HwKoSJdB9TDjRPvO/dALaH5ZYG+UAOaiOco25IRWMlqePJMrS24bbUVUmpcw2+8FEwMrRAHgkVasJrQXa3qEFa9kcjxGLBam24s4HnenzKDpedoJspCJIJZ6U3A==',                      #Base64 encoded string of the Security Credential, which is encrypted using M-Pesa public key 
    'CommandID'           => 'AccountBalance',        # Command ID, Possible value AccountBalance             
    'PartyA'              => '3012059',                      # ShortCode 1, or your Paybill(During Production) 
    'IdentifierType'      => '4',                      
    'Remarks'             => 'Account balance',                      # Comments- Anything can go here
    'QueueTimeOutURL'     => 'https://ygmlimited.com/admin/payments/bal_callback_url.php',                      # URL where Timeout Response will be sent to
    'ResultURL'           => 'https://ygmlimited.com/admin/payments/bal_callback_url.php'                       # URL where Result Response will be sent to
  );

  $data_string = json_encode($curl_post_data);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
  $curl_response = curl_exec($curl);
  print_r($curl_response);
  echo $curl_response;
?>
