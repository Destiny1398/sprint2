<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    // Retrieve form data
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Hash the password with md5
    $contactNo = $_POST['contactNo'];
    $profileImagePath = $_POST['profileImagePath'];
    $licenseNumber = $_POST['licenseNumber'];
    $dob = $_POST['dob'];

    

    // Insert data into Customer table
    $stmt = $conn->prepare("INSERT INTO customer (cust_first_name, cust_last_name, cust_email, cust_password, cust_contact_no, cust_profile_image, cust_license_number, cust_dob) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $firstName, $lastName, $email, $password, $contactNo, $profileImagePath, $licenseNumber, $dob);
    $stmt->execute();
    $cust_id = $stmt->insert_id;
    $stmt->close();

    // Store the user ID and first name in the session
    $_SESSION['userId'] = $cust_id;
    $_SESSION['firstName'] = $firstName;

    // API call to verify driver's license details
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
                "id_number" => $licenseNumber,
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
        $validFrom = $output['nt_validity_from'];
        $validTo = $output['nt_validity_to'];
        $relativeName = $output['relatives_name'];
        $state = $output['state'];
        $covDetails = $output['cov_details'];
        $covDetailsJson = json_encode($covDetails);

        // Insert data into driverlicensedetail table
        $stmt = $conn->prepare("INSERT INTO driverlicensedetail (CustomerID, ValidFrom, ValidTo, RelativeName, State, COVDetails) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $cust_id, $validFrom, $validTo, $relativeName, $state, $covDetailsJson);
        $stmt->execute();
        $stmt->close();

        $_SESSION['verification_details'] = [
            'valid_from' => $validFrom,
            'valid_to' => $validTo,
            'relative_name' => $relativeName,
            'state' => $state,
            'cov_details' => $covDetails,
        ];

        echo json_encode($_SESSION['verification_details']);
    } else {
        echo json_encode(['error' => 'No details found']);
    }

    $conn->close();
}
?>
