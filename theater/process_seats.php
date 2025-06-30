<?php
include 'includes/connection.php';
session_start();
// Ensure a theater is logged in
if (!isset($_SESSION['theater_id'])) {
    header("Location: login.php");
    exit();
}
$theater_id = $_SESSION['theater_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $screen_id = $_POST['screen_id'];
    $num_rows = (int)$_POST['num_rows'];
    $seats_per_row = (int)$_POST['seats_per_row'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $showtimes = $_POST['showtimes'];

    // Seat pricing
    $premium_price = (float)$_POST['premium_price'];
    $gold_price = (float)$_POST['gold_price'];
    $silver_price = (float)$_POST['silver_price'];

    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $interval = new DateInterval('P1D');
    $date_range = new DatePeriod($start, $interval, $end->modify('+1 day'));

    // Seat category distribution
    $silver_rows = ceil($num_rows * 0.3);
    $gold_rows = ceil($num_rows * 0.4);
    $premium_rows = $num_rows - ($silver_rows + $gold_rows);

    // Increase script execution time if needed (use with caution)
    set_time_limit(300); // Set max execution time to 5 minutes

    // Start a transaction
    $conn->begin_transaction();

    try {
        foreach ($date_range as $date) {
            $formatted_date = $date->format('Y-m-d');

            foreach ($showtimes as $showtime) {
                // Fetch existing showtime ID or insert new one
                $stmt = $conn->prepare("SELECT id FROM showtimes WHERE theater_id = ? AND screen_id = ? AND date = ? AND time = ?");
                $stmt->bind_param("iiss", $theater_id, $screen_id, $formatted_date, $showtime);
                $stmt->execute();
                $result = $stmt->get_result();
                $showtime_id = ($row = $result->fetch_assoc()) ? $row['id'] : null;
                $stmt->close();

                if (!$showtime_id) {
                    $stmt = $conn->prepare("INSERT INTO showtimes (theater_id, screen_id, date, time) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("iiss", $theater_id, $screen_id, $formatted_date, $showtime);
                    $stmt->execute();
                    $showtime_id = $stmt->insert_id;
                    $stmt->close();
                }

                // **Batch Insert Seats Instead of One-by-One**
                $seat_values = [];
                for ($row = 0; $row < $num_rows; $row++) {
                    $row_label = chr(65 + $row);

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
                        $seat_values[] = "($theater_id, $screen_id, $showtime_id, '$formatted_date', '$showtime', '$row_label', '$seat_number', '$seat_category', $seat_price, 'available')";
                    }
                }

                // Execute batch insert if there are seats to add
                if (!empty($seat_values)) {
                    $query = "INSERT INTO seats (theater_id, screen_id, showtime_id, show_date, show_time, row_label, seat_number, category, price, status) VALUES " . implode(',', $seat_values);
                    $conn->query($query);
                }
            }
        }

        // Commit transaction
        $conn->commit();
        header("Location: seat_list.php?success=Seats added successfully.");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        die("Error: " . $e->getMessage());
    }
}

mysqli_close($conn);
?>
