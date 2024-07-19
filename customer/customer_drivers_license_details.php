<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <header class="main-header">
        <div class="logo">
            <a href="../index.html"><h1>Drive</h1></a>
        </div>
        <nav class="main-nav">
            <ul class="nav-list">
                <li class="nav-item"><a href="../index.html" class="nav-link">Home</a></li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropbtn">Fleet</a>
                    <div class="dropdown-content">
                        <a href="../fleet/cars.html" class="dropdown-link">Cars</a>
                        <a href="#" class="dropdown-link">Motorcycles</a>
                        <a href="#" class="dropdown-link">Pickup Trucks</a>
                    </div>
                </li>
                <li class="nav-item"><a href="../contact.html" class="nav-link">Contact Us</a></li>
                <li class="nav-item"><a href="customer_sign_in.html" class="nav-link">Sign In</a></li>
            </ul>
        </nav>
    </header>
    <div class="main-content_register1">
        <div class="dl-form-container">
            <form id="dl-registrationForm" method="post" action="">
            <div class="input-group">
                <label for="dlNumber">Enter Drivers License Number</label>
                <input type="text" id="dlNumber" name="dlNumber" required>
            </div>
            <div class="input-group">
                <label for="dob">Date Of Birth</label>
                <input type="date" id="dob" name="dob" required>
            </div>    

            <div class="actions">
                <button type="submit">Submit</button>
            </div>
            </form>
        </div>
        
        <?php
            
            session_start();
    
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $dlNumber = $_POST['dlNumber'];
                $dob = $_POST['dob'];
    
                // Initialize cURL for POST request
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://eve.idfy.com/v3/tasks/async/verify_with_source/ind_driving_license',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode(array(
                        "task_id" => "74f4c926-250c-43ca-9c53-453e87ceacd1",
                        "group_id" => "8e16424a-58fc-4ba4-ab20-5bc8e7c3c41e",
                        "data" => array(
                            "id_number" => $dlNumber,
                            "date_of_birth" => $dob,
                            "advanced_details" => array(
                                "state_info" => true,
                                "age_info" => true,
                                "get_profile_image" => true
                            )
                        )
                    )),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'account-id: daa108fedf10/c17e8ee9-91df-41ed-8b3d-46544059e148',
                        'api-key: 9aaa1bf1-6b0a-4dbe-aa0b-efa7836f2d14'
                    ),
                ));
    
                $response = curl_exec($curl);
                curl_close($curl);
    
                $responseData = json_decode($response, true);
                $requestId = $responseData['request_id'];
    
                // Wait for a few seconds to ensure the task is processed
                sleep(5);
    
                // Initialize cURL for GET request
                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://eve.idfy.com/v3/tasks?request_id=$requestId",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'api-key: 9aaa1bf1-6b0a-4dbe-aa0b-efa7836f2d14',
                        'Content-Type: application/json',
                        'account-id: daa108fedf10/c17e8ee9-91df-41ed-8b3d-46544059e148'
                    ),
                ));
    
                $response = curl_exec($curl);
                curl_close($curl);
    
                $data = json_decode($response, true);
    
                if (!empty($data) && isset($data[0]['result']['source_output'])) {
                    $output = $data[0]['result']['source_output'];
                    $name = $output['name'];
                    $idNumber = $output['id_number'];
                    $validFrom = $output['nt_validity_from'];
                    $validTo = $output['nt_validity_to'];
                    $relativeName = $output['relatives_name'];
                    $state = $output['state'];
                    $covDetails = $output['cov_details'];
    
                    // Display results
                    echo "<div class='output-container'>";
                    echo "<div class='result-group'><label>Name:</label><span>$name</span></div>";
                    echo "<div class='result-group'><label>ID Number:</label><span>$idNumber</span></div>";
                    echo "<div class='result-group'><label>Valid From:</label><span>$validFrom</span></div>";
                    echo "<div class='result-group'><label>Valid To:</label><span>$validTo</span></div>";
                    echo "<div class='result-group'><label>Relative Name:</label><span>$relativeName</span></div>";
                    echo "<div class='result-group'><label>State:</label><span>$state</span></div>";
                    
    
                    if (!empty($covDetails)) {
                        echo "<div class='result-group'><label>COV Details:</label><ul class='cov-details'>";
                        foreach ($covDetails as $cov) {
                            $covType = $cov['cov'];
                            $issueDate = $cov['issue_date'];
                            echo "<li>COV: $covType, Issue Date: $issueDate</li>";
                        }
                        echo "</ul></div>";
                    }
    
                    echo "<form  action='customer_homepage.php' method='post'>";
                    echo "<input type='hidden' name='welcome_message' value='Welcome, $name!'>";
                    echo "<button type='submit' class='dl_result_continue_button'>Continue</button>"; 
                    echo "</form>";
    
                    echo "</div>";
    
                    // Database insertion
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
    
                    //  Retrieving Customer_id from session 
            if (isset($_SESSION['Customer_id'])) {
                $Customer_id = $_SESSION['Customer_id'];
            } else {
                echo "<div class='result-group'><span>Error: Customer ID not found in session.</span></div>";
                exit();
            }
                        // Prepare and bind
                        $covDetailsJson = json_encode($covDetails);
                        $stmt = $conn->prepare("INSERT INTO customer_dl_verification (Customer_id, name, id_number, valid_from, valid_to, relative_name, state, cov_details, dob) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("issssssss", $Customer_id, $name, $idNumber, $validFrom, $validTo, $relativeName, $state, $covDetailsJson, $dob);    
                    // Execute the statement
                    if ($stmt->execute()) {
                        echo "<div class='result-group'><span>Details saved successfully to the database.</span></div>";
                    } else {
                        echo "<div class='result-group'><span>Error: " . $stmt->error . "</span></div>";
                    }
    
                    // Close the statement and connection
                    $stmt->close();
                    $conn->close();
    
                } else {
                    echo "<div class='result-group'><span>No details found.</span></div>";
                }
            }
            ?>

    </div>
</body>
</html>
