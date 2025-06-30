<?php
include 'includes/connection.php';
include('includes/sidebar.php');

session_start();

// Ensure a theater is logged in
if (!isset($_SESSION['theater_id'])) {
    header("Location: login.php");
    exit();
}
$theater_id = $_SESSION['theater_id']; // Get theater ID from session

// Check if Showtime ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Showtime ID missing.");
}

$id = $_GET['id'];

// Fetch existing showtime details (Only for logged-in theater)
$query = "SELECT * FROM showtimes WHERE id = ? AND theater_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $id, $theater_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$showtime = mysqli_fetch_assoc($result);

if (!$showtime) {
    die("Showtime not found or unauthorized access.");
}

// Update Showtime
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $time = $_POST['time'];

    $update_query = "UPDATE showtimes SET date=?, time=? WHERE id=? AND theater_id=?";
    $update_stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($update_stmt, "ssii", $date, $time, $id, $theater_id);

    if (mysqli_stmt_execute($update_stmt)) {
        echo "<script>alert('Showtime updated successfully!'); window.location.href='showtime_list.php';</script>";
    } else {
        echo "<script>alert('Error updating showtime: " . mysqli_error($conn) . "');</script>";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Showtime</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }

        .form-container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
        }

        .btn-success {
            background-color: #1d8348;
            border: none;
            width: 100%;
        }

        .btn-success:hover {
            background-color: #145a32;
        }

        .btn-secondary {
            width: 100%;
            margin-top: 10px;
            background-color: #6c757d;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #545b62;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Edit Showtime</h2>
        <form action="" method="POST">
            <div class="mb-3 text-start">
                <label class="form-label">Date:</label>
                <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($showtime['date']); ?>" required>
            </div>

            <div class="mb-3 text-start">
                <label class="form-label">Time:</label>
                <input type="time" name="time" class="form-control" value="<?= htmlspecialchars($showtime['time']); ?>" required>
            </div>

            <button type="submit" class="btn btn-success">Update Showtime</button>
            <button type="button" class="btn btn-secondary" onclick="window.location.href='showtime_list.php'">Cancel</button>
        </form>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
