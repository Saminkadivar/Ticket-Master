<?php
include('includes/connection.php');
include('includes/sidebar.php');
// Ensure a theater is logged in
if (!isset($_SESSION['theater_id'])) {
    header("Location: login.php");
    exit();
}
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch movie details
    $stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $movie = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger'>Movie not found.</div>";
        exit;
    }
} else {
    echo "<div class='alert alert-danger'>Invalid request. Movie ID missing or invalid.</div>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $genre = $_POST['genre'];
    $release_date = $_POST['release_date'];
    $duration = $_POST['duration'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $trailer_link = $_POST['trailer_link'];
    $image = $movie['image']; // Preserve old image if no new upload

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp','avif'];
        $upload_dir = '../admin/uploads/';
        $image_name = basename($_FILES['image']['name']);
        $image_path = $upload_dir . $image_name;
        $file_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_types)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
                $image = 'uploads/' . $image_name; // Save relative path
            } else {
                echo "<div class='alert alert-danger'>Error uploading image.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>Invalid file type. Only JPG, JPEG, PNG, Webp, Avif and GIF are allowed.</div>";
        }
    }

    // Update movie details
    $stmt = $conn->prepare("UPDATE movies SET title=?, genre=?, release_date=?, duration=?, description=?, status=?, image=?, trailer_link=? WHERE id=?");
    $stmt->bind_param("sssissssi", $title, $genre, $release_date, $duration, $description, $status, $image, $trailer_link, $id);

    if ($stmt->execute()) {
        header("Location: manage_movies.php");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Error updating movie: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Movie</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        
        .main-content { flex-grow: 1; padding: 20px; background-color: #f8f9fa; }
        form { max-width: 600px; margin: 30px auto; padding: 20px; background: #fff; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        label { font-weight: bold; margin-bottom: 8px; color: #333; }
        input, textarea, select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        input:focus, textarea:focus, select:focus { border-color: #3aafa9; outline: none; }
        textarea { height: 120px; resize: vertical; }
        button { background-color: #3aafa9; color: white; padding: 10px 200px; font-size: 16px; border: none; border-radius: 5px; cursor: pointer; width: 100%; transition: background-color 0.3s ease; }
        button:hover { background-color: #2e8b85; }
        .movie-image { max-width: 200px; max-height: 200px; object-fit: cover; margin-bottom: 10px; }
    </style>
</head>
<body>

<!-- <div class="d-flex"> -->
    <div class="main-content">
        <h2>Edit Movie</h2>
        <div class="container">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($movie['title']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="genre" class="form-label">Genre</label>
                    <input type="text" class="form-control" id="genre" name="genre" value="<?php echo htmlspecialchars($movie['genre']); ?>">
                </div>
                <div class="mb-3">
                    <label for="release_date" class="form-label">Release Date</label>
                    <input type="date" class="form-control" id="release_date" name="release_date" value="<?php echo htmlspecialchars($movie['release_date']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="duration" class="form-label">Duration (mins)</label>
                    <input type="number" class="form-control" id="duration" name="duration" value="<?php echo htmlspecialchars($movie['duration']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description"><?php echo htmlspecialchars($movie['description']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control" id="status" name="status">
                        <option value="active" <?php if ($movie['status'] == 'active') echo 'selected'; ?>>Active</option>
                        <option value="inactive" <?php if ($movie['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
                    </select>
                </div>
                
                <!-- Trailer Link -->
                <div class="mb-3">
                    <label for="trailer_link" class="form-label">Trailer Link</label>
                    <input type="text" class="form-control" id="trailer_link" name="trailer_link" value="<?php echo htmlspecialchars($movie['trailer_link'] ?? ''); ?>">
                </div>

                <!-- Current Image -->
                <div class="mb-3">
                    <label class="form-label">Current Image</label><br>
                    <?php 
                        $imagePath = "../admin/" . htmlspecialchars($movie['image']);
                        $defaultImage = "../admin/uploads/default.gif"; // Default image path

                        // Check if the image file exists
                        if (!empty($movie['image']) && file_exists($imagePath)) {
                            $displayImage = $imagePath;
                        } else {
                            $displayImage = $defaultImage;
                        }
                    ?>
                    <img src="<?php echo $displayImage; ?>" class="movie-image" alt="Movie Image">
                </div>

                <!-- Image Upload -->
                <div class="mb-3">
                    <label for="image" class="form-label">Upload New Image (Optional)</label>
                    <input type="file" class="form-control" id="image" name="image">
                </div>

                <button type="submit">Update Movie</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
