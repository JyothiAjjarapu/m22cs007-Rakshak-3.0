<?php
require "this.php";

function message($status, $message)
{
    $data = [
        'status' => $status,
        'message' => $message
    ];

    return json_encode($data);
}

function create($inputdata)
{
    global $conn;

    // Validate and sanitize input data
    $patientid = mysqli_real_escape_string($conn, $inputdata['patientid']);
    $timestamp = mysqli_real_escape_string($conn, $inputdata['timestamp']);
    $heartrate = mysqli_real_escape_string($conn, $inputdata['heartrate']);
    $ecg = mysqli_real_escape_string($conn, $inputdata['ecg']);
    $spo2 = mysqli_real_escape_string($conn, $inputdata['spo2']);
    $temperature = mysqli_real_escape_string($conn, $inputdata['temperature']);

    // Check for missing data
    if (empty(trim($patientid)) || empty(trim($timestamp)) || empty(trim($heartrate)) || empty(trim($ecg)) || empty(trim($spo2)) || empty(trim($temperature))) {
        return message(422, "Data missing");
    } else {
        // Use prepared statement to insert data
        $sql = "INSERT INTO medical_data (dataId, patientid, heartrate, spo2, timestamp, ecg, temperature) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssss", $dataId, $patientid, $heartrate, $spo2, $timestamp, $ecg, $temperature);

        // Calculate the dataId
        $sql_count = "SELECT COUNT(*) AS total FROM medical_data";
        $result_count = $conn->query($sql_count);
        $row = $result_count->fetch_assoc();
        $dataId = $row['total'] + 1;

        // Execute the query
        if ($stmt->execute()) {
            return message(201, "Data inserted successfully");
        } else {
            return message(500, "Internal Server Error");
        }
    }
}
