<?php
@session_start();

require_once('config.php');


$invoice_id = 1; // your invoice id
$trnxId = 'trnx_'.uniqid();


$customer_name = 'John Doe';
$customer_phone = '01811XXXXXX'; // Valid Bangladeshi Number

$amount = 7500; // minimum amount 15 BDT

$service_charge = ceil(($amount * 2)/100); // if you take service charge from client then you can add here, other wise replace the line with $service_charge = 0;
$total_payable = $amount + $service_charge;

$purpose = 'Payment for Invoice # '.$invoice_id .'.  Total Amount - ৳'.$amount;

if($service_charge>0){
    $purpose.='. Service Charge (2%) - ৳'.$service_charge;
}


$details = 'Payment for Invoice # '.$invoice_id. ' Total Amount - ৳'.$invoice_total;

$emi_month = NULL; // EMI month can be 3,6,9,12 and EMI only available if you active their EMI package and total amount is more then 5000 BDT

		if($emi_month != NULL){
	      $purpose.=' + EMI Interest';
	      $details.=' + EMI Interest';
	}

$_SESSION['invoice_id'] = $invoice_id;
$_SESSION['transaction_id'] = $trnxId;

	try {
    
	    $header = [
	    'Content-Type: application/json',
			'client-id: '.$client_id,
			'client-secret: '.$client_secret
			];
    
    	$url = 'https://api.sheba.xyz/v1/ecom-payment/initiate';
    
    	    $postfields = [
	        'amount' => $total_payable,
	        'transaction_id'=>$trnxId,
	        'success_url'=>'https://inihub.com/success.php', /* chnage the url to your payment success page */
	        'fail_url'=>'https://inihub.com/failed.php', /* chnage the url to your payment failed page */
	        'customer_name'=> $customer_name,
	        'customer_mobile'=> $customer_phone,
	        'purpose'=> $purpose,
	        'payment_details'=> $details,
	        ];
    
    	     if($emi_month != NULL){
	           $postfields['emi_month'] = $emi_month;
	        }
    
        	    
	    $ch = curl_init();
        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);  
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postfields));
        $result = curl_exec($ch);
        curl_close($ch);
        
        $response = json_decode($result);
        
        if($response->code == 200){
          $url = $response->data->link;
          $_SESSION['payment_url'] = $url;
          header("Location: ".$url);
          die;
        }else{
          echo 'There is an error with payment';
        }
    
  }catch (\Exception $ex) {
      echo $ex->getMessage();
  }
