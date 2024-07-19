<?php
session_start(); // Start the session

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

// Get the form data
$vehicle_id = $_POST['vehicle_id'];
$pickup_location = $_POST['pickup_location'];
$return_location = $_POST['return_location'];
$pickup_date = $_POST['pickup_date'];
$pickup_time = $_POST['pickup_time'];
$return_date = $_POST['return_date'];
$return_time = $_POST['return_time'];
$price = $_POST['price'];

// Get the logged-in customer ID from the session
if (isset($_SESSION['userId'])) {
    $customer_id = $_SESSION['userId'];
} else {
    die("Customer not logged in.");
}

// Insert the booking details into the database
$sql = "INSERT INTO bookings (vehicle_id, pickup_location, return_location, pickup_date, pickup_time, return_date, return_time, price, customer_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("issssssdi", $vehicle_id, $pickup_location, $return_location, $pickup_date, $pickup_time, $return_date, $return_time, $price, $customer_id);

if ($stmt->execute()) {
    $_SESSION['booking_success'] = true;
    header("Location: customer_booking.php?vehicle_id=$vehicle_id&pickup_location=$pickup_location");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
