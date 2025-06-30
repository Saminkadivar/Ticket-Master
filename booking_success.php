<?php
session_start();
require "includes/header.php";

if (!isset($_GET['payment_id'])) {
    die("Error: Invalid access.");
}

$payment_id = $_GET['payment_id'];
include('connection.php');

// Fetch booking info
$query = "SELECT b.*, u.username, m.title AS movie_title 
          FROM bookings b 
          JOIN movies m ON b.movie_id = m.id 
          JOIN users u ON b.user_id = u.User_id 
          WHERE b.payment_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $payment_id);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();
$stmt->close();

if (!$booking) {
    die("Error: Booking not found.");
}

// ✅ Normalize seat numbers (remove spaces)
$seat_numbers_raw = explode(",", $booking['seat_numbers']);
$seat_numbers = array_map('trim', $seat_numbers_raw); // removes spaces like " A1 "
$seat_details = [];

// ✅ Prepare placeholders and bind types
$placeholders = implode(',', array_fill(0, count($seat_numbers), '?'));
$types = str_repeat('s', count($seat_numbers));

// ✅ Get prices for all seats
$sql = "SELECT seat_number, price FROM seats WHERE seat_number IN ($placeholders)";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$seat_numbers);
$stmt->execute();
$result = $stmt->get_result();

// ✅ Build map of seat_number => price
while ($row = $result->fetch_assoc()) {
    $seat = trim($row['seat_number']); // Just in case DB has trailing space
    $seat_details[$seat] = $row['price'];
}
$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #28a745;
        }
        p {
            font-size: 16px;
        }
        .details {
            text-align: left;
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fafafa;
        }
        button {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            margin: 10px;
            border-radius: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .print-btn {
            background-color: #28a745;
        }
        .print-btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
<br>
<div class="container">
    <h2>🎉 Booking Confirmed! 🎟️</h2>
    <p>Thank you, <b><?= htmlspecialchars($booking['username']); ?></b>! Your booking has been successfully confirmed.</p>
    
    <div class="details">
        <p><strong>🎬 Movie:</strong> <?= htmlspecialchars($booking['movie_title']); ?></p>
        <p><strong>🏢 Theater:</strong> <?= htmlspecialchars($booking['theater']); ?></p>
        <p><strong>🖥️ Screen:</strong> <?= htmlspecialchars($booking['screen']); ?></p>
        <p><strong>📆 Show Date:</strong> <?= htmlspecialchars($booking['show_date']); ?></p>
        <p><strong>⏰ Show Time:</strong> <?= htmlspecialchars($booking['show_time']); ?></p>
        
        <p><strong>💺 Seats & Prices:</strong></p>
        <ul style="text-align:left; padding-left: 25px;">
            <?php 
            $total_calculated = 0;
            foreach ($seat_numbers as $seat) {
                $price = $seat_details[$seat] ?? 0;
                $total_calculated += $price;
                echo "<li>Seat <b>$seat</b>: ₹$price</li>";
            }
            ?>
        </ul>

        <p><strong>💳 Payment ID:</strong> <?= htmlspecialchars($payment_id); ?></p>
        <p><strong>🧮 Calculated Total:</strong> ₹<?= $total_calculated; ?></p>
    </div>

    <button class="print-btn" onclick="printContainer();">🖨 Print Ticket</button>
    <button onclick="window.location.href='index.php';">🏠 Back to Home</button>
</div>

<script>
    function printContainer() {
        var container = document.querySelector('.container').innerHTML;
        var originalBody = document.body.innerHTML;
        
        document.body.innerHTML = container;
        window.print();
        document.body.innerHTML = originalBody;
    }
</script>

</body>
</html>
