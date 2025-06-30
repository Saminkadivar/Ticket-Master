<?php
include('includes/header.php');
?>
<?php
include('connection.php');

if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch user details
function fetchUserDetails($conn, $user_id) {
    $query = "SELECT Username, email, Phone_no, City, hash_password FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

$user = fetchUserDetails($conn, $user_id);

// ✅ Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $name = $_POST['Username'];
    $email = $_POST['email'];
    $mobile = $_POST['Phone_no'];
    $location = $_POST['City'];

    $sql = "UPDATE users SET Username=?, email=?, Phone_no=?, City=? WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $name, $email, $mobile, $location, $user_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: userprofile.php");
        exit();
    } else {
        $_SESSION['error'] = "Error updating profile: " . $conn->error;
    }

    $stmt->close();
}

// ✅ Handle Password Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (!password_verify($old_password, $user['hash_password'])) {
        $_SESSION['error'] = "Old password is incorrect!";
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['error'] = "New passwords do not match!";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_pass_query = "UPDATE users SET hash_password=? WHERE user_id=?";
        $stmt = $conn->prepare($update_pass_query);
        $stmt->bind_param("si", $hashed_password, $user_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Password changed successfully!";
            $user = fetchUserDetails($conn, $user_id);
        } else {
            $_SESSION['error'] = "Error updating password.";
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
    <title>User Profile</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to existing styles -->
    <style>
    /* 🎬 Background Styling */
    body {
        margin: 0;
        padding: 0;
        height: 120vh;
        justify-content: center;
        align-items: center;
        font-family: 'Arial', sans-serif;
    }

    /* Dark overlay for readability */
    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
    }

    /* 🎭 Glassmorphism Profile Box */
    .profile-container {
        width: 500px;

        max-width: 500px;
        padding: 30px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 15px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(10px);
        text-align: center;
        color: black; /* Ensures black text */
    }

    h2, h3, .form-label {
        color: black; /* Ensures all text is black */
    }

    .form-control {
        width: 450px;
        padding: 10px;
        border-radius: 5px;
        background: rgba(255, 255, 255, 0.3);
        border: 1px solid rgba(0, 0, 0, 0.3);
        color: black;
        margin-top: 5px;
    }

    .form-control:focus {
        box-shadow: 0 0 5px rgba(0, 31, 63, 0.8);
        border-color: #001F3F;
        background: rgba(255, 255, 255, 0.4);
        outline: none;
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
        margin-top: 15px;
        cursor: pointer;
        border: none;
    }

    .btn-custom:hover {
        background-color: #002855;
    }

    /* 🔹 Alert Messages */
    .alert {
        margin-bottom: 20px;
        padding: 10px;
        background-color: rgba(255, 0, 0, 0.8);
        color: white;
        border-radius: 5px;
    }

    /* 🔹 Centering the Form */
    .center-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        width: 100%;
    }
</style>

<body>
    <br>
    <br>
    <div class="overlay"></div>

    <div class="center-container">
        <div class="profile-container">
            <h2>User Profile</h2>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert" style="background: green;"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            <hr>
            <!-- Profile Update Form -->
            <form action="" method="post">
                <label class="form-label">Username:</label>
                <input type="text" class="form-control" name="Username" value="<?php echo htmlspecialchars($user['Username']); ?>" required>

                <label class="form-label">Email:</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                <label class="form-label">Mobile:</label>
                <input type="text" class="form-control" name="Phone_no" value="<?php echo htmlspecialchars($user['Phone_no']); ?>" required>

                <label class="form-label">Location:</label>
                <input type="text" class="form-control" name="City" value="<?php echo htmlspecialchars($user['City']); ?>" required>

                <button type="submit" name="update_profile" class="btn-custom">Update Profile</button>
            </form>
          <br>
            <hr>
            <!-- Password Change Form -->
            <h3>Change Password</h3>
            <form action="" method="post">
                <input type="password" class="form-control" name="old_password" placeholder="Old Password" required>
                <input type="password" class="form-control" name="new_password" placeholder="New Password" required>
                <input type="password" class="form-control" name="confirm_password" placeholder="Confirm New Password" required>
                <button type="submit" name="change_password" class="btn-custom">Change Password</button>
            </form>
        </div>
    </div>
</body>
