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

// Fetch vehicle details based on vehicle_id
if (isset($_GET['vehicle_id'])) {
    $vehicle_id = $_GET['vehicle_id'];

    // Fetch vehicle details
    $vehicle_query = "SELECT * FROM vehicles WHERE vehicle_id = ?";
    $stmt = $conn->prepare($vehicle_query);
    $stmt->bind_param("i", $vehicle_id);
    $stmt->execute();
    $vehicle_result = $stmt->get_result();
    $vehicle = $vehicle_result->fetch_assoc();
    $stmt->close();

    // Fetch vehicle features
    if ($vehicle['vehicle_type'] === 'Car') {
        $features_query = "SELECT * FROM car_features WHERE vehicle_id = ?";
    } elseif ($vehicle['vehicle_type'] === 'Pickup Truck') {
        $features_query = "SELECT * FROM pickup_truck_features WHERE vehicle_id = ?";
    } elseif ($vehicle['vehicle_type'] === 'Motorcycle') {
        $features_query = "SELECT * FROM motorcycle_features WHERE vehicle_id = ?";
    }

    if (isset($features_query)) {
        $stmt = $conn->prepare($features_query);
        $stmt->bind_param("i", $vehicle_id);
        $stmt->execute();
        $features_result = $stmt->get_result();
        $features = $features_result->fetch_assoc();
        $stmt->close();
    }
} else {
    header("Location: admin_vehicles.php");
    exit();
}

