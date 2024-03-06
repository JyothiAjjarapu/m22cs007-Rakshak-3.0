<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rakshak2";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) 
{
    die("Connection failed: " . $conn->connect_error);
}

function getUniquePatients() 
{
    global $conn;
    $sql = "SELECT DISTINCT `Patient Id`, `Name` FROM `patients`";
    $result = $conn->query($sql);
    $patients = [];

    if ($result->num_rows > 0) 
    {
        while ($row = $result->fetch_assoc()) 
        {
            $patients[$row["Patient Id"]] = $row["Name"];
        }
    }

    return $patients;
}

function getPatientDetails($patientId) 
{
    global $conn;

    if (empty($patientId)) 
    {
        return [
            'PatientName' => '-',
            'Age' => '-',
            'Gender' => '-',
            'Weight' => '-',
            'Height' => '-',
            'Medical_id' => '-'
        ];
    }

    $sql = "SELECT * FROM `patients` WHERE `Patient Id` = '$patientId'"; 
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) 
    {
        $patientDetails = $result->fetch_assoc();

        $patientDetails['Medical_Name'] = getMedicalName($patientId);
        return $patientDetails;
    } 
    else 
    {
        return [
            'PatientName' => '-',
            'Age' => '-',
            'Gender' => '-',
            'Weight' => '-',
            'Height' => '-',
            'Medical_Name' => '-'
        ];
    }
}


function getMedicalId($patientId) //for medicalID
{
    global $conn;

    $sql = "SELECT `Medical_id` FROM `medical_data` WHERE `Patient Id` = '$patientId' ORDER BY `Timestamp` DESC LIMIT 1"; 
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) 
    {
        $row = $result->fetch_assoc();
        return $row['Medical_id'];
    } 
    else 
    {
        return 'N/A';
    }
}

function getMedicalName($patientId) //medical-id..name
{
    global $conn;

    // First, retrieve the Medical_id from the medical_data table
    $medicalId = getMedicalId($patientId);

    if ($medicalId === 'N/A') 
    {
        return 'N/A'; // No valid Medical_id found
    }

    // Now, use the retrieved Medical_id to fetch the Name from the medical-info table
    $sql = "SELECT mi.`Name` FROM `medical-info` mi
            JOIN `medical_data` md ON mi.`Medical_id` = md.`Medical_id`
            WHERE md.`Medical_id` = '$medicalId'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) 
    {
        $row = $result->fetch_assoc();
        return $row['Name'];
    } 
    else 
    {
        return 'N/A'; // Medical Name not found
    }
}



//for heartrate and spo2
function getHeartRateFromDatabase($patientId) //to display heartrate
{
    global $conn;
    $sql = "SELECT `Heartrate` FROM `medical_data` WHERE `Patient Id` = '$patientId' ORDER BY `Timestamp` DESC LIMIT 1"; // Replace with your table and column names
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) 
    {
        $row = $result->fetch_assoc();
        return (int)$row['Heartrate'];
    } 
    else 
    {
        return 'N/A';
    }
}
function getSpo2FromDatabase($patientId) 
{
    global $conn;
    $sql = "SELECT `spO2` FROM `medical_data` WHERE `Patient Id` = '$patientId' ORDER BY `Timestamp` DESC LIMIT 1"; 
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) 
    {
        $row = $result->fetch_assoc();
        return (int)$row['spO2'];
    } 
    else 
    {
        return 'N/A';
    }
}

//getTemperature and Ecg data values
function getTemperatureFromDatabase($patientId) 
{
    global $conn;
    $sql = "SELECT `temperature` FROM `medical_data` WHERE `Patient Id` = '$patientId' ORDER BY `Timestamp` DESC LIMIT 1"; 
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) 
    {
        $row = $result->fetch_assoc();
        return (int)$row['temperature'];
    } 
    else 
    {
        return 'N/A';
    }
}
// function getEcgFromDatabase($patientId) 
// {
//     global $conn;
//     $sql = "SELECT `ecg` FROM `medical_data` WHERE `Patient Id` = '$patientId' ORDER BY `Timestamp` DESC LIMIT 1"; 
//     $result = $conn->query($sql);
//     if ($result && $result->num_rows > 0) 
//     {
//         $row = $result->fetch_assoc();
//         return (int)$row['ecg'];
//     } 
//     else 
//     {
//         return 'N/A';
//     }
// }
function getImagePath($patientId)
{
    // Assuming the images are stored in the "images" folder with the Patient ID as the filename
    $imagePath = "captured/{$patientId}_page2.png"; // Adjust the file extension as needed

    // Check if the image file exists
    if (file_exists($imagePath)) {
        return $imagePath;
    } else {
        return 'N/A';
    }
}

// Modify this function to use the new getImagePath function
function getEcgFromDatabase($patientId) 
{
    return getImagePath($patientId);
}




// for graphs.. x and y axis details fetching

function getHeartRateDataFromDatabase($patientId) 
{
    global $conn;
    $sql = "SELECT `Timestamp`, `Heartrate` FROM `medical_data` WHERE `Patient Id` = '$patientId' ORDER BY `Timestamp` ASC"; 
    $result = $conn->query($sql);
    if (!$result) 
    {
        die("Error: " . $conn->error);
    }
    $newData = [];
    if ($result->num_rows > 0) 
    {
        while ($row = $result->fetch_assoc()) 
        {
            $newData[] = [ 'x' => $row['Timestamp'], 'y' => (int)$row['Heartrate'], ];
        }
    }
    return $newData;
}

function getSpo2DataFromDatabase($patientId)
{
    global $conn;

    $sql = "SELECT `Timestamp`, `SpO2` FROM `medical_data` WHERE `Patient Id` = '$patientId' ORDER BY `Timestamp` ASC"; 
    $result = $conn->query($sql);
    $data = [];
    if ($result->num_rows > 0) 
    {
        while ($row = $result->fetch_assoc()) 
        {
            $data[] = [ 'x' => $row['Timestamp'], 'y' => (int)$row['SpO2'], ];
        }
    }
    return $data;
}


// for graphs.. x and y axis details fetching for temperature and ecg

function getTemperatureDataFromDatabase($patientId) 
{
    global $conn;
    $sql = "SELECT `Timestamp`, `temperature` FROM `medical_data` WHERE `Patient Id` = '$patientId' ORDER BY `Timestamp` ASC"; 
    $result = $conn->query($sql);
    if (!$result) 
    {
        die("Error: " . $conn->error);
    }
    $tempData = [];
    if ($result->num_rows > 0) 
    {
        while ($row = $result->fetch_assoc()) 
        {
            $tempData[] = [ 'x' => $row['Timestamp'], 'y' => (int)$row['temperature'], ];
        }
    }
    return $tempData;
}

// function getEcgDataFromDatabase($patientId)
// {
//     global $conn;

//     $sql = "SELECT `Timestamp`, `ecg` FROM `medical_data` WHERE `Patient Id` = '$patientId' ORDER BY `Timestamp` ASC"; 
//     $result = $conn->query($sql);
//     $data1 = [];
//     if ($result->num_rows > 0) 
//     {
//         while ($row = $result->fetch_assoc()) 
//         {
//             $data1[] = [ 'x' => $row['Timestamp'], 'y' => (int)$row['ecg'], ];
//         }
//     }
//     return $data1;
// }

