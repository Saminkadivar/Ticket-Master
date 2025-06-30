<?php
session_start();
require "includes/header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cancellation Policy - Ticket Master</title>

<!-- Bootstrap & Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

<!-- Custom Styles -->
<link rel="stylesheet" href="Style.css">
<style>
body {
    background-color: #001F3F; /* Deep Blue */
    color: #F5F5F5; /* Light White */
    font-family: Arial, sans-serif;
}

.container {
    margin-top: 40px;
    max-width: 900px;
}

.section {
    padding: 30px;
    border-radius: 10px;
    background-color: #003366; /* Slightly lighter blue */
    margin-bottom: 20px;
}

.section h2 {
    font-size: 2rem;
    color: #FFD700; /* Gold */
    text-align: center;
    margin-bottom: 20px;
}

.section h3 {
    color: #FFA500; /* Orange */
    margin-top: 15px;
}

.section p {
    font-size: 1.1rem;
    line-height: 1.6;
}

.section ul {
    list-style-type: none;
    padding: 0;
}

.section ul li {
    font-size: 1.1rem;
    margin-bottom: 8px;
}

.section a {
    color: #FFD700;
    text-decoration: none;
}

.section a:hover {
    text-decoration: underline;
}
</style>
</head>

<body>

<!-- Main Content -->
<div class="container">
    <div class="section">
        <h2>🚫 Cancellation & Refund Policy</h2>
        <p>We understand that plans can change. Below is our **ticket cancellation policy**:</p>

        <h3>1️⃣ Cancellation Guidelines</h3>
        <ul>
            <li>✔️ More than 12 hours before showtime: Eligible for a **full refund**.</li>
            <li>✔️ Between 12 hours and 15 minutes before showtime: Eligible for a **50% refund**.</li>
            <li>❌ Less than 15 minutes before showtime: Cancellation **not allowed**, no refund available.</li>
        </ul>

        <h3>2️⃣ Refund Process</h3>
        <p>- Refunds will be processed **within 3-5 business days** after cancellation.</p>
        <p>- The refunded amount will be credited **back to the original payment method**.</p>
        <p>- A No **cancellation fee** applies to full refunds.</p>

        <h3>3️⃣ Non-Refundable Situations</h3>
        <ul>
            <li>❌ If the show starts within **15 minutes**, cancellation is **not allowed**.</li>
            <li>❌ No refund for **missed events**.</li>
        </ul>

        <h3>4️⃣ How to Cancel Your Ticket?</h3>
        <ul>
            <li>➡️ Visit your <a href="user_bookings.php">My Bookings</a> page.</li>
            <li>➡️ Click on **Cancel Ticket** for the booking you want to cancel.</li>
            <li>➡️ Refunds (if applicable) will be processed automatically.</li>
        </ul>

        <h3>5️⃣ Need Help?</h3>
        <p>For any issues, reach out to us:</p>
        <p>📧 Email: <a href="mailto:support@ticketmaster.com">support@ticketmaster.com</a></p>
        <p>📞 Phone: <a href="tel:+918200026182">+91 8200026182</a></p>
    </div>
</div>

</body>
</html>

<?php 
require "includes/footer.php";
?>
