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

    // Fetch bookings of the logged-in user
    $sql = "SELECT b.booking_id, v.make, v.model, b.pickup_location, b.return_location, b.pickup_date, b.pickup_time, b.return_date, b.return_time, b.price 
            FROM bookings b 
            JOIN vehicles v ON b.vehicle_id = v.vehicle_id 
            WHERE b.customer_id = ? AND b.booking_cancelled_date IS NULL
            ORDER BY b.pickup_date DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cust_id);
    $stmt->execute();
    $bookingsResult = $stmt->get_result();
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Drive</title>
    <link rel="stylesheet" href="../styles/customer_homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body{
            margin:0;
            padding:0;
        }
        #cust_homepage_sidebar {
            width: 16%;
            background-color: #000;
            color: #fff;
            padding-top: 20px;
            display: flex;
            flex-direction: column;
            align-self: stretch; /* Ensure the sidebar stretches to full height */
        }
        h1 {
            text-align: center;
            margin-right: 44%;
        }
        .container {
            width: 60%;
            margin: 2rem auto;
            gap: 2rem;
        }
        .booking-container {
            background-color: black;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
            width: 100%;
            font-weight: lighter;
            color: white;
        }
        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .booking-header h2 {
            font-size: 1.5rem;
        }
        .booking-details p {
            font-size: 1rem;
            margin: 1rem 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .booking-actions {
            display: flex;
            gap: 1rem;
        }
        .booking-actions button {
            padding: 0.5rem 1rem;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            background-color: #0056b3;
            color: white;
            cursor: pointer;
        }
        .booking-actions button:hover {
            background-color: #004494;
        }
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
            margin-left:0%;
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
        .modal-confirm-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .modal-confirm-buttons button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .confirm-yes {
            background-color: #d9534f;
            color: white;
        }
        .confirm-no {
            background-color: #5bc0de;
            color: white;
        }
    </style>
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
                        <?php if (isset($firstName)) : ?>
                            <img src="../<?php echo htmlspecialchars($profileImagePath); ?>" alt="Profile Image">
                            <span style="font-size: 18px;  color: #333;"><?php echo htmlspecialchars($firstName); ?></span>
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
            <h1>My Bookings</h1>
            <div class="container">
                <?php if (isset($bookingsResult) && $bookingsResult->num_rows > 0): ?>
                    <?php while ($booking = $bookingsResult->fetch_assoc()): ?>
                        <div class="booking-container" id="booking-container-<?php echo $booking['booking_id']; ?>">
                            <div class="booking-header">
                                <h2><?php echo "{$booking['make']} {$booking['model']}"; ?></h2>
                                <div class="booking-actions">
                                    <button onclick="location.href='modify_booking.php?booking_id=<?php echo $booking['booking_id']; ?>'">Modify</button>
                                    <button onclick="showCancelModal(<?php echo $booking['booking_id']; ?>)">Cancel</button>
                                </div>
                            </div>
                            <div class="booking-details">
                                <p><i class="fas fa-map-marker-alt"></i> Pickup Location: <?php echo $booking['pickup_location']; ?></p>
                                <p><i class="fas fa-map-marker-alt"></i> Return Location: <?php echo $booking['return_location']; ?></p>
                                <p><i class="fas fa-calendar-alt"></i> Pickup Date: <?php echo $booking['pickup_date']; ?></p>
                                <p><i class="fas fa-clock"></i> Pickup Time: <?php echo $booking['pickup_time']; ?></p>
                                <p><i class="fas fa-calendar-alt"></i> Return Date: <?php echo $booking['return_date']; ?></p>
                                <p><i class="fas fa-clock"></i> Return Time: <?php echo $booking['return_time']; ?></p>
                                <p><i class="fas fa-dollar-sign"></i> Price: $<?php echo number_format($booking['price'], 2); ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No bookings found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal for cancellation confirmation -->
    <div id="cancelModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeCancelModal()">&times;</span>
            <p>Are you sure you want to cancel the booking?</p>
            <div class="modal-confirm-buttons">
                <button class="confirm-yes" onclick="cancelBooking()">Yes</button>
                <button class="confirm-no" onclick="closeCancelModal()">No</button>
            </div>
        </div>
    </div>

    <script>
        let currentBookingId = null;

        function showCancelModal(bookingId) {
            currentBookingId = bookingId;
            document.getElementById('cancelModal').style.display = 'block';
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').style.display = 'none';
            currentBookingId = null;
        }

        function cancelBooking() {
            if (currentBookingId) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'cancel_booking.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200 && xhr.responseText === 'Booking successfully cancelled.') {
                        const bookingContainer = document.getElementById('booking-container-' + currentBookingId);
                        bookingContainer.innerHTML = '<p>Booking successfully cancelled.</p>';
                        closeCancelModal();
                    } else {
                        alert('Error cancelling booking. Please try again.');
                        closeCancelModal();
                    }
                };
                xhr.send('booking_id=' + currentBookingId);
            }
        }
    </script>
</body>
</html>
