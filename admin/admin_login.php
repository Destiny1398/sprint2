<?php
session_start();
include('db_connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['admin_email'];
    $password = $_POST['admin_password'];

    // To protect from MySQL injection
    $email = stripslashes($email);
    $password = stripslashes($password);
    $email = $conn->real_escape_string($email);
    $password = $conn->real_escape_string($password);

    // Hashing the password
    $password = md5($password);

    $sql = "SELECT * FROM admin WHERE admin_email = '$email' AND admin_password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Setting session variables
        $_SESSION['admin_email'] = $email;
        header("location: admin_dashboard.php");
    } else {
        echo "Invalid Email or Password.";
    }
}
?>
