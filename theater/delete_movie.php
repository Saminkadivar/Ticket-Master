<?php 
session_start();
include('includes/connection.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure a theater is logged in
if (!isset($_SESSION['theater_id'])) {
    header("Location: login.php");
    exit();
}
if (!isset($_GET['id'])) {
    die("Movie ID is missing.");
}

$theater_id = $_SESSION['theater_id'];
$movie_id = intval($_GET['id']);

// Debug: Show received values
echo "Theater ID: $theater_id<br>";
echo "Movie ID: $movie_id<br>";

// Check if the movie exists and belongs to the theater
$sql = "SELECT image FROM movies WHERE id = ? AND theater_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $movie_id, $theater_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    die("Movie not found or you do not have permission to delete it.");
}

$stmt->bind_result($image);
$stmt->fetch();
$stmt->close();

// Delete the movie from the database
$delete_sql = "DELETE FROM movies WHERE id = ? AND theater_id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("ii", $movie_id, $theater_id);

if ($delete_stmt->execute()) {
    echo "Movie deleted successfully.<br>";

    // Remove the image file if it exists
    if (!empty($image) && file_exists("../admin/" . $image)) {
        unlink("../admin/" . $image);
        echo "Image deleted successfully.<br>";
    } else {
        echo "Image not found or already deleted.<br>";
    }

    header("Location: manage_movies.php");
    exit();
} else {
    echo "Error deleting movie: " . $conn->error;
}
?>
