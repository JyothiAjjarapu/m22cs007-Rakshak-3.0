<?php
// session_start(); // Start the session
include('functions-smj.php');
date_default_timezone_set('Asia/Kolkata');
$patients = getUniquePatients();

if (isset($_GET['role'])) {
   // echo "Role: " . $_GET['role']. "<br>";
    $role = $_GET['role'];
} else {
    header("Location: login.php");
    exit();
}

if($role == 'patient') 
{
   // echo "id: " . $_GET['patientID']. "<br>";
    $selectedPatientId = $_GET['patientID'];

    $patientDetails = getPatientDetails($selectedPatientId);
    // echo $patientDetails['age'];
    $heartRate = getHeartRateFromDatabase($selectedPatientId);
    $spO2 = getSpo2FromDatabase($selectedPatientId); 
    $temperature= getTemperatureFromDatabase($selectedPatientId);
    $Ecg=getEcgFromDatabase($selectedPatientId);

} 
else 
{
    // Initialize variables for initial display
    $selectedPatientId = '';
    $patientDetails = getPatientDetails($selectedPatientId);
    $heartRate = 'N/A';
    $spO2 = 'N/A';
    $temperature = 'N/A';
    $Ecg = 'N/A';
}

if (isset($_POST['patient-dropdown'])) 
{
    $selectedPatientId = $_POST['patient-dropdown'];
    $patientDetails = getPatientDetails($selectedPatientId);
    $heartRate = getHeartRateFromDatabase($selectedPatientId);
    $spO2 = getSpo2FromDatabase($selectedPatientId); 
    $temperature = getTemperatureFromDatabase($selectedPatientId) ;
    $Ecg = getEcgFromDatabase($selectedPatientId);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAKSHAK</title>

    <link rel="stylesheet" href="styles.css">   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>


<body>
    <header>
        <h1 class="title">RAKSHAK</h1>
       
    </header>
    <main>

        <div class="container">
                 <!-- Patient dropdown -->
                <?php
                if ($role == 'doctor') {
                    // Display the patient dropdown for doctors
                    $patients = getUniquePatients();
                    echo '<div class="dropdown-container">';
                    echo '<form method="POST">';
                    // echo '<label for="patient-dropdown">Select Patient:</label>';
                    echo '<select id="patient-dropdown" name="patient-dropdown" class="dropdown">';
                    echo '<option value="">Select Patient</option>';
                    foreach ($patients as $id => $name) {
                        echo '<option value="' . $id . '" ' . ($id === $selectedPatientId ? 'selected' : '') . '>' . $name . '</option>';
                    }
                    echo '</select>';
                    echo '<input type="submit" value="Submit">';
                    echo '</form>';
                    echo '</div>';
                }
                ?> 
<div class="patient-section">
    <div class="soldier-image-container">
        <img src="images/soldier-image.jpg" alt="Soldier Image" class="soldier-image">
    </div>
    <!-- Patient details section -->
    <div class="patient-details-box">
        <div class="patient-box">
            <img src="images/patient.png" alt="Patient Icon" class="patient-icon">
        </div>
        <div class="patient-info">
            <h2 class="patient-name"><?php echo isset($patientDetails['Name']) ? $patientDetails['Name'] : '-'; ?></h2>
            <p>Age: <span class="patient-age"><?php echo isset($patientDetails['Age']) ? $patientDetails['Age'] : '-'; ?></span></p>
            <p>Gender: <span class="patient-gender"><?php echo isset($patientDetails['Gender']) ? $patientDetails['Gender'] : '-'; ?></span></p>
            <p>Weight: <span class="patient-weight"><?php echo isset($patientDetails['Weight']) ? $patientDetails['Weight'] : '-'; ?></span></p>
            <p>Height: <span class="patient-height"><?php echo isset($patientDetails['Height']) ? $patientDetails['Height'] : '-'; ?></span></p>
            <p>Referred by: <span class="patient-medicalName"><?php echo isset($patientDetails['Medical_Name']) ? $patientDetails['Medical_Name'] : '-'; ?></span></p>
        </div>
    </div>
</div>



            <div class="data-section">
                <!-- Heart Rate data box -->
                <div class="data-box">

                    <div class="data-box-inner">
                        <img src="images/heart-rate.jpg" alt="Heart Icon" class="data-icon">
                        <h3 class="subtitle-small">Heart Rate</h3>
                    </div>

                    <p class="data-value heart-rate-value"><?php echo $heartRate; ?></p>
                    <div class="graph">
                    <?php
                        if ($role == "patient" || isset($_POST['patient-dropdown'])) 
                        {
                            //echo($_POST['patient-dropdown']);
                            print_r("\n");
                            if(isset($_POST['patient-dropdown'])) {
                                $selectedPatientId = $_POST['patient-dropdown'];
                            }
                          //  $patientDetails = getPatientDetails($selectedPatientId);
                            $heartRate = getHeartRateFromDatabase($selectedPatientId);
                           // echo $heartRate;
                            $all = getHeartRateDataFromDatabase($selectedPatientId) ;
                         //   print_r($temp);
                         //   print_r("\n");

                            // Initialize empty arrays for xValues and yValues
                            $xValues = [];
                            $yValues = [];

                            // Loop through the JSON data and separate x and y values
                            foreach ($all as $data) 
                            {
                                $xValues[] = $data['x'];
                                $yValues[] = $data['y'];
                            }

                            
                        }
                        ?>
                        <canvas id="heartRateChart" width="400" height="150">
                        hello world
                        <script>
                             // Use PHP arrays for xValues and yValues
                                var xValues = <?php echo json_encode($xValues); ?>;
                                var yValues = <?php echo json_encode($yValues); ?>;

                                new Chart("heartRateChart", {
                                type: "line",
                                data: {
                                    labels: xValues,
                                    datasets: [{
                                    label: "Heart Rate ",
                                    fill: false,
                                    lineTension: 0,
                                    backgroundColor: "rgba(255,0,0,1.0",
                                    borderColor: "rgba(0,0,255,0.1)",
                                    data: yValues
                                    }]
                                },
                                options: {
                                    // responsive: false,         // Disable automatic resizing
                                    // maintainAspectRatio: false
                                    legend: {display: false},
                                    scales: {
                                    yAxes: [{ticks: {min: 6, max:16}}],
                                    }
                                }
                                });
                            </script>
                    </div>                    
                </div>

                <!-- SpO2 data box -->
                <div class="data-box">

                    <div class="data-box-inner">
                        <img src="images/spO2-image.avif" alt="SpO2 Icon" class="data-icon">
                        <h3 class="subtitle-small">SpO2</h3>
                    </div>

                    <p class="data-value spo2-value"><?php echo $spO2; ?></p>
                    <div class="graph">
                    <?php
                        if ($role == "patient" || isset($_POST['patient-dropdown']))
                        {
                            if(isset($_POST['patient-dropdown'])) {
                                $selectedPatientId = $_POST['patient-dropdown'];
                            }
                            $spo2Rate = getSpo2DataFromDatabase($selectedPatientId);
                           
                            $temp1 = getSpo2DataFromDatabase($selectedPatientId) ;
                           // print_r($temp1);
                            $xValues = [];
                            $yValues = [];

                            // Loop through the JSON data and separate x and y values
                            foreach ($temp1 as $data) 
                            {
                                $xValues[] = $data['x'];
                                $yValues[] = $data['y'];
                            }                           
                        }
                        ?>
                        <canvas id="spo2Chart" width="400" height="150">
                        hello world
                        <script>
                             // Use PHP arrays for xValues and yValues
                                var xValues = <?php echo json_encode($xValues); ?>;
                                var yValues = <?php echo json_encode($yValues); ?>;

                                new Chart("spo2Chart", {
                                type: "line",
                                data: {
                                    labels: xValues,
                                    datasets: [{
                                    label: "SpO2 Rate",
                                    fill: false,
                                    lineTension: 0,
                                    backgroundColor: "rgba(255,0,0,1.0",
                                    borderColor: "rgba(0,0,255,0.1)",
                                    data: yValues
                                    }]
                                },
                                options: {
                                    // responsive: false,         // Disable automatic resizing
                                    // maintainAspectRatio: false
                                    legend: {display: false},
                                    scales: {
                                    yAxes: [{ticks: {min: 6, max:16}}],
                                    }
                                }
                                });
                            </script>
                    </div>

                </div>
            </div>


            <div class="data-section">
                <!-- Temperture data box -->
                <div class="data-box">

                    <div class="data-box-inner">
                        <img src="images/heart-rate.jpg" alt="Heart Icon" class="data-icon">
                        <h3 class="subtitle-small">Temperature</h3>
                    </div>

                    <p class="data-value heart-rate-value"><?php echo $temperature; ?></p>
                    <div class="graph">
                    <?php
                        if ($role == "patient" || isset($_POST['patient-dropdown'])) 
                        {
                            //echo($_POST['patient-dropdown']);
                            print_r("\n");
                            if(isset($_POST['patient-dropdown'])) {
                                $selectedPatientId = $_POST['patient-dropdown'];
                            }
                          //  $patientDetails = getPatientDetails($selectedPatientId);
                            $heartRate = getTemperatureFromDatabase($selectedPatientId);
                           // echo $heartRate;
                            $all = getTemperatureDataFromDatabase($selectedPatientId) ;
                         //   print_r($temp);
                         //   print_r("\n");

                            // Initialize empty arrays for xValues and yValues
                            $xValues = [];
                            $yValues = [];

                            // Loop through the JSON data and separate x and y values
                            foreach ($all as $data) 
                            {
                                $xtValues[] = $data['x'];
                                $ytValues[] = $data['y'];
                            }

                            
                        }
                        ?>
                        <canvas id="temperatureChart" width="400" height="150">
                        hello world
                        <script>
                             // Use PHP arrays for xValues and yValues
                                var xValues = <?php echo json_encode($xtValues); ?>;
                                var yValues = <?php echo json_encode($ytValues); ?>;

                                new Chart("temperatureChart", {
                                type: "line",
                                data: {
                                    labels: xValues,
                                    datasets: [{
                                    label: "Temperature",
                                    fill: false,
                                    lineTension: 0,
                                    backgroundColor: "rgba(255,0,0,1.0",
                                    borderColor: "rgba(0,0,255,0.1)",
                                    data: yValues
                                    }]
                                },
                                options: {
                                    // responsive: false,         // Disable automatic resizing
                                    // maintainAspectRatio: false
                                    legend: {display: false},
                                    scales: {
                                    yAxes: [{ticks: {min: 6, max:16}}],
                                    }
                                }
                                });
                            </script>
                    </div>                    
                </div>

               
             
            <!-- ECG data box -->
<div class="data-box">
    <div class="data-box-inner">
        <!-- Replace the <img> tag with PHP to dynamically generate the image path -->
        <?php
        // Assuming $Ecg contains the patient ID, update it accordingly based on your logic
        $imagePath = getImagePath($Ecg);
        ?>
        <img src="images/ecg-icon.png" alt="ECG Image" class="data-icon">
        <h3 class="subtitle-small">ECG</h3>
    </div>

    <?php
if ($role == "patient" || isset($_POST['patient-dropdown'])) {
    if (isset($_POST['patient-dropdown'])) {
        $selectedPatientId = $_POST['patient-dropdown'];
    }
    $Ecg = getEcgFromDatabase($selectedPatientId);

    // Check if it's an image path or a placeholder like 'N/A'
    if ($Ecg !== 'N/A') {
        // Display the image with adjusted size
        //echo '<img src="' . $Ecg . '" alt="ECG Image" class="graph-image" width="200%" height="auto">';
        echo '<img src="' . $Ecg . '" alt="ECG Image" class="graph-image" id="zoomed-image">';
        echo '<script>document.getElementById("zoomed-image").style.width = "200%";</script>';
        echo '<a href="new.html?patientID=' . $selectedPatientId . '">View Patient Files</a>';


    } else {
        // Display a placeholder or alternative content
        echo '<p>No ECG Image available</p>';
    }
}
?>

</div>


                </div>
            </div> 
        </div>

    </main>

    <footer>
        <p>Indian Institute of Technology, Jodhpur</p>
        <p id="current-date-time"><?php echo date("F j, Y, g:i a"); ?></p>
    </footer>

</body>

</html>
