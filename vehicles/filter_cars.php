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
$type_filter = isset($_GET['type']) ? $_GET['type'] : [];
$transmission_filter = isset($_GET['transmission']) ? $_GET['transmission'] : [];

// Build the SQL query with sorting and filtering
$sql = "SELECT vehicle_id, make, model, price, image_paths, vehicle_type, car_type,transmission, seats, fuel_type, year, airbags, doors FROM vehicles WHERE vehicle_type = 'Car'";

$filters = [];
if (!empty($make_filter)) {
    $filters[] = "make IN ('" . implode("','", array_map([$conn, 'real_escape_string'], $make_filter)) . "')";
}
if (!empty($model_filter)) {
    $filters[] = "model IN ('" . implode("','", array_map([$conn, 'real_escape_string'], $model_filter)) . "')";
}
if (!empty($type_filter)) {
    $filters[] = "car_type IN ('" . implode("','", array_map([$conn, 'real_escape_string'], $type_filter)) . "')";
}
if (!empty($transmission_filter)) {
    $filters[] = "transmission IN ('" . implode("','", array_map([$conn, 'real_escape_string'], $transmission_filter)) . "')";
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
