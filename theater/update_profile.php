<?php
include('includes/connection.php');
include('includes/sidebar.php');
session_start();

// Ensure a theater is logged in
if (!isset($_SESSION['theater_id'])) {
    header("Location: login.php");
    exit();
}

$theater_id = $_SESSION['theater_id'];

// Fetch Theater Details
$theater_query = $conn->query("SELECT * FROM theaters WHERE id = '$theater_id'");
if ($theater_query->num_rows > 0) {
    $theater = $theater_query->fetch_assoc();
} else {
    die("<script>alert('Theater details not found.'); window.location.href='dashboard.php';</script>");
}

// Update Profile Logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $location = $_POST['location'];

    // Update Theater Profile in the database
    $update_query = $conn->query("UPDATE theaters SET name = '$name', location = '$location' WHERE id = '$theater_id'");

    if ($update_query) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error updating profile: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/Style1.css">


    <style>
        body {
            background-color: #f8f9fa;
        }
        .container-form {
            max-width: 400px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: auto;
            margin-top: 50px;
            text-align: center;
        }
        h3 {
            margin-bottom: 20px;
            color: #333;
        }
        .btn-success {
            width: 100%;
            background-color: #137547;
            border: none;
        }
        .btn-success:hover {
            background-color: #0e5a35;
        }
        .btn-secondary {
            width: 100%;
            background-color: #6c757d;
            border: none;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<div class="container-form">
    <h3>Update Theater Profile</h3>
    <form method="POST">
        <div class="mb-3 text-start">
            <label class="form-label">Theater Name:</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($theater['name']); ?>" required>
        </div>

        <div class="mb-3 text-start">
            <label class="form-label">Location:</label>
            <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($theater['location']); ?>" required>
        </div>

        <button type="submit" name="update_profile" class="btn btn-success">Update Profile</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
