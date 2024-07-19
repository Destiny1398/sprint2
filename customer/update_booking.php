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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $return_location = $_POST['return_location'];
    $pickup_date = $_POST['pickup_date'];
    $pickup_time = $_POST['pickup_time'];
    $return_date = $_POST['return_date'];
    $return_time = $_POST['return_time'];
    $price = $_POST['price'];

    // Update booking details
    $sql = "UPDATE bookings SET return_location=?, pickup_date=?, pickup_time=?, return_date=?, return_time=?, price=?, booking_modified_date=CURRENT_TIMESTAMP WHERE booking_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $return_location, $pickup_date, $pickup_time, $return_date, $return_time, $price, $booking_id);

    if ($stmt->execute()) {
        $_SESSION['update_success'] = true;
        header("Location: modify_booking.php?booking_id=" . $booking_id);
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
