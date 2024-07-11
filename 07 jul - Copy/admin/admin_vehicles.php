<?php
include('session.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_sign_in.html"); // Redirect to login page if not logged in
    exit();
}

$admin_email = $_SESSION['admin_email'];

// Database connection
$servername = "localhost";
$username = "root"; // Change this to your database username
$password = ""; // Change this to your database password
$dbname = "customer_registration"; // Change this to your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Vehicles</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <style>
        .vehicle-container {
    display: flex;
    justify-content: space-around;
    
}

.vehicle-box {
    width: 30%;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    text-align: center;
    background-color: #f9f9f9;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-left: 20px;
}
.vehicle-box h2 {
    margin-bottom: 20px;
    font-size: 1.5em;
}
.vehicle-box i {
    font-size: 2em;
    margin-right: 10px;
    vertical-align: middle;
}
.vehicle-box button {
    padding: 10px 20px;
    font-size: 1em;
    border: none;
    border-radius: 4px;
    background-color: #007bff;
    color: white;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
.vehicle-box button:hover {
    background-color: #0056b3;
}
</style>
</head>
<body>
    <?php include('admin_dashboard.php'); ?>
    <div class="admin-main-content">
        
        <div class="vehicle-container">
            <div class="vehicle-box" id="add-vehicle-box">
                <h2><i class="fas fa-plus-circle"></i> Add Vehicle</h2>
                <button onclick="window.location.href='admin_add_vehicle.php'">Add Vehicle</button>
            </div>
            <div class="vehicle-box" id="edit-vehicle-box">
                <h2><i class="fas fa-edit"></i> Edit Vehicle</h2>
                <button onclick="window.location.href='admin_edit_vehicle.php'">Edit Vehicle</button>
            </div>
            <div class="vehicle-box" id="delete-vehicle-box">
                <h2><i class="fas fa-trash-alt"></i> Delete Vehicle</h2>
                <button onclick="window.location.href='admin_delete_vehicle.php'">Delete Vehicle</button>
            </div>
        </div>
    </div>
    
</body>
</html>
