<?php
// Connect to the MySQL database


$mysqli = new mysqli("localhost", "root", "", "rakshak2");

// Check the database connection
if ($mysqli->connect_error) 
{
    // Connection to the database failed
    $response = array('message' => 'Database connection failed');
    echo json_encode($response);
} 
else 
{
    // Connection to the database is successful
    $response = array('message' => 'Connection Successful');
    echo json_encode($response);
   // $mysqli->close(); // Close the database connection
}
?>