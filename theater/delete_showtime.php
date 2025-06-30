<?php
include 'includes/connection.php';
include('includes/sidebar.php'); 

if (!isset($_GET['id'])) {
    die("<script>alert('Showtime ID missing.'); window.location.href='showtime_list.php';</script>");
}

$id = $_GET['id'];

// Delete Showtime
$query = "DELETE FROM showtimes WHERE id = $id";
if (mysqli_query($conn, $query)) {
    echo "<script>
        alert('Showtime deleted successfully!');
        window.location.href = 'showtime_list.php';
    </script>";
} else {
    echo "<script>
        alert('Error deleting showtime: " . mysqli_error($conn) . "');
        window.location.href = 'showtime_list.php';
    </script>";
}

mysqli_close($conn);
?>
