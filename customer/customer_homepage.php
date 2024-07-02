<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Homepage</title>
    <link rel="stylesheet" href="../styles/styles.css"> <!-- Main styles -->
</head>
<body id = "customer_homepage_body">
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
                        <a href="" class="dropdown-link">Edit Profile</a>
                        <a href="#" class="dropdown-link">Settings</a>
                        <a href="#" class="dropdown-link">Logout</a>
                    </div>
                </li>
            </ul>
        </nav>
    </header>
    <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['welcome_message'])) {
                    echo "<h2>" . htmlspecialchars($_POST['welcome_message']) . "</h2>";
                } else {
                    echo "<h2>Welcome, Guest!</h2>";
                }
            ?>

</body>
</html>