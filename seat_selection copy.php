<?php 
session_start();
include('connection.php');
include('includes/header.php');


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_to'] = "seat_selection.php?movie_id=" . $_GET['movie_id'] . "&movie=" . urlencode($_GET['movie']) . "&theater=" . urlencode($_GET['theater']) . "&screen=" . urlencode($_GET['screen']) . "&time=" . urlencode($_GET['time']) . "&date=" . urlencode($_GET['date']);
    header('Location: login.php');
    exit();
}

// Get parameters from URL
$movie_id = $_GET['movie_id'] ?? '';
$movie_title = $_GET['movie'] ?? '';
$theater = $_GET['theater'] ?? '';
$screen = $_GET['screen'] ?? '';
$time = $_GET['time'] ?? '';
$date = $_GET['date'] ?? '';

// Fetch seat data
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
while ($row = $result->fetch_assoc()) {
    $seats[$row['row_label']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Selection</title>
    <link rel="stylesheet" href="xss/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .seat-container { display: flex; flex-direction: column; align-items: center; gap: 10px; }
        .row { display: flex; gap: 10px; justify-content: center; }
        .seat { width: 40px; height: 40px; text-align: center; line-height: 40px; cursor: pointer; font-weight: bold; border-radius: 5px; }
        .available { background-color: #3aafa9; color: white; }
        .selected { background-color: #ffa500; color: white; }
        .unavailable { background-color: #d9534f; color: white; pointer-events: none; }
        .proceed-container { display: flex; justify-content: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Select Your Seats</h2>
        <p class="text-center">
            <strong>Movie:</strong> <?= htmlspecialchars($movie_title); ?> |
            <strong>Theater:</strong> <?= htmlspecialchars($theater); ?> <br> 
            <strong>Screen:</strong> <?= htmlspecialchars($screen); ?> | 
            <strong>Date:</strong> <?= htmlspecialchars($date); ?> | 
            <strong>Time:</strong> <?= htmlspecialchars($time); ?>
        </p>

        <div class="seat-container">
            <?php foreach ($seats as $row_label => $row_seats) { ?>
                <div class="row">
                    <?php foreach ($row_seats as $seat) { 
                        $class = ($seat['status'] == 'available') ? 'available' : 'unavailable'; ?>
                        <div class="seat <?= $class; ?>" data-seat-id="<?= $seat['id']; ?>" onclick="selectSeat(this)">
                            <?= $seat['seat_number']; ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
        <div class="proceed-container">
            <button class="btn btn-warning mt-3" id="proceedButton" disabled onclick="proceedToPayment()">Proceed to Pay</button>
        </div>
    </div>

    <script>
        let selectedSeats = [];

        function selectSeat(element) {
            let seatId = element.dataset.seatId;
            if (element.classList.contains('unavailable')) return;

            if (element.classList.contains('selected')) {
                element.classList.remove('selected');
                selectedSeats = selectedSeats.filter(id => id !== seatId);
            } else {
                if (selectedSeats.length >= 6) {
                    alert("You can book a maximum of 6 seats per transaction.");
                    return;
                }
                element.classList.add('selected');
                selectedSeats.push(seatId);
            }
            document.getElementById("proceedButton").disabled = selectedSeats.length === 0;
        }

        function proceedToPayment() {
            if (selectedSeats.length === 0) {
                alert("Select at least one seat.");
                return;
            }
            let selectedSeatsString = selectedSeats.join(",");
            let url = "payment_page.php?movie_id=<?= urlencode($movie_id); ?>&movie_title=<?= urlencode($movie_title); ?>&theater=<?= urlencode($theater); ?>&screen=<?= urlencode($screen); ?>&time=<?= urlencode($time); ?>&date=<?= urlencode($date); ?>&seats=" + encodeURIComponent(selectedSeatsString);
            window.location.href = url;
        }
    </script>
</body>
</html>
