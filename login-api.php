<?php
// Connect to the MySQL database
$mysqli = new mysqli("localhost", "root", "", "rakshak2");

// Check the connection
if ($mysqli->connect_error)
{
    die("Connection failed: " . $mysqli->connect_error);
}

// Check if the HTTP request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    // Get JSON data from the Android app
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data);

    // Extract data
    $patientid = $data->patientid;
    $password = $data->password;

    // Check if the credentials exist in the database
    $sql = "SELECT * FROM `patient-info` WHERE `patientID` = '$patientid' AND `password` = '$password'";

    $result = $mysqli->query($sql);

    if ($result->num_rows > 0)
    {
        $response = array('status' => 'ok'); // Credentials found
    }
    else
    {
        $response = array('status' => 'not found'); // Credentials not found
    }

    // Return the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}
else
{
    // Handle other HTTP methods or invalid requests
    http_response_code(405); // Method Not Allowed
}

// Close the database connection
//$mysqli->close();
?>
