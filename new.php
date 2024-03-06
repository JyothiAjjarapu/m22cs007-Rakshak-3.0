<?php
// Get patient ID from the query parameter
$patientID = $_GET['patientID'];

// Validate and sanitize the patient ID (you might want to improve this)
if (!preg_match('/^\d+$/', $patientID)) 
{
    die('Invalid patient ID');
}

// Directory where the files are stored
$uploadsDir = 'uploads/';

// Get all PDF files related to the patient ID
$files = glob($uploadsDir . $patientID . '_*.pdf');

// Sort files by modification time (latest to oldest)
array_multisort(array_map('filemtime', $files), SORT_DESC, $files);

// Return the list of files as JSON
header('Content-Type: application/json');
echo json_encode($files);
