<?php

include('functions-smj.php');

// Connect to the MySQL database
$mysqli = new mysqli("localhost", "root", "", "rakshak2");

// Check the connection
if ($mysqli->connect_error)
{
    die("Connection failed: " . $mysqli->connect_error);
}

// Check if the HTTP request method is GET
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    // Retrieve patient_id from the query parameters
    $patientId = $_GET['patient_id'];

    // Query to retrieve data for the specified patient_id
    $sql = "SELECT `patient ID`, Heartrate, spO2, ecg, temperature, `Timestamp`
        FROM medical_data
        WHERE `patient ID` = $patientId
        ORDER BY `Data ID` ASC";

    $result = $mysqli->query($sql);
   
    if ($result->num_rows > 0)
    {
        // Create an array to store the retrieved data
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = array(
                'patient_id' => $row['patient ID'],
                'Heartrate' => $row['Heartrate'],
                'SpO2' => $row['spO2'],
                'Ecg' => $row['ecg'],
                'temperature' => $row['temperature'],
                'timestamp' => $row['Timestamp']
            );
        }
       
        // Return the data as a JSON array
        header('Content-Type: application/json');
        echo json_encode($data);
    }
    else
    {
        // No data found for the specified patient_id
        echo "No data found";
       
    }
}
else
{
    // Handle other HTTP methods or invalid requests
    http_response_code(405); // Method Not Allowed
}

// Close the database connection
//$mysqli->close();
?>
