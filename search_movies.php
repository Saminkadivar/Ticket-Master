<?php
include('connection.php');
include('includes/header.php');
$result = null;
$latestMovies = null;

// Check if search query is set
if (isset($_GET['query']) && !empty($_GET['query'])) {
    $query = htmlspecialchars($_GET['query']);

    // Search for movies by title, genre, or release date
    $sql = "SELECT * FROM movies WHERE title LIKE ? OR genre LIKE ? OR release_date LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%" . $query . "%";
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
}

// Fetch the latest 3 movies
$latestSql = "SELECT * FROM movies WHERE status = 'active' ORDER BY release_date DESC LIMIT 3";
$latestMovies = $conn->query($latestSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Search Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: rgb(232, 225, 225);
            color: #F5F5F5;
            font-family: 'Roboto', sans-serif;
        }
        .movie-card {
            margin: 20px 0;
            cursor: pointer;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease-in-out;
            background-color: #001F3F;
            color: rgb(16, 0, 0);
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }
        .movie-card:hover {
            transform: scale(1.05);
        }
        .card-body {
            background-color: #001F3F;
            color: #F5F5F5;
        }
        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            color: #FFD700;
        }
        .card-body p {
            font-size: 0.9rem;
            text-align: center;
            color: #F5F5F5;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="my-4 text-center text-warning">Search Results</h2>
    <div class="row">
        <?php if ($result && $result->num_rows > 0) {
            while ($movie = $result->fetch_assoc()) {
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
                    <p><strong>Genre:</strong> <?php echo htmlspecialchars($movie['genre']); ?></p>
                    <p><strong>Release Date:</strong> <?php echo htmlspecialchars($movie['release_date']); ?></p>
                </div>
            </div>
        </div>
        <?php }} else {
            echo "<p class='text-center text-dark'>No movies found.</p>";
        } ?>
    </div>

    <h2 class="my-4 text-center text-warning">Latest Movies</h2>
    <div class="row">
        <?php if ($latestMovies->num_rows > 0) {
            while ($movie = $latestMovies->fetch_assoc()) {
                $image_path = 'admin/' . htmlspecialchars($movie['image']);
                if (!file_exists(__DIR__ . '/' . $image_path) || empty($movie['image'])) {
                    $image_path = 'admin/uploads/default.jpg';
                }
        ?>
        <div class="col-md-4">
            <div class="card movie-card" onclick="window.location.href='movie_details.php?id=<?php echo $movie['id']; ?>'">
                <img src="<?php echo $image_path; ?>" class="card-img-top movie-image" alt="<?php echo htmlspecialchars($movie['title']); ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($movie['title']); ?></h5>
                    <p><strong>Genre:</strong> <?php echo htmlspecialchars($movie['genre']); ?></p>
                    <p><strong>Release Date:</strong> <?php echo htmlspecialchars($movie['release_date']); ?></p>
                </div>
            </div>
        </div>
        <?php }} ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
include 'includes/footer.php';

?>
