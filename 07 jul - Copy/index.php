<?php
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

// Fetch vehicles from database
$sql = "SELECT make, model, price, image_paths, vehicle_type, transmission, seats FROM vehicles";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Rental System - Drive</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <header class="main-header">
        <div class="logo">
            <a href="index.php"><h1>Drive</h1></a>
        </div>
        <nav class="main-nav">
            <ul class="nav-list">
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropbtn">Fleet</a>
                    <div class="dropdown-content">
                        <a href="fleet/cars.html" class="dropdown-link">Cars</a>
                        <a href="#" class="dropdown-link">Motorcycles</a>
                        <a href="#" class="dropdown-link">Pickup Trucks</a>
                    </div>
                </li>
                <li class="nav-item"><a href="#" class="nav-link">Contact Us</a></li>
                <li class="nav-item"><a href="customer/customer_sign_in.html" class="nav-link">Sign In</a></li>
                <li class="nav-item"><a href="customer/customer_sign_up.html" class="nav-link">Sign Up</a></li>
            </ul>
        </nav>
    </header>
    <main class="main-content">
        <div class="content">
            <div class="text-content">
                <h2>Find Your Vehicle to Rent</h2>
                <p>
                    Experience the best vehicle rental services with a wide range of cars, motorcycles, and pickup trucks.
                    Whether you need a vehicle for a day or a month, we have flexible rental plans to suit your needs.
                    Enjoy hassle-free booking, affordable rates, and excellent customer support.
                </p>
                <button class="view-button">View All Cars</button>
            </div>
            <div class="image-content">
                <img src="images/car.jpg" alt="Car">
            </div>
        </div>
        <div class="form-content">
            <form class="form-row">
                <div class="form-group">
                    <label for="pickup-location" class="form-label">Pickup Location</label>
                    <input type="text" id="pickup-location" name="pickup-location" class="form-input" placeholder="Enter city">
                </div>
                <div class="form-group">
                    <label for="pickup-date" class="form-label">Pickup Date</label>
                    <input type="date" id="pickup-date" name="pickup-date" class="form-input">
                </div>
                <div class="form-group">
                    <label for="pickup-time" class="form-label">Pickup Time</label>
                    <input type="time" id="pickup-time" name="pickup-time" class="form-input">
                </div>
                <div class="form-group">
                    <label for="return-date" class="form-label">Return Date</label>
                    <input type="date" id="return-date" name="return-date" class="form-input">
                </div>
                <div class="form-group">
                    <label for="return-time" class="form-label">Return Time</label>
                    <input type="time" id="return-time" name="return-time" class="form-input">
                </div>
                <div class="form-group">
                    <button type="submit" class="submit-button">Search</button>
                </div>
            </form>
        </div>
        <div class="vehicle-list">
           
            <div class="vehicle-cards">
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $images = json_decode($row["image_paths"]);
                        $imagePath = !empty($images) ? "admin/" . $images[0] : 'images/default_car.jpg';
                        echo "<div class='vehicle-card'>";
                        echo "<img src='$imagePath' alt='Vehicle Image'>";
                        echo "<h3>{$row['make']} {$row['model']}</h3>";
                        echo "<p>Price: \${$row['price']} / day</p>";                       
                        echo "<p>Transmission: {$row['transmission']}</p>";
                        echo "<p>Seats: {$row['seats']}</p>";
                        echo "<button class='details-button'>View details</button>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No vehicles available.</p>";
                }
                $conn->close();
                ?>
            </div>
        </div>
    </main>   
</body>
</html>
