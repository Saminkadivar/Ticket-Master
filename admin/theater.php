<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php'; 
include('connection.php');
include('includes/sidebar.php');

function sendEmail($email, $owner_name, $theater_name, $status) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'saminkadivar2911@gmail.com'; // Replace with your email
        $mail->Password = 'baeq zzsa ofmq mkop'; // Replace with your app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        $mail->setFrom('no-reply@Ticketmaster.com', 'TicketMaster');
        $mail->addAddress($email, $owner_name);
        $mail->isHTML(true);

       
        // Theater Theme Colors
        $bgColor = "#1c1c1c"; // Dark background
        $textColor = "#ffffff"; // White text
        $highlightColor = "#FFD700"; // Gold

        if ($status == 'approved') {
            $mail->Subject = 'Your Theater has been Approved!';
            $mail->Body = "
                <div style='background: $bgColor; color: $textColor; padding: 20px; text-align: center; font-family: Arial, sans-serif;'>
                    <h2 style='color: $highlightColor;'>🎬 Congratulations, $owner_name!</h2>
                    <p>Your theater '<b style='color: $highlightColor;'>$theater_name</b>' has been <b>approved</b>!</p>
                    <p>You can now start managing movies, showtimes, and bookings.</p>
                    <hr style='border-color: $highlightColor;'>
                    <p><a href='your-website-url.com' style='background: $highlightColor; color: $bgColor; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Go to Dashboard</a></p>
                    <p style='font-size: 12px; color: #bbbbbb;'>Thank you for being a part of our movie booking platform!</p>
                </div>";
        } else {
            $mail->Subject = 'Theater Request Rejected';
            $mail->Body = "
                <div style='background: $bgColor; color: $textColor; padding: 20px; text-align: center; font-family: Arial, sans-serif;'>
                    <h2 style='color: red;'>⚠️ Hello, $owner_name</h2>
                    <p>We regret to inform you that your theater '<b style='color: $highlightColor;'>$theater_name</b>' has been <b>rejected</b>.</p>
                    <p>Please contact our support team for further details.</p>
                    <hr style='border-color: red;'>
                    <p><a href='mailto:support@your-website.com' style='background: red; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Contact Support</a></p>
                    <p style='font-size: 12px; color: #bbbbbb;'>We hope to work with you in the future.</p>
                </div>";
        }

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Handle Approval/Rejection
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['theater_id']);
    $status = isset($_POST['approve']) ? 'approved' : 'rejected';

    $query = "SELECT * FROM theaters WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['email'];
        $owner_name = $row['owner_name'];
        $theater_name = $row['name'];
        
        $update_query = "UPDATE theaters SET status=? WHERE id=?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("si", $status, $id);
        
        if ($update_stmt->execute()) {
            sendEmail($email, $owner_name, $theater_name, $status);
            echo "<script>alert('Theater $status successfully! Email sent.'); window.location.href='theater.php';</script>";
        } else {
            echo "<script>alert('Error updating theater status: " . $conn->error . "');</script>";
        }
    }
}

// Fetch Theaters List
$query = "SELECT * FROM theaters ORDER BY status ASC, name ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Theaters</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        /* Flexbox Layout */
        .main-container {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 250px;
            background-color: #003366;
            padding: 20px;
            color: white;
            flex-shrink: 0;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .table-container {
            overflow-x: auto;
        }
    </style>
</head>
<body>

<div class="main-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <?php include('includes/sidebar.php'); ?>
    </div>

    <!-- Main Content -->
    <div class="content">
        <h2 class="my-4">Manage Theaters</h2>
        <div class="table-container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Owner Name</th>
                        <th>Email</th>
                        <th>Theater Name</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['owner_name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo ($row['status'] == 'approved') ? 'success' : 
                                        (($row['status'] == 'rejected') ? 'danger' : 'warning'); ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['status'] == 'pending') { ?>
                                    <form method="POST">
                                        <input type="hidden" name="theater_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="approve" class="btn btn-success btn-sm">Approve</button>
                                        <button type="submit" name="reject" class="btn btn-danger btn-sm">Reject</button>
                                    </form>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>


