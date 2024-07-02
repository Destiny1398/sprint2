<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_sign_in.html"); // Redirect to login page if not logged in
    exit();
}

$admin_email = $_SESSION['admin_email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #343a40;
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
        }
        .sidebar h2 {
            text-align: center;
            margin: 1em 0;
        }
        .sidebar a {
            padding: 1em;
            color: #fff;
            text-decoration: none;
            display: block;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .main-content {
            margin-left: 250px;
            padding: 2em;
        }
        .navbar {
            background-color: #343a40;
            padding: 1em;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar .account {
            display: flex;
            align-items: center;
        }
        .navbar .account img {
            border-radius: 50%;
            margin-right: 0.5em;
        }
        .dashboard-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 1em;
        }
        .card {
            background-color: #fff;
            padding: 1em;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            flex: 1;
            min-width: 200px;
            text-align: center;
        }
        .card h3 {
            margin: 0;
            font-size: 2em;
        }
        .card p {
            margin: 0.5em 0;
            color: #6c757d;
        }
        .card .full-detail {
            display: block;
            margin-top: 1em;
            color: #007bff;
            text-decoration: none;
        }
        .card .full-detail:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Car Rental Portal | Admin Panel</h2>
        <a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="#"><i class="fas fa-car"></i> Brands</a>
        <a href="#"><i class="fas fa-car-side"></i> Vehicles</a>
        <a href="#"><i class="fas fa-book"></i> Bookings</a>
        <a href="#"><i class="fas fa-comments"></i> Manage Testimonials</a>
        <a href="#"><i class="fas fa-question-circle"></i> Manage Contact Us Query</a>
        <a href="#"><i class="fas fa-users"></i> Reg Users</a>
        <a href="#"><i class="fas fa-file-alt"></i> Manage Pages</a>
        <a href="#"><i class="fas fa-address-book"></i> Update Contact Info</a>
        <a href="#"><i class="fas fa-envelope"></i> Manage Subscribers</a>
    </div>
    <div class="main-content">
        <div class="navbar">
            <div>Dashboard</div>
            <div class="account">
                <img src="path_to_admin_profile_picture.jpg" alt="Admin" width="30" height="30">
                <span><?php echo htmlspecialchars($admin_email); ?></span>
            </div>
        </div>
        <div class="dashboard-cards">
            <div class="card">
                <h3>2</h3>
                <p>Reg Users</p>
                <a href="#" class="full-detail">Full Detail</a>
            </div>
            <div class="card" style="background-color: #28a745; color: #fff;">
                <h3>8</h3>
                <p>Listed Vehicles</p>
                <a href="#" class="full-detail" style="color: #fff;">Full Detail</a>
            </div>
            <div class="card" style="background-color: #007bff; color: #fff;">
                <h3>1</h3>
                <p>Total Bookings</p>
                <a href="#" class="full-detail" style="color: #fff;">Full Detail</a>
            </div>
            <div class="card" style="background-color: #fd7e14; color: #fff;">
                <h3>6</h3>
                <p>Listed Brands</p>
                <a href="#" class="full-detail" style="color: #fff;">Full Detail</a>
            </div>
            <div class="card">
                <h3>2</h3>
                <p>Subscribers</p>
                <a href="#" class="full-detail">Full Detail</a>
            </div>
            <div class="card" style="background-color: #28a745; color: #fff;">
                <h3>1</h3>
                <p>Queries</p>
                <a href="#" class="full-detail" style="color: #fff;">Full Detail</a>
            </div>
            <div class="card" style="background-color: #007bff; color: #fff;">
                <h3>0</h3>
                <p>Testimonials</p>
                <a href="#" class="full-detail" style="color: #fff;">Full Detail</a>
            </div>
        </div>
    </div>
</body>
</html>
