    <?php
    include('includes/connection.php');
    include('includes/sidebar.php');
    session_start();
// Ensure a theater is logged in
if (!isset($_SESSION['theater_id'])) {
    header("Location: login.php");
    exit();
}

    $theater_id = $_SESSION['theater_id'];
    // Fetch Theater Details
    // $theater_query = $conn->query("SELECT * FROM theaters WHERE id = '$theater_id'");
    // $theater = $theater_query->fetch_assoc() ?? die("Theater details not found.");

    // // Fetch Canceled Ticket Count
    // $canceled_tickets_query = $conn->query("SELECT COUNT(*) AS canceled_count FROM bookings WHERE theater_id = '$theater_id' AND status = 'canceled'");
    // $canceled_tickets = $canceled_tickets_query->fetch_assoc()['canceled_count'] ?? 0;


    // Fetch Theater Details
    $theater_query = $conn->query("SELECT * FROM theaters WHERE id = '$theater_id'");
    $theater = $theater_query->fetch_assoc() ?? die("Theater details not found.");

    // Fetch Movies assigned to the Theater
    $movies_query = $conn->query("SELECT * FROM movies WHERE theater_id = '$theater_id'");

    // Fetch total bookings & revenue
    $total_bookings_query = $conn->query("SELECT COUNT(*) AS total FROM bookings WHERE theater_id = '$theater_id'");
    $total_bookings = $total_bookings_query->fetch_assoc()['total'] ?? 0;

    $total_revenue_query = $conn->query("SELECT SUM(amount) AS revenue FROM bookings WHERE theater_id = '$theater_id'");
    $total_revenue = $total_revenue_query->fetch_assoc()['revenue'] ?? 0;

    // Fetch Upcoming Showtimes
    $upcoming_showtimes_query = $conn->query("SELECT m.title, s.time FROM showtimes s JOIN movies m ON s.movie_id = m.id WHERE m.theater_id = '$theater_id' AND s.time > NOW() ORDER BY s.time ASC");

    // Fetch Bookings
    $bookings_query = $conn->query("SELECT users.username, users.email, movies.title AS movie_title, bookings.show_time AS showtime, bookings.booking_date, bookings.amount, bookings.status 
    FROM bookings 
    JOIN users ON bookings.user_id = users.user_id 
    JOIN movies ON bookings.movie_id = movies.id 
    WHERE bookings.theater_id = '$theater_id'");

// Fetch Canceled Booking Count
    $canceled_tickets_query = $conn->query("SELECT COUNT(*) AS cancelled FROM bookings WHERE theater_id = '$theater_id' AND status = 'cancelled'");
    $canceled_tickets = $canceled_tickets_query->fetch_assoc()['cancelled'] ?? 0;
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Theater Dashboard</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/Style1.css">

        <style>
            body { background-color: #f4f4f4; }
    .dashboard { margin-left: 270px; padding: 20px; }
            .card { padding: 20px; background: white; border-radius: 8px; box-shadow: 2px 2px 10px rgba(0,0,0,0.1); }
        </style>
    </head>
    <body>

        
        <div class="dashboard">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <h3>Welcome, <?php echo $theater['name']; ?> Theater</h3>
                        <p><strong>Location:</strong> <?php echo $theater['location']; ?></p>
                        <p><strong>Status:</strong> <?php echo $theater['status']; ?></p>
                        <a href="update_profile.php" class="btn btn-primary btn-sm">Update Profile</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <h3>Total Bookings</h3>
                        <h2><?php echo $total_bookings; ?></h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <h3>Total Earnings</h3>
                        <h2>₹<?php echo number_format($total_revenue, 2); ?></h2>
                    </div>
                </div>
                <!-- Canceled Ticket Count -->
                <div class="col-md-6">
                    <div class="card text-center">
                        <h3>Canceled Tickets</h3>
                        <h2><?php echo $canceled_tickets; ?></h2>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <h3>Movies Currently Showing</h3>
                <ul class="list-group">
                    <?php while ($movie = $movies_query->fetch_assoc()) { ?>
                        <li class="list-group-item">
                            <h5><?php echo $movie['title']; ?></h5>
                            <p><strong>Genre:</strong> <?php echo $movie['genre']; ?> | <strong>Duration:</strong> <?php echo $movie['duration']; ?> mins</p>
                            <ul>
                                <?php 
                                $showtimes = $conn->query("SELECT time FROM showtimes WHERE movie_id = " . $movie['id']);
                                if ($showtimes->num_rows > 0) {
                                    while ($showtime = $showtimes->fetch_assoc()) {
                                        echo "<li><strong>Showtime:</strong> " . $showtime['time'] . "</li>";
                                    }
                                } else {
                                    echo "<li>No showtimes available.</li>";
                                }
                                ?>
                            </ul>
                        </li>
                    <?php } ?>
                </ul>
            </div>

            <div class="card mt-3">
                <h3>Upcoming Showtimes</h3>
                <ul class="list-group">
                    <?php while ($showtime = $upcoming_showtimes_query->fetch_assoc()) { ?>
                        <li class="list-group-item">
                            <strong><?php echo $showtime['title']; ?></strong> - <?php echo $showtime['time']; ?>
                        </li>
                    <?php } ?>
                </ul>
            </div>

            <div class="card mt-3">
        <h3>Recent Bookings</h3>

        <?php
        if (!$bookings_query) {
            die("<p style='color:red;'>Query Failed: " . $conn->error . "</p>");
        }

        if ($bookings_query->num_rows > 0) {
        ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Movie</th>
                        <th>Showtime</th>
                        <th>Booking Date</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($booking = $bookings_query->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $booking['username']; ?></td>
                            <td><?php echo $booking['email']; ?></td>
                            <td><?php echo $booking['movie_title']; ?></td> <!-- Fixed column name -->
                            <td><?php echo $booking['showtime']; ?></td>
                            <td><?php echo $booking['booking_date']; ?></td>
                            <td>₹<?php echo number_format($booking['amount'], 2); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php
        } else {
            echo "<p>No recent bookings found.</p>";
        }
        ?>
    </div>

        </div>
    </body>
    </html>
