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

// Fetch screens for the logged-in theater
$stmt = $conn->prepare("SELECT id, screen_name FROM screens WHERE theater_id = ? AND status = 'Active'");
$stmt->bind_param("i", $theater_id);
$stmt->execute();
$screens = $stmt->get_result();
$stmt->close();

// Fetch showtimes
$showtime_stmt = $conn->prepare("SELECT DISTINCT time FROM showtimes WHERE theater_id = ?");
$showtime_stmt->bind_param("i", $theater_id);
$showtime_stmt->execute();
$showtimes = $showtime_stmt->get_result();
$showtime_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> <!-- Mobile Responsive -->
    <title>Add Seats</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 150vh;
            margin: 0;
        }
        .content-wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 100px;
        }
        .seat-form {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
            width: 50%;
            max-width: 700px;
        }

        @media (max-width: 768px) {
            .seat-form {
                width: 90%;
                padding: 20px;
            }
        }

    </style>
    <script>
        function generateRows() {
            const numRows = document.getElementById("num_rows").value;
            const seatCategoriesDiv = document.getElementById("seat_categories");
            seatCategoriesDiv.innerHTML = ""; // Clear existing rows

            for (let i = 0; i < numRows; i++) {
                let rowLetter = String.fromCharCode(65 + i); // Convert number to letter (A-Z)
                seatCategoriesDiv.innerHTML += `
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label class="form-label"><strong>Row ${rowLetter} Category:</strong></label>
                            <select name="row_category[${rowLetter}]" class="form-control" required>
                                <option value="Silver">Silver</option>
                                <option value="Gold">Gold</option>
                                <option value="Premium">Premium</option>
                            </select>
                        </div>
                    </div>
                `;
            }
        }
    </script>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <?php include 'includes/sidebar.php'; ?>
    </div>

    <!-- Main Content -->
    <div class="content-wrapper">
        <br>
        <div class="seat-form">
            <h2 class="text-center mb-4">Add Seats to Screen</h2> <!-- Heading now properly displayed -->
            <form action="process_seats.php" method="POST">
                <div class="mb-3">
                    <label for="screen_id" class="form-label"><strong>Select Screen:</strong></label>
                    <select name="screen_id" id="screen_id" class="form-control" required>
                        <option value="">-- Select Screen --</option>
                        <?php while ($screen = $screens->fetch_assoc()) { ?>
                            <option value="<?= $screen['id']; ?>"><?= htmlspecialchars($screen['screen_name']); ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="start_date"><strong>Start Date:</strong></label>
                    <input type="date" id="start_date" name="start_date" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="end_date"><strong>End Date:</strong></label>
                    <input type="date" id="end_date" name="end_date" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label><strong>Select Showtimes:</strong></label>
                    <?php while ($showtime = $showtimes->fetch_assoc()) { ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="showtimes[]" value="<?= $showtime['time']; ?>">
                            <label class="form-check-label"><?= htmlspecialchars($showtime['time']); ?></label>
                        </div>
                    <?php } ?>
                </div>

                <div class="mb-3">
                    <label for="num_rows"><strong>Number of Rows (A-Z):</strong></label>
                    <input type="number" id="num_rows" name="num_rows" class="form-control" min="1" max="26" required onchange="generateRows()">
                </div>

                <div class="mb-3">
                    <label for="seats_per_row"><strong>Seats per Row:</strong></label>
                    <input type="number" id="seats_per_row" name="seats_per_row" class="form-control" min="1" max="20" required>
                </div>

                <div id="seat_categories"></div> <!-- Categories will be generated here -->

                <div class="mb-3">
                    <label for="premium_price"><strong>Premium Seat Price:</strong></label>
                    <input type="number" id="premium_price" name="premium_price" class="form-control" min="1" step="0.01" required>
                </div>

                <div class="mb-3">
                    <label for="gold_price"><strong>Gold Seat Price:</strong></label>
                    <input type="number" id="gold_price" name="gold_price" class="form-control" min="1" step="0.01" required>
                </div>

                <div class="mb-3">
                    <label for="silver_price"><strong>Silver Seat Price:</strong></label>
                    <input type="number" id="silver_price" name="silver_price" class="form-control" min="1" step="0.01" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Add Seats</button>
            </form>
        </div>
    </div>

</body>
</html>

<?php mysqli_close($conn); ?>
