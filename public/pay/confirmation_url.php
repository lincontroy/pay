<?php

    # if this is your first time, you might need to check the directory 'Tutorial 1'  File first.
    
    require 'vendor/autoload.php';
    use AfricasTalking\SDK\AfricasTalking;
    require 'config.php';
    include_once 'dbconnect.php';
    include_once '../sandtext.php';
   
    header("Content-Type: application/json");

    $response = '{
        "ResultCode": 0, 
        "ResultDesc": "Confirmation Received Successfully"
    }';

    // Response from M-PESA Stream
    $mpesaResponse = file_get_contents('php://input');
    $date=date('H:i:s');
    // echo $date;

    // log the response
    $logFile = "M_PESAConfirmationResponse.txt";

    $jsonMpesaResponse = json_decode($mpesaResponse, true); // We will then use this to save to database

    $transaction = array(
            ':TransactionType'      => $jsonMpesaResponse['TransactionType'],
            ':TransID'              => $jsonMpesaResponse['TransID'],
            ':TransTime'            => $jsonMpesaResponse['TransTime'],
            ':TransAmount'          => $jsonMpesaResponse['TransAmount'],
            ':BusinessShortCode'    => $jsonMpesaResponse['BusinessShortCode'],
            ':BillRefNumber'        => $jsonMpesaResponse['BillRefNumber'],
            ':InvoiceNumber'        => $jsonMpesaResponse['InvoiceNumber'],
            ':OrgAccountBalance'    => $jsonMpesaResponse['OrgAccountBalance'],
            ':ThirdPartyTransID'    => $jsonMpesaResponse['ThirdPartyTransID'],
            ':MSISDN'               => $jsonMpesaResponse['MSISDN'],
            ':FirstName'            => $jsonMpesaResponse['FirstName'],
            ':MiddleName'           => $jsonMpesaResponse['MiddleName'],
            ':LastName'             => $jsonMpesaResponse['LastName']
    );

    //get the id and payment for what
    insert_response($transaction);
    $log = fopen($logFile, "a");
    fwrite($log, $mpesaResponse);
    fclose($log);

    // echo $response;

    $BillRefNumber = $jsonMpesaResponse['BillRefNumber'];
    
    $amount=$jsonMpesaResponse['TransAmount'];
    
    
    
                if((strlen($BillRefNumber))>=9){
   
                // Set your app credentials
                $username = "lifegeegs";
                
                $apikey   = "d83c98e3a9089b2995a155fd66e24dd2c09f4593c9efa82ca16867014c0f3529";
                
                // Initialize the SDK
                $AT       = new AfricasTalking($username, $apikey);
                
                // Get the airtime service
                $airtime  = $AT->airtime();
                
                // Set the phone number, currency code and amount in the format below
                $recipients = [[
                    "phoneNumber"  => $BillRefNumber,
                    "currencyCode" => "KES",
                    "amount"       => $amount
                ]];
                
                try {
                    // That's it, hit send and we'll take care of the rest
                    $results = $airtime->send([
                        "recipients" => $recipients
                    ]);
                
                    print_r($results);
                } catch(Exception $e) {
                    echo "Error: ".$e->getMessage();
                }
                        // }
                    }
    
    $TransAmount = $jsonMpesaResponse['TransAmount'];
    // write to file
    $amount=$TransAmount;
    
    //check if the response belongs to futureplan
    
    $check=substr( $BillRefNumber, 0, 2 ) === "fp";
    
    if($check){
        //get the transaction array and send as json to their server
        
        $transaction = array(
            'TransactionType'      => $jsonMpesaResponse['TransactionType'],
            'TransID'              => $jsonMpesaResponse['TransID'],
            'TransTime'            => $jsonMpesaResponse['TransTime'],
            'TransAmount'          => $jsonMpesaResponse['TransAmount'],
            'BillRefNumber'        => $jsonMpesaResponse['BillRefNumber'],
            'InvoiceNumber'        => $jsonMpesaResponse['InvoiceNumber'],
            'ThirdPartyTransID'    => $jsonMpesaResponse['ThirdPartyTransID'],
            'MSISDN'               => $jsonMpesaResponse['MSISDN'],
            'FirstName'            => $jsonMpesaResponse['FirstName'],
            'MiddleName'           => $jsonMpesaResponse['MiddleName'],
            'LastName'             => $jsonMpesaResponse['LastName']
    );
    
    //connect to the database check if there is that Ho
    
    
        $explode=explode("#",$BillRefNumber);
    
        $ho=$explode[0];
        
        $connect=mysqli_connect('localhost','futurepl_lifegeegs','futurepl_lifegeegs','futurepl_lifegeegs');
        
        $query=mysqli_query($connect,"select * from usermetas where ho='$ho' limit 1");
        
        $rows=mysqli_num_rows($query);
        
        if($rows>0){
            while($row=mysqli_fetch_assoc($query)){
                $callback=$row['callback'];
                
                // $what=$explode[1];
    
    
        
                $ch = curl_init();
        
                curl_setopt($ch, CURLOPT_URL,$callback);
                curl_setopt($ch, CURLOPT_POST, 1);
                // curl_setopt($ch, CURLOPT_POSTFIELDS,$transaction);
                
                // In real life you should use something like:
                curl_setopt($ch, CURLOPT_POSTFIELDS, 
                         http_build_query($transaction));
                
                // Receive server response ...
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                
                $server_output = curl_exec($ch);
                
                curl_close ($ch);
            }
        }
        
    }
    
   
    
    // $BillRefNumber = '37648948#L';
    $log2="data.txt";
    $log3=fopen($log2,"a");
    
    //amount

    // $amount=12;
    // echo $amount;
    // exit;
    $explode=explode("#",$BillRefNumber);

    $Nationalid=$explode[0];
    $what=$explode[1];
    fwrite($log3, $Nationalid);
    fclose($log3);
    //check customer
    //echo $Nationalid;
    $sql="SELECT * FROM `customers` WHERE `National_id`='$Nationalid'";
    $result = runQuery($connect,$sql);

    // Check if the transaction was successfull
    if(!$result){
        // Exit with warning
        // TODO: Please Handle error here, it means no customer with that national
        // ID was found or an SQL error occured
        die(mysqli_error($connect));
    }

