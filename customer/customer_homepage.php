<?php
    session_start();
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "online_vehicle_rental_system";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve user details from the database
    if (isset($_SESSION['userId'])) {
        $cust_id = $_SESSION['userId'];
        $sql = "SELECT cust_first_name, cust_profile_image FROM customer WHERE cust_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cust_id);
        $stmt->execute();
        $stmt->bind_result($firstName, $profileImagePath);
        $stmt->fetch();
        $stmt->close();
    }

    $conn->close();
    ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Homepage</title>
    <link rel="stylesheet" href="../styles/customer_homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
       body {
           margin:0;
           padding:0;
       }
    </style>
</head>
<body>
    <header id="cust_homepage_main_header">
        <div id="cust_homepage_logo">
            <a href="../index.php"><h1>Drive</h1></a>
        </div>
        <nav class="main-nav">
            <ul id="cust_homepage_nav_list">
                <li id="cust_homepage_nav_item"><a href="../index.php" id="cust_homepage_nav_link">Home</a></li>
                <li id="cust_homepage_nav_item" class="dropdown cust_homepage_dropdown fleet-dropdown">
                    <a href="#" id="cust_homepage_nav_link" class="cust_homepage_dropbtn">Fleet</a>
                    <div class="cust_homepage_dropdown_content">
                        <a href="../fleet/cars.html" class="cust_homepage_dropdown_link">Cars</a>
                        <a href="#" class="cust_homepage_dropdown_link">Motorcycles</a>
                        <a href="#" class="cust_homepage_dropdown_link">Pickup Trucks</a>
                    </div>
                </li>
                <li id="cust_homepage_nav_item"><a href="../contact.html" id="cust_homepage_nav_link">Contact Us</a></li>
                <li id="cust_homepage_nav_item" class="dropdown cust_homepage_dropdown">
                    <a href="#" id="cust_homepage_nav_link" class="cust_homepage_dropbtn profile-info">
                        <?php if (isset($firstName)) : ?>
                            <img src="../<?php echo htmlspecialchars($profileImagePath); ?>" alt="Profile Image">
                            <span style="font-size: 18px; color: #333;"><?php echo htmlspecialchars($firstName); ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="cust_homepage_dropdown_content">
                        <a href="customer_edit_profile.php" class="cust_homepage_dropdown_link">Edit Profile</a>
                        <a href="customer_change_password.php" class="cust_homepage_dropdown_link">Change password</a>
                        <a href="#" class="cust_homepage_dropdown_link">Settings</a>
                        <a href="customer_logout.php" class="cust_homepage_dropdown_link">Logout</a>
                    </div>
                </li>
            </ul>
        </nav>
    </header>

    <div id="cust_homepage_main_container">
        <div id="cust_homepage_sidebar">
            <a href="customer_homepage.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
            <a href="customer_bookings_details.php"><i class="fas fa-book"></i>View Bookings</a>
            <a href="#"><i class="fas fa-history"></i>Payment History</a>
            <a href="#"><i class="fas fa-tags"></i>Offers</a>
            <a href="#"><i class="fas fa-headset"></i>Support</a>
        </div>
        <div id="cust_homepage_content">
            <!-- Page-specific content goes here -->
        </div>
    </div>
</body>
</html>
