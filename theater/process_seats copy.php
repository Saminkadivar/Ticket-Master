<?php
include 'includes/connection.php';
session_start();

if (!isset($_SESSION['theater_id'])) {
    die("Access Denied.");
}

$theater_id = $_SESSION['theater_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $screen_id = $_POST['screen_id'];
    $num_rows = (int)$_POST['num_rows'];
    $seats_per_row = (int)$_POST['seats_per_row'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $showtimes = $_POST['showtimes'];

    // Get seat prices from form input
    $premium_price = (float)$_POST['premium_price'];
    $gold_price = (float)$_POST['gold_price'];
    $silver_price = (float)$_POST['silver_price'];

    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $interval = new DateInterval('P1D');
    $date_range = new DatePeriod($start, $interval, $end->modify('+1 day'));

    // Define seat category distribution based on row numbers
    $silver_rows = ceil($num_rows * 0.3);  // 30% rows as Silver
    $gold_rows = ceil($num_rows * 0.4);    // 40% rows as Gold
    $premium_rows = $num_rows - ($silver_rows + $gold_rows); // Remaining 30% rows as Premium

    foreach ($date_range as $date) {
        $formatted_date = $date->format('Y-m-d');

        foreach ($showtimes as $showtime) {
            // Check if showtime exists, if not, create a new one
            $stmt = $conn->prepare("SELECT id FROM showtimes WHERE theater_id = ? AND screen_id = ? AND date = ? AND time = ?");
            $stmt->bind_param("iiss", $theater_id, $screen_id, $formatted_date, $showtime);
            $stmt->execute();
            $result = $stmt->get_result();
            $showtime_id = null;

            if ($row = $result->fetch_assoc()) {
                $showtime_id = $row['id'];
            } else {
                $stmt = $conn->prepare("INSERT INTO showtimes (theater_id, screen_id, date, time) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iiss", $theater_id, $screen_id, $formatted_date, $showtime);
                $stmt->execute();
                $showtime_id = $stmt->insert_id;
                $stmt->close();
            }

            // Insert seats with correct categories
            for ($row = 0; $row < $num_rows; $row++) {
                $row_label = chr(65 + $row); // Convert number to A-Z

                // Assign seat category based on row position
                if ($row < $silver_rows) {
                    $seat_category = 'Silver';
                    $seat_price = $silver_price;
                } elseif ($row < ($silver_rows + $gold_rows)) {
                    $seat_category = 'Gold';
                    $seat_price = $gold_price;
                } else {
                    $seat_category = 'Premium';
                    $seat_price = $premium_price;
                }

                for ($seat = 1; $seat <= $seats_per_row; $seat++) {
                    $seat_number = $row_label . str_pad($seat, 2, '0', STR_PAD_LEFT);

                    $stmt = $conn->prepare("INSERT INTO seats (theater_id, screen_id, showtime_id, row_label, seat_number, category, price, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'available')");
                    $stmt->bind_param("iiisssd", $theater_id, $screen_id, $showtime_id, $row_label, $seat_number, $seat_category, $seat_price);
                    $stmt->execute();
                }
            }
        }
    }

    // Redirect with success message
    header("Location: seat_list.php?success=Seats added successfully.");
    exit();
}
?>