$user_id=$result['id'];
// $what = 'l';
//$amount = 20;
// $user_id = '5';

// Get the user loan records
if(($what=='l') || ($what=='L') || ($what=='')) {

    $s="SELECT * FROM `loans` WHERE `user_id`='$user_id'";
    $result = runQuery($connect, $s);

    // Check if the transaction was successfull
    if(!$result){
        // It seems an SQL error occurred or the use has finished the loan
        // So push it to savings
        pushToSavings($connect, $amount, $user_id);
        exit;
    }

    $credit =intval($result['credit']);
    $profit = $result['total_refunded'] - $credit;

    // Data was retrieved successfully
    // Calculate the sum of the amount she/he has paid previously
    // The global variables
    $i = $week = 0;
    $amount_paid = 0;
    $amount_to_pay_back = intval($result['total_refunded']);
 
    // Loop according to the number of installments 
    for($i = 1; $i <= 8; ++$i){
        // Add to the previous total amount
        $amount_paid += intval($result['wk' . $i . '_paid']);

        // Track the week of instllment the user is in
        if($result['wk' . $i . '_paid'] == 0 && $week == 0){
            $week = $i;
        }
    }

// 	print_r("<br>Amount paid now: " . $amount . "<br>");
//     print_r("<br>Amount paid before: " . $amount_paid . "<br>");
//     print_r("<br>Amount to pay back before: " . $amount_to_pay_back . "<br>");

    // Check if the user has cleared all his loans
    $amount_needed_to_finish_loan = $amount_to_pay_back - $amount_paid;

    // print_r("<br>Amount Needed to finish loan: " . $amount_needed_to_finish_loan . "<br>");

    // This is incase the user is trying to clear the loan but has already finished the loan previously
    if($amount_needed_to_finish_loan == 0){
        // The user has cleared loan
        // Clear the loan
        clearLoan($connect, $user_id, $amount_to_pay_back,$profit);

        // Push to savings account
        pushToSavings($connect, $amount, $user_id);

    } else if($amount == $amount_needed_to_finish_loan){
        // Clear the user loan
        clearLoan($connect, $user_id, $amount_to_pay_back,$profit); 
    }
    else if($amount > $amount_needed_to_finish_loan){ // The user has paid more than the required amount
        // Calculate the excess
        $excess = $amount  - ($amount_to_pay_back - $amount_paid);
        
        // Push the excess to savings
        pushToSavings($connect, $excess, $user_id);

        // Clear the loan
        clearLoan($connect, $user_id, $amount_to_pay_back,$profit);
    } else{
    	$paid_installements = 0;

    	$week = 1;
    	$amount_to_pay_this_week = intval($result['amount_wk1']);

    	for($i = 1; $i <= 8; $i++){
    		$needed_amount_for_this_week = intval($result['amount_wk' . $i]);
    		$amount_already_paid_this_week = intval($result['wk' . $i . '_paid']);

	        $amount_to_pay_this_week = $needed_amount_for_this_week - $amount_already_paid_this_week;

	        if($amount_to_pay_this_week <= $needed_amount_for_this_week && $amount_to_pay_this_week > 0){
	        	$week = $i;
	        	++$paid_installements;
	        	break;
	        } else {
	        	$week = $i;
	        }
    	}

    	// Recurively clear the amount for the next few weeks
	    recursivelyClearTheAmount($connect, $result, $user_id, $amount, $amount_to_pay_this_week, $paid_installements, $week);
	
	}
}

