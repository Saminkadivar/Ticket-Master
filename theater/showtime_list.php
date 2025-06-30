<?php
include 'includes/connection.php';
session_start();
// Ensure a theater is logged in
if (!isset($_SESSION['theater_id'])) {
    header("Location: login.php");
    exit();
}
$theater_id = $_SESSION['theater_id']; // Get theater ID from session

// Fetch showtimes for the logged-in theater
$query = "SELECT s.id, m.title AS movie, sc.screen_name, s.date, s.time 
          FROM showtimes s 
          JOIN movies m ON s.movie_id = m.id 
          JOIN screens sc ON s.screen_id = sc.id 
          WHERE s.theater_id = '$theater_id' 
          ORDER BY s.date, s.time";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Showtimes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/Style1.css">

    
    <!-- <style>
        /* Flexbox layout */
        .main-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 250px;
            background-color: #3aafa9;
            padding: 20px;
            color: white;
            position: fixed;
            height: 100%;
            overflow-y: auto;
        }

        /* Content Section */
        .content {
            flex: 1;
            margin-left: 270px; /* Ensures content does not overlap sidebar */
            padding: 40px;
            background-color: #f8f9fa;
            width: calc(100% - 270px);
        }

        /* Table Styling */
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table thead {
            background-color: #3aafa9;
            color: white;
        }

        .table th, .table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        /* Button Styling */
        .btn-primary { background-color: #3aafa9; border: none; }
        .btn-primary:hover { background-color: #2d8e8b; }
        .btn-warning:hover { background-color: #e0a800; }
        .btn-danger:hover { background-color: #c82333; }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }
            .content {
                margin-left: 210px;
                padding: 20px;
            }
        }

        @media (max-width: 576px) {
            .sidebar {
                width: 100%;
                position: relative;
            }
            .content {
                margin-left: 0;
            }
        }
    </style> -->
</head>
<body>

<div class="main-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <?php include 'includes/sidebar.php'; ?>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="table-container">
            <h2 class="mb-4 text-center">Your Showtimes</h2>

            <!-- Add Showtime Button -->
            <div class="mb-3 text-start">
                <a href="add_showtime.php" class="btn btn-primary">Add Showtime</a>
            </div>

            <!-- Showtimes Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Movie</th>
                            <th>Screen</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!$result) {
                            echo "<tr><td colspan='6' class='text-center text-danger'>Error: " . mysqli_error($conn) . "</td></tr>";
                        } elseif (mysqli_num_rows($result) > 0) {
                            $count = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                    <td>{$count}</td>
                                    <td>" . htmlspecialchars($row['movie']) . "</td>
                                    <td>" . htmlspecialchars($row['screen_name']) . "</td>
                                    <td>" . htmlspecialchars($row['date']) . "</td>
                                    <td>" . htmlspecialchars($row['time']) . "</td>
                                    <td>
                                        <a href='edit_showtime.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                                        <a href='delete_showtime.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                    </td>
                                </tr>";
                                $count++;
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center text-warning'>No showtimes found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
mysqli_close($conn);
?>
