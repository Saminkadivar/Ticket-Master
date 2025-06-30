<!-- Delete Screen (delete_screen.php) !-->
<?php
include('connection.php');
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM screenings WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "Screen deleted successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
    $conn->close();
}
?>