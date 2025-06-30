<?php
include('connection.php');
include 'includes/sidebar.php';

// SQL query to fetch bookings
$sql = "SELECT b.id, b.User_id, b.Username, b.movie_id, b.num_tickets, b.booking_date, 
               b.payment_id, b.theater, b.screen, b.show_date, b.show_time, b.theater_id, 
               b.amount, b.seat_numbers, m.title
        FROM bookings b
        JOIN movies m ON b.movie_id = m.id
        ORDER BY b.booking_date DESC";

// Execute query
$result = $conn->query($sql);

// Check if the query was successful
if ($result === false) {
    echo "Error with query: " . $conn->error;
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style1.css">
</head>
<body>

<div class="main-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <?php include('includes/sidebar.php'); ?>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h2>Manage Bookings</h2>

        <div class="table-container mt-4">
            <?php
            if ($result->num_rows > 0) {
                echo "<table class='table table-bordered table-striped'>";
                echo "<thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Movie</th>
                            <th>Theater</th>
                            <th>Screen</th>
                            <th>Seats</th>
                            <th>Show Date</th>
                            <th>Show Time</th>
                            <th>Amount</th>
                            <th>Payment ID</th>
                        </tr>
                      </thead>";
                echo "<tbody>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row["id"]. "</td>
                            <td>" . $row["Username"]. "</td>
                            <td>" . $row["title"]. "</td>
                            <td>" . $row["theater"]. "</td>
                            <td>" . $row["screen"]. "</td>
                            <td>" . $row["seat_numbers"]. "</td>
                            <td>" . $row["show_date"]. "</td>
                            <td>" . $row["show_time"]. "</td>
                            <td>₹" . $row["amount"]. "</td>
                            <td>" . $row["payment_id"]. "</td>
                          </tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<div class='alert alert-info'>No bookings found.</div>";
            }
            ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
