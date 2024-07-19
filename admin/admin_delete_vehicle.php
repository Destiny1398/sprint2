<?php
include('session.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_sign_in.html");
    exit();
}

$admin_email = $_SESSION['admin_email'];

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

// Fetch all vehicles
$sql = "SELECT vehicle_id, vehicle_type, make, model, year, price FROM vehicles";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Vehicle</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <style>
     
        .confirmation-message {
            background-color: #d4edda;
            color: #155724;
            padding: 10px;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            margin-top: 20px;
            text-align: center;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .form-section {
            margin: 20px 0;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            position: relative; /* Add position relative to th */
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .delete-button {
            background-color: #f44336;
            width: 100%;
            color: white;
            border: none;
            padding: 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }
        .delete-button:hover {
            background-color: #e53935;
        }
        .filter-dropdown {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .filter-dropdown a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .filter-dropdown a:hover {
            background-color: #f1f1f1;
        }
        .filter-btn {
            cursor: pointer;
        }
        .show {
            display: block;
        }
        #confirmationMessage {
            display: none; /* Initially hidden */
        }
        .delete-more-btn {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 0; /* Adjust the margin */
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none; /* Ensure it is a button style */
            cursor: pointer; /* Add cursor pointer */
        }
        .delete-more-btn:hover {
            background-color: #0056b3;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function showFilter() {
            document.getElementById("filterDropdown").classList.toggle("show");
        }

        function filterVehicles(vehicleType) {
            var rows = document.querySelectorAll("table tbody tr");
            rows.forEach(row => {
                var type = row.children[1].textContent;
                if (vehicleType === "All" || type === vehicleType) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
            document.getElementById("filterDropdown").classList.remove("show");
        }

        function deleteVehicle(vehicleId) {
            if (confirm('Are you sure you want to delete this vehicle?')) {
                $.ajax({
                    url: 'delete_vehicle.php',
                    type: 'POST',
                    data: { vehicle_id: vehicleId },
                    success: function(response) {
                        if (response === 'success') {
                            document.getElementById('confirmationMessage').innerText = 'Vehicle deleted. ';
                            document.getElementById('confirmationMessage').style.display = 'flex';
                            document.querySelector('.form-section').style.display = 'none';
                        } else {
                            alert('Failed to delete vehicle.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                        alert('Error in deleting vehicle.');
                    }
                });
            }
        }

        function showDeleteVehicleForm() {
            document.getElementById('confirmationMessage').style.display = 'none';
            document.querySelector('.form-section').style.display = 'block';
        }

        window.onclick = function(event) {
            if (!event.target.matches('.filter-btn')) {
                var dropdowns = document.getElementsByClassName("filter-dropdown");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }
    </script>
</head>
<body>
    <div class="admin-sidebar">
        <h2>Drive | Admin Panel</h2>
        <a href="admin_dashboard.php" class="nav-link" data-target="dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="admin_vehicles.php" class="nav-link" data-target="vehicles"><i class="fas fa-car-side"></i> Vehicles</a>
        <a href="admin_brands.php" class="nav-link" data-target="brands"><i class="fas fa-car"></i> Brands</a>
        <a href="admin_bookings.php" class="nav-link" data-target="bookings"><i class="fas fa-book"></i> Bookings</a>
        <a href="admin_testimonials.php" class="nav-link" data-target="testimonials"><i class="fas fa-comments"></i> Manage Testimonials</a>
        <a href="admin_contact-us.php" class="nav-link" data-target="contact-us"><i class="fas fa-question-circle"></i> Manage Contact Us Query</a>
        <a href="admin_users.php" class="nav-link" data-target="users"><i class="fas fa-users"></i> Reg Users</a>
        <a href="admin_pages.php" class="nav-link" data-target="pages"><i class="fas fa-file-alt"></i> Manage Pages</a>
    </div>
    <div class="admin-main-content">
        <div class="admin-navbar">
            <div>Delete Vehicle</div>
            <div class="admin-account">
                <img src="path_to_admin_profile_picture.jpg" alt="Admin" width="30" height="30">
                <span><?php echo htmlspecialchars($admin_email); ?></span>
            </div>
        </div>
        <div id="confirmationMessage" class="confirmation-message">
            Vehicle deleted.
            <button class="delete-more-btn" onclick="showDeleteVehicleForm()">Delete More Vehicles</button>
        </div>
        <div class="form-section">
            <h2>Vehicle List</h2>
            <table>
                <thead>
                    <tr>
                        <th>Vehicle ID</th>
                        <th>Vehicle Type <span class="filter-btn" onclick="showFilter()">&#9662;</span>
                            <div id="filterDropdown" class="filter-dropdown">
                                <a href="#" onclick="filterVehicles('All')">All</a>
                                <a href="#" onclick="filterVehicles('Car')">Car</a>
                                <a href="#" onclick="filterVehicles('Pickup Truck')">Pickup Truck</a>
                                <a href="#" onclick="filterVehicles('Motorcycle')">Motorcycle</a>
                            </div>
                        </th>
                        <th>Make</th>
                        <th>Model</th>
                        <th>Year</th>
                        <th>Price</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['vehicle_id']; ?></td>
                            <td><?php echo $row['vehicle_type']; ?></td>
                            <td><?php echo $row['make']; ?></td>
                            <td><?php echo $row['model']; ?></td>
                            <td><?php echo $row['year']; ?></td>
                            <td><?php echo $row['price']; ?></td>
                            <td>
                                <button class="delete-button" onclick="deleteVehicle(<?php echo $row['vehicle_id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
