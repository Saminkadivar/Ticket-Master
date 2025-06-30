<?php
// Include database connection
include('connection.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reports</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style1.css">

    <style>
        /* Flexbox Layout */
        .main-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background: #343a40;
            padding: 20px;
            color: white;
            min-height: 100vh;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .table-container {
            overflow-x: auto;
        }
        .table th {
            background-color: #000;
            color: white;
            text-align: center;
        }
        .table td {
            vertical-align: middle;
            text-align: center;
        }
        .print-btn {
            float: right;
            margin-bottom: 10px;
        }
    </style>

    <script>
        function printReport(reportId) {
            var content = document.getElementById(reportId).innerHTML;
            var printWindow = window.open('', '', 'height=800,width=1000');
            printWindow.document.write('<html><head><title>Report</title></head><body>');
            printWindow.document.write(content);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }

        
    </script>
</head>
<body>

<div class="main-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <?php include('includes/sidebar.php'); ?>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h2 class="mb-4">Admin Reports</h2>
   <!-- User Registration Report -->
   <div class="card p-3 mb-4">
        <h3>User Registration Report</h3>
        <div class="mb-3 text-end">
        <button class="btn btn-dark" onclick="printReport('userReport')">🖨 Print</button>
        <a href="export_csv.php?report=users" class="btn btn-success">📥 Download CSV</a>
    </div>
        <div id="userReport" class="table-responsive">
            <?php
            $users_sql = "SELECT User_id, username, email, created_at FROM users ORDER BY created_at DESC";
            $users_result = $conn->query($users_sql);
            if ($users_result->num_rows > 0) {
                echo "<table class='table table-striped'><thead><tr><th>User_id</th><th>Username</th><th>Email</th><th>Registered On</th></tr></thead><tbody>";
                while ($row = $users_result->fetch_assoc()) {
                    echo "<tr><td>{$row['User_id']}</td><td>{$row['username']}</td><td>{$row['email']}</td><td>{$row['created_at']}</td></tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No registered users found.</p>";
            }
            ?>
        </div>
    </div>

    <!-- Revenue Report -->
    <div class="card p-3 mb-4">
        <h3>Revenue Report</h3>
        <div class="mb-3 text-end">

        <button class="btn btn-dark" onclick="printReport('revenueReport')">🖨 Print</button>
        <a href="export_csv.php?report=revenue" class="btn btn-success">📥 Download CSV</a>
        </div>
        <div id="revenueReport" class="table-responsive">
            <?php
           $revenue_sql = "SELECT m.title, SUM(b.amount) as total_revenue 
           FROM bookings b 
           JOIN movies m ON b.movie_id = m.id 
           WHERE b.status != 'Cancelled' 
           GROUP BY m.title 
           ORDER BY total_revenue DESC";
$revenue_result = $conn->query($revenue_sql);
            if ($revenue_result->num_rows > 0) {
                echo "<table class='table table-striped'><thead><tr><th>Movie</th><th>Total Revenue (₹)</th></tr></thead><tbody>";
                while ($row = $revenue_result->fetch_assoc()) {
                    echo "<tr><td>{$row['title']}</td><td>₹{$row['total_revenue']}</td></tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No revenue data available.</p>";
            }
            ?>
        </div>
    </div>

    <!-- Most Booked Movies Report -->
    <div class="card p-3 mb-4">
        <h3>Most Booked Movies</h3>
        <div class="mb-3 text-end">
        <button class="btn btn-dark" onclick="printReport('topMoviesReport')">🖨 Print</button>
        <a href="export_csv.php?report=top_movies" class="btn btn-success">📥 Download CSV</a>
        </div>
        <div id="topMoviesReport" class="table-responsive">
            <?php
            $top_movies_sql = "SELECT m.title, COUNT(b.id) as total_bookings FROM bookings b JOIN movies m ON b.movie_id = m.id GROUP BY m.title ORDER BY total_bookings DESC LIMIT 5";
            $top_movies_result = $conn->query($top_movies_sql);
            if ($top_movies_result->num_rows > 0) {
                echo "<table class='table table-striped'><thead><tr><th>Movie</th><th>Total Bookings</th></tr></thead><tbody>";
                while ($row = $top_movies_result->fetch_assoc()) {
                    echo "<tr><td>{$row['title']}</td><td>{$row['total_bookings']}</td></tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No bookings data found.</p>";
            }
            ?>
        </div>
    </div>

    <!-- Daily Booking Summary -->
    <div class="card p-3 mb-4">
        <h3>Daily Booking Summary</h3>
        <div class="mb-3 text-end">
        <button class="btn btn-dark" onclick="printReport('dailyBookingsReport')">🖨 Print</button>
        <a href="export_csv.php?report=booking" class="btn btn-success">📥 Download CSV</a>
        </div>
        <div id="dailyBookingsReport" class="table-responsive">
            <?php
            $daily_bookings_sql = "SELECT show_date, COUNT(id) as total_bookings FROM bookings GROUP BY show_date ORDER BY show_date DESC";
            $daily_bookings_result = $conn->query($daily_bookings_sql);
            if ($daily_bookings_result->num_rows > 0) {
                echo "<table class='table table-striped'><thead><tr><th>Date</th><th>Total Bookings</th></tr></thead><tbody>";
                while ($row = $daily_bookings_result->fetch_assoc()) {
                    echo "<tr><td>{$row['show_date']}</td><td>{$row['total_bookings']}</td></tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No daily booking data available.</p>";
            }
            ?>
        </div>
    </div>

    <!-- Cancelled Bookings Report -->
    <div class="card p-3 mb-4">
        <h3>Cancelled Bookings</h3>       
         <div class="mb-3 text-end">

        <button class="btn btn-dark" onclick="printReport('cancelledReport')">🖨 Print</button>
        <a href="export_csv.php?report=Cancelled" class="btn btn-success">📥 Download CSV</a>

        </div>
        <div id="cancelledReport" class="table-responsive">
            <?php
            $cancelled_sql = "SELECT b.id, b.Username, m.title, b.show_date, b.show_time, b.seat_numbers FROM bookings b JOIN movies m ON b.movie_id = m.id WHERE b.status = 'Cancelled' ORDER BY b.show_date DESC";
            $cancelled_result = $conn->query($cancelled_sql);
            if ($cancelled_result->num_rows > 0) {
                echo "<table class='table table-striped'><thead><tr><th>ID</th><th>User</th><th>Movie</th><th>Date</th><th>Time</th><th>Seats</th></tr></thead><tbody>";
                while ($row = $cancelled_result->fetch_assoc()) {
                    echo "<tr><td>{$row['id']}</td><td>{$row['Username']}</td><td>{$row['title']}</td><td>{$row['show_date']}</td><td>{$row['show_time']}</td><td>{$row['seat_numbers']}</td></tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No canceled bookings.</p>";
            }
            ?>
        </div>
    </div>
        <div class="row">
            <!-- Tickets Report -->
            <div class="col-md-12">
                <div class="card p-3 mb-4">
                    <h3>Ticket Report</h3>
                    <div class="mb-3 text-end">
                    <a href="export_csv.php?report=Tickets" class="btn btn-success">📥 Download CSV</a>

                    <button class="btn btn-dark print-btn" onclick="printReport('ticketReport')">🖨 Print</button>
                    
                    </div>
                    <div id="ticketReport" class="table-container">
                        <?php
                        $sql = "SELECT b.id, b.Username, m.title, b.theater, b.screen, b.seat_numbers, 
                                       b.show_date, b.show_time, b.amount, b.payment_id
                                FROM bookings b
                                JOIN movies m ON b.movie_id = m.id
                                ORDER BY b.booking_date DESC";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            echo "<table class='table table-bordered table-striped'>";
                            echo "<thead><tr>
                                    <th>ID</th><th>User</th><th>Movie</th><th>Theater</th><th>Screen</th>
                                    <th>Seats</th><th>Show Date</th><th>Show Time</th><th>Amount</th><th>Payment ID</th>
                                  </tr></thead>";
                            echo "<tbody>";
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['Username']}</td>
                                        <td>{$row['title']}</td>
                                        <td>{$row['theater']}</td>
                                        <td>{$row['screen']}</td>
                                        <td>{$row['seat_numbers']}</td>
                                        <td>{$row['show_date']}</td>
                                        <td>{$row['show_time']}</td>
                                        <td>₹{$row['amount']}</td>
                                        <td>{$row['payment_id']}</td>
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

            <!-- Movies Report -->
            <div class="col-md-12">
                <div class="card p-3 mb-4">
                    <h3>Movies Report</h3>
                    <div class="mb-3 text-end">
                    <button class="btn btn-dark print-btn" onclick="printReport('moviesReport')">🖨 Print</button>
                    <a href="export_csv.php?report=Movies" class="btn btn-success">📥 Download CSV</a>
                    </div>
                    <div id="moviesReport" class="table-container">
                        <?php
                        $movies_sql = "SELECT id, title, genre, release_date, duration, description, status FROM movies ORDER BY release_date DESC";
                        $movies_result = $conn->query($movies_sql);

                        if ($movies_result->num_rows > 0) {
                            echo "<table class='table table-striped'>";
                            echo "<thead><tr>
                                    <th>ID</th><th>Title</th><th>Genre</th><th>Release Date</th>
                                    <th>Duration</th><th>Description</th><th>Status</th>
                                  </tr></thead>";
                            echo "<tbody>";
                            while ($row = $movies_result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['title']}</td>
                                        <td>{$row['genre']}</td>
                                        <td>{$row['release_date']}</td>
                                        <td>{$row['duration']}</td>
                                        <td>{$row['description']}</td>
                                        <td>{$row['status']}</td>
                                      </tr>";
                            }
                            echo "</tbody></table>";
                        } else {
                            echo "<div class='alert alert-warning'>No movies found.</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Theaters Report -->
            <div class="col-md-12">
                <div class="card p-3 mb-4">
                    <h3>Theaters Report</h3>
                    <div class="mb-3 text-end">
                    <button class="btn btn-dark print-btn" onclick="printReport('theatersReport')">🖨 Print</button>
                    <a href="export_csv.php?report=Theaters" class="btn btn-success">📥 Download CSV</a>
                    </div>  
                    <div id="theatersReport" class="table-container">
                        <?php
                        $theaters_sql = "SELECT id, name, location, status FROM theaters ORDER BY name ASC";
                        $theaters_result = $conn->query($theaters_sql);

                        if ($theaters_result->num_rows > 0) {
                            echo "<table class='table table-striped'>";
                            echo "<thead><tr>
                                    <th>ID</th><th>Name</th><th>Location</th><th>Status</th>
                                  </tr></thead>";
                            echo "<tbody>";
                            while ($row = $theaters_result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['name']}</td>
                                        <td>{$row['location']}</td>
                                        <td>{$row['status']}</td>
                                      </tr>";
                            }
                            echo "</tbody></table>";
                        } else {
                            echo "<div class='alert alert-warning'>No theaters found.</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
