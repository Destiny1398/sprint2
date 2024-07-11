<?php
// customer_sign_in.php

// Start the session
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection settings
$servername = "localhost";
$username = "root"; // your database username
$password = ""; // your database password
$dbname = "customer_registration"; // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Customer_email = $_POST['email'];
    $Customer_password = $_POST['password'];
    
    // Debugging output
    echo "Received email: " . $Customer_email . "<br>";
    echo "Received password: " . $Customer_password . "<br>";

    // Prepare and bind
    $stmt = $conn->prepare("SELECT Customer_password FROM customers_details WHERE Customer_email = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $Customer_email);

    // Execute the statement
    $stmt->execute();

    // Store the result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the hashed password from the database
        $row = $result->fetch_assoc();
        $hashed_password = $row['Customer_password'];

        // Verify the password
        if (password_verify($Customer_password, $hashed_password)) {
            // Successful login
            $_SESSION['Customer_email'] = $Customer_email;
            header("Location: customer_homepage.php"); // Redirect to the homepage or dashboard
            exit();
        } else {
            // Invalid password
            echo "Invalid email or password";
        }
    } else {
        // No user found with that email
        echo "Invalid email or password";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "Form data not received";
}
?>
