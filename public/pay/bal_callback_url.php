<?php
include ('dbconnect.php');


   $balResponse = file_get_contents('php://input');
    
    // $balResponse='{"Result":
    //     {"ResultType":0,
    //     "ResultCode":0,
    //     "ResultDesc":"The service request is processed successfully.",
    //     "OriginatorConversationID":"3083-7403617-1",
    //     "ConversationID":"AG_20200529_00004d780bd1e05470ba",
    //     "TransactionID":"OET0000000",
    //     "ResultParameters":
    //     {"ResultParameter":
    //         [{"Key":"AccountBalance",
    //         "Value":"Working Account|KES|3000.00|3000.00|0.00|0.00&Utility Account|KES|5904.19|5904.19|0.00|0.00&Charges Paid Account|KES|0.00|0.00|0.00|0.00"},
    //         {"Key":"BOCompletedTime","Value":20200529223529}]},
    //         "ReferenceData":{"ReferenceItem":{"Key":"QueueTimeoutURL","Value":"https:\/\/internalapi.safaricom.co.ke\/mpesa\/abresults\/v1\/submit"}}}}';

        //   $logFile = "BalResponse.json";
        //   $log = fopen($logFile, "a");
        //   fwrite($log, $balResponse);
        //   fclose($log);


    $data = json_decode($balResponse);
    // First get the balance string from decoded json
    $balance_str = $data->Result->ResultParameters->ResultParameter[0]->Value;

    // Will split the string into three because the accounts are separated by &
    $balances = explode('&', $balance_str);

    // Uncomment to debug
    // print_r($balances);
    // exit;

    // First one is working account string
    $workingAccount = explode('|', $balances[0])[2];
    //echo ($workingAccount);
    
    // $floatAccount = ;

    // Second is utility account
    $utilityAccount =  explode('|', $balances[1])[2];
    //echo ($utilityAccount);

    // Third is charges paid account
    $chargesPaidAccount =  explode('|', $balances[2])[2];
    //echo ($chargesPaidAccount);

    // $orgSettlmAcc = ;
        
    // Get it from the json
    $BOCompletedTime = $data->Result->ResultParameters->ResultParameter[1]->Value;
    $date=date('Y-m-d H:i:s');
    // $query1="INSERT INTO `accountbalance`(`WorkingAccount`, `FloatAccount`, `UtilityAccount`, `ChargesPaidAccount`, `OrganizationSettlementAccount`, `BOCompletedTime`, `updatedTime`)
    // VALUES ('10','100','100','100','100','','')";
    
    $query = mysqli_query($connect,"INSERT INTO `accountbalance`(`WorkingAccount`, `FloatAccount`, `UtilityAccount`, `ChargesPaidAccount`, `OrganizationSettlementAccount`, `BOCompletedTime`, `updatedTime`)
    VALUES ('$workingAccount','$workingAccount','$utilityAccount','$chargesPaidAccount','$workingAccount','$BOCompletedTime','$BOCompletedTime')");
    $query = mysqli_query($connect,"$query1");             
    $query=mysqli_query($connect,"update balances set mpesa_balance='$utilityAccount' where id='1'");

    