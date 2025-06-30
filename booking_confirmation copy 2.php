<?php
session_start();
include('connection.php');
require 'vendor/autoload.php'; // Load PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}

// ✅ Get data from session
$theater_id = $_SESSION['theater_id'];
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$selected_seats = $_SESSION['selected_seats']; // Array of seat IDs
$amount = $_SESSION['amount'];
$movie_title = $_SESSION['movie_title'];
$theater_name = $_SESSION['theater_name'];
$screen_name = $_SESSION['screen_name'];
$show_time = $_SESSION['time'];
$show_date = $_SESSION['date'];

if (empty($selected_seats) || empty($movie_title)) {
    die("Error: Missing booking details. <a href='index.php'>Go Back</a>");
}

$num_tickets = count($selected_seats); // Number of seats

// ✅ Fetch movie ID
$movie_query = "SELECT id FROM movies WHERE title = ?";
$movie_stmt = $conn->prepare($movie_query);
$movie_stmt->bind_param("s", $movie_title);
$movie_stmt->execute();
$movie_result = $movie_stmt->get_result();
$movie_row = $movie_result->fetch_assoc();
$movie_id = $movie_row['id'] ?? null;
$movie_stmt->close();

if (!$movie_id) {
    die("Error: Movie not found.");
}

// ✅ Convert seat IDs to Seat Numbers (A1, A2, etc.)
$seat_placeholders = implode(',', array_fill(0, count($selected_seats), '?'));
$seat_query = "SELECT seat_number FROM seats WHERE id IN ($seat_placeholders)";
$seat_stmt = $conn->prepare($seat_query);
$types = str_repeat('i', count($selected_seats));
$seat_stmt->bind_param($types, ...$selected_seats);
$seat_stmt->execute();
$seat_result = $seat_stmt->get_result();

$seat_numbers = [];
while ($row = $seat_result->fetch_assoc()) {
    $seat_numbers[] = $row['seat_number'];
}
$seat_stmt->close();

if (empty($seat_numbers)) {
    die("Error: No seats found.");
}

// ✅ Convert seat array to comma-separated string (e.g., "A1, A2, A3")
$seat_list = implode(', ', $seat_numbers);

// ✅ Start transaction to prevent double booking
$conn->begin_transaction();

