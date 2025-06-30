<?php
session_start();
require "includes/header.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Privacy Policy - Ticket Master</title>
<link rel="stylesheet" href="Style.css">

<!-- Bootstrap & Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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
        <h2>🔒 Privacy Policy</h2>
        <p>Your privacy is important to us. This policy explains how we collect, use, and protect your personal data.</p>

        <h3>1️⃣ Information We Collect</h3>
        <ul>
            <li>✔️ Name, email, and phone number when you book a ticket.</li>
            <li>✔️ Payment details are processed securely by our payment partner.</li>
            <li>✔️ Website usage data, including IP address and cookies, to improve services.</li>
        </ul>

        <h3>2️⃣ How We Use Your Information</h3>
        <ul>
            <li>✔️ To process your ticket bookings and send confirmations.</li>
            <li>✔️ To notify you about **upcoming events, offers, and updates**.</li>
            <li>✔️ To improve our services based on user interactions.</li>
        </ul>

        <h3>3️⃣ Data Protection & Security</h3>
        <ul>
            <li>✔️ Your data is **encrypted** and stored securely.</li>
            <li>✔️ We use advanced security measures to prevent unauthorized access.</li>
            <li>✔️ We do **not store sensitive payment information**.</li>
        </ul>

        <h3>4️⃣ Third-Party Sharing</h3>
        <p>We respect your privacy and **do not sell, trade, or share** your personal data with third parties, except:</p>
        <ul>
            <li>✔️ When required by law.</li>
            <li>✔️ With trusted service providers for **secure payment processing**.</li>
        </ul>

        <h3>5️⃣ Your Rights & Choices</h3>
        <ul>
            <li>✔️ You can request to access, update, or delete your personal data.</li>
            <li>✔️ You can opt out of promotional emails at any time.</li>
        </ul>

        <h3>6️⃣ Contact Us</h3>
        <p>If you have any concerns about your privacy, contact us:</p>
        <p>📧 Email: <a href="mailto:support@ticketmaster.com">support@ticketmaster.com</a></p>
        <p>📞 Phone: <a href="tel:+918200026182">+91 8200026182</a></p>

        <p><i>By using our services, you agree to this privacy policy.</i></p>
    </div>
</div>

</body>
</html>

<?php 
require "includes/footer.php";
?>
