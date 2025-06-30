<?php
include('connection.php');
require __DIR__ . '/../vendor/autoload.php';
use Razorpay\Api\Api;
session_start();

// Enable error reporting to see if any issues exist
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set the Content-Type header to application/json
header('Content-Type: application/json');

// Retrieve the POST data
$booking_id = $_POST['booking_id'] ?? '';
$payment_id = $_POST['payment_id'] ?? '';
$refund_amount = $_POST['amount'] ?? '';
$user_id = $_POST['user_id'] ?? '';

// Validate POST data
if (!$booking_id || !$payment_id || !$refund_amount || !$user_id) {
    echo json_encode(["status" => "error", "message" => "Missing required fields."]);
    exit;
}

// Razorpay API Credentials
$api_key = "rzp_test_529npOnJks99aK";
$api_secret = "jkZfsnHfFOQWJZz1GyJNcDWm";

// Log the incoming data
error_log('POST data: ' . print_r($_POST, true));  // Log POST data to the PHP error log

try {
    // Initialize Razorpay API
    $api = new Api($api_key, $api_secret);
    
    // Fetch payment details from Razorpay
    $payment = $api->payment->fetch($payment_id);
    
    // Check if the payment exists and is captured
    if (!$payment) {
        throw new Exception("The payment ID does not exist in Razorpay.");
    }

    if ($payment->status !== 'captured') {
        throw new Exception("The payment has not been captured or is in an invalid state.");
    }

    // Refund the payment
    $refund = $payment->refund([
        'amount' => intval($refund_amount * 100)  // Convert to paisa
    ]);

    // Log Razorpay refund response
    error_log('Refund Response: ' . print_r($refund, true));  // Log Razorpay response

    $refund_id = $refund->id;

    // Log refund details into the `refund_logs` table
    $stmt = $conn->prepare("INSERT INTO refund_logs (booking_id, user_id, amount, refund_id, status, created_at) VALUES (?, ?, ?, ?, 'Paid', NOW())");
    $stmt->bind_param("iids", $booking_id, $user_id, $refund_amount, $refund_id);
    $stmt->execute();

    // Update the bookings table with the refund status
    $stmt2 = $conn->prepare("UPDATE bookings SET status='Refunded', amount=? WHERE id=?");
    $stmt2->bind_param("di", $refund_amount, $booking_id);
    $stmt2->execute();

    // Return a success message in JSON format
    echo json_encode([
        'status' => 'success',
        'message' => 'Refund successful!',
        'refund_id' => $refund_id,
    ]);

} catch (Exception $e) {
    // Log any exception error
    error_log('Error: ' . $e->getMessage());  // Log the error message

    // Return an error message in JSON format
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
    ]);
}
?>
