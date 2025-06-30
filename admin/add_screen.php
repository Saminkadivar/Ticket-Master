<!-- Add Screen (add_screen.php) !-->
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include('connection.php');
    $theater_id = $_POST['theater_id'];
    $screen_name = $_POST['screen_name'];
    $total_seats = $_POST['total_seats'];
    $status = $_POST['status'];

    $sql = "INSERT INTO screenings (theater_id, screen_name, total_seats, status) VALUES ('$theater_id', '$screen_name', '$total_seats', '$status')";
    if ($conn->query($sql) === TRUE) {
        echo "Screen added successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
    $conn->close();
}
?>