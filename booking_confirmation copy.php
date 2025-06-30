    <?php
    session_start();
    include('connection.php');
    include('includes/header.php');
    require 'vendor/autoload.php'; // Load PHPMailer

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <link rel="stylesheet" href="style.css">

    </head>
    <body>
        
    </body>
    </html>
    <?php
    if (!isset($_SESSION['user_id'])) {
        die("Error: User not logged in.");
    }

    // Get data from URL
    $payment_id = $_GET['payment_id'] ?? '';
    $movie_title = isset($_GET['movie']) ? urldecode($_GET['movie']) : '';
    $theater_name = isset($_GET['theater']) ? urldecode($_GET['theater']) : '';
    $screen = isset($_GET['screen']) ? urldecode($_GET['screen']) : '';
    $seat_list = isset($_GET['seats']) ? urldecode($_GET['seats']) : '';
    $amount = $_GET['amount'] ?? 0;
    $show_date = $_GET['date'] ?? '';
    $show_time = $_GET['time'] ?? '';

    if (empty($payment_id) || empty($movie_title) || empty($seat_list)) {
        die("Error: Missing booking details. <a href='index.php'>Go Back</a>");
    }

    // Fetch movie ID
    $movie_query = "SELECT id FROM movies WHERE title = ?";
    $movie_stmt = $conn->prepare($movie_query);
    $movie_stmt->bind_param("s", $movie_title);
    $movie_stmt->execute();
    $movie_result = $movie_stmt->get_result();

    if ($movie_result->num_rows > 0) {
        $movie_row = $movie_result->fetch_assoc();
        $movie_id = $movie_row['id'];
    } else {
        die("Error: Movie not found.");
    }
    $movie_stmt->close();

    // Fetch theater ID
    $theater_query = "SELECT id FROM theaters WHERE name = ?";
    $theater_stmt = $conn->prepare($theater_query);
    $theater_stmt->bind_param("s", $theater_name);
    $theater_stmt->execute();
    $theater_result = $theater_stmt->get_result();

    if ($theater_result->num_rows > 0) {
        $theater_row = $theater_result->fetch_assoc();
        $theater_id = $theater_row['id'];
    } else {
        die("Error: Theater not found.");
    }
    $theater_stmt->close();

    // Fetch user details
    $user_id = $_SESSION['user_id'];
    $user_query = "SELECT First_name, Last_name, email FROM users WHERE User_id = ?";
    $user_stmt = $conn->prepare($user_query);
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();

    if ($user_result->num_rows > 0) {
        $user_row = $user_result->fetch_assoc();
        $username = $user_row['First_name'] . " " . $user_row['Last_name'];
        $user_email = $user_row['email'];  // Get user email for sending confirmation
    } else {
        die("Error: User not found.");
    }
    $user_stmt->close();

    // Count number of tickets
    $num_tickets = count(explode(',', $seat_list));

    // Start transaction
    $conn->begin_transaction();

    // Insert booking record
    $sql = "INSERT INTO bookings (user_id, username, movie_id, theater_id, theater, screen, seat_numbers, num_tickets, amount, payment_id, show_date, show_time, booking_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isiissssisss', $user_id, $username, $movie_id, $theater_id, $theater_name, $screen, $seat_list, $num_tickets, $amount, $payment_id, $show_date, $show_time);

    $success = $stmt->execute();

    if ($success) {
        // Fetch seat IDs and update status
        $seat_array = explode(',', $seat_list);
        $placeholders = implode(',', array_fill(0, count($seat_array), '?'));

        $fetch_seat_query = "SELECT id FROM seats WHERE seat_number IN ($placeholders) AND screen_id = (SELECT id FROM screens WHERE screen_name = ?)";
        $fetch_seat_stmt = $conn->prepare($fetch_seat_query);

        $params = array_merge($seat_array, [$screen]);
        $types = str_repeat('s', count($seat_array)) . 's';
        $fetch_seat_stmt->bind_param($types, ...$params);
        $fetch_seat_stmt->execute();

        $seat_ids = [];
        $seat_result = $fetch_seat_stmt->get_result();
        while ($seat_row = $seat_result->fetch_assoc()) {
            $seat_ids[] = $seat_row['id'];
        }

        if (!empty($seat_ids)) {
            $seat_placeholders = implode(',', array_fill(0, count($seat_ids), '?'));
            $update_seat_query = "UPDATE seats SET status = 'booked' WHERE id IN ($seat_placeholders)";
            $update_seat_stmt = $conn->prepare($update_seat_query);
            $types = str_repeat('i', count($seat_ids));
            $update_seat_stmt->bind_param($types, ...$seat_ids);
            $update_seat_stmt->execute();
        }

        $conn->commit(); // Commit transaction

        // **Send Email Confirmation**
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

    // Redirect after processing (without showing messages)
    ob_start();
    header("Location: index.php");
    ob_end_clean();
    exit;
} else {
    $conn->rollback(); // Rollback on failure
    error_log("Database Error: " . $stmt->error);
}
    $stmt->close();
    $conn->close();
    ?>
