<?php
include ('includes/header.php');
include ('carousel.php'); 
include('connection.php'); // Include database connection

// Fetch Upcoming Movies
$sqlUpcoming = "SELECT * FROM movies WHERE status = 'active' AND release_date > CURDATE() ORDER BY release_date ASC";
$resultUpcoming = $conn->query($sqlUpcoming);

// Fetch Released Movies
$sqlReleased = "SELECT * FROM movies WHERE status = 'active' AND release_date <= CURDATE() ORDER BY release_date DESC";
$resultReleased = $conn->query($sqlReleased);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="style.css"> -->
    <title>Movies</title>
    <style>
        body {
            background-color: rgb(232, 225, 225);
            color: #F5F5F5;
            font-family: 'Roboto', sans-serif;
        }

        /* .navbar-custom {
            background-color: #001F3F;
        }

        .navbar-custom a {
            color: #FFD700;
        }

        .navbar-custom a:hover {
            color: #F5F5F5;
        } */

        .movie-card {
            width: 100%;
            height: 500px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            border-radius: 15px;
            overflow: hidden;
            background-color: #001F3F;
            transition: transform 0.3s ease-in-out;
        }

        .movie-card:hover {
            transform: scale(1.05);
        }

        .movie-image {
            width: 90%;
            height: auto;
            max-height: 400px;
            object-fit: cover;
            padding: 2px;
            background-color: #222;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(7px);
            animation: fadeIn 1s ease-out;
            transition: transform 0.3s ease-in-out;
        }

        .movie-image:hover {
            transform: scale(1.1);
        }

        .card-body {
            flex-grow: 1;
            width: 100%;
            text-align: center;
            background-color: #001F3F;
            color: #F5F5F5;
            padding: 15px;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #FFD700;
        }

        .card-body p {
            font-size: 0.9rem;
            color: #F5F5F5;
            margin-bottom: 5px;
        }

        footer {
            background-color: #001F3F;
            color: #F5F5F5;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Upcoming Movies Section -->
    <h2 class="my-4 text-center text-black">Upcoming Movies</h2>
    <div class="row g-5">
        <?php
        if ($resultUpcoming->num_rows > 0) {
            while ($movie = $resultUpcoming->fetch_assoc()) {
                $image_path = 'admin/' . htmlspecialchars($movie['image']);
                if (!file_exists(__DIR__ . '/' . $image_path) || empty($movie['image'])) {
                    $image_path = 'admin/uploads/defult.gif';
                }
                ?>
                <div class="col-md-4">
                    <div class="card movie-card" onclick="window.location.href='movie_details.php?id=<?php echo $movie['id']; ?>'">
                        <img src="<?php echo $image_path; ?>" class="card-img-top movie-image" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($movie['title']); ?></h5>
                            <p class="card-text"><strong>Genre:</strong> <?php echo htmlspecialchars($movie['genre']); ?></p>
                            <p class="card-text"><strong>Release Date:</strong> <?php echo htmlspecialchars($movie['release_date']); ?></p>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p class='text-center text-white'>No upcoming movies available.</p>";
        }
        ?>
    </div>

    <!-- Released Movies Section -->
    <h2 class="my-4 text-center text-black">Released Movies</h2>
    <div class="row g-5">
        <?php
        if ($resultReleased->num_rows > 0) {
            while ($movie = $resultReleased->fetch_assoc()) {
                $image_path = 'admin/' . htmlspecialchars($movie['image']);
                if (!file_exists(__DIR__ . '/' . $image_path) || empty($movie['image'])) {
                    $image_path = 'admin/uploads/default.gif';
                }
                ?>
                <div class="col-md-4">
                    <div class="card movie-card" onclick="window.location.href='movie_details.php?id=<?php echo $movie['id']; ?>'">
                        <img src="<?php echo $image_path; ?>" class="card-img-top movie-image" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($movie['title']); ?></h5>
                            <p class="card-text"><strong>Genre:</strong> <?php echo htmlspecialchars($movie['genre']); ?></p>
                            <p class="card-text"><strong>Release Date:</strong> <?php echo htmlspecialchars($movie['release_date']); ?></p>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p class='text-center text-white'>No released movies available.</p>";
        }
        ?>
    </div>
</div>
<hr>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();

include 'includes/footer.php';
?>
