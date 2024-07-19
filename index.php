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

// Fetch vehicles from the database
$location = isset($_POST['location']) ? $_POST['location'] : '';
$sql = "SELECT vehicle_id, make, model, price, image_paths, vehicle_type, transmission, seats, fuel_type, year, airbags, doors FROM vehicles";
if (!empty($location)) {
    $sql .= " WHERE location = '" . $conn->real_escape_string($location) . "'";
}
$result = $conn->query($sql);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Rental System - Drive</title>
    <link rel="stylesheet" href="styles/header_style.css">
    <link rel="stylesheet" href="styles/index_page_styling.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script>
    function validateLocation(event) {
        const locationSelect = document.getElementById('location');
        const errorMessage = document.getElementById('location-error');
        if (!locationSelect.value) {
            event.preventDefault();
            locationSelect.classList.add('error');
            errorMessage.style.display = 'block';
            // Scroll to the locationSelect element
            locationSelect.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            locationSelect.classList.remove('error');
            errorMessage.style.display = 'none';
            // Get the vehicle ID from the clicked button
            const vehicleId = event.target.getAttribute('data-vehicle-id');
            // Redirect to the booking page with the selected location and vehicle ID
            window.location.href = `customer/customer_booking.php?vehicle_id=${vehicleId}&pickup_location=${locationSelect.value}`;
        }
    }

    function filterByMake(make) {
        const vehicleCards = document.querySelectorAll('.vehicle-card');
        vehicleCards.forEach(card => {
            if (make === 'All' || card.dataset.make === make) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
    </script>
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
                        <a href="vehicles/vehicle_type_car.php" class="dropdown-link">Cars</a>
                        <a href="vehicles/vehicle_type_pickuptrucks.php" class="dropdown-link">Pickup Trucks</a>
                        <a href="#" class="dropdown-link">Motorcycles</a>
                    </div>
                </li>
                <li class="nav-item"><a href="#" class="nav-link">Contact Us</a></li>
                <?php if (isset($_SESSION['userId'])): ?>
                    <li class="nav-item"><a href="customer/customer_homepage.php" class="nav-link">Dashboard</a></li>
                <?php else: ?>
                    <li class="nav-item"><a href="customer/customer_sign_in.php" class="nav-link">Sign In</a></li>
                    <li class="nav-item"><a href="customer/customer_sign_up.html" class="nav-link">Sign Up</a></li>
                <?php endif; ?>
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
                <button class="view-button" onclick="window.location.href='vehicles/vehicle_type_car.php'">View All Cars</button>
            </div>
            <div class="image-content">
                <img src="images/car.jpg" alt="Car">
            </div>
        </div>
        <div class="form-content">
            <h2>Reserve a Vehicle</h2>
            <form class="form-row" method="POST" action="">
                <div class="form-group">
                    <label for="location" class="form-label">Pickup Location</label>
                    <select id="location" name="location" class="form-input" required>
                        <option value="" disabled selected>Select location</option>
                        <?php
                        $locations = ["Ajax", "Burlington", "Etobicoke", "Georgetown", "Halton Hills", "Hamilton", "Kitchener", "Milton", "Mississauga", "Oakville", "Scarborough", "Toronto"];
                        sort($locations);
                        foreach ($locations as $loc) {
                            $selected = $loc == $location ? 'selected' : '';
                            echo "<option value=\"$loc\" $selected>$loc</option>";
                        }
                        ?>
                    </select>
                    <span id="location-error" class="error-message">Please select a pickup location to rent the vehicle.</span>
                </div>
                <div class="form-group">
                    <button type="submit" class="submit-button">Search</button>
                </div>    
            </form>
        </div>
        <div class="how-it-works">
            <h2>How It Works</h2>
            <div class="steps-container">
                <div class="step">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>1. Choose Locations</h3>
                    <p>Determine the date & location for your vehicle rental. 
                        Consider factors such as your travel itinerary, pickup/drop-off locations (e.g., airport, city center) and 
                        duration of rental.</p>
                </div>
                <div class="step">
                    <i class="fas fa-car-side"></i>
                    <h3>2. Check Availability and details</h3>
                    <p>Check the availability of your desired vehicle type for your chosen dates and location. 
                        Check the rental rates, taxes, fees, and any additional charges .</p>
                </div>
                <div class="step">
                    <i class="fas fa-car"></i>
                    <h3>3. Book your Vehicle</h3>
                    <p>Once you've found rental option, proceed to make a reservation. Provide the required information, including your details, driver's license, and payment details.</p>
                </div>
            </div>
        </div>
        <div class="popular-brands">
            <h2>Explore Most Popular Cars</h2>
            <p>Here's a list of some of the most popular cars globally, based on sales and customer preferences</p>
            <div class="brands-container">
                <button class="brand-button" onclick="filterByMake('All')">All</button>
                <button class="brand-button" onclick="filterByMake('Toyota')">Toyota</button>
                <button class="brand-button" onclick="filterByMake('Honda')">Honda</button>
                <button class="brand-button" onclick="filterByMake('BMW')">BMW</button>
                <button class="brand-button" onclick="filterByMake('Volkswagen')">Volkswagen</button>
            </div>
        </div>
        <div class="vehicle-list">
            <div class="vehicle-cards">
                <?php
                $location = isset($_POST['location']) ? $_POST['location'] : '';
                $sql = "SELECT vehicle_id, make, model, price, image_paths, vehicle_type, transmission, seats, fuel_type, year, airbags, doors FROM vehicles";
                if (!empty($location)) {
                    $sql .= " WHERE location = '" . $conn->real_escape_string($location) . "'";
                }
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $images = json_decode($row["image_paths"]);
                        $imagePath = !empty($images) ? "admin/" . $images[0] : 'images/default_car.jpg';
                        echo "<div class='vehicle-card' data-make='{$row['make']}'>";
                        echo "<img src='$imagePath' alt='Vehicle Image'>";
                        echo "<h3>{$row['make']} {$row['model']}</h3>";
                        echo "<hr>";
                        echo "<div class='details'>";
                        if ($row['vehicle_type'] == 'Car') {
                            echo "<div class='first-row'>";
                            echo "<div class='detail-item'><i class='fas fa-car'></i> {$row['vehicle_type']}</div>";
                            echo "<div class='detail-item'><i class='fas fa-gas-pump'></i> {$row['fuel_type']}</div>";
                            echo "<div class='detail-item'><i class='fas fa-calendar-alt'></i> {$row['year']}</div>";
                            echo "</div>";
                            echo "<div class='second-row'>";
                            echo "<div class='detail-item'><i class='fas fa-cogs'></i> {$row['transmission']}</div>";
                            echo "<div class='detail-item'><i class='fas fa-air-freshener'></i> {$row['airbags']} Airbags</div>";
                            echo "<div class='detail-item'><i class='fas fa-user-friends'></i> {$row['seats']} Seats</div>";
                            echo "</div>";
                        } elseif ($row['vehicle_type'] == 'Pickup Truck') {
                            echo "<div class='first-row'>";
                            echo "<div class='detail-item'><i class='fas fa-cogs'></i> {$row['transmission']}</div>";
                            echo "<div class='detail-item'><i class='fas fa-gas-pump'></i> {$row['fuel_type']}</div>";
                            echo "<div class='detail-item'><i class='fas fa-calendar-alt'></i> {$row['year']}</div>";
                            echo "</div>";
                            echo "<div class='second-row'>";
                            echo "<div class='detail-item'><i class='fas fa-air-freshener'></i> {$row['airbags']} Airbags</div>";
                            echo "<div class='detail-item'><i class='fas fa-door-closed'></i> {$row['doors']} Doors</div>";
                            echo "<div class='detail-item'><i class='fas fa-user-friends'></i> {$row['seats']} Seats</div>";
                            echo "</div>";
                        } elseif ($row['vehicle_type'] == 'Motorcycle') {
                            echo "<div class='first-row'>";
                            echo "<div class='detail-item'><i class='fas fa-calendar-alt'></i> {$row['year']}</div>";
                            echo "</div>";
                            echo "<div class='second-row'>";
                            echo "<div class='detail-item'><i class='fas fa-gas-pump'></i> {$row['fuel_type']}</div>";
                            echo "</div>";
                        }
                        echo "</div>";
                        echo "<div class='price-box'>\${$row['price']} / day</div>";
                        echo "<a href='#' class='details-button' onclick='validateLocation(event)' data-vehicle-id='{$row['vehicle_id']}'>Rent Now</a>";
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
