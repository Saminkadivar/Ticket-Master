<?php
include('connection.php');

// Sanitize input values
$theater = trim($_GET['theater'] ?? '');
$screen = trim($_GET['screen'] ?? '');
$time = trim($_GET['time'] ?? '');
$date = trim($_GET['date'] ?? '');

if (!$theater || !$screen || !$time || !$date) {
    die("<p>Error: Missing required parameters.</p>");
}

// ✅ Query to fetch seats based on user-selected showtime
$sql = "SELECT s.id, s.seat_number, s.row_label, s.status
        FROM seats s
        JOIN showtimes st ON s.showtime_id = st.id
        JOIN screens sc ON s.screen_id = sc.id
        JOIN theaters t ON s.theater_id = t.id
        WHERE t.name = ? AND sc.screen_name = ? AND st.time = ? AND st.date = ?
        ORDER BY s.row_label ASC, s.seat_number ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $theater, $screen, $time, $date);
$stmt->execute();
$result = $stmt->get_result();

$seats = [];

// ✅ Organize seats by row
while ($row = $result->fetch_assoc()) {
    $seats[$row['row_label']][] = $row;
}

// ✅ Check if seats exist before rendering
if (empty($seats)) {
    echo "<p>No seats available for the selected showtime.</p>";
} else {
    foreach ($seats as $row_label => $row_seats) {
        echo "<div class='row'>";
        foreach ($row_seats as $seat) {
            // ✅ Ensure correct class assignment
            $class = ($seat['status'] === 'available') ? 'available' : 'unavailable';
            echo "<div class='seat $class' data-seat-id='{$seat['id']}'>{$seat['seat_number']}</div>";
        }
        echo "</div>";
    }
}

// Close connection
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .row {
    display: flex;
    justify-content: center;
    margin-bottom: 10px;
}

.seat {
    width: 40px;
    height: 40px;
    margin: 5px;
    text-align: center;
    line-height: 40px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
}

.available {
    background-color: green;
    color: white;
}

.unavailable {
    background-color: red;
    color: white;
    pointer-events: none;
}
</style>
</head>
<body>
    
</body>
</html>