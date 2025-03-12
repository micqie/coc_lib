<?php

date_default_timezone_set("Asia/Manila");

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json");

include 'connect_pdo.php';


// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['text'])) {
    $text = $data['text'];
    $date = date("Y-m-d");
    $time = date("H:i:s");

    try {
        // Check latest time_in
        $sql = "SELECT time_in FROM lib_logs WHERE user_schoolId = :user_schoolId AND log_date = CURDATE() ORDER BY time_in DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['user_schoolId' => $text]);

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $lastTimeIn = strtotime($row['time_in']);
            $currentTime = time();
            $timeDifference = $currentTime - $lastTimeIn;
            $minutesPassed = floor($timeDifference / 60);

            if ($minutesPassed < 1) {
                echo json_encode([
                    "status" => "too_soon",
                    "message" => "You must wait at least 1 minute before scanning for time out."
                ]);
                exit;
            }
        }


        // Check if user already has time-in today and not timed out
        $sql = "SELECT * FROM lib_logs WHERE user_schoolId = :user_schoolId AND log_date = CURDATE() AND time_out IS NULL";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['user_schoolId' => $text]);

        if ($stmt->rowCount() > 0) {
            // Time-out logic
            if ($minutesPassed >= 1) {

                $updateSql = "UPDATE lib_logs SET time_out = :time_out 
                              WHERE user_schoolId = :user_schoolId AND log_date = CURDATE() AND time_out IS NULL";
                $updateStmt = $conn->prepare($updateSql);
                if ($updateStmt->execute(['time_out' => $time, 'user_schoolId' => $text])) {
                    echo json_encode([
                        "status" => "success",
                        "message" => "Time-out recorded successfully!",
                        "log_date" => $date,
                        "time_out" => $time
                    ]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Error updating time-out"]);
                }
            } else {
                echo json_encode(["status" => "too_soon", "message" => "You must wait at least 1 minute before scanning for time out."]);
            }
        } else {
            // temporary alteration. removed STATUS from query together with its value.
            // Time-in logic
            $insertSql = "INSERT INTO lib_logs (user_schoolId, time_in, log_date) 
                          VALUES (:user_schoolId, :time_in, CURDATE())";
            $insertStmt = $conn->prepare($insertSql);
            if ($insertStmt->execute(['user_schoolId' => $text, 'time_in' => $time])) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Time-in recorded successfully!",
                    "log_date" => $date,
                    "time_in" => $time
                ]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error inserting time-in"]);
            }
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing student ID"]);
}

// Close the database connection
$conn = null;
