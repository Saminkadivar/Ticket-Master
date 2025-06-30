<?php
include('connection.php');
include('includes/sidebar.php');

// Fetch the ticket details if 'id' is passed in the URL
if (isset($_GET['id'])) {
    $ticket_id = $_GET['id'];
    $sql = "SELECT * FROM tickets WHERE ticket_id = $ticket_id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $ticket = $result->fetch_assoc();
    } else {
        echo "Ticket not found!";
        exit;
    }
}

// Update ticket details
if (isset($_POST['update'])) {
    $user_id = $_POST['user_id'];
    $movie_id = $_POST['movie_id'];
    $theater_id = $_POST['theater_id'];
    $seat_number = $_POST['seat_number'];
    $status = $_POST['status'];

    // Prepare the SQL query to update the ticket
    $update_sql = "UPDATE tickets SET 
                    user_id = $user_id, 
                    movie_id = $movie_id, 
                    theater_id = $theater_id, 
                    seat_number = '$seat_number', 
                    status = '$status' 
                    WHERE ticket_id = $ticket_id";

    // Execute the update query and check for success
    if ($conn->query($update_sql) === TRUE) {
        echo "<div class='alert alert-success'>Ticket updated successfully!</div>";
        header("Location: manage_tickets.php");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ticket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container my-4">
        <h2>Edit Ticket</h2>

        <!-- Edit Ticket Form -->
        <form method="POST">
            <div class="mb-3">
                <label for="user_id" class="form-label">User ID:</label>
                <input type="text" class="form-control" name="user_id" value="<?= $ticket['user_id']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="movie_id" class="form-label">Movie ID:</label>
                <input type="text" class="form-control" name="movie_id" value="<?= $ticket['movie_id']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="theater_id" class="form-label">Theater ID:</label>
                <input type="text" class="form-control" name="theater_id" value="<?= $ticket['theater_id']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="seat_number" class="form-label">Seat Number:</label>
                <input type="text" class="form-control" name="seat_number" value="<?= $ticket['seat_number']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status:</label>
                <select class="form-select" name="status" required>
                    <option value="booked" <?= $ticket['status'] == 'booked' ? 'selected' : ''; ?>>Booked</option>
                    <option value="cancelled" <?= $ticket['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>

            <button type="submit" name="update" class="btn btn-primary">Update Ticket</button>
            <a href="manage_tickets.php" class="btn btn-secondary">Back to Manage Tickets</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
