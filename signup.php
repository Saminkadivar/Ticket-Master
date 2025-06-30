<?php
session_start(); // Start the session
include('connection.php');

if (isset($_POST['submit'])) {
    // Get the POST data
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $city = $_POST['city'];
    $phone_no = $_POST['phone'];
    $created_at = date('Y-m-d H:i:s'); // Set the created_at to the current date and time

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.');</script>";
    } else {
        // Check if email or username already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR Username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('Email or Username already exists.');</script>";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Prepare the SQL query to insert data into the users table
            $stmt = $conn->prepare("INSERT INTO users (email, hash_password, Username, First_name, Last_name, City, Phone_no, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssss", $email, $hashed_password, $username, $first_name, $last_name, $city, $phone_no, $created_at);

            // Execute the query
            if ($stmt->execute()) {
                // Store the user's name in the session
                $_SESSION['first_name'] = $first_name;
                
                // Show success alert and redirect to index.php
                echo "<script>
                        alert('User successfully signed up!');
                        window.location.href = 'index.php'; // Redirect to login page
                      </script>";
                exit();
            } else {
                echo "<script>alert('Data is not inserted. Error: " . $stmt->error . "');</script>";
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
    /* 🎬 Background Styling */
    body {
        margin: 0;
        padding: 0;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        background: url('admin/uploads/p5.jpg') no-repeat center center / cover;
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

    /* 🎭 Glassmorphism Signup Box */
   
    .container {
            max-width: 500px;
            margin-top: 50px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1); 
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
                        animation: fadeIn 1s ease-out;
            position: relative;
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

    /* 🔹 Links */
    .text-link {
        color: #ffffff;
        text-decoration: none;
    }

    .text-link:hover {
        text-decoration: underline;
    }
    </style>
</head>
<body>
    <div class="overlay"></div>
    <div class="container mt-5">
        <a href="index.php" class="back-arrow text-link"><i class="bi bi-arrow-left-circle"></i> Back to Login</a>
        <h2>User Sign Up</h2>
        <form action="signup.php" method="POST">
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
            </div>
            <div class="mb-3">
                <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
            </div>
            <div class="mb-3">
                <input type="number" name="phone" class="form-control" placeholder="Phone Number" required>
            </div>
            <div class="mb-3">
                <input type="text" name="city" class="form-control" placeholder="City" required>
            </div>
            <div class="mb-3">
                <input type="email" class="form-control" name="email" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" required>
            </div>
            <button type="submit" name="submit" value="signup" class="btn btn-custom w-100">Sign Up</button>
            <p class="text-center mt-3">Already have an account? <a href="index.php" class="text-link">Login</a></p>
        </form>
    </div>
</body>
</html>
