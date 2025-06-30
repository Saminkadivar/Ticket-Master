<?php
include('connection.php');
include('includes/header.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT b.id, m.title, b.theater, b.screen, b.seat_numbers, b.show_date, b.show_time, b.amount, b.status, b.cancellation_reason
        FROM bookings b
        JOIN movies m ON b.movie_id = m.id
        WHERE b.user_id = ?
        ORDER BY b.booking_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="Style.css">
    <style>
        h2 { color: #001F3F; }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2>My Booked Movies</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Movie</th>
                <th>Theater</th>
                <th>Screen</th>
                <th>Seats</th>
                <th>Show Date</th>
                <th>Show Time</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Cancellation Reason</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <?php 
                $showDateTime = strtotime($row['show_date'] . ' ' . $row['show_time']);
                $currentTime = time();
                $timeDiff = $showDateTime - $currentTime;
                $allowCancellation = $timeDiff > 3600; // Allow cancellation 1 hour before showtime
            ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['theater']) ?></td>
                <td><?= htmlspecialchars($row['screen']) ?></td>
                <td><?= htmlspecialchars($row['seat_numbers']) ?></td>
                <td><?= htmlspecialchars($row['show_date']) ?></td>
                <td><?= htmlspecialchars($row['show_time']) ?></td>
                <td>₹<?= number_format($row['amount'], 2) ?></td>
                <td>
                    <?php if ($row['status'] == 'Cancelled'): ?>
                        <span class="badge bg-danger">Cancelled</span>
                    <?php else: ?>
                        <span class="badge bg-success">Booked</span>
                    <?php endif; ?>
                </td>
                <td><?= $row['cancellation_reason'] ? htmlspecialchars($row['cancellation_reason']) : 'N/A' ?></td>
                <td>
                    <?php if ($row['status'] == 'Booked' && $allowCancellation): ?>
                        <button class="btn btn-danger cancel-btn" data-id="<?= $row['id'] ?>">Cancel</button>
                    <?php elseif (!$allowCancellation): ?>
                        <span class="text-muted">Cancellation not allowed</span>
                    <?php else: ?>
                        <button class="btn btn-secondary" disabled>Cancelled</button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
document.querySelectorAll('.cancel-btn').forEach(button => {
    button.addEventListener('click', function () {
        let bookingId = this.getAttribute('data-id');

        Swal.fire({
            title: "Cancel Ticket",
            html: `
                <p>Please select a reason for cancellation:</p>
                <div style="text-align: left;">
                    <label><input type="radio" name="cancel_reason" value="Change of plans"> Change of plans</label><br>
                    <label><input type="radio" name="cancel_reason" value="Health issues"> Health issues</label><br>
                    <label><input type="radio" name="cancel_reason" value="Booking mistake"> Booking mistake</label><br>
                    <label><input type="radio" name="cancel_reason" value="Show timing issue"> Show timing issue</label><br>
                    <label><input type="radio" name="cancel_reason" value="Other"> Other (Specify below)</label><br>
                    <input type="text" id="customReason" class="swal2-input" placeholder="Enter your reason..." style="display:none;">
                </div>
            `,
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, Cancel it!",
            preConfirm: () => {
                let selectedReason = document.querySelector('input[name="cancel_reason"]:checked');
                let customReason = document.getElementById('customReason').value.trim();

                if (!selectedReason) {
                    Swal.showValidationMessage("Please select a reason for cancellation.");
                    return false;
                }

                if (selectedReason.value === "Other" && !customReason) {
                    Swal.showValidationMessage("Please specify your reason.");
                    return false;
                }

                return selectedReason.value === "Other" ? customReason : selectedReason.value;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                let cancellationReason = result.value;

                fetch('cancel_ticket.php', {
                    method: 'POST',
                    body: new URLSearchParams({ 
                        booking_id: bookingId, 
                        cancellation_reason: cancellationReason 
                    }),
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
                })
                .then(response => response.json())
                .then(data => {
                    Swal.fire({
                        title: data.status === "success" ? "Cancelled!" : "Error",
                        text: data.message,
                        icon: data.status === "success" ? "success" : "error"
                    }).then(() => {
                        if (data.status === "success") {
                            location.reload();
                        }
                    });
                });
            }
        });

        // Show/hide custom reason input when "Other" is selected
        document.querySelectorAll('input[name="cancel_reason"]').forEach(radio => {
            radio.addEventListener('change', function () {
                document.getElementById('customReason').style.display = this.value === "Other" ? "block" : "none";
            });
        });
    });
});
</script>


</body>
</html>

<?php $stmt->close(); include 'includes/footer.php'; ?>
