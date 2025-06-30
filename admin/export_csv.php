<?php
// Include database connection
include('connection.php');

// Check if report type is specified
if (isset($_GET['report'])) {
    $reportType = $_GET['report'];
    
    // Define file name based on report type
    $filename = "{$reportType}_report_" . date('Y-m-d') . ".csv";
    
    // Set headers to force download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=' . $filename);
    
    // Open output stream
    $output = fopen('php://output', 'w');
    
    switch ($reportType) {
        case 'users':
            fputcsv($output, ['User ID', 'Username', 'Email', 'Registered On']);
            $query = "SELECT User_id, username, email, created_at FROM users ORDER BY created_at DESC";
            break;
        case 'revenue':
            fputcsv($output, ['Movie', 'Total Revenue (₹)']);
            $query = "SELECT m.title, SUM(b.amount) as total_revenue FROM bookings b JOIN movies m ON b.movie_id = m.id GROUP BY m.title ORDER BY total_revenue DESC";
            break;
        case 'top_movies':
            fputcsv($output, ['Movie', 'Total Bookings']);
            $query = "SELECT m.title, COUNT(b.id) as total_bookings FROM bookings b JOIN movies m ON b.movie_id = m.id GROUP BY m.title ORDER BY total_bookings DESC LIMIT 5";
            break;
        case 'booking':
            fputcsv($output, ['Date', 'Total Bookings']);
            $query = "SELECT show_date, COUNT(id) as total_bookings FROM bookings GROUP BY show_date ORDER BY show_date DESC";
            break;
        case 'Cancelled':
            fputcsv($output, ['ID', 'User', 'Movie', 'Date', 'Time', 'Seats']);
            $query = "SELECT b.id, b.Username, m.title, b.show_date, b.show_time, b.seat_numbers FROM bookings b JOIN movies m ON b.movie_id = m.id WHERE b.status = 'Cancelled' ORDER BY b.show_date DESC";
            break;
        case 'Tickets':
            fputcsv($output, ['ID', 'User', 'Movie', 'Theater', 'Screen', 'Seats', 'Show Date', 'Show Time', 'Amount', 'Payment ID']);
            $query = "SELECT b.id, b.Username, m.title, b.theater, b.screen, b.seat_numbers, b.show_date, b.show_time, b.amount, b.payment_id FROM bookings b JOIN movies m ON b.movie_id = m.id ORDER BY b.booking_date DESC";
            break;
        case 'Movies':
            fputcsv($output, ['ID', 'Title', 'Genre', 'Release Date', 'Duration', 'Description', 'Status']);
            $query = "SELECT id, title, genre, release_date, duration, description, status FROM movies ORDER BY release_date DESC";
            break;
        case 'Theaters':
            fputcsv($output, ['ID', 'Name', 'Location', 'Status']);
            $query = "SELECT id, name, location, status FROM theaters ORDER BY name ASC";
            break;
        default:
            exit('Invalid report type');
    }
    
    // Execute query and fetch results
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
    
    fclose($output);
}

// Close database connection
$conn->close();
?>
