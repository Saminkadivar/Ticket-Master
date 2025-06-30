<?php
include('connection.php');

// Get the ID from the URL
$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id) {
    // Fetch existing screening details from the database
    $sql = "SELECT * FROM screens WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $screen_name = $row['screen_name'];
        $total_seats = $row['total_seats'];
        $status = $row['status'];
    } else {
        echo "Screening not found!";
        exit;
    }
}

// If form is submitted, update the screening details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $screen_name = $_POST['screen_name'];
    $total_seats = $_POST['total_seats'];
    $status = $_POST['status'];

    $update_sql = "UPDATE screens 
                   SET screen_name='$screen_name', total_seats='$total_seats', status='$status' 
                   WHERE id=$id";

    if ($conn->query($update_sql) === TRUE) {
        echo "Screen updated successfully!";
        // Redirect to the listings page or another appropriate page
        header("Location: manage_screens.php"); // Adjust the redirect as necessary
    } else {
        echo "Error: " . $conn->error;
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Screen</title>
</head>
<body>
    <h2>Edit Screen</h2>
    <form action="" method="POST">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        
        <label for="screen_name">Screen Name:</label><br>
        <input type="text" id="screen_name" name="screen_name" value="<?php echo $screen_name; ?>" required><br><br>
        
        <label for="total_seats">Total Seats:</label><br>
        <input type="number" id="total_seats" name="total_seats" value="<?php echo $total_seats; ?>" required><br><br>
        
        <label for="status">Status:</label><br>
        <select name="status" id="status" required>
            <option value="Active" <?php echo ($status == 'Active') ? 'selected' : ''; ?>>Active</option>
            <option value="Inactive" <?php echo ($status == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
        </select><br><br>
        
        <input type="submit" value="Update Screen">
    </form>
</body>
</html>
