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

$vehicle_id = isset($_GET['vehicle_id']) ? $_GET['vehicle_id'] : 0;
$pickup_location = isset($_GET['pickup_location']) ? $_GET['pickup_location'] : '';

// Fetch vehicle details
$sql = "SELECT make, model, price, image_paths, vehicle_type, transmission, seats, fuel_type, year, airbags, doors FROM vehicles WHERE vehicle_id = $vehicle_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $vehicle = $result->fetch_assoc();
} else {
    die("Vehicle not found.");
}

// Fetch vehicle features based on vehicle type
$feature_table = strtolower(str_replace(' ', '_', $vehicle['vehicle_type'])) . "_features";
$feature_sql = "SELECT * FROM $feature_table WHERE vehicle_id = $vehicle_id";
$feature_result = $conn->query($feature_sql);
$features = $feature_result->fetch_assoc();

// Handle the success message after booking
$success_message = '';
if (isset($_SESSION['booking_success']) && $_SESSION['booking_success']) {
    $success_message = 'Booking successful. <a href="customer_bookings_details.php">View / Modify / Cancel Reservation</a>';
    unset($_SESSION['booking_success']);
}

// Define $images and decode image_paths if available
$images = [];
if (!empty($vehicle['image_paths'])) {
    $images = json_decode($vehicle['image_paths']);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Booking - Drive</title>   
    <link rel="stylesheet" href="../styles/header_style.css">
    <link rel="stylesheet" href="../styles/customer_booking_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const pricePerDay = <?php echo $vehicle['price']; ?>;
            const pickupDateInput = document.getElementById('pickup_date');
            const returnDateInput = document.getElementById('return_date');
            const pickupTimeInput = document.getElementById('pickup_time');
            const returnTimeInput = document.getElementById('return_time');
            const priceContainer = document.getElementById('price');
            const priceInput = document.getElementById('price_input');
            const modal = document.getElementById('pricing-modal');
            const infoIcon = document.getElementById('info-icon');
            const closeModal = document.getElementsByClassName('close')[0];
            const bookNowButton = document.querySelector('.form-group button');
            const errorMessage = document.getElementById('login-error');
            const images = <?php echo json_encode($images); ?>;
            let currentImageIndex = 0;

            function updatePrice() {
                const pickupDate = new Date(pickupDateInput.value + ' ' + pickupTimeInput.value);
                const returnDate = new Date(returnDateInput.value + ' ' + returnTimeInput.value);

                if (pickupDate && returnDate && pickupDate < returnDate) {
                    const timeDiff = returnDate - pickupDate;
                    const hours = timeDiff / (1000 * 60 * 60);
                    let totalPrice;

                    if (hours === 24) {
                        totalPrice = pricePerDay;
                    } else if (hours < 24) {
                        totalPrice = pricePerDay * 0.5;
                    } else {
                        const fullDays = Math.floor(hours / 24);
                        const remainingHours = hours % 24;

                        totalPrice = fullDays * pricePerDay;
                        if (remainingHours > 0 && remainingHours <= 24) {
                            totalPrice += pricePerDay * 0.5;
                        }
                    }

                    priceContainer.textContent = `Total Price: $${totalPrice.toFixed(2)}`;
                    priceInput.value = totalPrice.toFixed(2);
                } else {
                    priceContainer.textContent = 'Total Price: $0.00';
                    priceInput.value = '';
                }
            }

            function checkLogin(event) {
                const isLoggedIn = <?php echo isset($_SESSION['userId']) ? 'true' : 'false'; ?>;
                if (!isLoggedIn) {
                    event.preventDefault();
                    errorMessage.style.display = 'block';
                    <?php $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI']; ?>
                }
            }

            function showImage(index) {
                const imageElements = document.querySelectorAll('.carousel img');
                const dots = document.querySelectorAll('.carousel-dots span');
                imageElements.forEach((img, i) => {
                    img.classList.toggle('active', i === index);
                });
                dots.forEach((dot, i) => {
                    dot.classList.toggle('active', i === index);
                });
            }

            function showNextImage() {
                currentImageIndex = (currentImageIndex + 1) % images.length;
                showImage(currentImageIndex);
            }

            function showPrevImage() {
                currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
                showImage(currentImageIndex);
            }

            // Set the minimum and maximum dates for pickup and return
            const today = new Date().toISOString().split('T')[0];
            const maxPickupDate = new Date();
            maxPickupDate.setMonth(maxPickupDate.getMonth() + 5);
            const maxReturnDate = new Date();
            maxReturnDate.setMonth(maxReturnDate.getMonth() + 6);

            pickupDateInput.setAttribute('min', today);
            pickupDateInput.setAttribute('max', maxPickupDate.toISOString().split('T')[0]);
            returnDateInput.setAttribute('min', today);
            returnDateInput.setAttribute('max', maxReturnDate.toISOString().split('T')[0]);

            // Populate the time fields with 30-minute intervals in 12-hour format
            const timeOptions = [];
            for (let hour = 0; hour < 24; hour++) {
                for (let minute = 0; minute < 60; minute += 30) {
                    let hh = hour % 12 === 0 ? 12 : hour % 12;
                    let mm = minute.toString().padStart(2, '0');
                    let period = hour < 12 ? 'AM' : 'PM';
                    timeOptions.push(`${hh}:${mm} ${period}`);
                }
            }

            function populateTimeOptions(id) {
                const timeField = document.getElementById(id);
                timeOptions.forEach(time => {
                    const option = document.createElement('option');
                    option.value = time;
                    option.text = time;
                    timeField.add(option);
                });
            }

            populateTimeOptions('pickup_time');
            populateTimeOptions('return_time');

            pickupDateInput.addEventListener('change', updatePrice);
            returnDateInput.addEventListener('change', updatePrice);
            pickupTimeInput.addEventListener('change', updatePrice);
            returnTimeInput.addEventListener('change', updatePrice);

            infoIcon.onclick = function () {
                modal.style.display = 'block';
            }

            closeModal.onclick = function () {
                modal.style.display = 'none';
            }

            window.onclick = function (event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }

            bookNowButton.addEventListener('click', checkLogin);

            const successMessage = '<?php echo $success_message; ?>';
            if (successMessage) {
                document.getElementById('success-message').innerHTML = successMessage;
                document.getElementById('success-message').style.display = 'block';
            }

            document.querySelector('.carousel .prev').addEventListener('click', showPrevImage);
            document.querySelector('.carousel .next').addEventListener('click', showNextImage);
            document.querySelectorAll('.carousel-dots span').forEach((dot, index) => {
                dot.addEventListener('click', () => showImage(index));
            });

            showImage(currentImageIndex); // Show the first image by default
        });
    </script>
