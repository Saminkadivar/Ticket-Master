<?php
session_start();
include('connection.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die(json_encode(['status' => 'error', 'message' => 'User not logged in.']));
}

// Fetch data from POST request
$selectedSeats = $_POST['seats'] ?? '';
$payment_status = $_POST['payment_status'] ?? 'failed';

// Ensure payment was successful
if ($payment_status !== 'success') {
    die(json_encode(['status' => 'error', 'message' => 'Payment failed.']));
}

// Split the selected seats into an array
$selectedSeatsArray = explode(",", $selectedSeats);

// Ensure that seats were selected
if (empty($selectedSeatsArray)) {
    die(json_encode(['status' => 'error', 'message' => 'No seats selected.']));
}

// Start transaction to prevent double booking
$conn->begin_transaction();

// Array to hold booked seat information
$bookedSeats = [];

foreach ($selectedSeatsArray as $seatId) {
    // Check if the seat is already booked
    $checkQuery = "SELECT status FROM seats WHERE id = ? FOR UPDATE"; // Lock the row with FOR UPDATE
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("i", $seatId);
    $stmt->execute();
    $result = $stmt->get_result();
    $seat = $result->fetch_assoc();

    // If the seat is already booked, rollback the transaction and return error
    if ($seat && $seat['status'] == 'booked') {
        $conn->rollback();
        die(json_encode(['status' => 'error', 'message' => 'One or more seats have already been booked.']));
    }

    // Mark the seat as booked
    $updateQuery = "UPDATE seats SET status = 'booked' WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("i", $seatId);
    $stmt->execute();

    // Add the booked seat to the array
    $bookedSeats[] = $seatId;
}

// Commit the transaction to save the seat bookings
$conn->commit();

// Return success response and list of booked seats
echo json_encode([
    'status' => 'success',
    'message' => 'Seats booked successfully!',
    'booked_seats' => $bookedSeats
]);

?>
