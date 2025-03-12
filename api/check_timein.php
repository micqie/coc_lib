<?php

date_default_timezone_set('Asia/Manila');

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$server = "localhost";
$username = "root";
$password = "";
$dbname = "libdb";

$conn = new mysqli($server, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Get user ID from request
$userId = isset($_GET['user_schoolId']) ? $conn->real_escape_string($_GET['user_schoolId']) : '';

if ($userId) {
    $date = date("Y-m-d");

    // temporary modification. REmoved STATUS from query
    $sql = "SELECT time_in FROM lib_logs WHERE user_schoolId = '$userId' AND log_date = '$date' ORDER BY time_in DESC LIMIT 1";
    $query = $conn->query($sql);

    if ($query->num_rows > 0) {
        $row = $query->fetch_assoc();
        $lastTimeIn = strtotime($row['time_in']);
        $currentTime = time();
        $timeDifference = $currentTime - $lastTimeIn;
        $minutesPassed = floor($timeDifference / 60);

        if ($minutesPassed >= 1) {
            echo json_encode(["status" => "allow-timeout", "message" => "You can now scan for time out.", "time_difference" => $minutesPassed]);
        } else {
            $remainingTime = 60 - ($timeDifference % 60);

            echo json_encode(["status" => "too_soon", "message" => "You must wait at least 1 minute before scanning for time out.", "remaining_seconds" => $remainingTime]);
        }

        // echo json_encode(["status" => "success", "time_difference" => $timeDifference]);
    } else {
        echo json_encode(["status" => "no_record", "message" => "No time in record today."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing student ID"]);
}

$conn->close();
