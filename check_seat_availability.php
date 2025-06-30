<?php
include('connection.php');

if (!isset($_GET['seats'])) {
    echo json_encode(["status" => "error", "message" => "No seats selected"]);
    exit();
}

$seat_ids = explode(",", $_GET['seats']);

// Convert seat IDs to an SQL-friendly format
$placeholders = implode(',', array_fill(0, count($seat_ids), '?'));
$sql = "SELECT id FROM seats WHERE id IN ($placeholders) AND status = 'booked'";

$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat('i', count($seat_ids)), ...$seat_ids);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["status" => "unavailable"]);
} else {
    echo json_encode(["status" => "available"]);
}

$conn->close();
?>
