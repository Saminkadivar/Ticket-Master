<?php
// Database Connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'ticketmaster';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}
// Ensure a theater is logged in
if (!isset($_SESSION['theater_id'])) {
    header("Location: login.php");
    exit();
}
session_start();

// Theater Registration
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['owner_name'];
    $email = $_POST['email'];
    $phone = $_POST['owner_phone'];
    $theater_name = $_POST['name'];
    $location = $_POST['location']; // Added location field
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    $query = "INSERT INTO theaters (owner_name, email, owner_phone, name, location, password, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $name, $email, $phone, $theater_name, $location, $password);
    $stmt->execute();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theater Registration</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h2>Theater Registration</h2>
            <form method="POST">
                <label>Name:</label>
                <input type="text" name="owner_name" required>
                
                <label>Email:</label>
                <input type="email" name="email" required>
                
                <label>Phone:</label>
                <input type="text" name="owner_phone" required>
                
                <label>Theater Name:</label>
                <input type="text" name="name" required>
                
                <label>Location:</label>  <!-- Added location input -->
                <input type="text" name="location" required>
                
                <label>Password:</label>
                <input type="password" name="password" required>
                
                <button type="submit">Register</button>
            </form>
        </div>
    </div>
</body>
</html>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    body {
        font-family: Arial, sans-serif;
        background-color: #f1f1f1;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 100%;
    }
    .login-card {
        background-color: #ffffff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
    }
    h2 {
        font-size: 24px;
        margin-bottom: 20px;
        color: #333;
        text-align: center;
    }
    label {
        font-size: 14px;
        margin-bottom: 5px;
        display: block;
        color: #555;
    }
    input {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
        outline: none;
    }
    input:focus {
        border-color: #3aafa9;
    }
    button {
        width: 100%;
        padding: 12px;
        background-color: #3aafa9;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    button:hover {
        background-color: #319a8a;
    }
</style>
