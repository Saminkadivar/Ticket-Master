<?php
include 'includes/connection.php';
include 'includes/sidebar.php';
session_start();

// Ensure a theater is logged in
if (!isset($_SESSION['theater_id'])) {
    header("Location: login.php");
    exit();
}
$theater_id = $_SESSION['theater_id']; // Get theater ID from session

// Fetch active movies
$movies = mysqli_query($conn, "SELECT id, title FROM movies WHERE status = 'active'");

// Fetch screens belonging to the logged-in theater
$screens = mysqli_query($conn, "SELECT id, screen_name FROM screens WHERE theater_id = '$theater_id' AND status = 'Active'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Showtimes</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        function addCustomTime() {
            let customTimeInput = document.getElementById("customTime");
            let customTimeValue = customTimeInput.value.trim();

            if (customTimeValue !== "") {
                let showtimesDiv = document.getElementById("showtimes");

                // Convert time to 24-hour format
                let formattedTime = new Date("1970-01-01T" + customTimeValue).toLocaleTimeString('en-GB', { hour12: false });

                // Create new checkbox
                let checkbox = document.createElement("input");
                checkbox.type = "checkbox";
                checkbox.name = "time[]";
                checkbox.value = formattedTime;
                checkbox.id = "custom_" + formattedTime;

                // Create new label
                let label = document.createElement("label");
                label.htmlFor = "custom_" + formattedTime;
                label.textContent = customTimeValue;

                // Append to showtimes div
                showtimesDiv.appendChild(checkbox);
                showtimesDiv.appendChild(label);

                // Clear input field
                customTimeInput.value = "";
            }
        }
    </script>

    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 20px; text-align: center; }
        .container { max-width: 600px; margin: 0 auto; background-color: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        select, input[type="date"], input[type="submit"], input[type="time"], button { width: 100%; padding: 10px; margin-top: 10px; border: 1px solid #ccc; border-radius: 5px; }
        label { font-weight: bold; display: block; margin-top: 15px; }
        .showtimes { display: flex; flex-wrap: wrap; justify-content: space-around; margin-top: 10px; }
        .showtimes label { display: flex; align-items: center; gap: 5px; background: #e9ecef; padding: 8px 12px; border-radius: 5px; cursor: pointer; transition: 0.3s; }
        .showtimes input { display: none; }
        .showtimes label:hover { background: #d6d8db; }
        .showtimes input:checked + label { background: #28a745; color: white; }
        input[type="submit"] { background-color: rgba(0, 31, 63, 0.85); color: white; border: none; cursor: pointer; margin-top: 15px; }
        input[type="submit"]:hover { background-color: rgba(0, 31, 63, 0.85); }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add Movie Showtimes</h2>
        <form action="process_showtime.php" method="POST">
            
            <label>Movie:</label>
            <select name="movie_id" required>
                <option value="">-- Select Movie --</option>
                <?php while ($movie = mysqli_fetch_assoc($movies)) { ?>
                    <option value="<?= $movie['id']; ?>"><?= $movie['title']; ?></option>
                <?php } ?>
            </select>

            <label>Screen:</label>
            <select name="screen_id" required>
                <option value="">-- Select Screen --</option>
                <?php while ($screen = mysqli_fetch_assoc($screens)) { ?>
                    <option value="<?= $screen['id']; ?>"><?= $screen['screen_name']; ?></option>
                <?php } ?>
            </select>

            <label>Start Date:</label>
            <input type="date" name="start_date" required>

            <label>End Date:</label>
            <input type="date" name="end_date" required>

            <label>Select Showtimes:</label>
            <div id="showtimes" class="showtimes">
                <input type="checkbox" id="time1" name="time[]" value="09:00:00">
                <label for="time1">9:00 AM</label>

                <input type="checkbox" id="time2" name="time[]" value="12:00:00">
                <label for="time2">12:00 PM</label>

                <input type="checkbox" id="time3" name="time[]" value="16:00:00">
                <label for="time3">3:00 PM</label>

                <input type="checkbox" id="time4" name="time[]" value="18:00:00">
                <label for="time4">6:00 PM</label>

                <input type="checkbox" id="time5" name="time[]" value="21:00:00">
                <label for="time5">9:00 PM</label>
            </div>

            <label>Add Custom Showtime:</label>
            <input type="time" id="customTime">
            <button type="button" onclick="addCustomTime()">Add Time</button>

            <input type="hidden" name="theater_id" value="<?= $theater_id; ?>">

            <input type="submit" value="Add Showtimes">
        </form>
    </div>
</body>
</html>
