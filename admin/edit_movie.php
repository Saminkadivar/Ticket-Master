<?php
include('connection.php');
include('includes/sidebar.php'); 

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Fetch movie details
    $sql = "SELECT * FROM movies WHERE id = $id";
    $result = $conn->query($sql);

    if (!$result) {
        die("SQL Error: " . $conn->error);
    }

    if ($result->num_rows > 0) {
        $movie = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger'>Movie not found.</div>";
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $genre = $_POST['genre'];
    $release_date = $_POST['release_date'];
    $duration = $_POST['duration'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $trailer_link = $_POST['trailer_link'];
    $image = $movie['image'];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = 'uploads/';
        $image_name = basename($_FILES['image']['name']);
        $image_path = $upload_dir . $image_name;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $image_path)) {
            $image = $image_path;
        } else {
            echo "<div class='alert alert-danger'>Error uploading image.</div>";
        }
    }

    // Update movie details
    $sql = "UPDATE movies SET 
            title='$title', 
            genre='$genre', 
            release_date='$release_date', 
            duration=$duration, 
            description='$description', 
            status='$status', 
            image='$image', 
            trailer_link='$trailer_link' 
            WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>Movie updated successfully!</div>";
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
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <h2 class="text-center">Edit Movie</h2>
    <form method="POST" action="edit_movie.php?id=<?php echo isset($movie['id']) ? $movie['id'] : ''; ?>" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" 
                   value="<?php echo htmlspecialchars($movie['title']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="genre" class="form-label">Genre</label>
            <input type="text" class="form-control" id="genre" name="genre" 
                   value="<?php echo htmlspecialchars($movie['genre']); ?>">
        </div>
        <div class="mb-3">
            <label for="release_date" class="form-label">Release Date</label>
            <input type="date" class="form-control" id="release_date" name="release_date" 
                   value="<?php echo htmlspecialchars($movie['release_date']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="duration" class="form-label">Duration (mins)</label>
            <input type="number" class="form-control" id="duration" name="duration" 
                   value="<?php echo htmlspecialchars($movie['duration']); ?>" required>
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
        
        <div class="mb-3">
            <label for="trailer_link" class="form-label">Trailer Link</label>
            <input type="url" class="form-control" id="trailer_link" name="trailer_link" 
                   value="<?php echo htmlspecialchars($movie['trailer_link']); ?>" 
                   placeholder="Enter YouTube/Vimeo link">
        </div>

        <div class="mb-3">
    <label class="form-label">Current Image</label><br>
    <?php 
        $imagePath = htmlspecialchars($movie['image']); 
        $defaultImage = 'uploads/default.gif'; 

        // Check if the image exists in the folder
        if (!empty($imagePath) && file_exists($imagePath)) {
            $displayImage = $imagePath;
        } else {
            $displayImage = $defaultImage;
        }
    ?>
    <img src="<?php echo $displayImage; ?>" class="movie-image" alt="Movie Image">
</div>

        <div class="mb-3">
            <label for="image" class="form-label">Upload New Image (Optional)</label>
            <input type="file" class="form-control" id="image" name="image">
        </div>

        <button type="submit" class="btn btn-primary">Update Movie</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
