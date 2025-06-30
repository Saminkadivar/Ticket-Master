<?php
session_start();
include('connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['seat_ids']) || !isset($data['showtime_id'])) {
        echo json_encode(["status" => "error", "message" => "Invalid request data."]);
        exit;
    }

    $seat_ids = $data['seat_ids'];
    $showtime_id = $data['showtime_id'];

    if (!is_array($seat_ids) || empty($seat_ids)) {
        echo json_encode(["status" => "error", "message" => "No seats selected."]);
        exit;
    }

    // ✅ Start transaction
    $conn->begin_transaction();
    $conn->query("SET TRANSACTION ISOLATION LEVEL SERIALIZABLE"); // 🔒 Prevent double booking

    try {
        // ✅ Convert seat IDs into placeholders (for prepared statements)
        $placeholders = implode(',', array_fill(0, count($seat_ids), '?'));

        // ✅ Check if seats are available (with row lock)
        $checkQuery = "SELECT id FROM seats 
                       WHERE id IN ($placeholders) 
                       AND showtime_id = ? 
                       AND status = 'available' 
                       FOR UPDATE"; // 🔒 Locks the selected rows

        $checkStmt = $conn->prepare($checkQuery);
        $types = str_repeat("i", count($seat_ids)) . "i";  
        $checkStmt->bind_param($types, ...array_merge($seat_ids, [$showtime_id]));
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        // ✅ Ensure all selected seats are still available
        if ($checkResult->num_rows != count($seat_ids)) {
            $conn->rollback();
            echo json_encode(["status" => "error", "message" => "Some seats are already booked."]);
            exit;
        }

        // ✅ Mark seats as "pending" before finalizing the booking
        $updatePendingQuery = "UPDATE seats SET status = 'pending', updated_at = NOW() WHERE id IN ($placeholders) AND showtime_id = ?";
        $updatePendingStmt = $conn->prepare($updatePendingQuery);
        $updatePendingStmt->bind_param($types, ...array_merge($seat_ids, [$showtime_id]));
        $updatePendingStmt->execute();

        // ✅ Finalize booking by marking as "booked"
        $updateBookedQuery = "UPDATE seats SET status = 'booked' WHERE id IN ($placeholders) AND showtime_id = ?";
        $updateBookedStmt = $conn->prepare($updateBookedQuery);
        $updateBookedStmt->bind_param($types, ...array_merge($seat_ids, [$showtime_id]));
        $updateBookedStmt->execute();

        if ($updateBookedStmt->affected_rows > 0) {
            $conn->commit();
            echo json_encode(["status" => "success", "message" => "Seats successfully booked!", "booked_seats" => $seat_ids]);
        } else {
            $conn->rollback();
            echo json_encode(["status" => "error", "message" => "Failed to book seats."]);
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
}
?>




<script>
let selectedSeats = []; // ✅ Ensure selectedSeats is initialized

function selectSeat(seatId) {
    let index = selectedSeats.indexOf(seatId);
    let seatElement = document.getElementById(`seat-${seatId}`);

    if (index === -1) {
        selectedSeats.push(seatId);
        seatElement.style.backgroundColor = "green"; // Change to selected color
    } else {
        selectedSeats.splice(index, 1);
        seatElement.style.backgroundColor = ""; // Revert color
    }
}

function bookSeats() {
    if (selectedSeats.length === 0) {
        alert("Please select at least one seat.");
        return;
    }

    let showtimeElement = document.getElementById("showtime_id");
    if (!showtimeElement) {
        alert("Showtime ID is missing.");
        return;
    }

    let showtimeId = showtimeElement.value;

    fetch("book_seat.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ seat_ids: selectedSeats, showtime_id: showtimeId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            alert("Seats booked successfully!");

            // ✅ Update booked seats visually
            data.booked_seats.forEach(seatId => {
                let seatElement = document.getElementById(`seat-${seatId}`);
                if (seatElement) {
                    seatElement.style.backgroundColor = "red"; // Booked color
                    seatElement.classList.add("booked");
                }
            });

            selectedSeats = []; // ✅ Clear selected seats after booking
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("An error occurred while booking seats.");
    });
}


</script>
