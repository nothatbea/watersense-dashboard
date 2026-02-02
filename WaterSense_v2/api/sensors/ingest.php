<?php
$servername = "localhost"; // Replace with your MySQL server name
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "watersense_db"; // Replace with your database name
$api_key_value = "1234"; // A secret key for security

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $api_key = test_input($_POST["api_key"]);
    if($api_key == $api_key_value) {
        $location_id = test_input($_POST["location_id"]);
        $water_level = test_input($_POST["water_level"]);
        $status = test_input($_POST["status"]);
        $battery = test_input($_POST["battery"]);
		
        $sql = "INSERT INTO water_levels (location_id, water_level, status, battery)
                VALUES ('" . $location_id . "', '" . $water_level . "', '" . $status . "', '" . $battery . "')";

        if ($conn->query($sql) === TRUE) {
			
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Wrong API Key provided.";
    }
} else {
    echo "No data posted with HTTP POST.";
}

$conn->close();

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>