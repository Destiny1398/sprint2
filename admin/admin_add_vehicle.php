<?php
include('session.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_sign_in.html"); // Redirect to login page if not logged in
    exit();
}

$admin_email = $_SESSION['admin_email'];

// Database connection
$servername = "localhost";
$username = "root"; // Change this to your database username
$password = ""; // Change this to your database password
$dbname = "online_vehicle_rental_system"; // Change this to your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get admin ID
$admin_id_query = "SELECT admin_id FROM admin WHERE admin_email = ?";
$stmt = $conn->prepare($admin_id_query);
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$stmt->bind_result($admin_id);
$stmt->fetch();
$stmt->close();

// Function to handle file uploads
function uploadImages($files) {
    $uploadDirectory = "uploads/";
    $uploadedPaths = [];

    // Check if upload directory exists, if not create it
    if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    foreach ($files['name'] as $key => $name) {
        $tmp_name = $files['tmp_name'][$key];
        $path = $uploadDirectory . basename($name);

        if (move_uploaded_file($tmp_name, $path)) {
            $uploadedPaths[] = $path;
        } else {
            die("Failed to upload image: $name");
        }
    }

    return json_encode($uploadedPaths);
}

$response = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $vehicle_type = $_POST['vehicle_type'];
    $make = $_POST['make'];
    $model = $_POST['model'];
    $year = $_POST['year'];
    $car_type = isset($_POST['car_type']) ? $_POST['car_type'] : NULL;
    $fuel_type = $_POST['fuel_type'];
    $transmission = isset($_POST['transmission']) ? $_POST['transmission'] : NULL;
    $airbags = isset($_POST['airbags']) ? $_POST['airbags'] : NULL;
    $seats = isset($_POST['seats']) ? (empty($_POST['seats']) ? NULL : $_POST['seats']) : NULL;
    $doors = isset($_POST['doors']) ? $_POST['doors'] : NULL;
    $price = $_POST['price'];
    $location = $_POST['location'];
    $features = isset($_POST['features']) ? $_POST['features'] : [];

    $image_paths = uploadImages($_FILES['images']);

    $sql = "INSERT INTO vehicles (vehicle_type, make, model, year, car_type, fuel_type, transmission, airbags, seats, doors, price, location, image_paths, admin_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssiiidssi", $vehicle_type, $make, $model, $year, $car_type, $fuel_type, $transmission, $airbags, $seats, $doors, $price, $location, $image_paths, $admin_id);

    if ($stmt->execute() === TRUE) {
        $vehicle_id = $stmt->insert_id;

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
            
            $feature_sql = "INSERT INTO car_features (vehicle_id, ABS, rearview_camera, traction_control, air_conditioning, power_windows_locks, keyless_entry, cruise_control, adjustable_steering, bluetooth, navigation, sunroof, heated_seats, admin_id)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $feature_stmt = $conn->prepare($feature_sql);
            $feature_stmt->bind_param("iiiiiiiiiiiiii", $vehicle_id, $abs, $rearview_camera, $traction_control, $air_conditioning, $power_windows_locks, $keyless_entry, $cruise_control, $adjustable_steering, $bluetooth, $navigation, $sunroof, $heated_seats, $admin_id);
        } elseif ($vehicle_type === 'Pickup Truck') {
            $air_conditioning = in_array('Air Conditioning', $features);
            $four_wheel_drive = in_array('Four-Wheel Drive (4WD)', $features);
            $bed_liner = in_array('Bed Liner', $features);
            $rearview_camera = in_array('Rearview Camera', $features);
            $blind_spot_monitoring = in_array('Blind Spot Monitoring', $features);
            $lane_departure_warning = in_array('Lane Departure Warning', $features);
            $automatic_emergency_braking = in_array('Automatic Emergency Braking', $features);
            $infotainment_system = in_array('Infotainment System', $features);
            
            $feature_sql = "INSERT INTO pickup_truck_features (vehicle_id, air_conditioning, four_wheel_drive, bed_liner, rearview_camera, blind_spot_monitoring, lane_departure_warning, automatic_emergency_braking, infotainment_system, admin_id)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $feature_stmt = $conn->prepare($feature_sql);
            $feature_stmt->bind_param("iiiiiiiiii", $vehicle_id, $air_conditioning, $four_wheel_drive, $bed_liner, $rearview_camera, $blind_spot_monitoring, $lane_departure_warning, $automatic_emergency_braking, $infotainment_system, $admin_id);
        } elseif ($vehicle_type === 'Motorcycle') {
            $abs = in_array('Anti-lock Braking System (ABS)', $features);
            $multiple_riding_modes = in_array('Multiple Riding Modes', $features);
            $gps_navigation = in_array('GPS Navigation', $features);
            $bluetooth = in_array('Bluetooth Connectivity', $features);
            $security_system = in_array('Security System', $features);
            $mobile_phone_mount = in_array('Mobile Phone Mount', $features);
            
            $feature_sql = "INSERT INTO motorcycle_features (vehicle_id, ABS, multiple_riding_modes, GPS_navigation, bluetooth, security_system, mobile_phone_mount, admin_id)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $feature_stmt = $conn->prepare($feature_sql);
            $feature_stmt->bind_param("iiiiiiii", $vehicle_id, $abs, $multiple_riding_modes, $gps_navigation, $bluetooth, $security_system, $mobile_phone_mount, $admin_id);
        }

        if (isset($feature_stmt) && $feature_stmt->execute() === TRUE) {
            $response['message'] = "New vehicle and features added successfully";
        } else {
            $response['message'] = "Error adding features: " . $conn->error;
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
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../styles/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.form-section').forEach(section => {
                section.style.display = 'none';
            });
            // Show the selected section
            document.getElementById(sectionId).style.display = 'block';
        }

        function populateYearOptions() {
            const currentYear = new Date().getFullYear();
            const yearSelect = document.getElementById('year');
            for (let year = currentYear; year >= 1980; year--) {
                const option = document.createElement('option');
                option.value = year;
                option.text = year;
                yearSelect.appendChild(option);
            }
        }

        function populateNumberOptions(selectElement, start, end) {
            for (let i = start; i <= end; i++) {
                const option = document.createElement('option');
                option.value = i;
                option.text = i;
                selectElement.appendChild(option);
            }
        }

        function clearFields() {
            document.querySelectorAll('.form-section input, .form-section select').forEach(field => {
                if (field.tagName === 'SELECT') {
                    field.selectedIndex = 0;
                } else {
                    field.value = '';
                }
            });
        }

        function toggleFields() {
            const vehicleType = document.getElementById('vehicle_type').value;
            const makeField = document.getElementById('make');
            const modelField = document.getElementById('model');
            const yearField = document.getElementById('year');
            const carTypeField = document.getElementById('car_type');
            const fuelTypeField = document.getElementById('fuel_type');
            const transmissionField = document.getElementById('transmission');
            const airbagsField = document.getElementById('airbags');
            const seatsField = document.getElementById('seats');
            const doorsField = document.getElementById('doors');
            const priceField = document.getElementById('price');

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

            // Clear existing make and model options
            makeField.innerHTML = '<option value="" disabled selected>Select the make of vehicle</option>';
            modelField.innerHTML = '<option value="" disabled selected>Select the model of vehicle</option>';
            yearField.selectedIndex = 0;
            carTypeField.selectedIndex = 0;
            fuelTypeField.selectedIndex = 0;
            transmissionField.selectedIndex = 0;
            airbagsField.selectedIndex = 0;
            seatsField.selectedIndex = 0;
            doorsField.selectedIndex = 0;
            priceField.value = '';

            const makesAndModels = {
                Car: {
                    "Toyota": ["Corolla", "Camry", "Prius", "RAV4", "Highlander", "Tacoma", "Tundra"],
                    "Honda": ["Civic", "Accord", "Fit", "CR-V", "Pilot", "Odyssey"],
                    "Ford": ["Fusion", "Explorer", "Escape", "F-150", "Ranger", "Mustang"],
                    "Chevrolet": ["Malibu", "Equinox", "Traverse", "Silverado 1500", "Colorado", "Camaro"],
                    "Nissan": ["Altima", "Sentra", "Versa Note", "Rogue", "Pathfinder", "Frontier"],
                    "BMW": ["3 Series", "5 Series", "X5", "X3", "X7", "4 Series Gran Coupe"],
                    "Mercedes-Benz": ["C-Class", "E-Class", "GLE", "GLC", "GLS", "A-Class"],
                    "Audi": ["A3", "A4", "Q5", "Q7", "A8", "A5 Sportback"],
                    "Hyundai": ["Elantra", "Sonata", "Santa Fe", "Tucson", "Palisade", "Kona"],
                    "Volkswagen": ["Golf", "Passat", "Jetta", "Tiguan", "Atlas"]
                },
                "Pickup Truck": {
                    "Ford": ["F-150", "F-250 Super Duty", "F-350 Super Duty", "Ranger", "Maverick"],
                    "Chevrolet": ["Silverado 1500", "Silverado 2500HD", "Silverado 3500HD", "Colorado"],
                    "Ram (formerly Dodge Ram)": ["1500", "2500", "3500"],
                    "Toyota": ["Tacoma", "Tundra"],
                    "GMC": ["Sierra 1500", "Sierra 2500HD", "Sierra 3500HD", "Canyon"],
                    "Nissan": ["Frontier", "Titan", "Titan XD"],
                    "Honda": ["Ridgeline"],
                    "Jeep": ["Gladiator"],
                    "Rivian": ["R1T"]
                },
                Motorcycle: {
                    "Harley-Davidson": ["Sportster Iron 883", "Softail Standard", "Street Glide", "Road King", "Fat Boy", "Pan America 1250"],
                    "Honda": ["CBR600RR", "Gold Wing", "Africa Twin", "Rebel 500", "CB500X", "CRF450R"],
                    "Yamaha": ["YZF-R1", "MT-07", "MT-09", "FJR1300", "Tenere 700", "YZ450F"],
                    "Kawasaki": ["Ninja ZX-10R", "Z900", "Vulcan S", "Versys 650", "KX450", "Concours 14"],
                    "Ducati": ["Panigale V4", "Monster 821", "Multistrada V4", "Scrambler Icon", "Diavel 1260", "SuperSport"],
                    "BMW Motorrad": ["S1000RR", "R1250GS", "F850GS", "K1600GTL", "R nineT", "G310R"],
                    "Suzuki": ["GSX-R1000", "V-Strom 650", "Hayabusa", "Boulevard M109R", "SV650", "DR-Z400SM"],
                    "KTM": ["1290 Super Duke R", "390 Duke", "790 Adventure", "450 SX-F", "690 Enduro R", "250 EXC-F"],
                    "Triumph": ["Bonneville T120", "Street Triple RS", "Tiger 900", "Speed Twin", "Rocket 3", "Thruxton RS"],
                    "Indian Motorcycle": ["Scout Bobber", "Chief Dark Horse", "Chieftain", "Roadmaster", "FTR 1200", "Springfield"]
                }
            };

            const makes = makesAndModels[vehicleType];
            for (const make in makes) {
                const option = document.createElement('option');
                option.value = make;
                option.text = make;
                makeField.appendChild(option);
            }

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

        function updateModelOptions() {
            const vehicleType = document.getElementById('vehicle_type').value;
            const make = document.getElementById('make').value;
            const modelField = document.getElementById('model');

            // Clear existing model options
            modelField.innerHTML = '<option value="" disabled selected>Select the model of vehicle</option>';

            const makesAndModels = {
                Car: {
                    "Toyota": ["Corolla", "Camry", "Prius", "RAV4", "Highlander", "Tacoma", "Tundra"],
                    "Honda": ["Civic", "Accord", "Fit", "CR-V", "Pilot", "Odyssey"],
                    "Ford": ["Fusion", "Explorer", "Escape", "F-150", "Ranger", "Mustang"],
                    "Chevrolet": ["Malibu", "Equinox", "Traverse", "Silverado 1500", "Colorado", "Camaro"],
                    "Nissan": ["Altima", "Sentra", "Versa Note", "Rogue", "Pathfinder", "Frontier"],
                    "BMW": ["3 Series", "5 Series", "X5", "X3", "X7", "4 Series Gran Coupe"],
                    "Mercedes-Benz": ["C-Class", "E-Class", "GLE", "GLC", "GLS", "A-Class"],
                    "Audi": ["A3", "A4", "Q5", "Q7", "A8", "A5 Sportback"],
                    "Hyundai": ["Elantra", "Sonata", "Santa Fe", "Tucson", "Palisade", "Kona"],
                    "Volkswagen": ["Golf", "Passat", "Jetta", "Tiguan", "Atlas"]
                },
                "Pickup Truck": {
                    "Ford": ["F-150", "F-250 Super Duty", "F-350 Super Duty", "Ranger", "Maverick"],
                    "Chevrolet": ["Silverado 1500", "Silverado 2500HD", "Silverado 3500HD", "Colorado"],
                    "Ram (formerly Dodge Ram)": ["1500", "2500", "3500"],
                    "Toyota": ["Tacoma", "Tundra"],
                    "GMC": ["Sierra 1500", "Sierra 2500HD", "Sierra 3500HD", "Canyon"],
                    "Nissan": ["Frontier", "Titan", "Titan XD"],
                    "Honda": ["Ridgeline"],
                    "Jeep": ["Gladiator"],
                    "Rivian": ["R1T"]
                },
                Motorcycle: {
                    "Harley-Davidson": ["Sportster Iron 883", "Softail Standard", "Street Glide", "Road King", "Fat Boy", "Pan America 1250"],
                    "Honda": ["CBR600RR", "Gold Wing", "Africa Twin", "Rebel 500", "CB500X", "CRF450R"],
                    "Yamaha": ["YZF-R1", "MT-07", "MT-09", "FJR1300", "Tenere 700", "YZ450F"],
                    "Kawasaki": ["Ninja ZX-10R", "Z900", "Vulcan S", "Versys 650", "KX450", "Concours 14"],
                    "Ducati": ["Panigale V4", "Monster 821", "Multistrada V4", "Scrambler Icon", "Diavel 1260", "SuperSport"],
                    "BMW Motorrad": ["S1000RR", "R1250GS", "F850GS", "K1600GTL", "R nineT", "G310R"],
                    "Suzuki": ["GSX-R1000", "V-Strom 650", "Hayabusa", "Boulevard M109R", "SV650", "DR-Z400SM"],
                    "KTM": ["1290 Super Duke R", "390 Duke", "790 Adventure", "450 SX-F", "690 Enduro R", "250 EXC-F"],
                    "Triumph": ["Bonneville T120", "Street Triple RS", "Tiger 900", "Speed Twin", "Rocket 3", "Thruxton RS"],
                    "Indian Motorcycle": ["Scout Bobber", "Chief Dark Horse", "Chieftain", "Roadmaster", "FTR 1200", "Springfield"]
                }
            };

            const models = makesAndModels[vehicleType][make];
            models.forEach(model => {
                const option = document.createElement('option');
                option.value = model;
                option.text = model;
                modelField.appendChild(option);
            });
        }

        function submitForm(event) {
            event.preventDefault();
            const formData = new FormData(event.target);

            $.ajax({
                url: 'admin_add_vehicle.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    const jsonResponse = JSON.parse(response);
                    document.getElementById('confirmationMessage').innerText = jsonResponse.message;
                    document.querySelector('.dashboard-form-container').style.display = 'none'; // Hide entire form container
                    document.querySelector('.confirmation-message-container').style.display = 'block'; // Show confirmation message
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error: ', textStatus, errorThrown);
                    document.getElementById('confirmationMessage').innerText = "An error occurred while adding the vehicle.";
                }
            });
        }

        function showAddVehicleForm() {
            document.getElementById('confirmationMessage').innerText = "";
            document.querySelector('.confirmation-message-container').style.display = 'none';
            document.querySelector('.dashboard-form-container').style.display = 'block'; // Show entire form container again
            document.getElementById('add-vehicle-form').reset(); // Clear the form fields
            // Reset the form to initial state
            document.querySelectorAll('.car-field, .truck-field, .motorcycle-field, .car-features, .truck-features, .motorcycle-features').forEach(field => {
                field.classList.add('hidden');
            });
}

