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
    <title>Edit Vehicle</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <style>
       
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
        .edit-button {
            background-color: #4CAF50;
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
        .edit-button:hover {
            background-color: #45a049;
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
    </style>
    <script>
        function editVehicle(vehicleId) {
            // Redirect to the edit vehicle page with the selected vehicle ID
            window.location.href = 'admin_edit_vehicle_details.php?vehicle_id=' + vehicleId;
        }

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
            <div>Edit Vehicle</div>
            <div class="admin-account">
                <img src="path_to_admin_profile_picture.jpg" alt="Admin" width="30" height="30">
                <span><?php echo htmlspecialchars($admin_email); ?></span>
            </div>
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
                        <th>Edit</th>
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
                                <button class="edit-button" onclick="editVehicle(<?php echo $row['vehicle_id']; ?>)">Edit</button>
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
