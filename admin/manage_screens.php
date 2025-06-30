<?php
include('connection.php');
include('includes/sidebar.php');

// Handle deletion
if (isset($_GET['id']) && $_GET['action'] == 'delete') {
    $id = intval($_GET['id']); 
    $sql = "DELETE FROM screens WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Screen deleted successfully!'); window.location.href='manage_screens.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

// Fetch screens data
$sql = "SELECT screens.*, theaters.name AS theater_name 
        FROM screens 
        JOIN theaters ON screens.theater_id = theaters.id";
$result = $conn->query($sql);

if (!$result) {
    die("Query Failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Screens</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body>

<div class="main-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <?php include('includes/sidebar.php'); ?>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="card">
            <h2 class="text-center mb-4">Manage Screens</h2>

            <?php if ($result->num_rows > 0) { ?>
                <div class="table-container">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Theater</th>
                                <th>Screen Name</th>
                                <th>Total Seats</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['theater_name']; ?></td>
                                    <td><?php echo $row['screen_name']; ?></td>
                                    <td><?php echo $row['total_seats']; ?></td>
                                    <td><?php echo $row['status']; ?></td>
                                    <td class="actions">
                                        <!-- <a href="edit_screen.php?id=<?php //echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a> -->
                                        <a href="?id=<?php echo $row['id']; ?>&action=delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this screen?');">Delete</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="alert alert-warning text-center">No screens found.</div>
            <?php } ?>
        </div>
    </div>
</div>

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
