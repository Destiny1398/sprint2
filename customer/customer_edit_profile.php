<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "online_vehicle_rental_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve user details from the database
if (isset($_SESSION['userId'])) {
    $cust_id = $_SESSION['userId'];
    $sql = "SELECT cust_first_name, cust_email, cust_contact_no, cust_profile_image FROM customer WHERE cust_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cust_id);
    $stmt->execute();
    $stmt->bind_result($firstName, $email, $contactNo, $profileImagePath);
    $stmt->fetch();
    $stmt->close();

    // Store the first name in session
    $_SESSION['firstName'] = $firstName;
}

$changesMade = false;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newEmail = $_POST['email'];
    $newContactNo = $_POST['contactNo'];
    $newProfileImagePath = $profileImagePath;

    // Handle profile image upload
    if (isset($_FILES['profileImage']) && $_FILES['profileImage']['error'] == 0) {
        $targetDir = "../uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $targetFile = $targetDir . basename($_FILES["profileImage"]["name"]);
        if (move_uploaded_file($_FILES["profileImage"]["tmp_name"], $targetFile)) {
            $newProfileImagePath = "uploads/" . basename($_FILES["profileImage"]["name"]);
            $changesMade = true;
        }
    }

    // Check if changes were made
    if ($email !== $newEmail || $contactNo !== $newContactNo || $profileImagePath !== $newProfileImagePath) {
        $changesMade = true;
        $sql = "UPDATE customer SET cust_email = ?, cust_contact_no = ?, cust_profile_image = ? WHERE cust_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $newEmail, $newContactNo, $newProfileImagePath, $cust_id);
        if ($stmt->execute()) {
            $message = "Profile updated successfully!";
        } else {
            $message = "Error updating profile!";
        }
        $stmt->close();
    } else {
        $message = "No changes were made.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../styles/customer_homepage.css">
    <style>

    body {
    margin:0;
    padding: 0;
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
        <?php if (isset($message)) : ?>
            <div class="cust-profile-message">
                <?php echo $message; ?>
                <?php if ($message === "No changes were made.") : ?>
                    <a href="customer_edit_profile.php" class="go-back-btn">Go Back</a>
                <?php endif; ?>
            </div>
        <?php else : ?>
            <div class="form-content">
                <h2>Edit Profile</h2>
                <form id="editProfileForm" action="customer_edit_profile.php" method="POST" enctype="multipart/form-data">
                    <div class="cust-profile-form-group">
                        <label for="email" class="cust-profile-label">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" class="cust-profile-input" required>
                    </div>
                    <div class="cust-profile-form-group">
                        <label for="contactNo" class="cust-profile-label">Contact Number</label>
                        <input type="text" id="contactNo" name="contactNo" value="<?php echo htmlspecialchars($contactNo); ?>" class="cust-profile-input" required>
                        <small id="contactNoError" class="error-message">Contact No should be 10 digits long</small>
                    </div>
                    <div class="cust-profile-form-group">
                        <label for="profileImage" class="cust-profile-label">Profile Image</label>
                        <input type="file" id="profileImage" name="profileImage" class="cust-profile-input">
                    </div>
                    <button type="submit" class="cust-profile-btn">Update</button>
                </form>
            </div>

            <div class="profile-picture-container">
                <?php if ($profileImagePath) : ?>
                    <img src="../<?php echo htmlspecialchars($profileImagePath); ?>" alt="Profile Image" class="cust-profile-picture">
                <?php else : ?>
                    <img src="../uploads/default-profile.png" alt="Default Profile Image" class="cust-profile-picture">
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        var contactNo = document.getElementById("contactNo");
        var contactNoError = document.getElementById("contactNoError");

        function formatContactNo(value) {
            let cleaned = ('' + value).replace(/\D/g, '');
            let match = cleaned.match(/^(\d{0,3})(\d{0,3})(\d{0,4})$/);
            if (match) {
                let formatted = '(';
                if (match[1]) {
                    formatted += match[1];
                    if (match[1].length === 3) {
                        formatted += ') ';
                    }
                }
                if (match[2]) {
                    formatted += match[2];
                    if (match[2].length === 3) {
                        formatted += ' - ';
                    }
                }
                if (match[3]) {
                    formatted += match[3];
                }
                return formatted;
            }
            return value;
        }

        function validateContactNo() {
            let cleaned = contactNo.value.replace(/\D/g, '');
            if (cleaned.length !== 10) {
                contactNoError.style.display = 'block';
                return false;
            } else {
                contactNoError.style.display = 'none';
                return true;
            }
        }

        contactNo.addEventListener('input', function() {
            let cursorPosition = contactNo.selectionStart;
            let oldValue = contactNo.value;
            contactNo.value = formatContactNo(contactNo.value);

            if (oldValue.length > contactNo.value.length) {
                cursorPosition--;
            } else if (oldValue.length < contactNo.value.length) {
                cursorPosition++;
            }
            contactNo.setSelectionRange(cursorPosition, cursorPosition);

            validateContactNo();
        });

        document.getElementById('editProfileForm').addEventListener('submit', function(event) {
            if (!validateContactNo()) {
                event.preventDefault();
            }
        });
    </script>
</body>
</html>
