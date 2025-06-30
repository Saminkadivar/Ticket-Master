<?php
include 'includes/connection.php';
include 'includes/sidebar.php';
session_start();

// Ensure a theater is logged in
if (!isset($_SESSION['theater_id'])) {
    header("Location: login.php");
    exit();
}
$theater_id = $_SESSION['theater_id'];

// Fetch booked tickets for this theater
$query = "SELECT id, Username, num_tickets, booking_date, payment_id, theater, screen, show_date, show_time, amount, seat_numbers 
          FROM bookings WHERE theater_id = ? ORDER BY booking_date DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $theater_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booked Tickets</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/admin-style.css">
    <link rel="stylesheet" href="css/style1.css">
    <style>
        .main-container {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

       
        .content {
            flex: 1;
            margin-left: 260px;
            padding: 20px;
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

       
    </style>
</head>
<body>
    <div class="main-container">
        <div class="sidebar">
            <?php include 'includes/sidebar.php'; ?>
        </div>

        <div class="content">
            <h2>Booked Tickets</h2>
            <button class="btn btn-primary" onclick="window.print()">Print Report</button>
            <div class="table-container">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Username</th>
                            <th>Tickets</th>
                            <th>Booking Date</th>
                            <th>Payment ID</th>
                            <th>Theater</th>
                            <th>Screen</th>
                            <th>Show Date</th>
                            <th>Show Time</th>
                            <th>Amount</th>
                            <th>Seats</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']); ?></td>
                                <td><?= htmlspecialchars($row['Username']); ?></td>
                                <td><?= htmlspecialchars($row['num_tickets']); ?></td>
                                <td><?= htmlspecialchars($row['booking_date']); ?></td>
                                <td><?= htmlspecialchars($row['payment_id']); ?></td>
                                <td><?= htmlspecialchars($row['theater']); ?></td>
                                <td><?= htmlspecialchars($row['screen']); ?></td>
                                <td><?= htmlspecialchars($row['show_date']); ?></td>
                                <td><?= htmlspecialchars($row['show_time']); ?></td>
                                <td><?= htmlspecialchars($row['amount']); ?></td>
                                <td><?= htmlspecialchars($row['seat_numbers']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php mysqli_close($conn); ?>
