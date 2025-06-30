<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php'; 
include 'includes/connection.php';

// Function to send email notifications
function sendEmail($email, $owner_name, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
       // Google SMTP Configuration
       $mail->isSMTP();
       $mail->Host = 'smtp.gmail.com';  // Google's SMTP server
       $mail->SMTPAuth = true;
       $mail->Username = 'saminkadivar2911@gmail.com';  // Your Gmail email address
       $mail->Password = 'baeq zzsa ofmq mkop';  // Your Gmail email password or app-specific password
       $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
       $mail->Port = 587;

        $mail->setFrom('your-email@gmail.com', 'Movie Booking Admin');
        $mail->addAddress($email, $owner_name);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Handle Theater Registration
if (isset($_POST['request_theater_account'])) {
    $owner_name = $_POST['owner_name'];
    $email = $_POST['email'];
    $owner_phone = $_POST['owner_phone'];
    $name = $_POST['name'];
    $location = $_POST['location'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check_email = $conn->prepare("SELECT id FROM theaters WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    if ($check_email->num_rows > 0) {
        echo "<script>alert('Email already exists. Please use a different email.'); window.location.href='theater_registration_form.php';</script>";
        exit();
    }

    $query = "INSERT INTO theaters (owner_name, email, owner_phone, name, location, password, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $owner_name, $email, $owner_phone, $name, $location, $password);

    if ($stmt->execute()) {
        $subject = 'Theater Account Request Received';
        $body = "<p>Dear $owner_name,</p>
                 <p>Thank you for requesting a theater account. Your request is currently <b>pending approval</b>.</p>
                 <p><b>Theater Name:</b> $name</p>
                 <p><b>Location:</b> $location</p>
                 <p><b>Email:</b> $email</p>
                 <p><b>Phone Number:</b> $owner_phone</p>
                 <p>Best Regards,<br>Movie Booking Team</p>";
        sendEmail($email, $owner_name, $subject, $body);

        echo "<script>alert('Request submitted successfully! Check your email for updates.'); window.location.href='login.php';</script>";
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Handle Admin Approval/Rejection
if (isset($_POST['approve']) || isset($_POST['reject'])) {
    $id = intval($_POST['theater_id']);
    $status = isset($_POST['approve']) ? 'approved' : 'rejected';

    $check_query = "SELECT * FROM theaters WHERE id=$id";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        $row = $check_result->fetch_assoc();
        $email = $row['email'];
        $owner_name = $row['owner_name'];
        $theater_name = $row['name'];
        
        $update_query = "UPDATE theaters SET status='$status' WHERE id=$id";
        if ($conn->query($update_query)) {
            $subject = "Theater Approval Status Update";
            if ($status == 'approved') {
                $body = "<p>Dear $owner_name,</p>
                         <p>Your theater '$theater_name' has been <b>APPROVED</b>! 🎉</p>
                         <p>You can now list your movies and manage bookings.</p>
                         <p>Best Regards,<br>Admin Team</p>";
            } else {
                $body = "<p>Dear $owner_name,</p>
                         <p>We regret to inform you that your theater '$theater_name' has been <b>REJECTED</b>.</p>
                         <p>Please contact support for further details.</p>
                         <p>Best Regards,<br>Admin Team</p>";
            }
            sendEmail($email, $owner_name, $subject, $body);
            echo "<script>alert('Theater status updated successfully.'); window.location.href='admin_dashboard.php';</script>";
        } else {
            echo "Error updating status: " . $conn->error;
        }
    }
}
?>
