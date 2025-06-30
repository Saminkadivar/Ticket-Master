<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/Style1.css">
    <title>Admin Panel</title>
    <style>
      
     
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar">
        <h4>Admin Panel</h4>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="admin.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_movies.php"><i class="bi bi-film"></i> Manage Movies</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_users.php"><i class="bi bi-people"></i> Manage Users</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="theater.php"><i class="bi bi-building"></i> Manage Theater</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_screens.php"><i class="bi bi-easel"></i> Manage Screens</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_tickets.php"><i class="bi bi-ticket-perforated"></i> Manage Tickets</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="refund_return.php"><i class="bi bi-arrow-repeat"></i> Refund Return</a>
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
