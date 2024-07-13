<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Homepage</title>
    <link rel="stylesheet" href="../styles/styles.css"> <!-- Main styles -->
    <link rel="stylesheet" href="../styles/customer_homepage.css"> <!-- Custom styles for the homepage -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .main-container {
            display: flex;
        }
        .sidebar {
            width: 16%;
            background-color: #000;
            color: #fff;
            height: 100vh;
            padding-top: 20px;
        }
        .sidebar a {
            display: block;
            color: #fff;
            padding: 15px;
            text-decoration: none;
            margin-top: 20px;
            margin-left: 20px;
        }
        .sidebar a:hover {
            background-color: #575757;
        }
        .sidebar a .fas {
            margin-right: 10px;
        }
        .content {
            flex-grow: 1;
            padding: 20px;
        }
    </style>
</head>
<body>
    <?php
    session_start();
    if (isset($_SESSION['firstName'])) {
        $firstName = $_SESSION['firstName'];
        echo "<div class='welcome-message' >Welcome, $firstName!</div>";
    } else {
        echo "<div class='welcome-message'>Welcome, Guest!</div>";
    }
    ?>
    
    <header class="main-header">
        <div class="logo">
            <a href="../index.html"><h1>Drive</h1></a>
        </div>
        <nav class="main-nav">
            <ul class="nav-list">
                <li class="nav-item"><a href="../index.html" class="nav-link">Home</a></li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropbtn">Fleet</a>
                    <div class="dropdown-content">
                        <a href="../fleet/cars.html" class="dropdown-link">Cars</a>
                        <a href="#" class="dropdown-link">Motorcycles</a>
                        <a href="#" class="dropdown-link">Pickup Trucks</a>
                    </div>
                </li>
                <li class="nav-item"><a href="../contact.html" class="nav-link">Contact Us</a></li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropbtn">Profile</a>
                    <div class="dropdown-content">
                        <a href="#" class="dropdown-link" onclick="toggleEditProfile()">Edit Profile</a>
                        <a href="#" class="dropdown-link">Settings</a>
                        <a href="#" class="dropdown-link">Logout</a>
                    </div>
                </li>
            </ul>
        </nav>
    </header>

    <div class="main-container">
        <div class="sidebar">
            <a href="#"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
            <a href="#"><i class="fas fa-book"></i>View Bookings</a>
            <a href="#"><i class="fas fa-history"></i>Payment History</a>
            <a href="#"><i class="fas fa-tags"></i>Offers</a>
            <a href="#"><i class="fas fa-headset"></i>Support</a>
        </div>
        <div class="content">
            <!-- Page-specific content goes here -->
        </div>
    </div>
</body>
</html>