//membership
if(($what=='m') || ($what=='M')){
    //do membership stuff
    if($amount=='300'){
        $query=mysqli_query($connect,"INSERT INTO `memberships`(`user_id`, `amount`) VALUES ('$Nationalid','$amount')");
        if($query){
            $query=mysqli_query($connect,"update customers set `verified_phone`='1' where id='$user_id'");
        }
    }else{
        $query=mysqli_query($connect,"INSERT INTO `memberships`(`user_id`, `amount`) VALUES ('$Nationalid','$amount')");
    }
   // $query=mysqli_query($connect,"");
}

if(($what=='s') || ($what=='S')){
    //do savings stuff
    $date=date('Y-m-d');
    $s="SELECT * FROM `savings` WHERE `user_id`='$user_id'";
    $a=mysqli_query($connect,$s);
    $v=mysqli_fetch_array($a);
    $balance=$v['balance'];
    $nbalance=$balance+$amount;
    $transtype=$v['transtype'];
    //send message
    $query=mysqli_query($connect,"select * from customers where id='$user_id'");
    $row=mysqli_fetch_array($query);
    $mobile=$row['phone'];
    $n1=$row['username'];
    $n2=explode(" ",$n1);
    $name=$n2[0];
    
    
    $query=mysqli_query($connect,"INSERT INTO `d_savings`(`National_id`,`user_id`, `amount`,`balance`,`mode`) VALUES ('$Nationalid','$user_id','$amount','$nbalance','mpesa')");
    //$query=mysqli_query($connect,"INSERT INTO `d_savings`(`National_id`,`user_id`, `amount`) VALUES ('$Nationalid','$user_id','$amount')");
    if(($balance>'0')){
      $balance=$balance+$amount;
      $sql="UPDATE `savings` SET `balance`='$balance',`last_deposit`='$amount',`trans_fee`='0',`last_deposit_date`='$date',`transtype`='credit' WHERE `user_id`='$user_id' AND `transtype`='credit'";
      $query=mysqli_query($connect,$sql);
      $d="UPDATE `savings` SET `balance`='$balance' WHERE `user_id`='$user_id' AND `transtype`='debit'";
    
      $q=mysqli_query($connect,$d);
      
      $message="Dear $name Your savings of ksh $amount has been received.Your savings account balance is ksh $balance. Continue making your savings via Mpesa Paybill 4019891, A/c Id no#S. Thank you YGM Limited. Achieve it with us";
      sendtext($mobile,$message);
    
    }else{
    
      $sql="INSERT INTO `savings`(`user_id`,`amount`, `balance`, `last_deposit`, `last_deposit_date`,trans_fee,`transtype`) VALUES ('$user_id','$amount','$amount','$amount','$date','0','credit')";
      $query=mysqli_query($connect,$sql);
      $d="UPDATE `savings` SET `balance`='$amount' WHERE `user_id`='$user_id' AND `transtype`='debit'";
    
      $q=mysqli_query($connect,$d);
      $message="Dear $name Your savings of ksh $amount has been received.Your savings account balance is ksh $balance. Continue making your savings via Mpesa Paybill 4019891, A/c Id no#S. Thank you YGM Limited. Achieve it with us";
      sendtext($mobile,$message);
    
    }       
}
//savings stuff

