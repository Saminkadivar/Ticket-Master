<?php
$servername = "localhost";
$username = "root";  // Update with your MySQL username
$password = "";      // Update with your MySQL password
$dbname = "ticketmaster"; // Update with your database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
//   $conn = mysqli_connect("localhost", "root", "", "mini_project");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

