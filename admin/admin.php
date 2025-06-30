    <?php
    include('connection.php');
    include('includes/sidebar.php');
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <style>
            .main-content { margin-left: 250px; padding: 20px; background-color: #f8f9fa; min-height: 100vh; }
            .dashboard-card { border-radius: 10px; background: color: white; padding: 20px; text-align: center; }
            .quick-link-btn { width: 100%; margin-top: 10px; }
        </style>
    </head>
    <body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2 sidebar">
                <?php include('includes/sidebar.php'); ?>
            </div>
            <div class="col-md-9 col-lg-10 main-content">
                <h2>Admin Dashboard</h2>
                <hr>
                <div class="row">
                    <?php
                    $stats = [
                        ["Movies", "SELECT COUNT(*) FROM movies", "fa-film"],
                        ["Theaters", "SELECT COUNT(*) FROM theaters", "fa-theater-masks"],
                        ["Users", "SELECT COUNT(*) FROM users", "fa-users"],
                        ["Bookings", "SELECT COUNT(*) FROM bookings", "fa-ticket-alt"]
                    ];
                    foreach ($stats as $stat) {
                        $count = $conn->query($stat[1])->fetch_row()[0];
                        echo "<div class='col-md-3'><div class='card dashboard-card'><div class='card-body'>";
                        echo "<i class='fa {$stat[2]} fa-2x'></i><h5>{$stat[0]}</h5><p>{$count}</p>";
                        echo "</div></div></div>";
                    }
                    ?>
                </div>
                <h3 class="mt-4">Quick Links</h3>
                <div class="row">
                    <div class="col-md-3"><a href="manage_movies.php" class="btn btn-primary quick-link-btn"><i class="fas fa-film"></i> Manage Movies</a></div>
                    <div class="col-md-3"><a href="theater.php" class="btn btn-primary quick-link-btn"><i class="fas fa-theater-masks"></i> Manage Theaters</a></div>
                    <div class="col-md-3"><a href="manage_users.php" class="btn btn-primary quick-link-btn"><i class="fas fa-users"></i> Manage Users</a></div>
                    <div class="col-md-3"><a href="manage_tickets.php" class="btn btn-primary quick-link-btn"><i class="fas fa-ticket-alt"></i> Manage Bookings</a></div>
                </div>
              <h3 class="mt-4">Booking Statistics</h3>
<div class="d-flex flex-wrap justify-content-between">
    <!-- Daily Booking Trends Chart -->
    <div style="flex: 1; min-width: 300px; max-width: 50%; padding: 10px;">
        <canvas id="bookingChart"></canvas>
                </div>
                <?php
                $data = $conn->query("SELECT DATE(booking_date) AS date, COUNT(*) AS count FROM bookings GROUP BY date ORDER BY date ASC");
                $labels = []; $values = [];
                while ($row = $data->fetch_assoc()) { $labels[] = "'{$row['date']}'"; $values[] = $row['count']; }
                ?>
                <script>
                    var ctx = document.getElementById('bookingChart').getContext('2d');
                    var bookingChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: [<?php echo implode(',', $labels); ?>],
                            datasets: [{ label: 'Daily Bookings', data: [<?php echo implode(',', $values); ?>], borderColor: 'blue', backgroundColor: 'rgba(0, 0, 255, 0.2)', fill: true }]
                        }
                    });
                </script>
                
    <div style="flex: 1; min-width: 300px; max-width: 50%; padding: 10px;">
    <h3 class="mt-4">Top 5 Theaters by Bookings</h3>
        <canvas id="topTheatersChart"></canvas>
    </div>
</div>

    <script>
        var ctx = document.getElementById('topTheatersChart').getContext('2d');
        var topTheatersChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [<?php
                    $theaters = $conn->query("
                        SELECT t.name, COUNT(b.id) AS count 
                        FROM bookings b 
                        JOIN theaters t ON b.theater_id = t.id 
                        GROUP BY t.name 
                        ORDER BY count DESC 
                        LIMIT 5
                    ");
                    while ($row = $theaters->fetch_assoc()) {
                        echo "'{$row['name']}',";
                    }
                ?>],
                datasets: [{
                    label: 'Total Bookings',
                    data: [<?php
                        $theaters->data_seek(0);
                        while ($row = $theaters->fetch_assoc()) {
                            echo "{$row['count']},";
                        }
                    ?>],
                    backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff'],
                    borderColor: ['#cc5169', '#2b81c5', '#cc9d44', '#3c9b9b', '#7d5fcf'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</div>
                <h3 class="mt-4">Recent Bookings</h3>
                <table class="table table-striped">
                    <thead><tr><th>User</th><th>Movie</th><th>Genre</th><th>Theater</th><th>Showdate</th><th>Showtime</th><th>Amount</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php
    $recent = $conn->query("
        SELECT b.user_id, b.show_date, b.show_time, b.status, 
            m.title, m.genre, b.amount, 
            t.name
        FROM bookings b
        JOIN movies m ON b.movie_id = m.id
        JOIN theaters t ON b.theater_id = t.id
        ORDER BY b.booking_date DESC
        LIMIT 5
    ");

    if ($recent) {
        while ($row = $recent->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['user_id']}</td>
                    <td>{$row['title']}</td>
                    <td>{$row['genre']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['show_date']}</td>
                    <td>{$row['show_time']}</td>
                    <td>{$row['amount']}</td>
                    <td>{$row['status']}</td>
                </tr>";
        }
    } else {
        echo "Error: " . $conn->error;
    }
    ?>

                    </tbody>
                </table>
            
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>
