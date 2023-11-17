<?php

include('functions-smj.php');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rakshak2";

$conn = new mysqli($servername, $username, $password, $dbname);

$response = array(); // Create a response array

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    // Retrieve raw JSON data from the request body
    $json_data = file_get_contents('php://input');

    // Check if the JSON data is valid
    $data = json_decode($json_data, true);

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
        $heartRate = $data['heartrate'];
        $spo2 = $data['spo2'];
        $ecg=$data['ecg'];
        $temperature=$data['temperature'];
        $timestamp = $data['timestamp'];
        $Medical_Id = $data['Medical_Id'];

        // Perform data validation here if needed

        // Create a database connection
        if ($conn->connect_error)
        {
            $response['status'] = "error";
            $response['message'] = "Connection failed" ;
        }
        else
        {
            // Calculate the data_id automatically by counting rows in the table
            $sql_count = "SELECT COUNT(*) AS total FROM medical_data";
            $result_count = $conn->query($sql_count);
            $row = $result_count->fetch_assoc();
            $dataId = $row['total'] + 1;

            // Insert data into the medical_data table
            $sql = "INSERT INTO medical_data VALUES ('$dataId', '$patientId', '$heartRate', '$spo2', '$timestamp', '$ecg','$temperature','$Medical_Id')";

            if ($conn->query($sql) === TRUE)
            {
                $response['status'] = "Success";
                $response['message'] = "Data inserted successfully";
            }
            else
            {
                //echo($conn->query($sql));
                $sql_error = mysqli_error($conn);

                $response['status'] = "error";
               // $response['message'] = "Error ";
                $response['message'] = "SQL Query Error: " . $sql_error;
            }

            // Close the database connection
            $conn->close();
        }
    }
}
else
{
    // Handle other HTTP methods or invalid requests
    http_response_code(405); // Method Not Allowed
    $response['message'] = "Method Not Allowed";
    $response['status'] = "error";
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
