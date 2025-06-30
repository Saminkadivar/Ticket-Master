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

        <div class="row">
            <!-- Tickets Report -->
            <div class="col-md-12">
                <div class="card p-3 mb-4">
                    <h3>Ticket Report</h3>
                    <button class="btn btn-dark print-btn" onclick="printReport('ticketReport')">🖨 Print</button>
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
                    <button class="btn btn-dark print-btn" onclick="printReport('moviesReport')">🖨 Print</button>
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
                    <button class="btn btn-dark print-btn" onclick="printReport('theatersReport')">🖨 Print</button>
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
