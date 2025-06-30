<?php
include('connection.php');
include('includes/sidebar.php');
session_start();

// Fetch refund-eligible bookings (Cancelled status)
$sql = "SELECT b.id as booking_id, b.User_id, b.payment_id, b.amount, b.show_date, b.show_time, u.Username 
        FROM bookings b 
        LEFT JOIN users u ON b.User_id = u.User_id 
        WHERE b.status = 'Cancelled' 
        ORDER BY b.booking_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Refund Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="css/Style1.css"> <!-- Ensure this file contains your styles -->

    <style>
    

        /* SweetAlert button custom style */
        .swal2-confirm {
            background-color: green !important;
            color: white !important;
        }

        .swal2-cancel {
            background-color: #FF4136 !important;
            color: white !important;
        }
        .btn2{
            background-color: #001F3F !important;
            color: white !important;

        }
    </style>
</head>
<body>


<div class="content">
    <h2 class="my-4">Pending Refunds</h2>
    <div class="container">
    <div class="card">

    <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Show Date</th>
                    <th>Show Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['booking_id'] ?></td>
                        <td><?= htmlspecialchars($row['Username']) ?></td>
                        <td>₹<?= number_format($row['amount'], 2) ?></td>
                        <td><?= $row['show_date'] ?></td>
                        <td><?= $row['show_time'] ?></td>
                        <td>
                            <button class="btn btn2 refund-btn"
                                    data-booking-id="<?= $row['booking_id'] ?>"
                                    data-payment-id="<?= $row['payment_id'] ?>"
                                    data-user-id="<?= $row['User_id'] ?>"
                                    data-amount="<?= $row['amount'] ?>">
                                Process Refund
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>
</div>

<script>
document.querySelectorAll('.refund-btn').forEach(button => {
    button.addEventListener('click', () => {
        const bookingId = button.dataset.bookingId;
        const paymentId = button.dataset.paymentId;
        const refundAmount = button.dataset.amount;
        const userId = button.dataset.userId;

        Swal.fire({
            title: "Confirm Refund",
            text: `Refund ₹${refundAmount} to user?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, refund it!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('process_refund.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'booking_id=' + encodeURIComponent(bookingId) + '&payment_id=' + encodeURIComponent(paymentId) + '&amount=' + encodeURIComponent(refundAmount) + '&user_id=' + encodeURIComponent(userId)
                })
                .then(response => response.json())  // Ensure response is parsed as JSON
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire('Success', data.message, 'success');
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Something went wrong, please try again later.', 'error');
                });
            }
        });
    });
});
</script>

</body>
</html>
