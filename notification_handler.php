<?php
require_once('./vendor/autoload.php');
include('./dbconnection.php');

\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$serverKey = 'SB-Mid-server-kQCwgJQAfjbDa0DpxyAyo-oH';

$notif = new \Midtrans\Notification();

$transaction = $notif->transaction_status;
$fraud = $notif->fraud_status;
$order_id = $notif->order_id;

error_log("Order ID = $order_id, Transaction status = $transaction, Fraud status = $fraud");

// You need to update transaction data in the database
if ($transaction == 'settlement') {
    // Update payment status in your database to 'DONE'
    $stmt = $con->prepare("UPDATE tblpayments SET payment_status = 'DONE' WHERE orderID = ?");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
} else if ($transaction == 'deny') {
    // Payment is denied, update payment_status and status in your database.
    $stmt = $con->prepare("UPDATE tblpayments SET payment_status = 'DENY' WHERE orderID = ?");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
} else if ($transaction == 'expire') {
    // Payment has expired, update payment_status and status in your database.
    $stmt = $con->prepare("UPDATE tblpayments SET payment_status = 'EXPIRE' WHERE orderID = ?");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
} else if ($transaction == 'cancel') {
    // Payment is cancelled, update payment_status and status in your database.
    $stmt = $con->prepare("UPDATE tblpayments SET payment_status = 'CANCEL' WHERE orderID = ?");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
}
?>
