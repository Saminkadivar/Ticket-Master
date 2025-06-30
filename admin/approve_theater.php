<?php
include 'connection.php';

if (isset($_GET['approve_theater'])) {
    $id = $_GET['approve_theater'];

    $query = "UPDATE theaters SET status='approve' WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: theater_requests.php?success=approved");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
