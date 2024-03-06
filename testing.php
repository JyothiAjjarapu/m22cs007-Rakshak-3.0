<?php

header('Content-Type: application/json');

$response = ['status' => 'failed'];

if (true) 
{
    $response = ['status' => 'success'];
}

echo json_encode($response);
?>
