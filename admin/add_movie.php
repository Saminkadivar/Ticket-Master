<?php
include('connection.php');
include('includes/sidebar.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $genre = $_POST['genre'];
    $release_date = $_POST['release_date'];
    $duration = $_POST['duration'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $trailer_link = $_POST['trailer_link']; // Get trailer link

    // Handle image upload
    $image = $_FILES['image']['name'];
    $target_dir = "uploads/";

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $target_file = $target_dir . basename($image);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $check = getimagesize($_FILES['image']['tmp_name']);

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


    // Insert movie details into the database
    $sql = "INSERT INTO movies (title, genre, release_date, duration, description, status, image, trailer_link) 
            VALUES ('$title', '$genre', '$release_date', '$duration', '$description', '$status', '$target_file', '$trailer_link')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('New movie added successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $sql . "\\n" . $conn->error . "');</script>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/Style1.css"> <!-- Ensure the CSS file is correctly linked -->
    <title>Add Movie</title>
</head>
<body>

<div class="main-container">
    <div class="content">
        <h2>Add New Movie</h2>

        <div class="form-container">
            <form method="POST" action="add_movie.php" enctype="multipart/form-data">
                <label for="title">Movie Title:</label>
                <input type="text" id="title" name="title" required>

                <label for="genre">Genre:</label>
                <input type="text" id="genre" name="genre">

                <label for="release_date">Release Date:</label>
                <input type="date" id="release_date" name="release_date" required>

                <label for="duration">Duration (mins):</label>
                <input type="number" id="duration" name="duration" required>

                <label for="description">Description:</label>
                <textarea id="description" name="description"></textarea>

                <label for="status">Status:</label>
                <select id="status" name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>

                <label for="trailer_link">Trailer Link:</label>
                <input type="url" id="trailer_link" name="trailer_link" placeholder="Enter YouTube/Vimeo link">

                <label for="image">Movie Image:</label>
                <input type="file" id="image" name="image" accept="image/*">

                <button type="submit" class="btn-primary">Add Movie</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
