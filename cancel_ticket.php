<?php
include('connection.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Login required."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$booking_id = $_POST['booking_id'];
$cancellation_reason = trim($_POST['cancellation_reason'] ?? ''); // Get cancellation reason

// Validate reason
if (empty($cancellation_reason)) {
    echo json_encode(["status" => "error", "message" => "Cancellation reason is required."]);
    exit;
}

// Check if booking exists and is not already cancelled
$sql = "SELECT amount, status, show_date, show_time FROM bookings WHERE id = ? AND user_id = ? AND status = 'Booked'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    echo json_encode(["status" => "error", "message" => "Invalid booking or already cancelled."]);
    exit;
}

// Current time and showtime
$current_time = date("Y-m-d H:i:s");
$show_datetime = $booking['show_date'] . ' ' . $booking['show_time'];
$time_difference = strtotime($show_datetime) - strtotime($current_time);

// Cancellation policy
if ($time_difference < 900) { // Less than 15 minutes
    echo json_encode(["status" => "error", "message" => "You cannot cancel within 15 minutes of the showtime. No refund available."]);
    exit;
} elseif ($time_difference >= 43200) { // 12 hours or more (12 * 3600 = 43200 seconds)
    $refund_amount = $booking['amount']; // Full refund
} else { // Between 12 hours and 15 minutes
    $refund_amount = $booking['amount'] * 0.5; // 50% refund
}

// Update booking status with cancellation reason
$update_sql = "UPDATE bookings SET status = 'Cancelled', refund_amount = ?, cancellation_reason = ? WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("dsi", $refund_amount, $cancellation_reason, $booking_id);

if ($update_stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Ticket cancelled successfully! Refund: ₹$refund_amount"]);
} else {
    echo json_encode(["status" => "error", "message" => "Cancellation failed."]);
}
?>
