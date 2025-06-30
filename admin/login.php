<?php
// Start session
session_start();

// Include database connection
include('connection.php');

// Check if the form is submitted
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to check if the username and password match
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        if ($password === $admin['password']) { // ⚠️ Consider hashing passwords for security
            $_SESSION['admin'] = $username;
            header("Location: admin.php"); // Redirect to admin dashboard
            exit();
        } else {
            $error_message = "Invalid username or password.";
        }
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
/* 🎬 Background Styling */
body {
    margin: 0;
    padding: 0;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: url('../admin/uploads/p5.jpg') no-repeat center center / cover;
    font-family: 'Arial', sans-serif;
    position: relative;
}

/* Dark overlay for readability */
.overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 31, 63, 0.85); /* Dark blue overlay */
    z-index: -1;
}

/* 🎭 Glassmorphism Login Box */
.login-container {
    max-width: 400px;
    padding: 30px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(10px);
    animation: fadeIn 1s ease-out;
    color: white;
    text-align: center;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

h2 {
    color: #ffffff;
    margin-bottom: 20px;
}

.form-label {
    font-weight: bold;
    color: #ffffff;
}

.form-control {
    border-radius: 5px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
}

.form-control:focus {
    box-shadow: 0 0 5px rgba(0, 31, 63, 0.8);
    border-color: #001F3F;
    background: rgba(255, 255, 255, 0.2);
}

/* 🔹 Button Styling */
.btn-custom {
    background-color: #001F3F; /* Dark Blue */
    color: white;
    font-weight: bold;
    width: 100%;
    padding: 10px;
    border-radius: 5px;
    transition: 0.3s ease;
}

.btn-custom:hover {
    background-color: #002855;
}

/* 🔹 Alert Messages */
.alert {
    margin-bottom: 20px;
    background-color: rgba(255, 0, 0, 0.8);
    color: white;
    border: none;
}

    </style>
</head>
<body>

<div class="overlay"></div> <!-- Dark overlay effect -->

<div class="login-container">
    <h2>Admin Login</h2>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" name="username" id="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" id="password" required>
        </div>
        <button type="submit" name="login" class="btn btn-custom">Login</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
