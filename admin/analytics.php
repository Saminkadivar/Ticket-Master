<?php
include('connection.php'); // Database connection
include('includes/sidebar.php'); // Sidebar include
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reports</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js Library -->
    <style>
        .main-container {
            display: flex;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
        }
        .reports-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .report-card {
            flex: 1 1 45%;
            min-width: 350px;
            max-width: 600px;
            padding: 20px;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h2 {
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>

<div class="main-container">
    <!-- Sidebar already included at the top -->
    <div class="content">
        <h2>Admin Reports & Analytics</h2>

        <div class="reports-container">
            <div class="report-card">
                <h4>Booking Reports (Daily, Weekly, Monthly)</h4>
                <canvas id="bookingChart"></canvas>
            </div>

            <div class="report-card">
                <h4>Revenue Reports</h4>
                <canvas id="revenueChart"></canvas>
            </div>

            <div class="report-card">
                <h4>User Activity Reports</h4>
                <canvas id="userChart"></canvas>
            </div>

            <div class="report-card">
                <h4>Most Booked Movies</h4>
                <canvas id="moviesChart"></canvas>
            </div>

            <div class="report-card">
                <h4>Most Booked Theaters</h4>
                <canvas id="theatersChart"></canvas>
            </div>

            <div class="report-card">
                <h4>Popular Showtimes & Days</h4>
                <canvas id="showtimesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<?php
// Fetch Booking Data (Daily)
$bookingQuery = "SELECT DATE(booking_date) AS date, COUNT(*) AS total_bookings 
                 FROM bookings 
                 WHERE booking_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH) 
                 GROUP BY date";
$bookingResult = $conn->query($bookingQuery);
$booking_dates = [];
$booking_counts = [];
while ($row = $bookingResult->fetch_assoc()) {
    $booking_dates[] = $row['date'];
    $booking_counts[] = $row['total_bookings'];
}

// Fetch Revenue Data
$revenueQuery = "SELECT DATE(booking_date) AS date, SUM(amount) AS total_revenue 
                 FROM bookings 
                 WHERE booking_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH) 
                 GROUP BY date";
$revenueResult = $conn->query($revenueQuery);
$revenue_dates = [];
$revenue_amounts = [];
while ($row = $revenueResult->fetch_assoc()) {
    $revenue_dates[] = $row['date'];
    $revenue_amounts[] = $row['total_revenue'];
}

// Fetch User Activity Data
$userQuery = "SELECT DATE(created_at) AS date, COUNT(*) AS new_users 
              FROM users 
              WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH) 
              GROUP BY date";
$userResult = $conn->query($userQuery);
$user_dates = [];
$user_counts = [];
while ($row = $userResult->fetch_assoc()) {
    $user_dates[] = $row['date'];
    $user_counts[] = $row['new_users'];
}

// Fetch Most Booked Movies
$moviesQuery = "SELECT m.title, COUNT(b.id) AS total_bookings 
                FROM bookings b 
                JOIN movies m ON b.movie_id = m.id 
                GROUP BY b.movie_id 
                ORDER BY total_bookings DESC 
                LIMIT 5";
$moviesResult = $conn->query($moviesQuery);
$movie_names = [];
$movie_bookings = [];
while ($row = $moviesResult->fetch_assoc()) {
    $movie_names[] = $row['title'];
    $movie_bookings[] = $row['total_bookings'];
}

// Fetch Most Booked Theaters
$theatersQuery = "SELECT t.name AS theater_name, COUNT(b.id) AS total_bookings 
                  FROM bookings b 
                  JOIN theaters t ON b.theater_id = t.id 
                  GROUP BY b.theater_id 
                  ORDER BY total_bookings DESC 
                  LIMIT 5";
$theatersResult = $conn->query($theatersQuery);
$theater_names = [];
$theater_bookings = [];
while ($row = $theatersResult->fetch_assoc()) {
    $theater_names[] = $row['theater_name'];
    $theater_bookings[] = $row['total_bookings'];
}

// Fetch Popular Showtimes
$showtimesQuery = "SELECT TIME(show_time) AS show_slot, COUNT(id) AS total_bookings 
                   FROM bookings 
                   GROUP BY show_slot 
                   ORDER BY total_bookings DESC 
                   LIMIT 5";
$showtimesResult = $conn->query($showtimesQuery);
$showtime_slots = [];
$showtime_counts = [];
while ($row = $showtimesResult->fetch_assoc()) {
    $showtime_slots[] = $row['show_slot'];
    $showtime_counts[] = $row['total_bookings'];
}

$conn->close();
?>

<script>
// Debugging
console.log("Theater Names:", <?php echo json_encode($theater_names); ?>);
console.log("Theater Bookings:", <?php echo json_encode($theater_bookings); ?>);
console.log("Showtime Slots:", <?php echo json_encode($showtime_slots); ?>);
console.log("Showtime Counts:", <?php echo json_encode($showtime_counts); ?>);

// Booking Chart
new Chart(document.getElementById("bookingChart").getContext("2d"), {
    type: "line",
    data: {
        labels: <?php echo json_encode($booking_dates); ?>,
        datasets: [{
            label: "Total Bookings",
            data: <?php echo json_encode($booking_counts); ?>,
            borderColor: "blue",
            borderWidth: 2
        }]
    }
});

// Revenue Chart
new Chart(document.getElementById("revenueChart").getContext("2d"), {
    type: "bar",
    data: {
        labels: <?php echo json_encode($revenue_dates); ?>,
        datasets: [{
            label: "Total Revenue",
            data: <?php echo json_encode($revenue_amounts); ?>,
            backgroundColor: "green"
        }]
    }
});
// User Activity Chart
var ctx3 = document.getElementById("userChart").getContext("2d");
new Chart(ctx3, {
    type: "line",
    data: {
        labels: <?php echo json_encode($user_dates); ?>,
        datasets: [{
            label: "New Users",
            data: <?php echo json_encode($user_counts); ?>,
            backgroundColor: "rgba(75, 192, 192, 0.2)",
            borderColor: "rgba(75, 192, 192, 1)",
            borderWidth: 2
        }]
    }
});

// Most Booked Movies Chart
var ctx4 = document.getElementById("moviesChart").getContext("2d");
new Chart(ctx4, {
    type: "pie",
    data: {
        labels: <?php echo json_encode($movie_names); ?>,
        datasets: [{
            data: <?php echo json_encode($movie_bookings); ?>,
            backgroundColor: ["#ff6384", "#36a2eb", "#cc65fe", "#ffce56", "#2ecc71"]
        }]
    }
});

// Most Booked Theaters Chart
new Chart(document.getElementById("theatersChart").getContext("2d"), {
    type: "bar",
    data: {
        labels: <?php echo json_encode($theater_names); ?>,
        datasets: [{
            label: "Most Booked Theaters",
            data: <?php echo json_encode($theater_bookings); ?>,
            backgroundColor: "orange"
        }]
    }
});

// Popular Showtimes Chart
new Chart(document.getElementById("showtimesChart").getContext("2d"), {
    type: "pie",
    data: {
        labels: <?php echo json_encode($showtime_slots); ?>,
        datasets: [{
            data: <?php echo json_encode($showtime_counts); ?>,
            backgroundColor: ["red", "blue", "yellow", "green", "purple"]
        }]
    }
});
</script>

</body>
</html>
