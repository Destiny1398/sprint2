<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "customer_registration";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO customers_details (Customer_firstname, Customer_lastname, Customer_email, Customer_password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $Customer_firstname, $Customer_lastname, $Customer_email, $Customer_password);

    // Set parameters and execute
    $Customer_firstname = $_POST['firstName'];
    $Customer_lastname = $_POST['lastName'];
    $Customer_email = $_POST['email'];
    $Customer_password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    $stmt->execute();

    // Store the Customer ID in the session
    $_SESSION['Customer_id'] = $conn->insert_id;

     // Close the statement and connection
     $stmt->close();
     $conn->close();

    header("Location: customer_drivers_license_details.php");
    exit();

   
}
?>
