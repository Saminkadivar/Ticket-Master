<?php
session_start();
include('includes/connection.php');
include('includes/sidebar.php');

// Ensure a theater is logged in
if (!isset($_SESSION['theater_id'])) {
    header("Location: login.php");
    exit();
}
$theater_id = $_SESSION['theater_id'];

// Fetch movies added by the logged-in theater
$sql = "SELECT * FROM movies WHERE theater_id = ? ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $theater_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Movies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/Style1.css">

    <style>
        .content {
            margin-left: 250px;
            padding: 40px;
            background-color: #f4f4f4;
            min-height: 100vh;
        }
        .movie-image {
            max-width: 100px;
            height: auto;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
            background: white;
            border-radius: 5px;
            overflow: hidden;
        }
        .table thead {
            background-color: #001F3F;
            color: white;
        }
        .table th, .table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }
        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tbody tr:hover {
            background-color: #e0e0e0;
        }
    </style>
</head>
<body>

<div class="content">
    <h2 class="mb-4">Manage Movies</h2>

    <!-- Add Movie Button -->
    <a href="add_movie.php" class="btn btn-primary mb-3">Add New Movie</a>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Genre</th>
                        <th>Release Date</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Trailer</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($movie = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $movie['id']; ?></td>
                            <td><?php echo htmlspecialchars($movie['title']); ?></td>
                            <td><?php echo htmlspecialchars($movie['genre']); ?></td>
                            <td><?php echo htmlspecialchars($movie['release_date']); ?></td>
                            <td><?php echo htmlspecialchars($movie['duration']); ?> mins</td>
                            <td><?php echo htmlspecialchars($movie['status']); ?></td>
                            <td>
                                <?php if (!empty($movie['trailer_link'])): ?>
                                    <a href="<?php echo htmlspecialchars($movie['trailer_link']); ?>" target="_blank" class="btn btn-info btn-sm">Watch</a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                    $imagePath = "../admin/" . htmlspecialchars($movie['image']);
                                    $defaultImage = "../admin/uploads/default.gif"; // Ensure this image exists in the uploads folder

                                    // Check if the image file exists
                                    if (!empty($movie['image']) && file_exists($imagePath)) {
                                        $displayImage = $imagePath;
                                    } else {
                                        $displayImage = $defaultImage;
                                    }
                                ?>
                                <img src="<?php echo $displayImage; ?>" alt="Movie Image" class="movie-image">
                            </td>

                            <td>
                                <a href="edit_movie.php?id=<?php echo $movie['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="delete_movie.php?id=<?php echo $movie['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this movie?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No movies found.</div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
