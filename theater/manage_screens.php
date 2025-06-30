<?php
session_start();
include('includes/connection.php');
// Ensure a theater is logged in
if (!isset($_SESSION['theater_id'])) {
    header("Location: login.php");
    exit();
}

$theater_id = $_SESSION['theater_id']; // Get theater ID from session

// Fetch screens for the logged-in theater
$query = "SELECT * FROM screens WHERE theater_id = '$theater_id' ORDER BY id";
$result = mysqli_query($conn, $query);

// Handle screen deletion
if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == 'delete') {
    $screen_id = intval($_GET['id']);
    
    // Check if the screen belongs to the logged-in theater
    $check_query = "SELECT * FROM screens WHERE id = $screen_id AND theater_id = $theater_id";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $delete_query = "DELETE FROM screens WHERE id = $screen_id";
        if (mysqli_query($conn, $delete_query)) {
            echo "<script>alert('Screen deleted successfully!'); window.location.href='manage_screens.php';</script>";
        } else {
            echo "<script>alert('Error deleting screen: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Unauthorized action!'); window.location.href='manage_screens.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Screens</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/Style1.css">

    
    <style>
      
    </style>
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
            <h2 class="mb-4 text-center">Manage Screens</h2>

            <!-- Add Screen Button -->
            <div class="mb-3 text-start">
                <a href="add_screen.php?theater_id=<?php echo $theater_id; ?>" class="btn btn-primary">Add Screen</a>
            </div>

            <!-- Screens Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Screen Name</th>
                            <th>Total Seats</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!$result) {
                            echo "<tr><td colspan='5' class='text-center text-danger'>Error: " . mysqli_error($conn) . "</td></tr>";
                        } elseif (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                    <td>" . htmlspecialchars($row['id']) . "</td>
                                    <td>" . htmlspecialchars($row['screen_name']) . "</td>
                                    <td>" . htmlspecialchars($row['total_seats']) . "</td>
                                    <td>" . htmlspecialchars($row['status']) . "</td>
                                    <td>
                                        <a href='edit_screen.php?id={$row['id']}' class='btn btn-warning btn-sm'>Edit</a>
                                        <a href='manage_screens.php?id={$row['id']}&action=delete' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center text-warning'>No screens found.</td></tr>";
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
