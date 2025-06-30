<?php
include 'includes/connection.php'; // Include your database connection file
include('includes/sidebar.php'); 

$movie_id = 1; // Change this to your actual movie ID
$theater_id = 1; // Change to the correct theater ID
$screen_id = 1; // Assign a valid screen ID
$start_date = "2025-02-13"; // Show start date
$end_date = date('Y-m-d', strtotime($start_date . ' + 29 days')); // End date

$show_times = ['10:00:00', '14:00:00', '18:00:00']; // Show timings

for ($date = strtotime($start_date); $date <= strtotime($end_date); $date += 86400) {
    $formatted_date = date('Y-m-d', $date);
    
    foreach ($show_times as $time) {
        $query = "INSERT INTO showtimes (screen_id, date, time, theater_id) 
                  VALUES ($screen_id, '$formatted_date', '$time', $theater_id)";
        
        if (mysqli_query($conn, $query)) {
            echo "Showtime added for $formatted_date at $time <br>";
        } else {
            echo "Error: " . mysqli_error($conn) . "<br>";
        }
    }
}

mysqli_close($conn);
?>
