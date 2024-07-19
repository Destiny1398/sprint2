<?php
include('session.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_sign_in.html");
    exit();
}

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

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vehicle_id'])) {
    $vehicle_id = $_POST['vehicle_id'];

    // Delete vehicle features based on vehicle type
    $vehicle_type_query = "SELECT vehicle_type FROM vehicles WHERE vehicle_id = ?";
    $stmt = $conn->prepare($vehicle_type_query);
    $stmt->bind_param("i", $vehicle_id);
    $stmt->execute();
    $stmt->bind_result($vehicle_type);
    $stmt->fetch();
    $stmt->close();

    if ($vehicle_type === 'Car') {
        $delete_features_sql = "DELETE FROM car_features WHERE vehicle_id = ?";
    } elseif ($vehicle_type === 'Pickup Truck') {
        $delete_features_sql = "DELETE FROM pickup_truck_features WHERE vehicle_id = ?";
    } elseif ($vehicle_type === 'Motorcycle') {
        $delete_features_sql = "DELETE FROM motorcycle_features WHERE vehicle_id = ?";
    }

    if (isset($delete_features_sql)) {
        $stmt = $conn->prepare($delete_features_sql);
        $stmt->bind_param("i", $vehicle_id);
        if (!$stmt->execute()) {
            echo 'error: ' . $stmt->error;
            $stmt->close();
            $conn->close();
            exit();
        }
        $stmt->close();
    }

    // Delete vehicle
    $delete_vehicle_sql = "DELETE FROM vehicles WHERE vehicle_id = ?";
    $stmt = $conn->prepare($delete_vehicle_sql);
    $stmt->bind_param("i", $vehicle_id);
    if ($stmt->execute() === TRUE) {
        echo 'success';
    } else {
        echo 'error: ' . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>
