<?php

require "this.php";

function message($status, $message)
{
    $data = [
        'status' => $status,
        'message' => $message
    ];

    echo json_encode($data); // Display the JSON message
}

function create($inputdata)
{
    global $mysqli;
    echo("hiiiiii!! im inside create function");
    // Check if the database connection is successful
    if (!$mysqli) {
        message(500, "Database connection failed");
        return; // Exit the function
    }

    $patientid = mysqli_real_escape_string($mysqli, $inputdata['patientid']);
    $timestamp = mysqli_real_escape_string($mysqli, $inputdata['timestamp']);
    $heartrate = mysqli_real_escape_string($mysqli, $inputdata['heartrate']);
    $ecg = mysqli_real_escape_string($mysqli, $inputdata['ecg']);
    $spo2 = mysqli_real_escape_string($mysqli, $inputdata['spo2']);
    $temperature = mysqli_real_escape_string($mysqli, $inputdata['temperature']);

    $changedTime = date("Y-m-d H:i:s", $timestamp);

    $sql_count = "SELECT COUNT(*) AS total FROM medical_data";
    $result_count = $mysqli->query($sql_count);
    $row = $result_count->fetch_assoc();
    $dataId = $row['total'] + 1;

    if (empty(trim($patientid)) || empty(trim($timestamp)) || empty(trim($heartrate)) || empty(trim($ecg)) || empty(trim($spo2)) || empty(trim($temperature))) {
        message(422, "Data missing");
        return; // Exit the function
    }

    $query = "INSERT INTO medical_data VALUES ('$dataId','$patientid','$heartrate','$spo2','$changedTime','$ecg','$temperature')";
    $result = mysqli_query($mysqli, $query);
    if ($result) {
        return message(201, "Data inserted successfully");
    } else {
        return message(500, "Internal Server Error");
    }
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming your POST data is in JSON format
    $postData = json_decode(file_get_contents('php://input'), true);

    if ($postData) {
        // Call the create function with the parsed POST data
        create($postData);
    } else {
        message(400, "Invalid JSON data");
    }
} else {
    message(405, "Method Not Allowed");
}
