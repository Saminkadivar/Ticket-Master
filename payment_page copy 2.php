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

    <script>
   function payNow() {
       var amount = <?= $total_amount * 100; ?>; // Convert to paisa

       // Step 1: Check seat availability before payment
       fetch('check_seat_availability.php?seats=<?= implode(",", $seat_ids); ?>')
       .then(response => response.json())
       .then(data => {
           if (data.status === "unavailable") {
               alert("Some seats have already been booked! Please select different seats.");
               window.location.href = "seat_selection.php"; // Redirect back to seat selection
           } else {
               // Step 2: Proceed with Razorpay Payment
               var options = {
                   "key": "rzp_test_529npOnJks99aK", // Replace with your Razorpay Key ID
                   "amount": amount,
                   "currency": "INR",
                   "name": "Movie Ticket Booking",
                   "description": "Payment for <?= htmlspecialchars($movie_title); ?>, Seats: <?= implode(", ", $seat_list); ?>",
                   "handler": function (response) {
                       let paymentId = response.razorpay_payment_id;

                       // Redirect to confirmation page after successful payment
                       let url = "booking_confirmation.php?payment_id=" + encodeURIComponent(paymentId) +
                           "&movie=<?= urlencode($movie_title); ?>" +
                           "&theater=<?= urlencode($theater_name); ?>" +
                           "&screen=<?= urlencode($screen_name); ?>" +
                           "&time=<?= urlencode($time); ?>" +
                           "&date=<?= urlencode($date); ?>" +
                           "&seats=<?= urlencode(implode(", ", $seat_list)); ?>" +
                           "&amount=" + (amount / 100);

                       window.location.href = url;
                   },
                   "prefill": {
                       "name": "Samin",
                       "email": "saminkadivar2911@gmail.com",
                       "contact": "8200026182"
                   },
                   "theme": {
                       "color": "#3aafa9"
                   }
               };

               var rzp = new Razorpay(options);
               rzp.open();
           }
       })
       .catch(error => console.error('Error checking seat availability:', error));
   }
</script>

</body>
</html>
<?php $conn->close(); ?>  
