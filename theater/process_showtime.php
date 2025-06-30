<?php
include 'includes/connection.php';
session_start();

// Ensure a theater is logged in
if (!isset($_SESSION['theater_id'])) {
    header("Location: login.php");
    exit();
}
$theater_id = $_SESSION['theater_id']; // Get theater ID from session

// Fetch movies and screens for dropdown selection
$movies = mysqli_query($conn, "SELECT * FROM movies WHERE theater_id = $theater_id");
$screens = mysqli_query($conn, "SELECT * FROM screens WHERE theater_id = $theater_id");

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $movie_id = $_POST['movie_id'] ?? '';
    $screen_id = $_POST['screen_id'] ?? '';
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $show_times = $_POST['time'] ?? [];

    // Validate input
    if (empty($movie_id) || empty($screen_id) || empty($start_date) || empty($end_date) || empty($show_times)) {
        die("<script>alert('All fields are required.'); window.location.href='add_showtime.php';</script>");
    }

    // Convert dates to DateTime objects
    $start_date = new DateTime($start_date);
    $end_date = new DateTime($end_date);

    // Loop through each day from start date to end date
    $current_date = $start_date;
    while ($current_date <= $end_date) {
        $formatted_date = $current_date->format('Y-m-d');

        // Insert showtimes for the current date
        $query = "INSERT INTO showtimes (movie_id, screen_id, theater_id, date, time) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        foreach ($show_times as $show_time) {
            $stmt->bind_param("iiiss", $movie_id, $screen_id, $theater_id, $formatted_date, $show_time);
            $stmt->execute();
        }

        $current_date->modify('+1 day'); // Move to the next day
    }

    $stmt->close();
    $conn->close();

    // Instant Redirect
    echo "<script>
        alert('Showtimes added successfully!');
        window.location.href = 'showtime_list.php';
    </script>";
    exit;
}
?>
