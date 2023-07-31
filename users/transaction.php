<?php
 // Don't forget to include autoloader if you haven't already
require_once '../vendor/autoload.php'; 

// Set your Merchant Server Key
\Midtrans\Config::$serverKey = 'SB-Mid-server-kQCwgJQAfjbDa0DpxyAyo-oH';
// Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
\Midtrans\Config::$isProduction = false;
// Set sanitization on (default)
\Midtrans\Config::$isSanitized = true;
// Set 3DS transaction for credit card to true
\Midtrans\Config::$is3ds = true;

$params = array(
    'transaction_details' => array(
        'order_id' => rand(),
        'gross_amount' => 12000,
    ),
    'customer_details' => array(
        'first_name' => isset($_POST['firstname']) ? $_POST['firstname'] : '',
        'last_name' => isset($_POST['lastname']) ? $_POST['lastname'] : '',
        'email' => isset($_POST['email']) ? $_POST['email'] : '',
    ),
);

try {
    $snapToken = \Midtrans\Snap::getSnapToken($params);
    echo $snapToken;
} catch (Exception $e) {
    // handle exception
    echo 'Error occurred: ' . $e->getMessage();
}

?>
