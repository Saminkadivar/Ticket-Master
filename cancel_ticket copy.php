<?php
include('connection.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Login required."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$booking_id = $_POST['booking_id'];

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

// Check if cancellation is within allowed time (e.g., 15 minutes before showtime)
$current_time = date("Y-m-d H:i:s");
$show_datetime = $booking['show_date'] . ' ' . $booking['show_time'];

if (strtotime($show_datetime) - strtotime($current_time) < 900) { // 900 seconds = 15 minutes
    echo json_encode(["status" => "error", "message" => "You cannot cancel within 15 minutes of the showtime."]);
    exit;
}

// Calculate refund (e.g., full refund if cancelled 1 hour before, else 50%)
$refund_amount = ($booking['amount'] * 0.5); // Example: 50% refund

// Update booking status
$update_sql = "UPDATE bookings SET status = 'Cancelled', refund_amount = ? WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("di", $refund_amount, $booking_id);

if ($update_stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Ticket cancelled successfully! Refund: ₹$refund_amount"]);
} else {
    echo json_encode(["status" => "error", "message" => "Cancellation failed."]);
}
?>

