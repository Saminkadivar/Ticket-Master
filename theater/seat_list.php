<?php
include 'includes/connection.php';
include 'includes/sidebar.php';
session_start();

// Ensure a theater is logged in
if (!isset($_SESSION['theater_id'])) {
    header("Location: login.php");
    exit();
}

$theater_id = $_SESSION['theater_id'];
$screen_id = $_GET['screen_id'] ?? '';

// Fetch available screens for the theater
$screen_sql = "SELECT id, screen_name FROM screens WHERE theater_id = ?";
$screen_stmt = $conn->prepare($screen_sql);
$screen_stmt->bind_param("i", $theater_id);
$screen_stmt->execute();
$screen_result = $screen_stmt->get_result();

// Fetch seat details only if a screen is selected
$seats = [];
$screen_name = "Select a screen";
if (!empty($screen_id)) {
    $seat_sql = "SELECT s.id, s.seat_number, s.row_label, s.status, s.category, s.price, st.date, st.time, sc.screen_name
                 FROM seats s
                 JOIN showtimes st ON s.showtime_id = st.id
                 JOIN screens sc ON s.screen_id = sc.id
                 WHERE s.theater_id = ? AND s.screen_id = ?
                 ORDER BY st.date, st.time, s.row_label, s.seat_number";

    $seat_stmt = $conn->prepare($seat_sql);
    $seat_stmt->bind_param("ii", $theater_id, $screen_id);
    $seat_stmt->execute();
    $seats = $seat_stmt->get_result();
    
    // Fetch screen name separately before iterating
    if ($seats->num_rows > 0) {
        $first_row = $seats->fetch_assoc();
        $screen_name = $first_row['screen_name'];
        $seats->data_seek(0); // Reset pointer
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style1.css">
    <style>
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .form-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }
        .form-select {
            max-width: 300px;
        }
       
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">Seat List</h2>

        <!-- Dropdown to select screen -->
        <div class="form-container">
            <form method="GET" class="mb-3 d-flex flex-column align-items-center">
                <label for="screen_id" class="mb-2"><strong>Select Screen:</strong></label>
                <select name="screen_id" id="screen_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Choose a Screen --</option>
                    <?php while ($screen = $screen_result->fetch_assoc()) { ?>
                        <option value="<?= $screen['id']; ?>" <?= ($screen_id == $screen['id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($screen['screen_name']); ?>
                        </option>
                    <?php } ?>
                </select>
            </form>
        </div>

        <?php if (!empty($screen_id)) { ?>
            <h3 class="text-center mb-3">Screen: <?= htmlspecialchars($screen_name); ?></h3>

            <div class="table-container">
                <table class="table table-bordered table-striped text-center">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Showtime</th>
                            <th>Row</th>
                            <th>Seat Number</th>
                            <th>Category</th>
                            <th>Price (INR)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($seats->num_rows > 0) {
                            while ($seat = $seats->fetch_assoc()) { ?>
                                <tr>
                                    <td><?= htmlspecialchars($seat['date']); ?></td>
                                    <td><?= htmlspecialchars($seat['time']); ?></td>
                                    <td><?= htmlspecialchars($seat['row_label']); ?></td>
                                    <td><?= htmlspecialchars($seat['seat_number']); ?></td>
                                    <td><?= htmlspecialchars(ucfirst($seat['category'])); ?></td>
                                    <td><?= htmlspecialchars(number_format($seat['price'], 2)); ?></td>
                                    <td><?= htmlspecialchars($seat['status']); ?></td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr><td colspan="7" class="text-center">No seats available for this screen.</td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } else { ?>
            <p class="text-center">Please select a screen to view seat details.</p>
        <?php } ?>
    </div>
</body>
</html>

<?php
$screen_stmt->close();
if (!empty($screen_id)) {
    $seat_stmt->close();
}
$conn->close();
?>
