<?php
session_start();
include('connection.php');
include('includes/header.php');

if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}

// Fetch selected seats from URL
$selected_seats = isset($_GET['seats']) ? explode(",", $_GET['seats']) : [];
if (empty($selected_seats)) {
    die("Error: No seats selected.");
}

// Fetch movie, theater, screen, seat details, and price
$sql = "SELECT m.title AS movie_title, t.name AS theater_name, sc.screen_name, st.time, st.date, 
               s.seat_number, s.id AS seat_id, s.price AS seat_price 
        FROM seats s
        JOIN showtimes st ON s.showtime_id = st.id
        JOIN screens sc ON s.screen_id = sc.id
        JOIN theaters t ON s.theater_id = t.id
        JOIN movies m ON st.movie_id = m.id
        WHERE s.id IN (" . implode(",", array_map('intval', $selected_seats)) . ")";

$result = $conn->query($sql);
if (!$result) {
    die("SQL Error: " . $conn->error);
}
if ($result->num_rows == 0) {
    die("Error: Unable to fetch booking details.");
}

// Initialize booking details
$movie_title = '';
$theater_name = '';
$screen_name = '';
$time = '';
$date = '';
$seat_list = [];
$seat_ids = [];
$total_amount = 0;

while ($row = $result->fetch_assoc()) {
    $movie_title = $row['movie_title'];
    $theater_name = $row['theater_name'];
    $screen_name = $row['screen_name'];
    $time = $row['time'];
    $date = $row['date'];
    $seat_list[] = $row['seat_number'];
    $seat_ids[] = $row['seat_id'];
    $total_amount += $row['seat_price'];  // Sum up the seat prices dynamically
}

// Store details in session
$_SESSION['selected_seats'] = $seat_ids;
$_SESSION['amount'] = $total_amount;
$_SESSION['movie_title'] = $movie_title;
$_SESSION['theater_name'] = $theater_name;
$_SESSION['screen_name'] = $screen_name;
$_SESSION['time'] = $time;
$_SESSION['date'] = $date;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <link rel="stylesheet" href="style.css">

    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; background-color: #001F3F; color:#fff; border-radius: 8px; text-align: center; }
        .btns { 
            background-color: #001F3F; 
            color: white; 
            padding: 12px 24px; 
            border-radius: 8px; 
            font-size: 16px; 
            font-weight: bold;
            border: 2px solid #fff;
            cursor: pointer; 
            transition: background-color 0.3s ease-in-out, transform 0.2s;
        }

        .btns:hover { 
            background-color:#FFD700; 
            color:#001F3F;
            border-color: #fff;
            transform: scale(1.05); 
        }

        .btns:active { 
            background-color: #000d1a;
            transform: scale(0.98); 
        }

        h2 {
            font-size: 2rem;
            color: #FFD700; 
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <br>
    <div class="container">
        <h2>Confirm Your Booking</h2>
        <p><strong>Movie:</strong> <?= htmlspecialchars($movie_title); ?></p>
        <p><strong>Theater:</strong> <?= htmlspecialchars($theater_name); ?></p>
        <p><strong>Screen:</strong> <?= htmlspecialchars($screen_name); ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars($date); ?></p>
        <p><strong>Time:</strong> <?= htmlspecialchars($time); ?></p>
        <p><strong>Seats:</strong> <?= implode(", ", $seat_list); ?></p>
        <p><strong>Total Amount:</strong> ₹<?= $total_amount; ?></p>
        <button class="btns" onclick="payNow()">Proceed to Pay</button>
    </div>

    <form id="paymentForm" method="POST" action="booking_confirmation.php" style="display:none;">
        <input type="hidden" name="payment_id" id="payment_id">
        <input type="hidden" name="amount" value="<?= $total_amount; ?>">
    </form>

    <script>
        function payNow() {
            var amount = <?= $total_amount * 100; ?>; // Convert to paisa

            var options = {
                "key": "rzp_test_529npOnJks99aK", // Use valid Razorpay Key ID
                "amount": amount,
                "currency": "INR",
                "name": "Movie Ticket Booking",
                "description": "Payment for <?= htmlspecialchars($movie_title); ?>, Seats: <?= implode(", ", $seat_list); ?>",
                "handler": function (response) {
                    let paymentId = response.razorpay_payment_id;
                    console.log("Payment Successful. Payment ID:", paymentId);

                    // Pass payment ID to the form and submit it
                    document.getElementById('payment_id').value = paymentId;
                    document.getElementById('paymentForm').submit();
                },
                "prefill": {
                    "name": "Samin",
                    "email": "saminkadivar2911@gmail.com",
                    "contact": "8200026182"
                },
                "theme": {
                    "color": "#001F3F"
                }
            };

            var rzp = new Razorpay(options);
            rzp.open();
        }
    </script>

</body>
</html>
<?php $conn->close(); ?>  
