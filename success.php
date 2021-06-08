<?php

@session_start();

require_once('config.php');


if (isset($_SESSION['transaction_id']) && isset($_SESSION['invoice_id']) && isset($_SESSION['payment_url'])) {

    $transaction_id = $_SESSION['transaction_id'];
    $invoice_id = $_SESSION['invoice_id'];
    $payment_url = $_SESSION['payment_url'];

    try {
        $header = [
            'Content-Type: application/json',
            'client-id: '.$client_id,
            'client-secret: '.$client_secret
        ];

        $url = 'https://api.sheba.xyz/v1/ecom-payment/details?transaction_id='.$transaction_id;

	    
	    
        $ch = curl_init();
         curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
         curl_setopt($ch, CURLOPT_HEADER, 0);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);  
         curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
         $result = curl_exec($ch);
         curl_close($ch);
         
         $response = json_decode($result);
         
         
         if ($response->code == 200) {


            if($response->data->payment_status == 'completed'){
		    
            /* unset stored session */
	    @unset($_SESSION['transaction_id']);
            @unset($_SESSION['invoice_id']);    
            @unset($_SESSION['payment_url']);
	   /* unset stored session */
		    
            // it means your payment is completed, now you can do anything with your invoice.
            // you can get your invoice id from session using $invoice_id (predefined) variable 

            }else{
                // here means your payment request is not completed yet. you can redirect user to the payment url again for complete the payment

                header("Location: ".$payment_url);
                die;
            }

         }else{

            echo $response->message;

         }
    }



}else{
    echo 'Sorry we cannot find any payment request!';
}