// Handle form submission to update vehicle details
$response = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vehicle_id = $_POST['vehicle_id'];
    $vehicle_type = $_POST['vehicle_type'];
    $make = $_POST['make'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $car_type = isset($_POST['car_type']) ? $_POST['car_type'] : NULL;
    $fuel_type = $_POST['fuel_type'];
    $transmission = isset($_POST['transmission']) ? $_POST['transmission'] : NULL;
    $airbags = isset($_POST['airbags']) ? $_POST['airbags'] : NULL;
    $doors = isset($_POST['doors']) ? $_POST['doors'] : NULL;
    $seats = isset($_POST['seats']) ? (empty($_POST['seats']) ? NULL : $_POST['seats']) : NULL;
    $price = $_POST['price'];
    $location = $_POST['location'];
    $features = isset($_POST['features']) ? $_POST['features'] : [];
    $image_paths = isset($_POST['image_paths']) ? $_POST['image_paths'] : $vehicle['image_paths'];

    // Update vehicle details
    $sql = "UPDATE vehicles SET vehicle_type = ?, make = ?, model = ?, year = ?, car_type = ?, fuel_type = ?, transmission = ?, airbags = ?, doors = ?, seats = ?, price = ?, location = ?, image_paths = ? WHERE vehicle_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssiiidssi", $vehicle_type, $make, $model, $year, $car_type, $fuel_type, $transmission, $airbags, $doors, $seats, $price, $location, $image_paths, $vehicle_id);

    if ($stmt->execute() === TRUE) {
        // Update vehicle features
        if ($vehicle_type === 'Car') {
            $abs = in_array('Anti-lock Braking System (ABS)', $features);
            $rearview_camera = in_array('Rearview Camera', $features);
            $traction_control = in_array('Traction Control', $features);
            $air_conditioning = in_array('Air Conditioning', $features);
            $power_windows_locks = in_array('Power Windows and Locks', $features);
            $keyless_entry = in_array('Keyless Entry', $features);
            $cruise_control = in_array('Cruise Control', $features);
            $adjustable_steering = in_array('Adjustable Steering Wheel', $features);
            $bluetooth = in_array('Bluetooth Connectivity', $features);
            $navigation = in_array('Navigation System', $features);
            $sunroof = in_array('Sunroof', $features);
            $heated_seats = in_array('Heated Seats', $features);

            $feature_sql = "UPDATE car_features SET ABS = ?, rearview_camera = ?, traction_control = ?, air_conditioning = ?, power_windows_locks = ?, keyless_entry = ?, cruise_control = ?, adjustable_steering = ?, bluetooth = ?, navigation = ?, sunroof = ?, heated_seats = ? WHERE vehicle_id = ?";
            $feature_stmt = $conn->prepare($feature_sql);
            $feature_stmt->bind_param("iiiiiiiiiiiii", $abs, $rearview_camera, $traction_control, $air_conditioning, $power_windows_locks, $keyless_entry, $cruise_control, $adjustable_steering, $bluetooth, $navigation, $sunroof, $heated_seats, $vehicle_id);
        } elseif ($vehicle_type === 'Pickup Truck') {
            $air_conditioning = in_array('Air Conditioning', $features);
            $four_wheel_drive = in_array('Four-Wheel Drive (4WD)', $features);
            $bed_liner = in_array('Bed Liner', $features);
            $rearview_camera = in_array('Rearview Camera', $features);
            $blind_spot_monitoring = in_array('Blind Spot Monitoring', $features);
            $lane_departure_warning = in_array('Lane Departure Warning', $features);
            $automatic_emergency_braking = in_array('Automatic Emergency Braking', $features);
            $infotainment_system = in_array('Infotainment System', $features);

            $feature_sql = "UPDATE pickup_truck_features SET air_conditioning = ?, four_wheel_drive = ?, bed_liner = ?, rearview_camera = ?, blind_spot_monitoring = ?, lane_departure_warning = ?, automatic_emergency_braking = ?, infotainment_system = ? WHERE vehicle_id = ?";
            $feature_stmt = $conn->prepare($feature_sql);
            $feature_stmt->bind_param("iiiiiiii", $air_conditioning, $four_wheel_drive, $bed_liner, $rearview_camera, $blind_spot_monitoring, $lane_departure_warning, $automatic_emergency_braking, $infotainment_system, $vehicle_id);
        } elseif ($vehicle_type === 'Motorcycle') {
            $abs = in_array('Anti-lock Braking System (ABS)', $features);
            $multiple_riding_modes = in_array('Multiple Riding Modes', $features);
            $gps_navigation = in_array('GPS Navigation', $features);
            $bluetooth = in_array('Bluetooth Connectivity', $features);
            $security_system = in_array('Security System', $features);
            $mobile_phone_mount = in_array('Mobile Phone Mount', $features);

            $feature_sql = "UPDATE motorcycle_features SET ABS = ?, multiple_riding_modes = ?, GPS_navigation = ?, bluetooth = ?, security_system = ?, mobile_phone_mount = ? WHERE vehicle_id = ?";
            $feature_stmt = $conn->prepare($feature_sql);
            $feature_stmt->bind_param("iiiiiii", $abs, $multiple_riding_modes, $gps_navigation, $bluetooth, $security_system, $mobile_phone_mount, $vehicle_id);
        }

        if (isset($feature_stmt) && $feature_stmt->execute() === TRUE) {
            $response['message'] = "Vehicle and features updated successfully";
        } else {
            $response['message'] = "Error updating features: " . $conn->error;
        }
    } else {
        $response['message'] = "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
    $conn->close();
    echo json_encode($response);
    exit();
}
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
        .hidden {
            display: none;
        }
        .features label {
            display: block;
            align-items: left;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .features input[type="checkbox"] {
            margin-right: 10px;
            width: auto;
        }
        .form-section h2 {
            margin-bottom: 20px;
            font-size: 1.5em;
        }
        .form-section label {
            display: block;
            margin-bottom: 0.5em;
            font-weight: bold;
            text-align: left;
        }
        .form-section input,
        .form-section select,
        .form-section button {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px; /* Increase margin-bottom for better spacing */
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-section button {
            padding: 10px 20px;
            font-size: 1em;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .form-section button:hover {
            background-color: #0056b3;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function toggleFields() {
            const vehicleType = document.getElementById('vehicle_type').value;
            const carFields = document.querySelectorAll('.car-field');
            const truckFields = document.querySelectorAll('.truck-field');
            const motorcycleFields = document.querySelectorAll('.motorcycle-field');
            const carFeatures = document.querySelector('.car-features');
            const truckFeatures = document.querySelector('.truck-features');
            const motorcycleFeatures = document.querySelector('.motorcycle-features');

            carFields.forEach(field => field.classList.add('hidden'));
            truckFields.forEach(field => field.classList.add('hidden'));
            motorcycleFields.forEach(field => field.classList.add('hidden'));
            carFeatures.classList.add('hidden');
            truckFeatures.classList.add('hidden');
            motorcycleFeatures.classList.add('hidden');

            if (vehicleType === 'Car') {
                carFields.forEach(field => field.classList.remove('hidden'));
                carFeatures.classList.remove('hidden');
            } else if (vehicleType === 'Pickup Truck') {
                truckFields.forEach(field => field.classList.remove('hidden'));
                truckFeatures.classList.remove('hidden');
            } else if (vehicleType === 'Motorcycle') {
                motorcycleFields.forEach(field => field.classList.remove('hidden'));
                motorcycleFeatures.classList.remove('hidden');
            }
        }

        $(document).ready(function() {
            toggleFields();
            $('#vehicle_type').change(function() {
                toggleFields();
            });
        });
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
            <h2>Edit Vehicle</h2>
            <form id="edit-vehicle-form" action="" method="POST">
                <input type="hidden" name="vehicle_id" value="<?php echo $vehicle['vehicle_id']; ?>">

                <label for="vehicle_type">Vehicle Type:</label>
                <select id="vehicle_type" name="vehicle_type" required>
                    <option value="Car" <?php if ($vehicle['vehicle_type'] == 'Car') echo 'selected'; ?>>Car</option>
                    <option value="Pickup Truck" <?php if ($vehicle['vehicle_type'] == 'Pickup Truck') echo 'selected'; ?>>Pickup Truck</option>
                    <option value="Motorcycle" <?php if ($vehicle['vehicle_type'] == 'Motorcycle') echo 'selected'; ?>>Motorcycle</option>
                </select>

                <label for="make">Make:</label>
                <input type="text" id="make" name="make" value="<?php echo htmlspecialchars($vehicle['make']); ?>" required>

                <label for="model">Model:</label>
                <input type="text" id="model" name="model" value="<?php echo htmlspecialchars($vehicle['model']); ?>" required>

                <label for="year">Year:</label>
                <input type="number" id="year" name="year" value="<?php echo htmlspecialchars($vehicle['year']); ?>" min="1980" max="<?php echo date('Y'); ?>">

                <div class="car-field <?php if ($vehicle['vehicle_type'] != 'Car') echo 'hidden'; ?>">
                    <label for="car_type">Type:</label>
                    <select id="car_type" name="car_type">
                        <option value="" disabled selected>Select car type</option>
                        <option value="Sedan" <?php if ($vehicle['car_type'] == 'Sedan') echo 'selected'; ?>>Sedan</option>
                        <option value="Hatchback" <?php if ($vehicle['car_type'] == 'Hatchback') echo 'selected'; ?>>Hatchback</option>
                        <option value="SUV" <?php if ($vehicle['car_type'] == 'SUV') echo 'selected'; ?>>SUV</option>
                        <option value="Coupe" <?php if ($vehicle['car_type'] == 'Coupe') echo 'selected'; ?>>Coupe</option>
                        <option value="Convertible" <?php if ($vehicle['car_type'] == 'Convertible') echo 'selected'; ?>>Convertible</option>
                        <option value="Compact" <?php if ($vehicle['car_type'] == 'Compact') echo 'selected'; ?>>Compact</option>
                        <option value="Luxury" <?php if ($vehicle['car_type'] == 'Luxury') echo 'selected'; ?>>Luxury</option>
                        <option value="Sports Car" <?php if ($vehicle['car_type'] == 'Sports Car') echo 'selected'; ?>>Sports Car</option>
                    </select>
                </div>

                <div class="car-field truck-field <?php if ($vehicle['vehicle_type'] != 'Car' && $vehicle['vehicle_type'] != 'Pickup Truck') echo 'hidden'; ?>">
                    <label for="fuel_type">Fuel Type:</label>
                    <select id="fuel_type" name="fuel_type">
                        <option value="" disabled selected>Select fuel type</option>
                        <option value="Electric" <?php if ($vehicle['fuel_type'] == 'Electric') echo 'selected'; ?>>Electric</option>
                        <option value="Hybrid" <?php if ($vehicle['fuel_type'] == 'Hybrid') echo 'selected'; ?>>Hybrid</option>
                        <option value="Gas" <?php if ($vehicle['fuel_type'] == 'Gas') echo 'selected'; ?>>Gasoline</option>
                        <option value="Diesel" <?php if ($vehicle['fuel_type'] == 'Diesel') echo 'selected'; ?>>Diesel</option>
                    </select>
                </div>

                <div class="car-field truck-field <?php if ($vehicle['vehicle_type'] != 'Car' && $vehicle['vehicle_type'] != 'Pickup Truck') echo 'hidden'; ?>">
                    <label for="transmission">Transmission:</label>
                    <select id="transmission" name="transmission">
                        <option value="" disabled selected>Select transmission</option>
                        <option value="Automatic" <?php if ($vehicle['transmission'] == 'Automatic') echo 'selected'; ?>>Automatic</option>
                        <option value="Manual" <?php if ($vehicle['transmission'] == 'Manual') echo 'selected'; ?>>Manual</option>
                    </select>
                </div>

                <div class="car-field truck-field <?php if ($vehicle['vehicle_type'] != 'Car' && $vehicle['vehicle_type'] != 'Pickup Truck') echo 'hidden'; ?>">
                    <label for="airbags">Number of Airbags:</label>
                    <input type="number" id="airbags" name="airbags" value="<?php echo htmlspecialchars($vehicle['airbags']); ?>" min="0" max="12">
                </div>

                <div class="car-field truck-field <?php if ($vehicle['vehicle_type'] != 'Car' && $vehicle['vehicle_type'] != 'Pickup Truck') echo 'hidden'; ?>">
                    <label for="doors">Doors:</label>
                    <input type="number" id="doors" name="doors" value="<?php echo htmlspecialchars($vehicle['doors']); ?>" min="2" max="6">
                </div>

                <div class="car-field truck-field <?php if ($vehicle['vehicle_type'] != 'Car' && $vehicle['vehicle_type'] != 'Pickup Truck') echo 'hidden'; ?>">
                    <label for="seats">Seats:</label>
                    <input type="number" id="seats" name="seats" value="<?php echo htmlspecialchars($vehicle['seats']); ?>" min="1" max="14">
                </div>

                <label for="price">Price:</label>
                <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($vehicle['price']); ?>" required min="1" step="1">
                
                <label for="location">Location:</label>
                <select id="location" name="location">
                    <option value="Ajax" <?php if ($vehicle['location'] == 'Ajax') echo 'selected'; ?>>Ajax</option>
                    <option value="Burlington" <?php if ($vehicle['location'] == 'Burlington') echo 'selected'; ?>>Burlington</option>
                    <option value="Etobicoke" <?php if ($vehicle['location'] == 'Etobicoke') echo 'selected'; ?>>Etobicoke</option>
                    <option value="Georgetown" <?php if ($vehicle['location'] == 'Georgetown') echo 'selected'; ?>>Georgetown</option>
                    <option value="Halton Hills" <?php if ($vehicle['location'] == 'Halton Hills') echo 'selected'; ?>>Halton Hills</option>
                    <option value="Hamilton" <?php if ($vehicle['location'] == 'Hamilton') echo 'selected'; ?>>Hamilton</option>
                    <option value="Kitchener" <?php if ($vehicle['location'] == 'Kitchener') echo 'selected'; ?>>Kitchener</option>
                    <option value="Milton" <?php if ($vehicle['location'] == 'Milton') echo 'selected'; ?>>Milton</option>
                    <option value="Mississauga" <?php if ($vehicle['location'] == 'Mississauga') echo 'selected'; ?>>Mississauga</option>
                    <option value="Oakville" <?php if ($vehicle['location'] == 'Oakville') echo 'selected'; ?>>Oakville</option>
                    <option value="Scarborough" <?php if ($vehicle['location'] == 'Scarborough') echo 'selected'; ?>>Scarborough</option>
                    <option value="Toronto" <?php if ($vehicle['location'] == 'Toronto') echo 'selected'; ?>>Toronto</option>
                </select>

                <!-- New field for image_paths -->
                <label for="image_paths">Image Paths:</label>
                <input type="text" id="image_paths" name="image_paths" value="<?php echo htmlspecialchars($vehicle['image_paths']); ?>" required>

                <div class="features car-features <?php if ($vehicle['vehicle_type'] != 'Car') echo 'hidden'; ?>">
                    <h3>Car Features</h3>
                    <label><input type="checkbox" name="features[]" value="Anti-lock Braking System (ABS)" <?php if ($features['ABS']) echo 'checked'; ?>> Anti-lock Braking System (ABS)</label><br>
                    <label><input type="checkbox" name="features[]" value="Rearview Camera" <?php if ($features['rearview_camera']) echo 'checked'; ?>> Rearview Camera</label><br>
                    <label><input type="checkbox" name="features[]" value="Traction Control" <?php if ($features['traction_control']) echo 'checked'; ?>> Traction Control</label><br>
                    <label><input type="checkbox" name="features[]" value="Air Conditioning" <?php if ($features['air_conditioning']) echo 'checked'; ?>> Air Conditioning</label><br>
                    <label><input type="checkbox" name="features[]" value="Power Windows and Locks" <?php if ($features['power_windows_locks']) echo 'checked'; ?>> Power Windows and Locks</label><br>
                    <label><input type="checkbox" name="features[]" value="Keyless Entry" <?php if ($features['keyless_entry']) echo 'checked'; ?>> Keyless Entry</label><br>
                    <label><input type="checkbox" name="features[]" value="Cruise Control" <?php if ($features['cruise_control']) echo 'checked'; ?>> Cruise Control</label><br>
                    <label><input type="checkbox" name="features[]" value="Adjustable Steering Wheel" <?php if ($features['adjustable_steering']) echo 'checked'; ?>> Adjustable Steering Wheel</label><br>
                    <label><input type="checkbox" name="features[]" value="Bluetooth Connectivity" <?php if ($features['bluetooth']) echo 'checked'; ?>> Bluetooth Connectivity</label><br>
                    <label><input type="checkbox" name="features[]" value="Navigation System" <?php if ($features['navigation']) echo 'checked'; ?>> Navigation System</label><br>
                    <label><input type="checkbox" name="features[]" value="Sunroof" <?php if ($features['sunroof']) echo 'checked'; ?>> Sunroof</label><br>
                    <label><input type="checkbox" name="features[]" value="Heated Seats" <?php if ($features['heated_seats']) echo 'checked'; ?>> Heated Seats</label><br>
                </div>

                <div class="features truck-features <?php if ($vehicle['vehicle_type'] != 'Pickup Truck') echo 'hidden'; ?>">
                    <h3>Pickup Truck Features</h3>
                    <label><input type="checkbox" name="features[]" value="Air Conditioning" <?php if ($features['air_conditioning']) echo 'checked'; ?>> Air Conditioning</label><br>
                    <label><input type="checkbox" name="features[]" value="Four-Wheel Drive (4WD)" <?php if ($features['four_wheel_drive']) echo 'checked'; ?>> Four-Wheel Drive (4WD)</label><br>
                    <label><input type="checkbox" name="features[]" value="Bed Liner" <?php if ($features['bed_liner']) echo 'checked'; ?>> Bed Liner</label><br>
                    <label><input type="checkbox" name="features[]" value="Rearview Camera" <?php if ($features['rearview_camera']) echo 'checked'; ?>> Rearview Camera</label><br>
                    <label><input type="checkbox" name="features[]" value="Blind Spot Monitoring" <?php if ($features['blind_spot_monitoring']) echo 'checked'; ?>> Blind Spot Monitoring</label><br>
                    <label><input type="checkbox" name="features[]" value="Lane Departure Warning" <?php if ($features['lane_departure_warning']) echo 'checked'; ?>> Lane Departure Warning</label><br>
                    <label><input type="checkbox" name="features[]" value="Automatic Emergency Braking" <?php if ($features['automatic_emergency_braking']) echo 'checked'; ?>> Automatic Emergency Braking</label><br>
                    <label><input type="checkbox" name="features[]" value="Infotainment System" <?php if ($features['infotainment_system']) echo 'checked'; ?>> Infotainment System</label><br>
                </div>

                <div class="features motorcycle-features <?php if ($vehicle['vehicle_type'] != 'Motorcycle') echo 'hidden'; ?>">
                    <h3>Motorcycle Features</h3>
                    <label><input type="checkbox" name="features[]" value="Anti-lock Braking System (ABS)" <?php if ($features['ABS']) echo 'checked'; ?>> Anti-lock Braking System (ABS)</label><br>
                    <label><input type="checkbox" name="features[]" value="Multiple Riding Modes" <?php if ($features['multiple_riding_modes']) echo 'checked'; ?>> Multiple Riding Modes</label><br>
                    <label><input type="checkbox" name="features[]" value="GPS Navigation" <?php if ($features['GPS_navigation']) echo 'checked'; ?>> GPS Navigation</label><br>
                    <label><input type="checkbox" name="features[]" value="Bluetooth Connectivity" <?php if ($features['bluetooth']) echo 'checked'; ?>> Bluetooth Connectivity</label><br>
                    <label><input type="checkbox" name="features[]" value="Security System" <?php if ($features['security_system']) echo 'checked'; ?>> Security System</label><br>
                    <label><input type="checkbox" name="features[]" value="Mobile Phone Mount" <?php if ($features['mobile_phone_mount']) echo 'checked'; ?>> Mobile Phone Mount</label><br>
                </div>

                <button type="submit">Update Vehicle</button>
            </form>
        </div>
    </div>
</body>
</html>
