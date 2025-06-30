<?php
include 'includes/connection.php';
include('includes/sidebar.php');
session_start();

// Ensure the user is logged in as a theater
if (!isset($_SESSION['theater_id'])) {
    die("Access Denied. Please log in as a theater. <a href='login.php'>Login</a>");
}

$theater_id = $_SESSION['theater_id']; // Get theater ID from session

// Fetch screens belonging to the logged-in theater
$stmt = $conn->prepare("SELECT id, screen_name FROM screens WHERE theater_id = ? AND status = 'Active'");
$stmt->bind_param("i", $theater_id);
$stmt->execute();
$screens = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Seats</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: rgb(242, 242, 242);
            font-family: Arial, sans-serif;
        }
        .form-container {
            width: 400px;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .form-container h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        select, input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #3aafa9;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            border-radius: 6px;
            transition: 0.3s ease;
        }
        button:hover {
            background-color: #2a7c7a;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Add Seats to Screen</h2>
        <form action="process_seats.php" method="POST">
            <div class="form-group">
                <label for="screen_id"><strong>Screen:</strong></label>
                <select name="screen_id" id="screen_id" required>
                    <option value="">-- Select Screen --</option>
                    <?php while ($screen = $screens->fetch_assoc()) { ?>
                        <option value="<?= $screen['id']; ?>"><?= htmlspecialchars($screen['screen_name']); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="num_rows"><strong>Number of Rows (A-Z):</strong></label>
                <input type="number" id="num_rows" name="num_rows" min="1" max="26" required>
            </div>

            <div class="form-group">
                <label for="seats_per_row"><strong>Seats per Row:</strong></label>
                <input type="number" id="seats_per_row" name="seats_per_row" min="1" max="20" required>
            </div>

            <button type="submit">Add Seats</button>
        </form>
    </div>

</body>
</html>

<?php mysqli_close($conn); ?>
