<?php
session_start();
require '../vendor/autoload.php';
include('../dbconnection.php');

if (!isset($_SESSION['vpmsuid'])) {
    die("User ID not found. Please login first.");
}

$userId = $_SESSION['vpmsuid'];

// Set your Merchant Server Key
Midtrans\Config::$serverKey = 'YOUR_SERVER_KEY';
// Set to Development/Sandbox Environment
Midtrans\Config::$isProduction = false;

// Assume that parking fee is a constant
$parking_fee = 12000; // Change this to your actual parking fee

// Fetch data from tblpayments
$payment_query = "SELECT * FROM tblpayments WHERE user_id = ?"; 
$stmt = $con->prepare($payment_query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$payment_result = $stmt->get_result();
$payment_data = $payment_result->fetch_assoc();

// Fetch data from tblregusers
$user_query = "SELECT * FROM tblregusers WHERE ID = ?";
$stmt = $con->prepare($user_query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();

$transaction_details = array(
  'order_id' => $payment_data['orderID'],
  'gross_amount' => $parking_fee,
);

$item_details = array (
    array(
      'id' => 'item1',
      'price' => $parking_fee,
      'quantity' => 1,
      'name' => "Parking Fee",
    )
);

// Define customer details
$customer_details = array(
  'first_name' => $user_data['FirstName'],
  'last_name' => $user_data['LastName'],
  'email' => $user_data['Email'], 
);

$transaction = array(
  'transaction_details' => $transaction_details,
  'item_details' => $item_details,
  'customer_details' => $customer_details
);


// Update the status to "accepted" if payment_status is "DONE"
if ($payment_data['payment_status'] === 'DONE') {
    $update_status_query = "UPDATE tblregusers SET status = 'accepted' WHERE ID = ?";
    $stmt = $con->prepare($update_status_query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
}

$snapToken = Midtrans\Snap::getSnapToken($transaction);
$_SESSION['snapToken'] = $snapToken;

header("Location: dashboard.php");
exit();
?>
