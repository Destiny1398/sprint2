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

$booking_id = isset($_GET['booking_id']) ? $_GET['booking_id'] : 0;

// Fetch booking details
$sql = "SELECT b.booking_id, b.vehicle_id, b.pickup_location, b.return_location, b.pickup_date, b.pickup_time, b.return_date, b.return_time, b.price, v.make, v.model, v.price as vehicle_price 
        FROM bookings b 
        JOIN vehicles v ON b.vehicle_id = v.vehicle_id 
        WHERE b.booking_id = $booking_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $booking = $result->fetch_assoc();
} else {
    die("Booking not found.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Booking - Drive</title>
    <link rel="stylesheet" href="../styles/customer_homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
body {
    font-family: Arial, sans-serif;
    background-color: #F2F7F6;
}

.booking-details {
    width: 78%;
    margin: 0 auto;
    margin-top: 2rem;
    padding: 2rem;
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.booking-details h2 {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.form-group {
    margin-bottom: 1rem;
    width: 90%;
    margin-left: auto;
    margin-right: auto;
}

.form-group label {
    display: block;
    font-size: 1rem;
    margin-bottom: 0.5rem;
}

.form-group input, .form-group select {
    width: 100%;
    padding: 0.75rem;
    font-size: 1rem;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.form-group input[type="date"] {
    width: 100%;
}

.price-container {
    font-size: 1.5rem;
    font-weight: bold;
    margin-top: 1rem;
    margin-bottom: 1rem;
    
    margin-left: 5%;
}

.form-group button {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    border: none;
    border-radius: 5px;
    background-color: #0056b3;
    color: white;
    cursor: pointer;
    width: 80%;
    margin-left: auto;
    margin-right: auto;
    display: block;
}

.form-group button:hover {
    background-color: #004494;
}

.info-icon {
    cursor: pointer;
    margin-left: 0.5rem;
    color: #0056b3;
    display: inline-flex;
    align-items: center;
}

.info-icon:hover {
    color: #004494;
}

.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5);
    padding-top: 60px;
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 500px;
    border-radius: 10px;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
.success-message {
    font-size: 1.5rem;
    font-weight: bold;
    color: green;
    text-align: center;
    margin-top: 2rem;
}
    </style>
    <script>
       document.addEventListener('DOMContentLoaded', function() {
           const pricePerDay = <?php echo $booking['vehicle_price']; ?>;
           const pickupDateInput = document.getElementById('pickup_date');
           const returnDateInput = document.getElementById('return_date');
           const pickupTimeInput = document.getElementById('pickup_time');
           const returnTimeInput = document.getElementById('return_time');
           const priceContainer = document.getElementById('price');
           const priceInput = document.getElementById('price_input');
           const modal = document.getElementById('pricing-modal');
           const infoIcon = document.getElementById('info-icon');
           const closeModal = document.getElementsByClassName('close')[0];
           const updateButton = document.querySelector('.form-group button');

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

           // Set the minimum and maximum dates for pickup and return
           const today = new Date().toISOString().split('T')[0];
           pickupDateInput.setAttribute('min', today);
           returnDateInput.setAttribute('min', today);

           pickupDateInput.addEventListener('change', function() {
               returnDateInput.setAttribute('min', pickupDateInput.value);
               updatePrice();
           });
           returnDateInput.addEventListener('change', updatePrice);
           pickupTimeInput.addEventListener('change', updatePrice);
           returnTimeInput.addEventListener('change', updatePrice);

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

           infoIcon.onclick = function() {
               modal.style.display = 'block';
           }

           closeModal.onclick = function() {
               modal.style.display = 'none';
           }

           window.onclick = function(event) {
               if (event.target == modal) {
                   modal.style.display = 'none';
               }
           }

           updateButton.addEventListener('click', function(event) {
               event.preventDefault();
               const formData = new FormData(document.querySelector('.booking-form'));
               fetch('update_booking.php', {
                   method: 'POST',
                   body: formData
               }).then(response => response.text()).then(data => {
                   alert(data);
                   window.location.href = 'customer_bookings_details.php';
               });
           });

           updatePrice();
       });
    </script>
</head>
<body>
    <header id="cust_homepage_main_header">
        <div id="cust_homepage_logo">
            <a href="../index.html"><h1>Drive</h1></a>
        </div>
        <nav class="main-nav">
            <ul id="cust_homepage_nav_list">
                <li id="cust_homepage_nav_item"><a href="../index.php" id="cust_homepage_nav_link">Home</a></li>
                <li id="cust_homepage_nav_item" class="dropdown cust_homepage_dropdown">
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
                        <?php if (isset($_SESSION['firstName'])) : ?>
                            <img src="../<?php echo htmlspecialchars($_SESSION['profileImagePath']); ?>" alt="Profile Image">
                            <span style="font-size: 18px; color: #333;"><?php echo htmlspecialchars($_SESSION['firstName']); ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="cust_homepage_dropdown_content">
                        <a href="customer_edit_profile.php" class="cust_homepage_dropdown_link">Edit Profile</a>
                        <a href="customer_change_password.php" class="cust_homepage_dropdown_link">Change Password</a>
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
            <?php if (isset($_SESSION['update_success']) && $_SESSION['update_success']) : ?>
                <div class="success-message">
                    Booking successfully updated.
                </div>
                <?php unset($_SESSION['update_success']); ?>
            <?php else : ?>
                <div class="booking-details">
                    <h2>Modify Booking for <?php echo "{$booking['make']} {$booking['model']}"; ?></h2>
                    <form class="booking-form" method="POST" action="update_booking.php">
                        <div class="form-group">
                            <label for="pickup_location">Pickup Location:</label>
                            <input type="text" id="pickup_location" name="pickup_location" value="<?php echo htmlspecialchars($booking['pickup_location']); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="return_location">Return Location:</label>
                            <select id="return_location" name="return_location" required>
                                <option value="" disabled>Select location</option>
                                <?php
                                $locations = ["Ajax", "Burlington", "Etobicoke", "Georgetown", "Halton Hills", "Hamilton", "Kitchener", "Milton", "Mississauga", "Oakville", "Scarborough", "Toronto"];
                                foreach ($locations as $location) {
                                    $selected = ($booking['return_location'] === $location) ? 'selected' : '';
                                    echo "<option value=\"$location\" $selected>$location</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="date-time-container">
                            <div class="form-group">
                                <label for="pickup_date">Pickup Date:</label>
                                <input type="date" id="pickup_date" name="pickup_date" value="<?php echo htmlspecialchars($booking['pickup_date']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="pickup_time">Pickup Time:</label>
                                <select id="pickup_time" name="pickup_time" required>
                                    <option value="">Select Time</option>
                                    <?php
                                    foreach ($timeOptions as $time) {
                                        $selected = ($booking['pickup_time'] === $time) ? 'selected' : '';
                                        echo "<option value=\"$time\" $selected>$time</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="return_date">Return Date:</label>
                                <input type="date" id="return_date" name="return_date" value="<?php echo htmlspecialchars($booking['return_date']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="return_time">Return Time:</label>
                                <select id="return_time" name="return_time" required>
                                    <option value="">Select Time</option>
                                    <?php
                                    foreach ($timeOptions as $time) {
                                        $selected = ($booking['return_time'] === $time) ? 'selected' : '';
                                        echo "<option value=\"$time\" $selected>$time</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="price-container" id="price">
                            Price per Day: $<?php echo number_format($booking['vehicle_price'], 2); ?>
                            <br>
                            <br>
                            Total Price: $<?php echo number_format($booking['price'], 2); ?>
                        </div>
                        <input type="hidden" name="price" id="price_input" value="<?php echo $booking['price']; ?>">
                        <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                        <div class="form-group">
                            <button type="submit">Update Booking</button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