try {
    // ✅ Ensure selected seats are available
    $seat_check_query = "SELECT id FROM seats WHERE id IN ($seat_placeholders) AND status = 'booked'";
    $seat_check_stmt = $conn->prepare($seat_check_query);
    $seat_check_stmt->bind_param($types, ...$selected_seats);
    $seat_check_stmt->execute();
    $seat_check_result = $seat_check_stmt->get_result();

    if ($seat_check_result->num_rows > 0) {
        $conn->rollback();
        die("Error: One or more selected seats are already booked. Please select different seats.");
    }
    $seat_check_stmt->close();

    // ✅ Insert booking record with seat numbers
    $payment_id = uniqid('PAY_');

    $sql = "INSERT INTO bookings (user_id, username, movie_id, theater_id, theater, screen, seat_numbers, num_tickets, amount, payment_id, show_date, show_time, booking_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isiissssisss', $user_id, $username, $movie_id, $theater_id, $theater_name, $screen_name, $seat_list, $num_tickets, $amount, $payment_id, $show_date, $show_time);
    $stmt->execute();
    $stmt->close();

    // ✅ Update seat status to "booked"
    $update_seat_query = "UPDATE seats SET status = 'booked' WHERE id IN ($seat_placeholders)";
    $update_seat_stmt = $conn->prepare($update_seat_query);
    $update_seat_stmt->bind_param($types, ...$selected_seats);
    $update_seat_stmt->execute();
    $update_seat_stmt->close();

    // ✅ Commit transaction if everything is successful
    $conn->commit();

    // ✅ Fetch user email
    $user_query = "SELECT email FROM users WHERE User_id = ?";
    $user_stmt = $conn->prepare($user_query);
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user_row = $user_result->fetch_assoc();
    $user_email = $user_row['email'] ?? null;
    $user_stmt->close();

    if (!$user_email) {
        die("Error: User email not found.");
    }

    // ✅ Send Email Confirmation with Seat Numbers
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'saminkadivar2911@gmail.com'; 
        $mail->Password = 'baeq zzsa ofmq mkop'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->CharSet = "UTF-8";
$mail->Encoding = "base64";

        $mail->setFrom('no-reply@Ticketmaster.com', 'TicketMaster');
        $mail->addAddress($user_email, $username);
        $mail->isHTML(true);
        $mail->Subject = "Movie Ticket Booking Confirmation - $movie_title";
        $mail->Body = "
        <html>
        <head>
            <style>
                body { font-family: 'Arial', sans-serif; background-color: #111; color: #fff; padding: 20px; text-align: center; }
                .email-container { max-width: 600px; margin: auto; background: #222; padding: 20px; border-radius: 10px; box-shadow: 0 4px 10px rgba(255, 215, 0, 0.5); }
                .header { background: linear-gradient(135deg, #d4af37, #ffcc00); color: #222; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; border-radius: 10px 10px 0 0; }
                .movie-details { text-align: center; margin: 20px 0; padding: 10px; background: rgba(255, 215, 0, 0.1); border-radius: 10px; }
                .movie-name { font-size: 28px; font-weight: bold; color: #ffcc00; }
                .info-box { background: rgba(255, 255, 255, 0.1); padding: 15px; border-radius: 10px; margin: 10px 0; text-align: left; }
                .info-box p { margin: 8px 0; font-size: 16px; color: #ddd; }
                .ticket-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
                .ticket-table th, .ticket-table td { border: 1px solid rgba(255, 215, 0, 0.5); padding: 12px; text-align: left; font-size: 15px; }
                .ticket-table th { background: #d4af37; color: #222; text-align: center; }
                .qr-code { text-align: center; margin-top: 20px; color: #d4af37;}
                .footer { text-align: center; font-size: 14px; padding: 10px; color: #bbb; margin-top: 15px; }
                .cta-button { background: #d4af37; color: #222; text-decoration: none; padding: 12px 20px; font-size: 16px; font-weight: bold; border-radius: 5px; display: inline-block; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='email-container'>
                <div class='header'>🎬 Your Movie Ticket - TicketMaster</div>
                <div class='movie-details'>
                    <p class='movie-name'>$movie_title</p>
                    <p><strong>Theater:</strong> $theater_name | <strong>Screen:</strong> $screen</p>
                </div>
                <div class='info-box'>
                    <p><strong>📅 Show Date:</strong> $show_date</p>
                    <p><strong>⏰ Show Time:</strong> $show_time</p>
                    <p><strong>🎟️ Seats:</strong> $seat_list</p>
                    <p><strong>🎫 Tickets:</strong> $num_tickets</p>
                    <p><strong>💰 Amount Paid:</strong> ₹$amount</p>
                    <p><strong>🔑 Transaction ID:</strong> $payment_id</p>
                </div>
                <div class='qr-code'>
                    <p><strong > Scan this QR code at the entrance:</strong></p>
                    <img src='https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=$payment_id' alt='QR Code'>
                </div>
                <div style='text-align: center;'>
                    <a href='https://yourwebsite.com/mybookings' class='cta-button'>🎥 View My Bookings</a>
                </div>
                <div class='footer'>Thank you for booking with <strong>TicketMaster</strong>. Enjoy your movie! 🍿</div>
            </div>
        </body>
        </html>";
        

    

        $mail->send();
} catch (Exception $e) {
    error_log("Email sending failed: " . $mail->ErrorInfo);
}

    // ✅ Redirect after successful booking
    header("Location: booking_success.php?payment_id=$payment_id");
    exit;
} catch (Exception $e) {
    $conn->rollback(); // Rollback transaction in case of failure
    die("Booking failed: " . $e->getMessage());
}
?>
