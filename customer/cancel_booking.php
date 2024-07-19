<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_vehicle_rental_system";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $booking_id = isset($_POST['booking_id']) ? $_POST['booking_id'] : 0;

    if ($booking_id > 0) {
        $sql = "UPDATE bookings SET booking_cancelled_date = CURRENT_TIMESTAMP WHERE booking_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $booking_id);
        if ($stmt->execute()) {
            echo "Booking successfully cancelled.";
        } else {
            echo "Error cancelling booking: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Invalid booking ID.";
    }
}

$conn->close();
?>
