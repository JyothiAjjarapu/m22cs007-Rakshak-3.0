<?php
session_start(); // Start the session
include('functions-smj.php');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rakshak2";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER["REQUEST_METHOD"] == "POST")  
{
    $role = $_POST['role'];
    $id = $_POST['id'];
    $password = $_POST['password'];

    if ($role == 'doctor' && $id == 'Admin' && $password == 'rakshak') 
    {
        header("Location: index1.php?role=$role");
        exit(); 
    } 
    elseif ($role == 'patient') 
    {
        // Create a new mysqli connection
        
        
        // Check the connection
        if ($conn->connect_error) 
        {
            die("Connection failed: " . $conn->connect_error);
        }

        // Create a prepared statement
        $stmt = $conn->prepare("SELECT * FROM `patient-info` WHERE patientID = ? AND password = ?");
        
        // Check if the statement was prepared successfully
        if (!$stmt) 
        {
            die("Preparation failed: " . $conn->error);
        }
        
        // Bind the parameters
        $stmt->bind_param("ss", $id, $password);
        
        // Execute the prepared statement
        if ($stmt->execute()) 
        {
            // Get the result
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) 
            {
                header("Location: index1.php?role=$role&patientID=$id");
                exit(); 
            }
            else 
            {
                echo "Invalid login credentials.";
            }
        } 
        else 
        {
            echo "Error executing query: " . $stmt->error;
        }
        
        // Close the prepared statement and database connection
        $stmt->close();
       // $conn->close();
    }
}
?>

<!-- The HTML form remains the same -->
<!DOCTYPE html>
<html>

<head>
    <title>Registration Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            max-width: 400px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
        }

        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function setDefaultValues() 
        {
            var role = document.getElementById("role").value;
            var nameField = document.getElementById("id");
            var passwordField = document.getElementById("password");

            if (role === "doctor") 
            {
                nameField.value = "Admin";
                passwordField.value = "rakshak";
                nameField.readOnly = true;
                passwordField.readOnly = true;
            } 
            else 
            {
                nameField.value = "";
                passwordField.value = "";
                nameField.readOnly = false;
                passwordField.readOnly = false;
            }
        }
    </script>
</head>
<body>
    <h2>Register</h2>
    <form method="POST">
        <label for="role">Select Role:</label>
        <select id="role" name="role" required onchange="setDefaultValues()">
            <option value="doctor">Doctor</option>
            <option value="patient" selected>Patient</option>
        </select><br><br>

        <label for="id">ID:</label>
        <input type="text" id="id" name="id" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <input type="submit" value="Login">
    </form>
</body>
</html>