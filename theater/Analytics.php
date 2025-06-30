<?php
include('includes/connection.php'); // Database connection
include('includes/sidebar.php'); // Sidebar for theaters

// Ensure session is started
session_start();

// Ensure a theater is logged in
if (!isset($_SESSION['theater_id'])) {
    header("Location: login.php");
    exit();
}
$theater_id = $_SESSION['theater_id'];

// Fetch Booking Data for Last 30 Days
$bookingQuery = "SELECT DATE(booking_date) AS date, COUNT(*) AS total_bookings 
                 FROM bookings 
                 WHERE theater_id = ? AND booking_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH) 
                 GROUP BY date ORDER BY date ASC";
$stmt = $conn->prepare($bookingQuery);
$stmt->bind_param("i", $theater_id);
$stmt->execute();
$result = $stmt->get_result();
$booking_dates = [];
$booking_counts = [];
while ($row = $result->fetch_assoc()) {
    $booking_dates[] = $row['date'];
    $booking_counts[] = $row['total_bookings'];
}
$stmt->close();

// Fetch Revenue Data for Last 30 Days
$revenueQuery = "SELECT DATE(booking_date) AS date, SUM(amount) AS total_revenue 
                 FROM bookings 
                 WHERE theater_id = ? AND booking_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH) 
                 GROUP BY date ORDER BY date ASC";
$stmt = $conn->prepare($revenueQuery);
$stmt->bind_param("i", $theater_id);
$stmt->execute();
$result = $stmt->get_result();
$revenue_dates = [];
$revenue_amounts = [];
while ($row = $result->fetch_assoc()) {
    $revenue_dates[] = $row['date'];
    $revenue_amounts[] = $row['total_revenue'] ?? 0;
}
$stmt->close();

// Fetch Most Booked Movies
$moviesQuery = "SELECT m.title, COUNT(b.id) AS total_bookings 
                FROM bookings b 
                JOIN movies m ON b.movie_id = m.id 
                WHERE b.theater_id = ? 
                GROUP BY b.movie_id 
                ORDER BY total_bookings DESC 
                LIMIT 5";
$stmt = $conn->prepare($moviesQuery);
$stmt->bind_param("i", $theater_id);
$stmt->execute();
$result = $stmt->get_result();
$movie_names = [];
$movie_bookings = [];
while ($row = $result->fetch_assoc()) {
    $movie_names[] = $row['title'];
    $movie_bookings[] = $row['total_bookings'];
}
$stmt->close();

// Fetch Hourly Booking Trends (Fixed)
$hourlyQuery = "SELECT HOUR(STR_TO_DATE(booking_date, '%Y-%m-%d %H:%i:%s')) AS hour, COUNT(*) AS total_bookings 
                FROM bookings 
                WHERE theater_id = ? AND booking_date >= DATE_SUB(NOW(), INTERVAL 1 MONTH) 
                GROUP BY hour ORDER BY hour ASC";
$stmt = $conn->prepare($hourlyQuery);
$stmt->bind_param("i", $theater_id);
$stmt->execute();
$result = $stmt->get_result();
$booking_hours = [];
$booking_by_hour = [];
while ($row = $result->fetch_assoc()) {
    $booking_hours[] = $row['hour'] . ":00";
    $booking_by_hour[] = $row['total_bookings'];
}
$stmt->close();

// Fetch Seat Category Distribution (Fixed)
$seatQuery = "SELECT s.category, COUNT(*) AS total_seats 
              FROM bookings b
              JOIN seats s ON FIND_IN_SET(s.seat_number, b.seat_numbers) 
              WHERE b.theater_id = ? 
              GROUP BY s.category";
$stmt = $conn->prepare($seatQuery);
$stmt->bind_param("i", $theater_id);
$stmt->execute();
$result = $stmt->get_result();
$seat_categories = [];
$seat_counts = [];
while ($row = $result->fetch_assoc()) {
    $seat_categories[] = $row['category'];
    $seat_counts[] = $row['total_seats'];
}
$stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theater Analytics Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
     body {
    background-color: #f4f4f4;
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
}

.main-container {
    display: flex;
    width: 100%;
    min-height: 100vh;
}

.content {
    flex-grow: 1;
    margin-left: 250px; /* Same as sidebar width */
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
        <!-- Sidebar -->
        <div class="sidebar">
            <?php include('includes/sidebar.php'); ?>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Theater Analytics Dashboard</h2>
            <div class="reports-container">
                <div class="report-card">
                    <h4>Booking Trend (Last 30 Days)</h4>
                    <canvas id="bookingChart"></canvas>
                </div>
                <div class="report-card">
                    <h4>Revenue Trend (Last 30 Days)</h4>
                    <canvas id="revenueChart"></canvas>
                </div>
                <div class="report-card">
                    <h4>Top 5 Most Booked Movies</h4>
                    <canvas id="moviesChart"></canvas>
                </div>
                <div class="report-card">
                    <h4>Hourly Booking Pattern</h4>
                    <canvas id="hourlyChart"></canvas>
                </div>
                <div class="report-card">
                    <h4>Seat Category Distribution</h4>
                    <canvas id="seatChart"></canvas>
                </div>
            </div>
        </div>
    </div>


<script>
const charts = {
    booking: { labels: <?php echo json_encode($booking_dates); ?>, data: <?php echo json_encode($booking_counts); ?>, type: "line", color: "blue" },
    revenue: { labels: <?php echo json_encode($revenue_dates); ?>, data: <?php echo json_encode($revenue_amounts); ?>, type: "bar", color: "green" },
    movies: { labels: <?php echo json_encode($movie_names); ?>, data: <?php echo json_encode($movie_bookings); ?>, type: "pie", colors: ["red", "blue", "yellow", "green", "purple"] },
    hourly: { labels: <?php echo json_encode($booking_hours); ?>, data: <?php echo json_encode($booking_by_hour); ?>, type: "bar", color: "orange" },
    seat: { labels: <?php echo json_encode($seat_categories); ?>, data: <?php echo json_encode($seat_counts); ?>, type: "doughnut", colors: ["pink", "cyan", "purple", "yellow"] }
};

Object.keys(charts).forEach(chart => {
    new Chart(document.getElementById(`${chart}Chart`), {
        type: charts[chart].type,
        data: { labels: charts[chart].labels, datasets: [{ label: chart, data: charts[chart].data, backgroundColor: charts[chart].colors || charts[chart].color }] }
    });
});
</script>

</body>
</html>

