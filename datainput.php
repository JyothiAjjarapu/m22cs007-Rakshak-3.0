<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rakshak2";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

$response = array();

// Check the connection
if ($conn->connect_error) {
    $response['status'] = "error";
    $response['message'] = "Connection Failed";
} else {
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    if ($data === null) {
        $response['status'] = "error";
        $response['message'] = "Invalid JSON data";
        http_response_code(400); // Bad Request
    } else {
        $patientId = $data['patientid'];
        $heartRate = $data['heartrate'];
        $spo2 = $data['spo2'];
        $ecg = $data['ecg'];
        $temperature = $data['temperature'];
        $timestamp = $data['timestamp'];

        $changedTime = date("Y-m-d H:i:s", $timestamp);

        // Calculate the data_id automatically by counting rows in the table
        $sql_count = "SELECT COUNT(*) AS total FROM medical_data";
        $result_count = $conn->query($sql_count);
        $row = $result_count->fetch_assoc();
        $dataId = $row['total'] + 1;

        $sql = "INSERT INTO medical_data VALUES ('$dataId', '$patientId', '$heartRate', '$spo2', '$changedTime', '$ecg','$temperature')";

        if ($conn->query($sql) === TRUE) {
            $response['status'] = "Success";
            $response['message'] = "Data inserted successfully";
        } else {
            $response['status'] = "error";
            $response['message'] = "Error ";
        }

        // Close the database connection
     //   $conn->close();
    }
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
