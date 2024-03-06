<?php
$patientID = $_GET['patientID'];

$files = glob('uploads/' . $patientID . '_*.pdf');

header('Content-Type: application/json');
echo json_encode(['files' => $files]);
?>