// Utility function
// This function recurively clears the rest of the weeks if excess amount has been paid
function recursivelyClearTheAmount($connect, $user_details, $user_id, $amount_paid, 
	$needed_amount_for_the_week, $paid_installments, $week){

    // Prevent loop from passing 8 weeks
    if($week > 8){
        return;
    }


    // print_r("<br>Amount paid for this week: $amount_paid<br>");
    // print_r("<br>Amount needed for this week: $needed_amount_for_the_week<br>");
    // print_r("<br>Paid installments: $paid_installments<br>");
    // print_r("<br>Week: $week<br>");

	if($amount_paid == $needed_amount_for_the_week){
        // The amount for next week has cleared 
        clearInstallmentForThatWeek($connect, $user_id, $amount_paid, $paid_installments, $week);
    }
    else if($amount_paid > $needed_amount_for_the_week){
        // The user has paid excess, recursively call this function
        $excess = $amount_paid -  $needed_amount_for_the_week;

        // Clear for this week
        clearInstallmentForThatWeek($connect, $user_id, $needed_amount_for_the_week, $paid_installments, $week);

        // Recursively clear for next week
        if($week < 8)
            recursivelyClearTheAmount($connect, $user_details, $user_id, $excess, $user_details['amount_wk' . ($week + 1)], $paid_installments + 1, $week + 1);
    }
    else{
        // The amount is less than the one needed for the next data
        clearInstallmentForThatWeek($connect, $user_id, $amount_paid, $paid_installments - 1, $week);
    }
}

// This function update the amount for a particular week
/**
 * @param $connect - Connection to server
 * @param $user_id - The ID of the user to update
 * @param $amount_paid - The amount the user paid
 * @param $required - The amount needed for that week
 * @param $paid_installments - The installment the user has completed
 * @param $week - The week to to update
 */
function clearInstallmentForThatWeek($connect, $user_id,$amount_paid, $paid_installments, $week){
    // TODO: Uncomment for debugging
    // print_r("<br>Cleared for week $week with amount $amount_paid and paid installments of $paid_installments for user $user_id <br>");
    
    $query=mysqli_query($connect,"SELECT wk" .$week. "_paid FROM loans WHERE user_id = '$user_id'");
    $row=mysqli_fetch_array($query);
    // print_r($row);
    $amt=intval($row["wk" .$week. "_paid"]);
    $new=$amt+$amount_paid;
    
    // echo $amt;
    // echo $amount_paid;
    //print_r($row);
    
    $query6 = mysqli_query($connect,"UPDATE loans SET wk" . $week . "_paid  = '$new', paid_installements = '$paid_installments' WHERE user_id = '$user_id'");
   
   	$query2=mysqli_query($connect,"SELECT wk1_paid,wk2_paid,wk3_paid,wk4_paid,wk5_paid,wk6_paid,wk7_paid,wk8_paid, ( wk1_paid+wk2_paid+wk3_paid+wk4_paid+wk5_paid+wk6_paid+wk7_paid+wk8_paid) AS sum FROM loans WHERE user_id='$user_id'");
   
   	$query3=mysqli_query($connect,"SELECT * FROM loans WHERE user_id='$user_id'");

   	$row3=mysqli_fetch_array($query3);
    $row=mysqli_fetch_array($query2);
    $w1=$row['sum'];

    //echo $w1;
    // $w2=$row['wk2_paid'];
    // $w3=$row['wk3_paid'];
    // $w4=$row['wk4_paid'];
    // $w5=$row['wk5_paid'];
    // $w6=$row['wk6_paid'];
    // $w7=$row['wk7_paid'];
    // $w8=$row['wk8_paid'];

    //$bal=($w1+$w2+$w3+$w4+$w5+$w6+$w7+$w8);
    $tr=$row3['total_refunded'];
    $credit=$row3['credit'];
    $lb=$tr-$w1;

    $q=mysqli_query($connect,"UPDATE loans SET loan_balance='$lb' WHERE user_id = '$user_id'");
    
    
    //update earnings
    
    if($w1>$credit){
        $amt=$w1-$credit;
        $query=mysqli_query($connect,"INSERT INTO `earnings`(`amount`) VALUES ('$amt')");
    }
    
    
    
    $result = runQuery($connect, $query);
    // echo $Nationalid;
        
    $query=mysqli_query($connect,"select * from customers where id='$user_id'");
    $row=mysqli_fetch_array($query);
    $mobile=$row['phone'];
    $n1=$row['username'];
    $nat=$row['National_id'];
    $n2=explode(" ",$n1);
    $name=$n2[0];
    
    $query=mysqli_query($connect,"INSERT INTO `repayments`(`National_id`, `amount`, `balance`, `mode`) VALUES ('$nat','$amount_paid','$lb','mpesa')");
    
    
    
    $message="Dear $name,Your repayment of ksh $amount_paid has been received.Your loan balance is $lb. Continue paying  your loans via Mpesa Paybill 4019891, A/c Id no#L.YGM Limited~Achieve it with us";
   sendtext($mobile,$message);
    

    // Check if the query ran successfully
    if(!$result){
        // Query was not succesful
        // TODO: Handle the error here
        die(mysqli_error($connect));
    }

    return true;
}

