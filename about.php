<?php
session_start(); // Start the session at the beginning of the script
include ('includes/header.php');

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = 'Guest'; // Default user
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About Us - Ticket Master</title>

<!-- Bootstrap & Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<!-- Custom Styles -->
<style>
/* General Styles */
body {
    background-color: #f0f8ff; /* Light background for contrast */
    color: #ffffff;
    font-family: Arial, sans-serif;
}



/* Hero Section */
.hero {
    background-color: #001F3F;
    padding: 60px 20px;
    text-align: center;
}
.hero h2 {
    font-size: 2.5rem;
    color: #FFD700; /* Gold */
    font-weight: bold;
}

/* Content Sections */
.container {
    margin-top: 40px;
}
.section {
    margin-bottom: 40px;
    padding: 30px;
    border-radius: 10px;
    background-color: #003366; /* Slightly lighter deep blue */
}
.section h2 {
    font-size: 2rem;
    color: #FFD700; /* Gold */
    margin-bottom: 20px;
}
.section p {
    font-size: 1.1rem;
    line-height: 1.8;
}
.section img {
    width: 100%;
    border-radius: 10px;
    margin-top: 20px;
}

/* Contact Info */
.contact-info a {
    color: #FFD700;
    text-decoration: none;
    font-weight: bold;
}
.contact-info a:hover {
    text-decoration: underline;
}


.footer a:hover {
    text-decoration: underline;
}
</style>
</head>

<body>

<!-- Navbar
<header>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="logo.png" alt="Ticket Master Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">HOME</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="about.php">ABOUT US</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout (<?php //echo htmlspecialchars($_SESSION['username']); ?>)</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header> -->



<!-- Main Content -->
<div class="container">

    <!-- About Ticket Master -->
    <div class="section">
        <h2>About Ticket Master</h2>
        <p>Ticket Master is the ultimate destination for booking your favorite movie tickets with ease and security. We aim to provide seamless access to entertainment while ensuring a hassle-free booking experience.</p>
    </div>

    <!-- Our Mission -->
    <div class="section">
        <h2>Our Mission</h2>
        <p>At Ticket Master, we believe in creating unforgettable experiences for our users. Our mission is to bridge the gap between movie enthusiasts and seamless ticketing, making entertainment accessible at the click of a button.</p>
    </div>

    <!-- Contact Information -->
    <div class="section contact-info">
        <h2>Contact Us</h2>
        <p>If you have any questions or need assistance, feel free to get in touch:</p>
        <p>Email: <a href="mailto:support@ticketmaster.com">support@ticketmaster.com</a></p>
        <p>Phone: <a href="tel:+918200026182">+91 8200026182</a></p>
        <p>For more information, visit our <a href="index.php" target="_blank">official website</a>.</p>
    </div>

</div>



</body>
</html>
<?php
include 'includes/footer.php';
?>