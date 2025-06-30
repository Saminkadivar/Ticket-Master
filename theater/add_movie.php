<?php
session_start();
include('includes/connection.php');
include('includes/sidebar.php');

// Ensure a theater is logged in
if (!isset($_SESSION['theater_id'])) {
    header("Location: login.php");
    exit();
}

$theater_id = $_SESSION['theater_id']; // Get logged-in theater's ID

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $genre = trim($_POST['genre']);
    $release_date = $_POST['release_date'];
    $duration = intval($_POST['duration']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];
    $trailer_link = trim($_POST['trailer_link']);
    $image = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];
        $upload_dir = '../admin/uploads/';
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        $image_path = $upload_dir . $image_name;
        $file_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_types)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                $image = 'uploads/' . $image_name; // Save relative path
            } else {
                echo "<div class='alert alert-danger'>Error uploading image.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Invalid file type. Only JPG, JPEG, PNG,avif, and GIF are allowed.</div>";
        }
    }


    // Insert into database using prepared statement
    $sql = "INSERT INTO movies (theater_id, title, genre, release_date, duration, description, status, trailer_link, image) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssissss", $theater_id, $title, $genre, $release_date, $duration, $description, $status, $trailer_link, $image);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Movie added successfully!";
        header("Location: manage_movies.php");
        exit;
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
        header("Location: add_movie.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Movie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
        }
        .form-container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<div class="content">
    <div class="form-container">
        <h2 class="mb-4 text-center">Add New Movie</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form method="POST" action="add_movie.php" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Movie Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Genre</label>
                <input type="text" name="genre" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Release Date</label>
                <input type="date" name="release_date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Duration (minutes)</label>
                <input type="number" name="duration" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Trailer Link (YouTube/Vimeo)</label>
                <input type="url" name="trailer_link" class="form-control" placeholder="https://www.youtube.com/watch?v=example">
            </div>

            <div class="mb-3">
                <label class="form-label">Movie Image</label>
                <input type="file" name="image" class="form-control" accept="image/*" required>
            </div>

            <button type="submit" class="btn btn-success w-100">Add Movie</button>
            <a href="manage_movies.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