// This function is used to push a certain amount of cash to savings
function pushToSavings($connect, $amount_to_push, $user_id){
    // Uncomment here for debug
    // print_r("<br>Added $amount_to_push to savings for user $user_id<br>");
    // return;
    
    // Run query to push to savings
    $query = "SELECT balance FROM `savings` WHERE user_id = '$user_id' AND transtype = 'debit'";

    // Run the sql query
    $result = runQuery($connect, $query);
    
    if(!$result){
         $query = "SELECT balance FROM `savings` WHERE user_id = '$user_id' AND transtype = 'credit'";
         
         $result = runQuery($connect, $query);
         
         print_r($result);
         
         // Check if the query ran successfully
        if(!$result){
            // Query was not succesful
            // TODO: Please for the love of God handle the error here
            die(mysqli_error($connect));
        }
    }

    // Get the balance
    $balance = intval($result['balance']);
    // print_r("<br>The old balance is $balance<br>");
    
    $balance += intval($amount_to_push);
    // print_r("<br>The new balance is $balance<br>");

    $query = "UPDATE `savings` SET balance = $balance WHERE user_id = '$user_id'";

    // Run the sql query
    $result = runQuery($connect, $query);

    // Check if the query ran successfully
    if(!$result){
        // Query was not succesful
        // TODO: Please please Handle the error here
        die(mysqli_error($connect));
    }

    // The query was succesful, the savings were successfully updated
    return true;
}

// Used to run SQL query and return the results
function runQuery($mysql_connection, $query){
    // Run the quey
    $query_results = mysqli_query($mysql_connection, $query);
    
    // Fetch the data
    if(!$query_results){
        return false;
    }

    if($query_results === true){
        return true;
    }

    return mysqli_fetch_assoc($query_results);
}

