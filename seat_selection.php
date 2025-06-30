<?php 
session_start();
include('connection.php');
include('includes/header.php');

// Check login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_to'] = "seat_selection.php?movie_id=" . $_GET['movie_id'] . "&movie=" . urlencode($_GET['movie']) . "&theater=" . urlencode($_GET['theater']) . "&screen=" . urlencode($_GET['screen']) . "&time=" . urlencode($_GET['time']) . "&date=" . urlencode($_GET['date']);
    header('Location: login.php');
    exit();
}

// Get URL parameters
$movie_id = $_GET['movie_id'] ?? '';
$movie_title = $_GET['movie'] ?? '';
$theater = $_GET['theater'] ?? '';
$screen = $_GET['screen'] ?? '';
$time = $_GET['time'] ?? '';
$date = $_GET['date'] ?? '';

// Fetch seat data
$sql = "SELECT s.id, s.seat_number, s.row_label, s.status, s.category, s.price
        FROM seats s
        JOIN showtimes st ON s.showtime_id = st.id
        JOIN screens sc ON s.screen_id = sc.id
        JOIN theaters t ON s.theater_id = t.id
        WHERE t.name = ? AND sc.screen_name = ? AND st.time = ? AND st.date = ? AND s.showtime_id = st.id
        ORDER BY s.category DESC, s.row_label ASC, s.seat_number ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $theater, $screen, $time, $date);
$stmt->execute();
$result = $stmt->get_result();

$seats = [];
while ($row = $result->fetch_assoc()) {
    $seats[$row['category']][$row['row_label']][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Seat Selection</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .screen {
            background: #ccc;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            margin: 25px auto;
            font-weight: bold;
            font-size: 18px;
            width: 60%;
        }
        .seat-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .row {
            display: flex;
            gap: 8px;
            justify-content: center;
            margin-bottom: 10px;
        }
     
        .seat {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            cursor: pointer;
            border-radius: 4px;
            border: 1px solid #ccc;
            user-select: none;
            text-align: center;
        }

        .available { background-color: #28a745; color: white; }
        .selected { background-color: #ffc107 !important; color: black; }
        .unavailable { background-color: #dc3545; color: white; pointer-events: none; }

        .price-info {
            font-size: 10px;
            display: block;
            margin-top: -3px;
        }

        .proceed-container {
            margin-top: 20px;
            text-align: center;
        }

        #totalAmount {
            font-weight: bold;
            font-size: 20px;
            margin: 10px 0;
        }

        .category-title {
            font-weight: bold;
            margin-top: 25px;
            font-size: 18px;
            color: #333;
        }

        .row-label {
            font-weight: bold;
            width: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .seats-row {
            display: flex;
            align-items: center;
        }

        .divider {
            height: 1px;
            width: 100%;
            background: #ccc;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center mb-4">Select Your Seats</h2>
        <p class="text-center">
            <strong>Movie:</strong> <?= htmlspecialchars($movie_title); ?> |
            <strong>Theater:</strong> <?= htmlspecialchars($theater); ?><br>
            <strong>Screen:</strong> <?= htmlspecialchars($screen); ?> |
            <strong>Date:</strong> <?= htmlspecialchars($date); ?> |
            <strong>Time:</strong> <?= htmlspecialchars($time); ?>
        </p>

        <div class="screen">All eyes this way please!</div>

        <div class="seat-container">
            <?php foreach ($seats as $category => $rows): ?>
                <div class="category-title"><?= strtoupper($category) ?> - ₹<?= current(current($rows))['price'] ?? 0 ?></div>
                <?php foreach ($rows as $row_label => $seat_row): ?>
                    <div class="seats-row">
                        <div class="row-label"><?= $row_label ?></div>
                        <div class="row">
                            <?php foreach ($seat_row as $seat): 
                                $statusClass = $seat['status'] === 'available' ? 'available' : 'unavailable';
                            ?>
                                <div class="seat <?= $statusClass ?>" 
                                    data-seat-id="<?= $seat['id'] ?>" 
                                    data-price="<?= $seat['price'] ?>" 
                                    onclick="selectSeat(this)">
                                    <?= $seat['seat_number'] ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="divider"></div>
            <?php endforeach; ?>
        </div>

        <div class="proceed-container">
            <div id="totalAmount">Total: ₹0</div>
            <button class="btn btn-danger" id="proceedButton" disabled onclick="proceedToPayment()">Pay ₹0</button>
        </div>
    </div>

    <script>
        let selectedSeats = [];
        let totalAmount = 0;

        function selectSeat(el) {
            const seatId = el.dataset.seatId;
            const price = parseInt(el.dataset.price);

            if (el.classList.contains('unavailable')) return;

            if (el.classList.contains('selected')) {
                el.classList.remove('selected');
                selectedSeats = selectedSeats.filter(id => id !== seatId);
                totalAmount -= price;
            } else {
                if (selectedSeats.length >= 6) {
                    alert("You can book a maximum of 6 seats.");
                    return;
                }
                el.classList.add('selected');
                selectedSeats.push(seatId);
                totalAmount += price;
            }

            document.getElementById("totalAmount").innerText = "Total: ₹" + totalAmount;
            document.getElementById("proceedButton").innerText = "Pay ₹" + totalAmount;
            document.getElementById("proceedButton").disabled = selectedSeats.length === 0;
        }

        function proceedToPayment() {
            if (selectedSeats.length === 0) {
                alert("Please select at least one seat.");
                return;
            }

            const selected = selectedSeats.join(",");
            const redirectURL = `payment_page.php?movie_id=<?= urlencode($movie_id); ?>&movie_title=<?= urlencode($movie_title); ?>&theater=<?= urlencode($theater); ?>&screen=<?= urlencode($screen); ?>&time=<?= urlencode($time); ?>&date=<?= urlencode($date); ?>&seats=${encodeURIComponent(selected)}`;
            window.location.href = redirectURL;
        }
    </script>
</body>
</html>
