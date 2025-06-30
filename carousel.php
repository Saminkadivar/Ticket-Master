<?php
include('connection.php'); // Include database connection

$query = "SELECT * FROM movies ORDER BY id DESC LIMIT 8"; // Fetch latest 8 movies
$result = mysqli_query($conn, $query);

$totalMovies = mysqli_num_rows($result);
$itemsPerSlide = 4; // Show 4 movies per slide on large screens
$numSlides = ceil($totalMovies / $itemsPerSlide);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Carousel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* Movie Card Styling */
      

        .movie-card:hover {
            transform: scale(1.05);
            box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.3);
        }

        .movie-img {
            width: 100%;
            height:500px;
            max-height: 500px; /* Ensures a uniform height */
            object-fit: cover;
            border-radius: 10px;
        }

        .carousel-item {
            padding: 10px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .movie-img {
                max-height: 200px; /* Reduce image height for mobile */
            }
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <div id="movieCarousel" class="carousel slide" data-bs-ride="carousel">
        <!-- Carousel Indicators -->
        <div class="carousel-indicators">
            <?php for ($i = 0; $i < $numSlides; $i++) { ?>
                <button type="button" data-bs-target="#movieCarousel" data-bs-slide-to="<?php echo $i; ?>"
                    <?php echo ($i == 0) ? 'class="active"' : ''; ?> aria-label="Slide <?php echo ($i + 1); ?>">
                </button>
            <?php } ?>
        </div>

        <!-- Carousel Items -->
        <div class="carousel-inner">
            <?php
            mysqli_data_seek($result, 0); // Reset result set pointer
            $counter = 0;
            $first = true;

            while ($row = mysqli_fetch_assoc($result)) {
                if ($counter % $itemsPerSlide == 0) {
                    if (!$first) echo '</div></div>'; // Close previous row and carousel-item
                    ?>
                    <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                        <div class="row justify-content-center">
                    <?php
                    $first = false;
                }

                // Handle image path correctly
                $image_src = 'admin/' . htmlspecialchars($row['image']);
                if (!file_exists(__DIR__ . '/' . $image_src) || empty($row['image'])) {
                    $image_src = 'admin/uploads/default.gif'; // Default image if missing
                }
            ?>
                <div class="col-6 col-md-3 mb-4"> <!-- 2 per row on mobile, 4 per row on large screens -->
                    <a href="movie_details.php?id=<?php echo $row['id']; ?>" class="movie-link">
                        <div class="">
                            <img src="<?php echo $image_src; ?>" class="movie-img" alt="Movie Image">
                        </div>
                    </a>
                </div>
            <?php
                $counter++;
            }
            ?>
            </div></div> <!-- Close last row and carousel-item -->
        </div>

        <!-- Carousel Controls (Next/Prev) -->
        <button class="carousel-control-prev" type="button" data-bs-target="#movieCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#movieCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
