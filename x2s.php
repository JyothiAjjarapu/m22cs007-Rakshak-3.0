<?php
$uploadDir = 'uploads/';
$webUrl = '/opt/lampp/htdocs/SMJ1';

// Update the path to the Python executable
$pythonExecutable = '/opt/lampp/htdocs/SMJ1/venv/bin/python3'; // Replace with the actual path
$pythonScript = 'capture-image-firstCopy.py'; // assuming both scripts are in the same directory

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['pdfFile']) && $_FILES['pdfFile']['error'] === UPLOAD_ERR_OK) {
        $uploadedFilePath = $uploadDir . basename($_FILES['pdfFile']['name']);

        if (move_uploaded_file($_FILES['pdfFile']['tmp_name'], $uploadedFilePath)) {
            $sharedLink = $webUrl . '/' . $uploadedFilePath;
            //echo json_encode(['link' => $sharedLink]);
            echo json_encode(['message' => 'File uploaded successfully']);
            flush();

            // Send a response to the API
            $apiResponse = ['status' => 'File received successfully'];
            $ch = curl_init($apiEndpoint);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiResponse));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $apiResult = curl_exec($ch);
            curl_close($ch);

            // Check the API response if needed
            if ($apiResult === false) {
                // Handle error, e.g., log it
                file_put_contents('api_error_log.txt', "API Error: " . curl_error($ch) . PHP_EOL, FILE_APPEND);
                   }


            // Trigger the Python script asynchronously using AJAX
            $pdfFilename = basename($_FILES['pdfFile']['name']);
            $command = "$pythonExecutable $pythonScript $pdfFilename";

            // Log the command to a file for debugging
            file_put_contents('/opt/lampp/htdocs/SMJ1/logs/exec_log.txt', $command . PHP_EOL, FILE_APPEND);

            // Execute the command and capture the output
            exec($command, $output, $returnCode);

            // Log the output and return code to the same file
            file_put_contents('exec_log.txt', "Return Code: $returnCode" . PHP_EOL, FILE_APPEND);
            file_put_contents('exec_log.txt', "Output: " . print_r($output, true) . PHP_EOL, FILE_APPEND);

            // Check for errors during execution
            if ($returnCode !== 0) {
                http_response_code(500);
                header('Content-Type: application/json'); // Add this line
                echo json_encode(['error' => 'Failed to execute Python script']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to move uploaded file']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid file upload or no file provided']);
    }
} else {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
}
?>
