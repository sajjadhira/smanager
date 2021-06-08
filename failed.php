<?php

@session_start();

require_once('config.php');

// This is faild page you can do anything with ths customer/ user

// you can redirect the customer to the payment page again

// or,

// you can show error message to the customer if you want


// if you want to redirect the customer to the payment page url then just follow the procedure.

if (isset($_SESSION['transaction_id']) && isset($_SESSION['invoice_id']) && isset($_SESSION['payment_url'])) {

    header("Location: ".$payment_url);
    $payment_url = $_SESSION['payment_url'];
    die;
}


// if you want to message only then avoid the redirect system avobe and show message only
