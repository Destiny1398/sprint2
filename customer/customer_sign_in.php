<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_vehicle_rental_system";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT cust_id, cust_password, cust_first_name, cust_last_name, cust_contact_no, cust_profile_image FROM customer WHERE cust_email = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $cust_id = $row['cust_id'];
        $hashed_password = $row['cust_password'];
        $firstName = $row['cust_first_name'];
        $lastName = $row['cust_last_name'];
        $contactNo = $row['cust_contact_no'];
        $profileImagePath = $row['cust_profile_image'];

        if (password_verify($password, $hashed_password)) {
            $_SESSION['userId'] = $cust_id;
            $_SESSION['firstName'] = $firstName;
            $_SESSION['lastName'] = $lastName;
            $_SESSION['contactNo'] = $contactNo;
            $_SESSION['profileImagePath'] = $profileImagePath;

            if (isset($_SESSION['redirect_url'])) {
                $redirect_url = $_SESSION['redirect_url'];
                unset($_SESSION['redirect_url']);
                header("Location: $redirect_url");
            } else {
                header("Location: customer_homepage.php");
            }
            exit();
        } else {
            $error_message = "Invalid email or password";
        }
    } else {
        $error_message = "Invalid email or password";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Drive</title>
    <link rel="stylesheet" href="../styles/header_style.css">
    <link rel="stylesheet" href="../styles/customer_signin.css">
</head>
<body id="signinbody">
    <header class="main-header">
        <div class="logo">
            <a href="../index.php"><h1>Drive</h1></a>
        </div>
        <nav class="main-nav">
            <ul class="nav-list">
                <li class="nav-item"><a href="../index.php" class="nav-link">Home</a></li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropbtn">Fleet</a>
                    <div class="dropdown-content">
                        <a href="../fleet/cars.html" class="dropdown-link">Cars</a>
                        <a href="#" class="dropdown-link">Motorcycles</a>
                        <a href="#" class="dropdown-link">Pickup Trucks</a>
                    </div>
                </li>
                <li class="nav-item"><a href="../contact.html" class="nav-link">Contact Us</a></li>
                <li class="nav-item"><a href="customer_sign_up.html" class="nav-link">Sign Up</a></li>
            </ul>
        </nav>
    </header>
    <main class="main-content">
        <div class="signin-container">
            <form class="signin-form" action="customer_sign_in.php" method="POST">
                <div class="form-group">
                    <input type="text" id="email" name="email" class="form-input" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" id="password" name="password" class="form-input" placeholder="Password" required>
                </div>
                <?php if (!empty($error_message)): ?>
                    <div class="form-group error-message">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <button type="submit" class="login-button">Log In</button>
                </div>
                <div class="form-group">
                    <a href="#" class="forgot-password">Forgot password?</a>
                </div>
                <hr>
                <a href="customer_sign_up.html" class="create-account-button">Create new account</a>
            </form>
        </div>
    </main>
</body>
</html>