</head>

<body>
    <header class="main-header">
        <!-- Header content remains the same -->
        <div class="logo">
            <a href="../index.php"><h1>Drive</h1></a>
        </div>
        <nav class="main-nav">
            <ul class="nav-list">
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropbtn">Fleet</a>
                    <div class="dropdown-content">
                        <a href="../fleet/cars.html" class="dropdown-link">Cars</a>
                        <a href="#" class="dropdown-link">Motorcycles</a>
                        <a href="#" class="dropdown-link">Pickup Trucks</a>
                    </div>
                </li>
                <li class="nav-item"><a href="#" class="nav-link">Contact Us</a></li>
                <li class="nav-item"><a href="customer_sign_in.html">Sign In</a></li>
                <li class="nav-item"><a href="customer_sign_up.html">Sign Up</a></li>
            </ul>
        </nav>
    </header>
    <main class="main-content">
        <div class="vehicle-details">
            <h2><?php echo "{$vehicle['make']} {$vehicle['model']}"; ?></h2>
            <div class="intro-text">
                <?php
                if ($vehicle['vehicle_type'] == 'Car') {
                    echo "Experience the best ride with our premium cars.";
                } elseif ($vehicle['vehicle_type'] == 'Pickup Truck') {
                    echo "Get the job done with our powerful pickup trucks.";
                } elseif ($vehicle['vehicle_type'] == 'Motorcycle') {
                    echo "Feel the thrill of the open road with our motorcycles.";
                }
                ?>
            </div>
            <div class="details-container">
                <div class="vehicle-info">
                    <!-- Vehicle info content remains the same -->
                    <h3>Details</h3>
                    <p><i class='fas fa-car'></i> Type: <?php echo $vehicle['vehicle_type']; ?></p>
                    <p><i class='fas fa-gas-pump'></i> Fuel Type: <?php echo $vehicle['fuel_type']; ?></p>
                    <p><i class='fas fa-calendar-alt'></i> Year: <?php echo $vehicle['year']; ?></p>
                    <p><i class='fas fa-cogs'></i> Transmission: <?php echo $vehicle['transmission']; ?></p>
                    <p><i class='fas fa-air-freshener'></i> Airbags: <?php echo $vehicle['airbags']; ?></p>
                    <p><i class='fas fa-door-closed'></i> Doors: <?php echo $vehicle['doors']; ?></p>
                    <p><i class='fas fa-user-friends'></i> Seats: <?php echo $vehicle['seats']; ?></p>
                    <p><i class='fas fa-dollar-sign'></i> Price: $<?php echo $vehicle['price']; ?> / day</p>
                </div>

                <div class="vehicle-features">
                    <h3>Features</h3>
                    <?php
                    if ($vehicle['vehicle_type'] == 'Car') {
                        echo "<p><i class='fas fa-car'></i> ABS: " . ($features['ABS'] ? 'Yes' : 'No') . "</p>";
                        echo "<p><i class='fas fa-camera'></i> Rearview Camera: " . ($features['rearview_camera'] ? 'Yes' : 'No') . "</p>";
                        echo "<p><i class='fas fa-thermometer-three-quarters'></i> Air Conditioning: " . ($features['air_conditioning'] ? 'Yes' : 'No') . "</p>";
                    } elseif ($vehicle['vehicle_type'] == 'Pickup Truck') {
                        echo "<p><i class='fas fa-snowflake'></i> Air Conditioning: " . ($features['air_conditioning'] ? 'Yes' : 'No') . "</p>";
                        echo "<p><i class='fas fa-road'></i> Four-Wheel Drive: " . ($features['four_wheel_drive'] ? 'Yes' : 'No') . "</p>";
                    } elseif ($vehicle['vehicle_type'] == 'Motorcycle') {
                        echo "<p><i class='fas fa-biking'></i> ABS: " . ($features['ABS'] ? 'Yes' : 'No') . "</p>";
                        echo "<p><i class='fas fa-biking'></i> Bluetooth: " . ($features['bluetooth'] ? 'Yes' : 'No') . "</p>";
                    }
                    ?>
                </div>
            </div>
            <div class="vehicle-image carousel">
                <?php
                foreach ($images as $index => $imagePath) {
                    echo "<img src='../admin/$imagePath' class='" . ($index === 0 ? 'active' : '') . "' alt='Vehicle Image'>";
                }
                ?>
                <a class="prev">&#10094;</a>
                <a class="next">&#10095;</a>
            </div>
            <div class="carousel-dots">
                <?php
                foreach ($images as $index => $imagePath) {
                    echo "<span class='" . ($index === 0 ? 'active' : '') . "'></span>";
                }
                ?>
            </div>
        </div>

        <div class="booking-section">
            <form class="booking-form" action="process_booking.php" method="POST">
                <div class="date-time-container">
                    <div class="form-group">
                        <label for="return_location">Return Location:</label>
                        <select id="return_location" name="return_location" required>
                            <option value="" disabled selected>Select a location</option>
                            <option value="Ajax">Ajax</option>
                            <option value="Burlington">Burlington</option>
                            <option value="Etobicoke">Etobicoke</option>
                            <option value="Georgetown">Georgetown</option>
                            <option value="Halton Hills">Halton Hills</option>
                            <option value="Kitchener">Kitchener</option>
                            <option value="Milton">Milton</option>
                            <option value="Mississauga">Mississauga</option>
                            <option value="Oakville">Oakville</option>
                            <option value="Scarborough">Scarborough</option>
                            <option value="Toronto">Toronto</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="pickup_date">Pickup Date:</label>
                        <input type="date" id="pickup_date" name="pickup_date" required>
                    </div>
                    <div class="form-group">
                        <label for="pickup_time">Pickup Time:</label>
                        <select id="pickup_time" name="pickup_time" required></select>
                    </div>
                    <div class="form-group">
                        <label for="return_date">Return Date:</label>
                        <input type="date" id="return_date" name="return_date" required>
                    </div>
                    <div class="form-group">
                        <label for="return_time">Return Time:</label>
                        <select id="return_time" name="return_time" required></select>
                    </div>
                </div>
                <input type="hidden" name="vehicle_id" value="<?php echo $vehicle_id; ?>">
                <input type="hidden" name="pickup_location" value="<?php echo $pickup_location; ?>">
                <input type="hidden" name="price" id="price_input" value="">
                <div class="price-container" id="price">
                    Total Price: $0.00
                    <span class="info-icon" id="info-icon">
                        <i class="fas fa-info-circle"></i>
                    </span>
                </div>
                <div class="form-group">
                    <button type="submit">Book Now</button>
                </div>
                <div id="login-error" class="error-message">
                    You need to <a href="../customer/customer_sign_in.php">sign in</a> or <a href="../customer/customer_sign_up.html">sign up</a> to book a vehicle.
                </div>
                <div id="success-message" class="success-message">
                    <!-- Success message will be displayed here -->
                </div>
            </form>
        </div>
    </main>

    <div id="pricing-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h4>Pricing Information</h4>
            <p>Our pricing is calculated based on the rental duration:</p>
            <ul>
                <li>For rentals of exactly 24 hours, the charge is $<?php echo $vehicle['price']; ?> per day.</li>
                <li>For rentals less than 24 hours, the charge is $<?php echo $vehicle['price'] / 2; ?> (half-day rate).</li>
                <li>For rentals longer than 24 hours, the charge is $<?php echo $vehicle['price']; ?> per day plus $<?php echo $vehicle['price'] / 2; ?> for any remaining hours up to 24 hours.</li>
            </ul>
            <p>Total price will be displayed based on your selected pickup and return times.</p>
        </div>
    </div>
</body>

</html>
