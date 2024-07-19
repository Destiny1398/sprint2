<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root"; // your database username
$password = ""; // your database password
$dbname = "online_vehicle_rental_system"; // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve user details from the session
if (isset($_SESSION['userId'])) {
    $cust_id = $_SESSION['userId'];
    $sql = "SELECT cust_first_name, cust_profile_image FROM customer WHERE cust_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cust_id);
    $stmt->execute();
    $stmt->bind_result($firstName, $profileImagePath);
    $stmt->fetch();
    $stmt->close();

    $_SESSION['firstName'] = $firstName;
}

$response = ["message" => ""];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] === 'checkCurrentPassword') {
        $currentPassword = $_POST['currentPassword'];
        $sql = "SELECT cust_password FROM customer WHERE cust_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cust_id);
        $stmt->execute();
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();
        $stmt->close();

        if (password_verify($currentPassword, $hashedPassword)) {
            echo json_encode(["status" => "valid"]);
        } else {
            echo json_encode(["status" => "invalid"]);
        }
        exit;
    }

    if (isset($_POST['action']) && $_POST['action'] === 'changePassword') {
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];

        if ($newPassword !== $confirmPassword) {
            $response["message"] = "New password and confirm password do not match!";
        } else if (strlen($newPassword) < 4 || !preg_match('/[0-9]/', $newPassword) || !preg_match('/[!@#$%^&*]/', $newPassword)) {
            $response["message"] = "Password does not meet the requirements!";
        } else {
            $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE customer SET cust_password = ? WHERE cust_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $newHashedPassword, $cust_id);
            if ($stmt->execute()) {
                $response["message"] = "Password changed successfully! You will be logged out in 5 seconds. Please sign in again.";
                $response["success"] = true;
            } else {
                $response["message"] = "Error changing password!";
                $response["success"] = false;
            }
            $stmt->close();
        }
        echo json_encode($response);
        exit;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../styles/customer_homepage.css">
    <style>
        body {    
    margin:0;
    padding: 0;
}
        .requirement {
            color: red;
            list-style-type: none;
        }
        .requirement.valid {
            color: green;
        }
        .message {
            color: red;
        }
        .message.valid {
            color: green;
        }
        .hidden {
            display: none;
        }
        .confirmation {
            color: green;
            font-size: 18px;
        }
        .error {
            color: red;
            font-size: 18px;
        }
    </style>
</head>
<body>

<header id="cust_homepage_main_header">
        <div id="cust_homepage_logo">
            <a href="../index.php"><h1>Drive</h1></a>
        </div>
        <nav class="main-nav">
            <ul id="cust_homepage_nav_list">
                <li id="cust_homepage_nav_item"><a href="../index.php" id="cust_homepage_nav_link">Home</a></li>
                <li id="cust_homepage_nav_item" class="dropdown cust_homepage_dropdown fleet-dropdown">
                    <a href="#" id="cust_homepage_nav_link" class="cust_homepage_dropbtn">Fleet</a>
                    <div class="cust_homepage_dropdown_content">
                        <a href="../fleet/cars.html" class="cust_homepage_dropdown_link">Cars</a>
                        <a href="#" class="cust_homepage_dropdown_link">Motorcycles</a>
                        <a href="#" class="cust_homepage_dropdown_link">Pickup Trucks</a>
                    </div>
                </li>
                <li id="cust_homepage_nav_item"><a href="../contact.html" id="cust_homepage_nav_link">Contact Us</a></li>
                <li id="cust_homepage_nav_item" class="dropdown cust_homepage_dropdown">
                    <a href="#" id="cust_homepage_nav_link" class="cust_homepage_dropbtn profile-info">
                        <?php if (isset($firstName)) : ?>
                            <img src="../<?php echo htmlspecialchars($profileImagePath); ?>" alt="Profile Image">
                            <span style="font-size: 18px; color: #333;"><?php echo htmlspecialchars($firstName); ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="cust_homepage_dropdown_content">
                        <a href="customer_edit_profile.php" class="cust_homepage_dropdown_link">Edit Profile</a>
                        <a href="customer_change_password.php" class="cust_homepage_dropdown_link">Change password</a>
                        <a href="#" class="cust_homepage_dropdown_link">Settings</a>
                        <a href="customer_logout.php" class="cust_homepage_dropdown_link">Logout</a>
                    </div>
                </li>
            </ul>
        </nav>
    </header>

