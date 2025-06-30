<?php
include 'includes/header.php';
include 'connection.php';

// Check if ID is passed
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch movie details
    $sql = "SELECT * FROM movies WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $movie = $result->fetch_assoc();
        
        // Construct the correct image path
        $image_src = 'admin/' . htmlspecialchars($movie['image']);
        
        // Check if the image exists, otherwise use the default image
        if (!file_exists(__DIR__ . '/' . $image_src) || empty($movie['image'])) {
            $image_src = 'admin/uploads/default.gif';
        }

        // Get release date
        $release_date = strtotime($movie['release_date']);
        $current_date = time();
        $is_released = $current_date >= $release_date; // True if movie is released
    } else {
        echo "<p class='text-center text-danger'>Movie not found.</p>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($movie['title']); ?> - Movie Details</title>
    <link rel="stylesheet" href="style.css">

    <style>
        body {
            background-color: #121212;
            color: #F5F5F5;
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
        }

        .movie-container {
            display: flex;
            max-width: 900px;
            margin: 50px auto;
            background-color: #001F3F;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0px 0px 15px rgba(255, 215, 0, 0.3);
        }

        .movie-image {
            width: 40%;
            background-color: #000;
        }

        .movie-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .movie-details {
            width: 60%;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .movie-title {
            font-size: 2rem;
            font-weight: bold;
            color: #FFD700;
            margin-bottom: 10px;
        }

        .movie-info {
            font-size: 1rem;
            margin-bottom: 10px;
        }

        .description {
            font-size: 0.9rem;
            color: #ddd;
            margin-bottom: 15px;
        }

        .btns-container {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btns {
            flex: 1;
            text-align: center;
            padding: 10px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }

        .btns-trailer {
            background-color: #FF0000;
            color: #fff;
        }

        .btns-trailer:hover {
            background-color: #CC0000;
        }

        .btns-book {
            background-color: #FFD700;
            color: #001F3F;
        }

        .btns-book:hover {
            background-color: #F5F5F5;
            color: #001F3F;
        }

        .btns-disabled {
            background-color: #666;
            color: #aaa;
            cursor: not-allowed;
        }

        .countdown {
            font-size: 1.2rem;
            font-weight: bold;
            color: #ff4d4d;
            text-align: center;
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .movie-container {
                flex-direction: column;
                max-width: 100%;
            }

            .movie-image {
                width: 100%;
                height: 300px;
            }

            .movie-details {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="movie-container">
    <!-- Movie Poster -->
    <div class="movie-image">
        <img src="<?php echo $image_src; ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
    </div>

    <!-- Movie Details -->
    <div class="movie-details">
        <h1 class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></h1>
        <p class="movie-info"><strong>Genre:</strong> <?php echo htmlspecialchars($movie['genre']); ?></p>
        <p class="movie-info"><strong>Release Date:</strong> <?php echo htmlspecialchars($movie['release_date']); ?></p>
        <p class="movie-info"><strong>Duration:</strong> <?php echo htmlspecialchars($movie['duration']); ?> mins</p>
        <p class="description"><?php echo nl2br(htmlspecialchars($movie['description'])); ?></p>

        <!-- Countdown if movie is not released -->
        <?php if (!$is_released) { ?>
            <p class="countdown">Countdown to Release: <span id="countdown"></span></p>
        <?php } ?>

        <div class="btns-container">
            <?php if (!empty($movie['trailer_link'])) { ?>
                <a href="<?php echo htmlspecialchars($movie['trailer_link']); ?>" target="_blank" class="btns btns-trailer">Watch Trailer</a>
            <?php } ?>

            <?php if ($is_released) { ?>
                <a href="movie_showtime.php?id=<?php echo $movie['id']; ?>" class="btns btns-book">Book Movie Ticket</a>
            <?php } else { ?>
                <span class="btns btns-book btns-disabled">Booking Not Available</span>
            <?php } ?>
        </div>
    </div>
</div>

<script>
    // Countdown Timer
    function startCountdown() {
        var releaseDate = new Date(<?php echo json_encode(date("Y-m-d H:i:s", $release_date)); ?>).getTime();
        var countdownElement = document.getElementById('countdown');

        function updateCountdown() {
            var now = new Date().getTime();
            var timeLeft = releaseDate - now;

            if (timeLeft > 0) {
                var days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
                var hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

                countdownElement.innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
            } else {
                countdownElement.innerHTML = "Now Released!";
                location.reload(); // Refresh page when countdown ends
            }
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    }

    <?php if (!$is_released) { ?>
        startCountdown();
    <?php } ?>
</script>

</body>
</html>

<?php
$conn->close();
?>
