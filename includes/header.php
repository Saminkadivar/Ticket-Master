<?php
include_once('connection.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        /* Navbar Customization */
        .navbar-custom {
            background-color: #001F3F;
        }
     
        .navbar-custom a:hover {
            color: #F5F5F5;
        }
        /* Active Navbar Link */
        .navbar-nav .nav-item .nav-link.active {
            color: #F5F5F5;
        }
        /* Navbar Logo */
        .navbar-brand img {
            height: 40px;
        }
        /* Sticky Navbar */
        header {
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        /* Profile Dropdown */
        .profile-dropdown .dropdown-menu {
            background-color: #001F3F;
        }
        .profile-dropdown .dropdown-item {
            color: white;
        }
        .profile-dropdown .dropdown-item:hover {
            background-color: #002855;
        }
        /* Profile Icon */
        .profile-icon {
            font-size: 20px;
            margin-right: 5px;
        }
    </style>
</head>

<body style="background-color:#f0f8ff;">
    <header>
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">
                    <img src="img/logo1.png" alt="Ticket Master Logo" 
                        style="height: 45px; width: 80px; object-fit: cover; 
                        clip-path: polygon(10% 0%, 90% 0%, 100% 20%, 100% 80%, 90% 100%, 10% 100%, 0% 80%, 0% 20%);">
                </a>

                <!-- Search Bar -->
                <form class="d-flex mx-auto search-bar" style="max-width: 500px; width: 100%;" action="search_movies.php" method="GET">
                    <input id="locationInput" class="form-control me-2 search-input" type="text" name="query" placeholder="Search by title, genre, or release date" required>
                    <button class="btn btn-outline-light search-btn" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </form>

                <!-- Navbar Links -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="bi bi-house-door-fill"></i> HOME
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="about.php">
                            <i class="bi bi-info-circle-fill"></i> About us
                        </a>
                    </li>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link active" href="user_bookings.php">
                                <i class="bi bi-ticket-detailed-fill"></i> View My Tickets
                            </a>
                        </li>

                        <!-- Profile Dropdown (Only Visible When User is Logged In) -->
                        <li class="nav-item dropdown profile-dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle profile-icon"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="userprofile.php"><i class="bi bi-person-fill"></i> View Profile</a></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary text-white mx-2" href="login.php">
                                <i class="bi bi-person-fill"></i> Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>
</body>
</html>
