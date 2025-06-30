<?php
session_start();
include('connection.php');
include('includes/header.php');

// Get the selected movie ID
$movie_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch movie details
$movie_sql = "SELECT title FROM movies WHERE id = ?";
$movie_stmt = $conn->prepare($movie_sql);
$movie_stmt->bind_param("i", $movie_id);
$movie_stmt->execute();
$movie_result = $movie_stmt->get_result();
$movie = $movie_result->fetch_assoc();

if (!$movie) {
    die("<p class='text-center text-danger'>Movie not found.</p>");
}

$movie_title = htmlspecialchars($movie['title']);

// Get current date
$current_date = date('Y-m-d');
$dates = [];
for ($i = 0; $i < 7; $i++) {
    $dates[] = date('D d M', strtotime("+$i days"));
}

// Handle date selection, ensuring no past dates
$selected_date = isset($_GET['date']) ? $_GET['date'] : $current_date;
if ($selected_date < $current_date) {
    $selected_date = $current_date;
}

// Fetch theaters, screens, and showtimes
$sql = "SELECT theaters.id AS theater_id, theaters.name AS theater_name, theaters.location, 
               screens.screen_name, showtimes.time, 
               (SELECT COUNT(*) FROM seats WHERE showtime_id = showtimes.id AND status = 'booked') AS booked_seats,
               (SELECT COUNT(*) FROM seats WHERE showtime_id = showtimes.id) AS total_seats
        FROM theaters 
        JOIN screens ON theaters.id = screens.theater_id
        JOIN showtimes ON screens.id = showtimes.screen_id
        WHERE showtimes.date = ? AND showtimes.movie_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $selected_date, $movie_id);
$stmt->execute();
$result = $stmt->get_result();

$theaters = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $availability_level = "high"; // Default color (green)

        if ($row['total_seats'] > 0) {
            $availability_percentage = ($row['booked_seats'] / $row['total_seats']) * 100;
            if ($availability_percentage > 80) {
                $availability_level = "low"; // Red
            } elseif ($availability_percentage > 50) {
                $availability_level = "medium"; // Orange
            }
        } else {
            $availability_level = "full"; // Fully booked (gray)
        }

        $row['availability_level'] = $availability_level;

        $theaters[$row['theater_name']]['id'] = $row['theater_id'];
        $theaters[$row['theater_name']]['location'] = $row['location'];
        $theaters[$row['theater_name']]['screens'][$row['screen_name']][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $movie_title; ?> Showtimes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">

    <style>
        body { background-color:rgb(232, 225, 225); color: #222; }
        h2 { color: #100; }
        h4 { color:#FFD700; }
        .date-btn { margin: 5px; border: 2px solid #001F3F; color: #001F3F; background: transparent; }
        .date-btn:hover, .date-btn.active { background: #001F3F; color: #FFD700; }
        .showtime-container { background: #001F3F; padding: 15px; margin-top: 10px; border-radius: 8px; color: white; }
        
        /* Showtime Button Colors */
        .showtime-btn { margin: 5px; font-weight: bold; }
        .showtime-btn.high { background: #28a745; color: white; } /* Green */
        .showtime-btn.medium { background: #ffa500; color: black; } /* Orange */
        .showtime-btn.low { background: #dc3545; color: white; } /* Red */
        .showtime-btn.full, .showtime-btn.disabled { background: #6c757d; color: white; pointer-events: none; } /* Gray */
    </style>
</head>
<body>
    <div class="container">
        <br>
        <h2 class="text-center"><?php echo $movie_title; ?> - Select a Date</h2>
        <div class="d-flex justify-content-center flex-wrap">
            <?php foreach ($dates as $date) { 
                $date_value = date('Y-m-d', strtotime($date));
                ?>
                <a href="?id=<?php echo $movie_id; ?>&date=<?php echo $date_value; ?>" 
                   class="btn date-btn <?php echo ($selected_date == $date_value) ? 'active' : ''; ?>" 
                   <?php echo ($date_value < $current_date) ? 'disabled' : ''; ?>>
                    <?php echo $date; ?>
                </a>
            <?php } ?>
        </div>

        <?php if (!empty($theaters)) { ?>
            <h4 class="text-center text-black mt-4">Showtimes for <?php echo date('D d M', strtotime($selected_date)); ?></h4>
            <?php foreach ($theaters as $theater => $data) { ?>
                <div class="showtime-container">
                    <h4><?php echo htmlspecialchars($theater); ?></h4>
                    <p>Location: <?php echo htmlspecialchars($data['location']); ?></p>
                    <?php foreach ($data['screens'] as $screen => $shows) { ?>
                        <h6>Screen: <?php echo htmlspecialchars($screen); ?></h6>
                        <?php foreach ($shows as $show) { 
                            $show_time = $show['time'];
                            $availability_level = $show['availability_level'];
                            
                            // Check if the showtime is within the next 15 minutes
                            $current_datetime = new DateTime();
                            $show_datetime = new DateTime("$selected_date $show_time");
                            $interval = $current_datetime->diff($show_datetime);
                            $minutes_diff = ($interval->invert == 1) ? -($interval->h * 60 + $interval->i) : ($interval->h * 60 + $interval->i);
                            
                            $disabled_class = ($minutes_diff < 15) ? "disabled" : "";
                            ?>
                            
                            <a href="seat_selection.php?movie_id=<?php echo $movie_id; ?>&movie=<?php echo urlencode($movie_title); ?>&theater_id=<?php echo urlencode($data['id']); ?>&theater=<?php echo urlencode($theater); ?>&screen=<?php echo urlencode($screen); ?>&time=<?php echo urlencode($show_time); ?>&date=<?php echo urlencode($selected_date); ?>" 
                               class="btn showtime-btn <?php echo $availability_level . " " . $disabled_class; ?>">
                                <?php echo htmlspecialchars($show_time); ?>
                            </a>
                        <?php } ?>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p class="text-center text-danger">No showtimes available for this movie on this date.</p>
        <?php } ?>
    </div>
</body>
</html>

<?php $conn->close(); ?>
