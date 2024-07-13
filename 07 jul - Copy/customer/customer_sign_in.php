<?php
// Start the session
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection settings
$servername = "localhost";
$username = "root"; // your database username
$password = ""; // your database password
$dbname = "online_vehicle_rental_system"; // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Debugging output
    echo "Received email: " . $email . "<br>";
    echo "Received password: " . $password . "<br>";

    // Prepare and bind
    $stmt = $conn->prepare("SELECT cust_id, cust_password, cust_first_name, cust_last_name, cust_contact_no, cust_profile_image FROM customer WHERE cust_email = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $email);

    // Execute the statement
    $stmt->execute();

    // Store the result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the user details from the database
        $row = $result->fetch_assoc();
        $cust_id = $row['cust_id'];
        $hashed_password = $row['cust_password'];
        $firstName = $row['cust_first_name'];
        $lastName = $row['cust_last_name'];
        $contactNo = $row['cust_contact_no'];
        $profileImagePath = $row['cust_profile_image'];

        // Verify the password
        if (md5($password) === $hashed_password) {
            // Successful login
            $_SESSION['userId'] = $cust_id;
            $_SESSION['firstName'] = $firstName;
            $_SESSION['lastName'] = $lastName;
            $_SESSION['contactNo'] = $contactNo;
            $_SESSION['profileImagePath'] = $profileImagePath;
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
