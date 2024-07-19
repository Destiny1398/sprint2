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

// Fetch filter options
$make_options = $conn->query("SELECT DISTINCT make FROM vehicles WHERE vehicle_type = 'Car'");
$model_options = $conn->query("SELECT DISTINCT model FROM vehicles WHERE vehicle_type = 'Car'");
$type_options = $conn->query("SELECT DISTINCT car_type FROM vehicles WHERE vehicle_type = 'Car'");
$transmission_options = $conn->query("SELECT DISTINCT transmission FROM vehicles WHERE vehicle_type = 'Car'");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Rental System - Drive</title>
    <link rel="stylesheet" href="../styles/header_style.css">
    <link rel="stylesheet" href="../styles/Fleet/vehicle_type_car.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <header class="main-header">
        <div class="logo">
            <a href="../index.php"><h1>Drive</h1></a>
        </div>
        <nav class="main-nav">
            <ul class="nav-list">
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropbtn">Fleet</a>
                    <div class="dropdown-content">
                        <a href="vehicle_type_car.php" class="dropdown-link">Cars</a>
                        <a href="#" class="dropdown-link">Motorcycles</a>
                        <a href="#" class="dropdown-link">Pickup Trucks</a>
                    </div>
                </li>
                <li class="nav-item"><a href="#" class="nav-link">Contact Us</a></li>
                <?php if (isset($_SESSION['userId'])): ?>
                    <li class="nav-item"><a href="../customer/customer_homepage.php" class="nav-link">Dashboard</a></li>
                <?php else: ?>
                    <li class="nav-item"><a href="../customer/customer_sign_in.php" class="nav-link">Sign In</a></li>
                    <li class="nav-item"><a href="../customer/customer_sign_up.html" class="nav-link">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <div class="container">
        <aside class="filter-options">
            <h2>Filter by</h2>
            <form id="filter-form">
                <div class="filter-group">
                    <label>Car Make:</label>
                    <?php while ($row = $make_options->fetch_assoc()): ?>
                        <div>
                            <input type="checkbox" name="make[]" value="<?= $row['make'] ?>"> <?= $row['make'] ?>
                        </div>
                    <?php endwhile; ?>
                </div>
                <div class="filter-group">
                    <label>Car Model:</label>
                    <?php while ($row = $model_options->fetch_assoc()): ?>
                        <div>
                            <input type="checkbox" name="model[]" value="<?= $row['model'] ?>"> <?= $row['model'] ?>
                        </div>
                    <?php endwhile; ?>
                </div>
                <div class="filter-group">
                    <label>Car Type:</label>
                    <?php while ($row = $type_options->fetch_assoc()): ?>
                        <div>
                            <input type="checkbox" name="type[]" value="<?= $row['car_type'] ?>"> <?= $row['car_type'] ?>
                        </div>
                    <?php endwhile; ?>
                </div>
                <div class="filter-group">
                    <label>Transmission:</label>
                    <?php while ($row = $transmission_options->fetch_assoc()): ?>
                        <div>
                            <input type="checkbox" name="transmission[]" value="<?= $row['transmission'] ?>"> <?= $row['transmission'] ?>
                        </div>
                    <?php endwhile; ?>
                </div>
                <div class="filter-group">
                    <label for="sort">Sort By:</label>
                    <select id="sort" name="sort">
                        <option value="price_asc">Price: Low to High</option>
                        <option value="price_desc">Price: High to Low</option>
                        <option value="year">Year (New to Old)</option>
                        <option value="make">Make (A to Z)</option>
                    </select>
                </div>
                <button type="button" onclick="applyFilters()">Apply Filters</button>
                <button type="button" onclick="clearFilters()">Clear Filters</button>
            </form>
        </aside>

        <div class="vehicle-list">
            <div class="vehicle-cards" id="vehicle-cards">
                <!-- Vehicle cards will be loaded here via AJAX -->
            </div>
        </div>
    </div>

    <script>
        function applyFilters() {
            const form = document.getElementById('filter-form');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();

            fetch(`filter_cars.php?${params}`)
                .then(response => response.json())
                .then(data => {
                    const vehicleCards = document.getElementById('vehicle-cards');
                    vehicleCards.innerHTML = '';
                    data.forEach(vehicle => {
                        const card = `
                            <div class="vehicle-card">
                                <img src="../admin/${vehicle.image_paths}" alt="Vehicle Image">
                                <h3>${vehicle.make} ${vehicle.model}</h3>
                                <hr>
                                <div class="details">
                                    <div class="first-row">
                                        <div class="detail-item"><i class="fas fa-car"></i> ${vehicle.vehicle_type}</div>
                                        <div class="detail-item"><i class="fas fa-gas-pump"></i> ${vehicle.fuel_type}</div>
                                        <div class="detail-item"><i class="fas fa-calendar-alt"></i> ${vehicle.year}</div>
                                    </div>
                                    <div class="second-row">
                                        <div class="detail-item"><i class="fas fa-cogs"></i> ${vehicle.transmission}</div>
                                        <div class="detail-item"><i class="fas fa-air-freshener"></i> ${vehicle.airbags} Airbags</div>
                                        <div class="detail-item"><i class="fas fa-user-friends"></i> ${vehicle.seats} Seats</div>
                                    </div>
                                </div>
                                <div class="price-box">$${vehicle.price} / day</div>
                                <a href="../customer/customer_booking.php?vehicle_id=${vehicle.vehicle_id}" class="details-button">Rent Now</a>
                            </div>
                        `;
                        vehicleCards.innerHTML += card;
                    });
                });
        }

        function clearFilters() {
            const form = document.getElementById('filter-form');
            form.reset();
            applyFilters();
        }

        // Initial load
        window.onload = applyFilters;
    </script>
</body>
</html>
