<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Theater Panel</title>
    <style>
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #343a40;
            padding: 20px;
            position: fixed;
        }
        .sidebar h4 {
            color: #fff;
            text-align: center;
            margin-bottom: 20px;
        }
        .sidebar .nav-link {
            color: #adb5bd;
            padding: 10px;
            display: flex;
            align-items: center;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        .sidebar .nav-link:hover {
            background: #495057;
            border-radius: 5px;
            color: white;
        }
        .logout {
            color: #dc3545;
            padding: 10px;
            text-align: center;
            display: block;
            margin-top: 20px;
        }
        .logout:hover {
            background: #bd2130;
            color: white;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar">
        <h4>Theater Panel</h4>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="add_movie.php"><i class="bi bi-film"></i> Add Movies</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_movies.php"><i class="bi bi-pencil-square"></i> Manage Movies</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="showtime_list.php"><i class="bi bi-clock"></i> Movie Showtimes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_screens.php"><i class="bi bi-easel"></i> Manage Screens</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="seat_list.php"><i class="bi bi-chair"></i> Seat Configuration</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="booked_tickets.php"><i class="bi bi-ticket-perforated"></i> Bookings</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_users.php"><i class="bi bi-people"></i> Users</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="reports.php"><i class="bi bi-bar-chart"></i> Reports</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="analytics.php"><i class="bi bi-pie-chart"></i> Analytics</a>
            </li>
        </ul>

        <!-- Logout -->
        <a href="logout.php" class="logout"><i class="bi bi-box-arrow-right"></i> Log Out</a>
    </div>
</div>

</body>
</html>
