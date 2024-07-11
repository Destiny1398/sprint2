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
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../styles/styles.css">

</head>
<body>
    <div class="admin-sidebar">
        <h2>Drive | Admin Panel</h2>
        <a href="admin_dashboard.php" class="nav-link" data-target="dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="admin_vehicles.php" class="nav-link" data-target="vehicles"><i class="fas fa-car-side"></i> Vehicles</a>
        <a href="admin_brands.php" class="nav-link" data-target="brands"><i class="fas fa-car"></i> Brands</a>
        <a href="admin_bookings.php" class="nav-link" data-target="bookings"><i class="fas fa-book"></i> Bookings</a>
        <a href="admin_testimonials.php" class="nav-link" data-target="testimonials"><i class="fas fa-comments"></i> Manage Testimonials</a>
        <a href="admin_contact-us.php" class="nav-link" data-target="contact-us"><i class="fas fa-question-circle"></i> Manage Contact Us Query</a>
        <a href="admin_users.php" class="nav-link" data-target="users"><i class="fas fa-users"></i> Reg Users</a>
        <a href="admin_pages.php" class="nav-link" data-target="pages"><i class="fas fa-file-alt"></i> Manage Pages</a>
    </div>
    <div class="admin-main-content">
        <div class="admin-navbar">
            <div>Dashboard</div>
            <div class="admin-account">
                <img src="path_to_admin_profile_picture.jpg" alt="Admin" width="30" height="30">
                <span><?php echo htmlspecialchars($admin_email); ?></span>
            </div>
        </div>

</div>    
    
    
</body>
</html>
