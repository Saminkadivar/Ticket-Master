<?php
session_start();
include('includes/connection.php');
include('includes/sidebar.php');

// Ensure a theater is logged in
if (!isset($_SESSION['theater_id'])) {
    header("Location: login.php");
    exit();
}

$owner_id = $_SESSION['theater_id'];

// Fetch the theater associated with the logged-in owner
$theater_query = "SELECT * FROM theaters WHERE id = $owner_id";
$theater_result = $conn->query($theater_query);
$theater = $theater_result->fetch_assoc();

if (!$theater) {
    echo "<script>alert('No theater found for your account.'); window.location.href='dashboard.php';</script>";
    exit;
}

$theater_id = $theater['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $screen_name = trim($_POST['screen_name']);
    $total_seats = intval($_POST['total_seats']);
    $status = $_POST['status'];

    // Check for duplicate screen name within the same theater
    $check_query = "SELECT * FROM screens WHERE theater_id = '$theater_id' AND screen_name = '$screen_name'";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        echo "<script>alert('A screen with this name already exists in your theater. Please choose another name.');</script>";
    } else {
        $insert_query = "INSERT INTO screens (theater_id, screen_name, total_seats, status) 
                         VALUES ('$theater_id', '$screen_name', '$total_seats', '$status')";

        if ($conn->query($insert_query) === TRUE) {
            echo "<script>alert('Screen added successfully!'); window.location.href='manage_screens.php';</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <title>Add Screen</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            color: white;
            padding: 20px;
            min-height: 100vh;
        }
        .content {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .card {
            width: 100%;
            max-width: 500px;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background: white;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <?php include('includes/sidebar.php'); ?>
    </div>

    <div class="content">
        <div class="card">
            <h2 class="text-center mb-3">Add Screen</h2>
            <form method="POST">
                <div class="mb-3">
                    <label>Screen Name:</label>
                    <input type="text" name="screen_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Total Seats:</label>
                    <input type="number" name="total_seats" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Status:</label>
                    <select name="status" class="form-select">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success w-100">Add Screen</button>
                <a href="manage_screens.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
            </form>
        </div>
    </div>

</body> 
</html>
