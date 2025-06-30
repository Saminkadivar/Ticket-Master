<?php
include('connection.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete movie from the database
    $sql = "DELETE FROM movies WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "Movie deleted successfully";
        header("Location: manage_movies.php"); // Redirect back to movie management page
    } else {
        echo "Error deleting movie: " . $conn->error;
    }
}

$conn->close();
?>