// This function clears the user loans
function clearLoan($connect, $user_id, $clearance_amount,$profit){
    // Uncomment here for debug
    // print_r("<br>Cleared loan for user $user_id amount $clearance_amount<br>");
    // return;

    // Run query to clear user here
    $query = mysqli_query($connect,"INSERT into loans_cleared (user_id, amount) VALUES ('$user_id', '$clearance_amount')");
    //get user national id
    
     //get user loan balance
    $query=mysqli_query($connect,"select * from customers where id='$user_id'");
    
    $row=mysqli_fetch_assoc($query);
    $id=$row['National_id'];
    //get user loan balance
    $query=mysqli_query($connect,"select * from loans where user_id='$user_id'");
    
    
    
    $row=mysqli_fetch_assoc($query);
    
    $balance=$row['loan_balance'];
    $credit=$row['credit'];
    
    
     $query=mysqli_query($connect,"INSERT INTO `repayments`(`National_id`, `amount`, `balance`, `mode`) VALUES ('$id','$balance','0','mpesa')");
     
     $query2=mysqli_query($connect,"SELECT wk1_paid,wk2_paid,wk3_paid,wk4_paid,wk5_paid,wk6_paid,wk7_paid,wk8_paid, ( wk1_paid+wk2_paid+wk3_paid+wk4_paid+wk5_paid+wk6_paid+wk7_paid+wk8_paid) AS sum FROM loans WHERE user_id='$user_id'");
   
    $row=mysqli_fetch_array($query2);
    $w1=$row['sum'];
     
     if($w1>$credit){
        $amt=$w1-$credit;
        $query=mysqli_query($connect,"INSERT INTO `earnings`(`amount`) VALUES ('$amt')");
        if($query){
            $query1  =mysqli_query($connect,"DELETE FROM loans WHERE user_id = '$user_id'");
        }
    }
    

    // Run the sql query
    
    //$result = runQuery($connect, $query);
    
  
    
    //update Earnings 
    $sqw="SELECT * FROM balances";
    $qw=mysqli_query($connect,$sqw);

    $row=mysqli_fetch_assoc($qw);
    $earn=$row['Earnings'];

    $newern=$earn+$profit;
    $query="UPDATE balances SET Earnings='$newern'";
    $result = runQuery($connect, $query);

    // Check if the query ran successfully
    if(!$result){
        // Query was not succesful
        // TODO: Handle the error here
        die(mysqli_error($connect));
    }
    $datefig=date('m');
    $jan=$row['jan'];
    $feb=$row['feb'];
    $march=$row['march'];
    $april=$row['april'];
    $may=$row['may'];
    $june=$row['june'];
    $july=$row['july'];
    $aug=$row['aug'];
    $sep=$row['sep'];
    $oct=$row['oct'];
    $nov=$row['nov'];
    $dece=$row['dece'];

    if($datefig==1){
      $rvn=$jan+$fee;
      $sql3="UPDATE balances SET `jan`='$rvn'";
      $q=mysqli_query($connect,$sql3);
    }elseif($datefig==2){
      $rvn=$feb+$fee;
      $sql3="UPDATE balances SET `feb`='$rvn'";
      $q=mysqli_query($connect,$sql3);
    }elseif($datefig==3){
      $rvn=$march+$fee;
      $sql3="UPDATE balances SET `march`='$rvn'";
      $q=mysqli_query($connect,$sql3);
    }elseif($datefig==4){
      $rvn=$april+$fee;
      $sql3="UPDATE balances SET `april`='$rvn'";
      $q=mysqli_query($connect,$sql3);
    }elseif($datefig==5){
      $rvn=$may+$fee;
      $sql3="UPDATE balances SET `may`='$rvn'";
      $q=mysqli_query($connect,$sql3);
    }elseif($datefig==6){
      $rvn=$june+$fee;
      $sql3="UPDATE balances SET `june`='$rvn'";
      $q=mysqli_query($connect,$sql3);
    }elseif($datefig==7){
      $rvn=$july+$fee;
      $sql3="UPDATE balances SET `july`='$rvn'";
      $q=mysqli_query($connect,$sql3);
    }elseif($datefig==8){
      $rvn=$aug+$fee;
      $sql3="UPDATE balances SET `aug`='$rvn'";
      $q=mysqli_query($connect,$sql3);
    }elseif($datefig==9){
      $rvn=$sep+$fee;
      $sql3="UPDATE balances SET `sep`='$rvn'";
      $q=mysqli_query($connect,$sql3);
    }elseif($datefig==10){
      $rvn=$oct+$fee;
      $sql3="UPDATE balances SET `oct`='$rvn'";
      $q=mysqli_query($connect,$sql3);
    }elseif($datefig==11){
      $rvn=$nov+$fee;
      $sql3="UPDATE balances SET `nov`='$rvn'";
      $q=mysqli_query($connect,$sql3);
    }elseif($datefig==12){
      $rvn=$dece+$fee;
      $sql3="UPDATE balances SET `dece`='$rvn'";
      $q=mysqli_query($connect,$sql3);
    }else{
        return false;
    }

    // Delete the data from loans table, this is to ensure that the user is legible for another loan
    $query = "DELETE FROM loans WHERE user_id = '$user_id'";

    // Run the sql query
    $result = runQuery($connect, $query);

    // Check if the query ran successfully
    if(!$result){
        // Query was not succesful
        // TODO: Handle the error here
        die(mysqli_error($connect));
    }

    return true;
}

