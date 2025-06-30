<?php
include('connection.php');

if (isset($_GET['id'])) {
    $ticket_id = $_GET['id'];
    $delete_sql = "DELETE FROM tickets WHERE ticket_id = $ticket_id";
    if ($conn->query($delete_sql) === TRUE) {
        echo "Ticket deleted successfully!";
        header("Location: manage_tickets.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
