<?php
session_start();
if (!isset($_SESSION['login'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}
?>
