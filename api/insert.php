<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
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

// Read and decode JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['text'])) {
    $text = $conn->real_escape_string($data['text']);
    $date = date("Y-m-d");
    $time = date("H:i:s");

    // Check if user has already timed in but not yet timed out
    $sql = "SELECT time_in FROM lib_logs WHERE user_schoolId = '$text' AND log_date = '$date' AND STATUS = '0' ORDER BY time_in DESC LIMIT 1";
    $query = $conn->query($sql);

    if ($query->num_rows > 0) {
        $row = $query->fetch_assoc();
        $lastTimeIn = strtotime($row['time_in']);
        $currentTime = time();
        $timeDifference = $currentTime - $lastTimeIn;

        if ($timeDifference < 60) { // Less than 1 minute
            echo json_encode(["status" => "error", "message" => "Time Out Invalid! Please wait " . (60 - $timeDifference) . " seconds"]);
        } else {
            // Update time-out
            $updateSql = "UPDATE lib_logs SET time_out = NOW(), STATUS = '1' WHERE user_schoolId = '$text' AND log_date = '$date' AND STATUS = '0'";
            if ($conn->query($updateSql) === TRUE) {
                echo json_encode(["status" => "success", "message" => "Time Out Success"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error updating time out: " . $conn->error]);
            }
        }
    } else {
        // Insert new time-in
        $insertSql = "INSERT INTO lib_logs (user_schoolId, log_date, time_in, STATUS) VALUES ('$text', '$date', '$time', '0')";
        if ($conn->query($insertSql) === TRUE) {
            echo json_encode(["status" => "success", "message" => "Time In Success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error inserting time in: " . $conn->error]);
        }
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing student ID"]);
}

$conn->close();
?>
