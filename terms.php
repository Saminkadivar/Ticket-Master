<?php
session_start();
require "includes/header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Terms & Conditions - Ticket Master</title>

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
        <h2>📜 Terms & Conditions</h2>
        <p>By accessing or using <b>Ticket Master</b>, you agree to comply with these terms. Please read them carefully before making any bookings.</p>

        <h3>1️⃣ Ticket Booking & Payments</h3>
        <ul>
            <li>✔️ All ticket purchases are **final** and subject to availability.</li>
            <li>✔️ Prices may fluctuate based on demand. We are not responsible for price variations.</li>
            <li>✔️ Refunds and cancellations are processed as per our <a href="cancellation-policy.php">Cancellation Policy</a>.</li>
            <li>✔️ Payments are securely processed through our payment system.</li>
        </ul>

        <h3>2️⃣ User Responsibilities</h3>
        <ul>
            <li>✔️ Users must provide **accurate and truthful information** while booking.</li>
            <li>✔️ Any **fraudulent activity, misuse, or ticket resale** is strictly prohibited and may lead to legal action.</li>
            <li>✔️ Users are responsible for maintaining the confidentiality of their account credentials.</li>
        </ul>

        <h3>3️⃣ Event Cancellations & Rescheduling</h3>
        <ul>
            <li>✔️ If an event is **canceled**, refunds will be processed as per the organizer’s policy.</li>
            <li>✔️ If an event is **rescheduled**, your ticket remains valid for the **new date/time**.</li>
            <li>✔️ We are not responsible for **changes made by event organizers**.</li>
        </ul>

        <h3>4️⃣ Privacy & Security</h3>
        <ul>
            <li>✔️ We prioritize your privacy and **do not sell or share** personal data with third parties.</li>
            <li>✔️ Payment transactions are **secured using encryption technology**.</li>
            <li>✔️ Users are encouraged to review our <a href="privacy-policy.php">Privacy Policy</a> for more details.</li>
        </ul>

        <h3>5️⃣ Prohibited Activities</h3>
        <ul>
            <li>🚫 Ticket **scalping, reselling, or fraudulent use** is strictly prohibited.</li>
            <li>🚫 Attempting to hack, manipulate, or disrupt our website is a **criminal offense**.</li>
            <li>🚫 Any form of **harassment, abuse, or spamming** towards our platform or users will lead to a permanent ban.</li>
        </ul>

        <h3>6️⃣ Limitation of Liability</h3>
        <p>We are not liable for any **loss, injury, or inconvenience** caused due to event changes, cancellations, or service disruptions.</p>

        <h3>7️⃣ Changes to Terms</h3>
        <p>We may update these Terms & Conditions from time to time. Any changes will be posted on this page.</p>

        <h3>8️⃣ Contact Us</h3>
        <p>If you have any questions or concerns regarding our terms, reach out to us:</p>
        <p>📧 Email: <a href="mailto:support@ticketmaster.com">support@ticketmaster.com</a></p>
        <p>📞 Phone: <a href="tel:+918200026182">+91 8200026182</a></p>

        <p><i>By continuing to use our services, you acknowledge that you have read and agreed to these terms.</i></p>
    </div>
</div>

</body>
</html>

<?php 
require "includes/footer.php";
?>
