<?php
// Connect to the MySQL database
$mysqli = new mysqli("localhost", "root", "", "rakshak2");

// Check the connection
if ($mysqli->connect_error)
{
    die("Connection failed: " . $mysqli->connect_error);
}

// Retrieve raw JSON data from the request body
$json_data = file_get_contents('php://input');

// Check if the JSON data is valid
$data = json_decode($json_data, true);

$response = array(); // Create a response array

if ($data === null)
{
    // JSON data is not valid
    $response['status'] = "error";
    $response['message'] = "Invalid JSON data";
    http_response_code(400); // Bad Request
}
else
{
    // Extract data from the JSON
    $patientId = $data['patientid'];
    $password = $data['password'];
    $name = $data['name'];
    $age = $data['age'];
    $gender = $data['gender'];
    $weight = $data['weight'];
    $height = $data['height'];

    // Perform data validation here if needed

    // Check if the patient_id already exists
    $sql_check_duplicate = "SELECT * FROM `patient-info` WHERE `patientID` = '$patientId'";
    $result_check_duplicate = $mysqli->query($sql_check_duplicate);

    if ($result_check_duplicate->num_rows > 0)
    {
        // Duplicate patient_id found
        $response['status'] = "error";
        $response['message'] = "Duplicate patient_id";
    }
    else
    {
        // Insert data into the patient_table
        $sql_patient_table = "INSERT INTO patients VALUES ('$patientId', '$name', '$age', '$gender', '$weight', '$height')";
       
        // Insert data into the patient_info table
        $sql_patient_info = "INSERT INTO `patient-info` VALUES ('$patientId', '$password')";
       
        if ($mysqli->query($sql_patient_table) === TRUE && $mysqli->query($sql_patient_info) === TRUE)
        {
            // Registration Successful
            $response['status'] = "success";
            $response['message'] = "Registration Successful";
        }
        else
        {
            // Registration Error
            $response['status'] = "error";
            $response['message'] = "Registration Error";
        }
    }

    // Close the database connection
   // $mysqli->close();
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>