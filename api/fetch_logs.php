<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json");

$server = "localhost";
$username = "root";
$password = "";
$dbname = "libdb";

$conn = new mysqli($server, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Fetch logs from database
$sql = "SELECT user_schoolId, log_date, time_in, COALESCE(time_out, NULL) AS time_out 
        FROM lib_logs 
        ORDER BY log_date DESC, time_in DESC";
        
$result = $conn->query($sql);

$logs = [];
while ($row = $result->fetch_assoc()) {
    $logs[] = $row;
}

// Return JSON response
echo json_encode(["status" => "success", "data" => $logs]);

$conn->close();
?>
