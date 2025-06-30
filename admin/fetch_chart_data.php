<?php
include('connection.php');

$sql = "SELECT m.title, COUNT(b.id) as count FROM bookings b JOIN movies m ON b.movie_id = m.id GROUP BY m.title";
$result = $conn->query($sql);

$data = ['movies' => [], 'counts' => []];
while ($row = $result->fetch_assoc()) {
    $data['movies'][] = $row['title'];
    $data['counts'][] = $row['count'];
}

echo json_encode($data);
?>
