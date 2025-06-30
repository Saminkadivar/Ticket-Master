<?php
include('connection.php');
include('includes/sidebar.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Movies</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/Style1.css"> <!-- Ensure this file contains your styles -->
    <style>
        .movie-image {
            width: 100px;
            height: 120px;
            object-fit: cover;
            border-radius: 5px;
        }
    
    h2 {
        color: #001F3F;
    }
    
      
    </style>
</head>
<body>

<div class="main-container">
    <!-- Sidebar -->

    <!-- Main Content -->
    <div class="content">
        <div class="container">
            <div class="card table-container">
                <h2>Manage Movies</h2>

                <!-- Add Movie Button -->
                <div class="mb-3 text-end">
                    <a href="add_movie.php" class="btn btn-primary">+ Add Movie</a>
                </div>

                <!-- Movie Table -->
                <?php
                $sql = "SELECT id, title, genre, release_date, duration, description, status, image FROM movies";
                $result = $conn->query($sql);

                if (!$result) {
                    echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
                } elseif ($result->num_rows > 0) {
                    echo "<table class='table table-bordered table-hover'>
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Genre</th>
                                    <th>Release Date</th>
                                    <th>Duration</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>";

                    while ($row = $result->fetch_assoc()) {
                        if (!isset($row['id'])) continue;

                        $image = $row['image'];
                        $image_path = file_exists($image) ? $image : 'uploads/default.gif'; // Fallback image

                        echo "<tr>
                                <td><img src='$image_path' class='movie-image' alt='Movie'></td>
                                <td>" . htmlspecialchars($row['title']) . "</td>
                                <td>" . htmlspecialchars($row['genre']) . "</td>
                                <td>" . htmlspecialchars($row['release_date']) . "</td>
                                <td>" . htmlspecialchars($row['duration']) . "</td>
                                <td>" . htmlspecialchars($row['description']) . "</td>
                                <td>" . htmlspecialchars($row['status']) . "</td>
                                <td class='actions'>
                                    <a href='edit_movie.php?id=" . $row['id'] . "' class='btn btn-warning btn-sm'>Edit</a> 
                                    <a href='delete_movie.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                                </td>
                              </tr>";
                    }

                    echo "</tbody></table>";
                } else {
                    echo "<div class='alert alert-warning text-center'>No movies found.</div>";
                }

                $conn->close();
                ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
