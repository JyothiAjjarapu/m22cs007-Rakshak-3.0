<?php
error_reporting(0);
header('Access-Control-Allow-Origin:*');
header('Content-Type: application/json');
header('Access-Control-Allow-Method: POST');
header('Access-Control-Allow-Headers: Content-Type , Access-Control-Allow-Headers, Authorization,X-Request-With');

include('functions.php');

$requestmethod = $_SERVER["REQUEST_METHOD"];

if ($requestmethod == "POST") 
{

    $inputdata = json_decode(file_get_contents("php://input"),true);
    if(empty($inputdata))
    {
        $storedata = create($_POST);
    }
    else
    {
        $storedata = create($inputdata);
    }
    echo $storedata;
    
}
else
{
    echo message(405,"Method not allowed");
}
?>