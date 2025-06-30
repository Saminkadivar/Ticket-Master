<?php
session_start();
include('includes/connection.php');
include 'includes/sidebar.php';
// Ensure a theater is logged in
if (!isset($_SESSION['theater_id'])) {
    header("Location: login.php");
    exit();
}

$theater_id = $_SESSION['theater_id']; // Get theater ID from session

// Fetch user details (without booking/movies) only for the logged-in theater
$query = "SELECT DISTINCT u.User_id, u.First_name, u.Last_name, u.email, u.Phone_no, u.City 
          FROM bookings b
          JOIN users u ON b.user_id = u.User_id
          WHERE b.theater_id = ?
          ORDER BY u.Last_name, u.First_name";

$stmt = $conn->prepare($query);

if (!$stmt) {
    die("SQL Error: " . $conn->error); // Debugging: Show SQL error
}

$stmt->bind_param("i", $theater_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="css/style1.css">
</head>
<body>
    <div class="main-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <?php include 'includes/sidebar.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="content">
            <div class="table-container">
                <h2 class="mb-4 text-center">User Details</h2>
                <?php if ($result->num_rows > 0) { ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>Phone No</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['User_id']; ?></td>
                                <td><?php echo $row['First_name'] . ' ' . $row['Last_name']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['Phone_no']; ?></td>
                                <td><?php echo $row['City']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php } else { ?>
                    <p class="alert alert-warning text-center">No users found for your theater.</p>
                <?php } ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php 
$stmt->close();
$conn->close(); 
?>
