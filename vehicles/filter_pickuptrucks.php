<?php
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

// Determine sorting preference
$sort_option = isset($_GET['sort']) ? $_GET['sort'] : '';

// Determine filter preferences
$make_filter = isset($_GET['make']) ? $_GET['make'] : [];
$model_filter = isset($_GET['model']) ? $_GET['model'] : [];
$year_filter = isset($_GET['year']) ? $_GET['year'] : [];
$fuel_type_filter = isset($_GET['fuel_type']) ? $_GET['fuel_type'] : [];
$transmission_filter = isset($_GET['transmission']) ? $_GET['transmission'] : [];
$location_filter = isset($_GET['location']) ? $_GET['location'] : [];

// Build the SQL query with sorting and filtering
$sql = "SELECT vehicle_id, make, model, price, image_paths, vehicle_type, year, fuel_type, transmission, location FROM vehicles WHERE vehicle_type = 'Pickup Truck'";

$filters = [];
if (!empty($make_filter)) {
    $filters[] = "make IN ('" . implode("','", array_map([$conn, 'real_escape_string'], $make_filter)) . "')";
}
if (!empty($model_filter)) {
    $filters[] = "model IN ('" . implode("','", array_map([$conn, 'real_escape_string'], $model_filter)) . "')";
}
if (!empty($year_filter)) {
    $filters[] = "year IN ('" . implode("','", array_map([$conn, 'real_escape_string'], $year_filter)) . "')";
}
if (!empty($fuel_type_filter)) {
    $filters[] = "fuel_type IN ('" . implode("','", array_map([$conn, 'real_escape_string'], $fuel_type_filter)) . "')";
}
if (!empty($transmission_filter)) {
    $filters[] = "transmission IN ('" . implode("','", array_map([$conn, 'real_escape_string'], $transmission_filter)) . "')";
}
if (!empty($location_filter)) {
    $filters[] = "location IN ('" . implode("','", array_map([$conn, 'real_escape_string'], $location_filter)) . "')";
}

if (!empty($filters)) {
    $sql .= " AND " . implode(' AND ', $filters);
}

switch ($sort_option) {
    case 'price_asc':
        $sql .= " ORDER BY price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY price DESC";
        break;
    case 'year':
        $sql .= " ORDER BY year DESC";
        break;
    case 'make':
        $sql .= " ORDER BY make ASC, model ASC";
        break;
    default:
        $sql .= " ORDER BY make ASC, model ASC";
        break;
}

$result = $conn->query($sql);

$vehicles = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['image_paths'] = json_decode($row['image_paths'])[0]; // Assuming the first image path is enough
        $vehicles[] = $row;
    }
}

echo json_encode($vehicles);

$conn->close();
?>
