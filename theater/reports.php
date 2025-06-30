<?php
include 'includes/connection.php'; // Database connection file
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theater-Side Reports</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/Style1.css">

</head>
<body>
    <div class="main-container">
        <div class="sidebar">
            <?php include('includes/connection.php'); // Database connection
include('includes/sidebar.php'); // Sidebar for theaters

// Ensure session is started
session_start();

// Ensure a theater is logged in
if (!isset($_SESSION['theater_id'])) {
    header("Location: login.php");
    exit();
}
$theater_id = $_SESSION['theater_id'];

?>
        </div>
        <div class="content">
            <div class="report-container">
                <h2>Theater-Side Reports</h2>
                <button class="btn btn-primary" onclick="window.print()">Print Report</button>
                


<!-- Booking Report -->
<div class="report-box">
    <h3>Booking Report</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Username</th> <!-- Replacing theater name with User ID/Name -->
                <th>Movie</th>
                <th>Show Date</th>
                <th>Show Time</th>
                <th>Tickets Sold</th>
                <th>Revenue</th>
                <th>Booked Seats</th>
                <th>Booking Status</th> <!-- New column for status -->
            </tr>
        </thead>
        <tbody>
            <?php
            // Query to get the booking details for the logged-in theater
            $query = "SELECT b.Username, m.title AS movie_name, b.show_date, b.show_time, 
                             SUM(b.num_tickets) AS total_tickets, 
                             SUM(CASE WHEN b.status = 'booked' THEN b.amount ELSE 0 END) AS total_revenue,
                             GROUP_CONCAT(b.seat_numbers SEPARATOR ', ') AS booked_seats,
                             GROUP_CONCAT(b.status SEPARATOR ', ') AS booking_status
                      FROM bookings b 
                      JOIN movies m ON b.movie_id = m.id 
                      WHERE b.theater_id = '$theater_id'  -- Filter by the logged-in theater
                      GROUP BY b.theater, m.title, b.show_date, b.show_time";

            $result = mysqli_query($conn, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                // Replace 'theater' with 'user ID or user name' if necessary
                echo "<tr>
                        <td>{$row['Username']}</td> 
                        <td>{$row['movie_name']}</td>
                        <td>{$row['show_date']}</td>
                        <td>{$row['show_time']}</td>
                        <td>{$row['total_tickets']}</td>
                        <td>{$row['total_revenue']}</td>
                        <td>{$row['booked_seats']}</td>
                        <td>{$row['booking_status']}</td> <!-- Display status -->
                      </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Cancel Report -->
<div class="report-box">
    <h3>Cancel Report</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>User Name</th> <!-- Replacing theater name with User ID/Name -->
                <th>Movie</th>
                <th>Show Date</th>
                <th>Show Time</th>
                <th>Tickets Canceled</th>
                <th>Refunded Amount</th>
                <th>Canceled Seats</th>
                <th>Cancellation Status</th> <!-- Column for cancellation status -->
            </tr>
        </thead>
        <tbody>
            <?php
            // Query to get the canceled ticket details for the logged-in theater
            $query = "SELECT b.Username, m.title AS movie_name, b.show_date, b.show_time, 
                             SUM(b.num_tickets) AS canceled_tickets, 
                             SUM(CASE WHEN b.status = 'Cancelled' THEN b.amount ELSE 0 END) AS refunded_amount,
                             GROUP_CONCAT(b.seat_numbers SEPARATOR ', ') AS canceled_seats,
                             GROUP_CONCAT(b.status SEPARATOR ', ') AS cancellation_status
                      FROM bookings b 
                      JOIN movies m ON b.movie_id = m.id 
                      WHERE b.status = 'Cancelled' 
                      AND b.theater_id = '$theater_id'  -- Filter by the logged-in theater
                      GROUP BY b.theater, m.title, b.show_date, b.show_time";

            $result = mysqli_query($conn, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                // Replace 'Username' with 'user ID or user name' if necessary
                echo "<tr>
                        <td>{$row['Username']}</td> 
                        <td>{$row['movie_name']}</td>
                        <td>{$row['show_date']}</td>
                        <td>{$row['show_time']}</td>
                        <td>{$row['canceled_tickets']}</td>
                        <td>{$row['refunded_amount']}</td>
                        <td>{$row['canceled_seats']}</td>
                        <td>{$row['cancellation_status']}</td> <!-- Display Cancellation Status -->
                      </tr>";
            }
            ?>
        </tbody>
    </table>
</div>



                <!-- Daily Revenue Report -->
                <div class="report-box">
                    <h3>Daily Revenue Report</h3>
                    <table class="table table-bordered">
                        <thead>
                            <tr><th>Date</th><th>Total Revenue</th></tr>
                        </thead>
                        <tbody>
                            <?php
$query = "SELECT show_date, SUM(amount) AS total_revenue 
FROM bookings 
WHERE status = 'booked' 
GROUP BY show_date 
ORDER BY show_date DESC";
                  $result = mysqli_query($conn, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr><td>{$row['show_date']}</td><td>{$row['total_revenue']}</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

         <!-- Seat Occupancy Report -->
<div class="report-box">
    <h3>Seat Occupancy Report</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Theater</th>
                <th>Booked Seats</th>
                <th>Canceled Seats</th> <!-- New column for canceled seats -->
            </tr>
        </thead>
        <tbody>
            <?php
            // Get the theater_id from the session
            $theater_id = $_SESSION['theater_id'];

            // Update the query to filter by the logged-in theater
            $query = "SELECT theater, 
                             SUM(CASE WHEN status = 'booked' THEN num_tickets ELSE 0 END) AS booked_seats,
                             SUM(CASE WHEN status = 'Cancelled' THEN num_tickets ELSE 0 END) AS canceled_seats
                      FROM bookings 
                      WHERE status IN ('booked', 'Cancelled') 
                      AND theater_id = '$theater_id'  -- Filter by the logged-in theater
                      GROUP BY theater";

            $result = mysqli_query($conn, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>{$row['theater']}</td>
                        <td>{$row['booked_seats']}</td>
                        <td>{$row['canceled_seats']}</td> <!-- Display Cancelled seats -->
                      </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

            </div>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>