<div id="cust_homepage_main_container">
    <div id="cust_homepage_sidebar">
            <a href="customer_homepage.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a>
            <a href="customer_bookings_details.php"><i class="fas fa-book"></i>View Bookings</a>
            <a href="#"><i class="fas fa-history"></i>Payment History</a>
            <a href="#"><i class="fas fa-tags"></i>Offers</a>
            <a href="#"><i class="fas fa-headset"></i>Support</a>
        </div>

    <div class="cust-profile-container">
        <div class="form-content">
            <h2>Change Password</h2>
            <form id="changePasswordForm" method="POST">
                <div id="formMessage" class="cust-profile-message hidden"></div>
                <div class="cust-profile-form-group">
                    <label for="currentPassword" class="cust-profile-label">Current Password</label>
                    <input type="password" id="currentPassword" name="currentPassword" class="cust-profile-input" required>
                    <div id="currentPasswordFeedback" class="message"></div>
                </div>
                <div class="cust-profile-form-group">
                    <label for="newPassword" class="cust-profile-label">New Password</label>
                    <input type="password" id="newPassword" name="newPassword" class="cust-profile-input" required>
                    <ul id="passwordRequirements">
                        <li id="minLength" class="requirement">X At least 4 letters</li>
                        <li id="number" class="requirement">X At least one number</li>
                        <li id="specialChar" class="requirement">X At least one special character (!@#$%^&*)</li>
                    </ul>
                </div>
                <div class="cust-profile-form-group">
                    <label for="confirmPassword" class="cust-profile-label">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" class="cust-profile-input" required>
                    <div id="passwordMatch" class="message"></div>
                </div>
                <button type="button" class="cust-profile-btn" id="changePasswordButton" onclick="updatePassword()">Change Password</button>
            </form>
        </div>
    </div>
</div>

<script>
    const currentPassword = document.getElementById('currentPassword');
    const currentPasswordFeedback = document.getElementById('currentPasswordFeedback');
    const newPassword = document.getElementById('newPassword');
    const confirmPassword = document.getElementById('confirmPassword');
    const passwordRequirements = {
        minLength: document.getElementById('minLength'),
        number: document.getElementById('number'),
        specialChar: document.getElementById('specialChar')
    };
    const passwordMatch = document.getElementById('passwordMatch');
    const formMessage = document.getElementById('formMessage');
    const formContent = document.querySelector('.form-content');
    const changePasswordButton = document.getElementById('changePasswordButton');

    const specialCharacters = /[!@#$%^&*]/;
    const numbers = /[0-9]/;

    currentPassword.addEventListener('input', checkCurrentPassword);
    newPassword.addEventListener('input', validatePassword);
    confirmPassword.addEventListener('input', matchPasswords);

    function checkCurrentPassword() {
        const currentPasswordValue = currentPassword.value;

        fetch('customer_change_password.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `action=checkCurrentPassword&currentPassword=${encodeURIComponent(currentPasswordValue)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'valid') {
                currentPasswordFeedback.classList.add('valid');
                currentPasswordFeedback.classList.remove('message');
                currentPasswordFeedback.innerHTML = '✔ Current password matches';
            } else {
                currentPasswordFeedback.classList.remove('valid');
                currentPasswordFeedback.classList.add('message');
                currentPasswordFeedback.innerHTML = 'X Current password is incorrect';
            }
        })
        .catch(error => console.error('Error:', error));
    }

    function validatePassword() {
        const value = newPassword.value;
        passwordRequirements.minLength.classList.toggle('valid', value.length >= 4);
        passwordRequirements.minLength.innerHTML = value.length >= 4 ? '✔ At least 4 letters' : 'X At least 4 letters';

        passwordRequirements.number.classList.toggle('valid', numbers.test(value));
        passwordRequirements.number.innerHTML = numbers.test(value) ? '✔ At least one number' : 'X At least one number';

        passwordRequirements.specialChar.classList.toggle('valid', specialCharacters.test(value));
        passwordRequirements.specialChar.innerHTML = specialCharacters.test(value) ? '✔ At least one special character (!@#$%^&*)' : 'X At least one special character (!@#$%^&*)';

        matchPasswords();
    }

    function matchPasswords() {
        const newPasswordValue = newPassword.value;
        const confirmPasswordValue = confirmPassword.value;

        passwordMatch.classList.toggle('valid', newPasswordValue === confirmPasswordValue);
        passwordMatch.innerHTML = newPasswordValue === confirmPasswordValue ? '✔ Passwords match' : 'X Passwords do not match';
    }

    function updatePassword() {
        const currentPasswordValue = currentPassword.value;
        const newPasswordValue = newPassword.value;
        const confirmPasswordValue = confirmPassword.value;

        if (newPasswordValue === confirmPasswordValue &&
            newPasswordValue.length >= 4 &&
            numbers.test(newPasswordValue) &&
            specialCharacters.test(newPasswordValue)) {
            fetch('customer_change_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `action=changePassword&currentPassword=${encodeURIComponent(currentPasswordValue)}&newPassword=${encodeURIComponent(newPasswordValue)}&confirmPassword=${encodeURIComponent(confirmPasswordValue)}`
            })
            .then(response => response.json())
            .then(data => {
                formMessage.textContent = data.message;
                formMessage.className = data.message.includes("successfully") ? "message valid confirmation" : "message error";
                formMessage.classList.remove('hidden');
                if (data.success) {
                    document.getElementById('changePasswordForm').reset();
                    document.querySelectorAll('.cust-profile-form-group').forEach(group => group.classList.add('hidden'));
                    changePasswordButton.classList.add('hidden');
                    setTimeout(() => {
                        window.location.href = "customer_sign_in.php";
                    }, 5000);
                }
            })
            .catch(error => console.error('Error:', error));
        } else {
            alert('Please ensure all password requirements are met and passwords match.');
        }
    }
</script>

</body>
</html>