window.onload = function() {
            populateYearOptions();
            populateNumberOptions(document.getElementById('airbags'), 0, 12);
            populateNumberOptions(document.getElementById('seats'), 1, 14);
            populateNumberOptions(document.getElementById('doors'), 2, 6);
            document.getElementById('vehicle_type').addEventListener('change', toggleFields);
            document.getElementById('make').addEventListener('change', updateModelOptions);
            toggleFields(); // Initial call to set the correct state
            showSection('vehicles'); // Show the vehicles section by default
        };
    </script>
    <style>
        /* Admin Dashboard Form Container Styling */
        .dashboard-form-container {
            width: 50%;
            margin: 50px auto 20px auto; /* Center the form container and add top margin */
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .dashboard-form-container label {
            display: block;
            margin-bottom: 0.5em;
            font-weight: bold;
            text-align: left;
        }

        .dashboard-form-container input,
        .dashboard-form-container select,
        .dashboard-form-container button {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px; /* Increase margin-bottom for better spacing */
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        #add-vehicle-form h2 {
            margin-bottom: 20px;
            font-size: 1.5em;
        }

        #add-vehicle-form select,
        #add-vehicle-form input[type="text"],
        #add-vehicle-form input[type="number"],
        #add-vehicle-form input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        #add-vehicle-form button {
            padding: 10px 20px;
            font-size: 1em;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #add-vehicle-form button:hover {
            background-color: #0056b3;
        }

        .hidden {
            display: none;
        }

        .features {
            text-align: left;
            margin-bottom: 20px;
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

        .confirmation-message-container {
            display: none;
            width: 50%;
            margin: 50px auto 20px auto; /* Center the form container and add top margin */
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .confirmation-message-container h2 {
            font-size: 1.5em;
            margin-bottom: 10px;
        }

        .confirmation-message-container button {
            padding: 10px 20px;
            font-size: 1em;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .confirmation-message-container button:hover {
            background-color: #0056b3;
        }
    </style>
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
            <div>Dashboard</div>
            <div class="admin-account">
                <img src="path_to_admin_profile_picture.jpg" alt="Admin" width="30" height="30">
                <span><?php echo htmlspecialchars($admin_email); ?></span>
            </div>
        </div>

        <div id="vehicles" class="form-section">
            <div class="dashboard-form-container">
                <h2>Add Vehicle</h2>
                <form id="add-vehicle-form" action="admin_add_vehicle.php" method="POST" enctype="multipart/form-data" onsubmit="submitForm(event)">
                    <div class="form-fields">
                        <label for="vehicle_type">Vehicle Type:</label>
                        <select id="vehicle_type" name="vehicle_type" required>
                            <option value="" disabled selected>Select a vehicle type</option>
                            <option value="Car">Car</option>
                            <option value="Pickup Truck">Pickup Truck</option>
                            <option value="Motorcycle">Motorcycle</option>
                        </select>

                        <div class="car-field truck-field motorcycle-field">
                            <label for="make">Make:</label>
                            <select id="make" name="make" required>
                                <option value="" disabled selected>Select the make of vehicle</option>
                            </select>
                        </div>

                        <div class="car-field truck-field motorcycle-field">
                            <label for="model">Model:</label>
                            <select id="model" name="model" required>
                                <option value="" disabled selected>Select the model of vehicle</option>
                            </select>
                        </div>

                        <div class="car-field truck-field motorcycle-field">
                            <label for="year">Year:</label>
                            <select id="year" name="year">
                                <option value="" disabled selected>Select year</option>
                                <!-- Options will be populated by JavaScript -->
                            </select>
                        </div>

                        <div class="car-field">
                            <label for="car_type">Type:</label>
                            <select id="car_type" name="car_type">
                                <option value="" disabled selected>Select car type</option>
                                <option value="Sedan">Sedan</option>
                                <option value="Hatchback">Hatchback</option>
                                <option value="SUV">SUV (Sport Utility Vehicle)</option>
                                <option value="Coupe">Coupe</option>
                                <option value="Convertible">Convertible</option>
                                <option value="Compact">Compact</option>
                                <option value="Luxury">Luxury</option>
                                <option value="Sports Car">Sports Car</option>
                            </select>
                        </div>

                        <div class="car-field truck-field motorcycle-field">
                            <label for="fuel_type">Fuel Type:</label>
                            <select id="fuel_type" name="fuel_type">
                                <option value="" disabled selected>Select fuel type</option>
                                <option value="Electric">Electric</option>
                                <option value="Hybrid">Hybrid</option>
                                <option value="Gas">Gasoline</option>
                                <option value="Diesel">Diesel</option>
                            </select>
                        </div>

                        <div class="car-field truck-field">
                            <label for="transmission">Transmission:</label>
                            <select id="transmission" name="transmission">
                                <option value="" disabled selected>Select transmission</option>
                                <option value="Automatic">Automatic</option>
                                <option value="Manual">Manual</option>
                            </select>
                        </div>

                        <div class="car-field truck-field">
                            <label for="airbags">Number of Airbags:</label>
                            <select id="airbags" name="airbags">
                                <option value="" disabled selected>Select number of airbags</option>
                                    <!-- Options will be populated by JavaScript -->
                            </select>
                        </div>

                        <div class="car-field truck-field">
                            <label for="seats">Seats:</label>
                            <select id="seats" name="seats">
                                <option value="" disabled selected>Select number of seats</option>
                                <!-- Options will be populated by JavaScript -->
                            </select>
                        </div>

                        <div class="car-field truck-field">
                            <label for="doors">Doors:</label>
                            <select id="doors" name="doors">
                                <option value="" disabled selected>Select number of doors</option>
                                <!-- Options will be populated by JavaScript -->
                            </select>
                        </div>

                        <div class="car-field truck-field motorcycle-field">
                            <label for="price">Price:</label>
                            <input type="number" id="price" name="price" required min="1" step="1">
                        </div>

                        <div class="car-field truck-field motorcycle-field">
                            <label for="location">Location:</label>
                            <select id="location" name="location" required>
                                <option value="" disabled selected>Select location</option>
                                <?php
                                $locations = ["Ajax", "Burlington", "Etobicoke", "Georgetown", "Halton Hills", "Hamilton", "Kitchener", "Milton", "Mississauga", "Oakville", "Scarborough", "Toronto"];
                                sort($locations);
                                foreach ($locations as $location) {
                                    echo "<option value=\"$location\">$location</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="car-field truck-field motorcycle-field">
                            <label for="images">Images:</label>
                            <input type="file" id="images" name="images[]" accept="image/*" multiple required>
                        </div>

                        <div class="features car-features hidden">
                            <h3>Car Features</h3>
                            <label><input type="checkbox" name="features[]" value="Anti-lock Braking System (ABS)"> Anti-lock Braking System (ABS)</label><br>
                            <label><input type="checkbox" name="features[]" value="Rearview Camera"> Rearview Camera</label><br>
                            <label><input type="checkbox" name="features[]" value="Traction Control"> Traction Control</label><br>
                            <label><input type="checkbox" name="features[]" value="Air Conditioning"> Air Conditioning</label><br>
                            <label><input type="checkbox" name="features[]" value="Power Windows and Locks"> Power Windows and Locks</label><br>
                            <label><input type="checkbox" name="features[]" value="Keyless Entry"> Keyless Entry</label><br>
                            <label><input type="checkbox" name="features[]" value="Cruise Control"> Cruise Control</label><br>
                            <label><input type="checkbox" name="features[]" value="Adjustable Steering Wheel"> Adjustable Steering Wheel</label><br>
                            <label><input type="checkbox" name="features[]" value="Bluetooth Connectivity"> Bluetooth Connectivity</label><br>
                            <label><input type="checkbox" name="features[]" value="Navigation System"> Navigation System</label><br>
                            <label><input type="checkbox" name="features[]" value="Sunroof"> Sunroof</label><br>
                            <label><input type="checkbox" name="features[]" value="Heated Seats"> Heated Seats</label><br>
                        </div>

                        <div class="features truck-features hidden">
                            <h3>Pickup Truck Features</h3>
                            <label><input type="checkbox" name="features[]" value="Air Conditioning"> Air Conditioning</label><br>
                            <label><input type="checkbox" name="features[]" value="Four-Wheel Drive (4WD)"> Four-Wheel Drive (4WD)</label><br>
                            <label><input type="checkbox" name="features[]" value="Bed Liner"> Bed Liner</label><br>
                            <label><input type="checkbox" name="features[]" value="Rearview Camera"> Rearview Camera</label><br>
                            <label><input type="checkbox" name="features[]" value="Blind Spot Monitoring"> Blind Spot Monitoring</label><br>
                            <label><input type="checkbox" name="features[]" value="Lane Departure Warning"> Lane Departure Warning</label><br>
                            <label><input type="checkbox" name="features[]" value="Automatic Emergency Braking"> Automatic Emergency Braking</label><br>
                            <label><input type="checkbox" name="features[]" value="Infotainment System"> Infotainment System</label><br>
                        </div>

                        <div class="features motorcycle-features hidden">
                            <h3>Motorcycle Features</h3>
                            <label><input type="checkbox" name="features[]" value="Anti-lock Braking System (ABS)"> Anti-lock Braking System (ABS)</label><br>
                            <label><input type="checkbox" name="features[]" value="Multiple Riding Modes"> Multiple Riding Modes</label><br>
                            <label><input type="checkbox" name="features[]" value="GPS Navigation"> GPS Navigation</label><br>
                            <label><input type="checkbox" name="features[]" value="Bluetooth Connectivity"> Bluetooth Connectivity</label><br>
                            <label><input type="checkbox" name="features[]" value="Security System"> Security System</label><br>
                            <label><input type="checkbox" name="features[]" value="Mobile Phone Mount"> Mobile Phone Mount</label><br>
                        </div>
                    </div>

                    <button type="submit">Add Vehicle</button>
                </form>
            </div>

            <div class="confirmation-message-container">
                <h2 id="confirmationMessage"></h2>
                <h3>Add more vehicles</h3>
                <button onclick="showAddVehicleForm()">Add Another Vehicle</button>
            </div>
        </div>
    </div>
</body>
</html>


