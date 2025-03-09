<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json");

include 'connect_pdo.php';

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['text'])) {
    $text = $data['text']; // Student ID
    $date = date("Y-m-d");
    $time = date("H:i:s");

    try {
        // Check if the student has already timed in today but has no time-out yet
        $sql = "SELECT log_id FROM lib_logs WHERE user_schoolId = :user_schoolId AND log_date = :log_date AND time_out IS NULL ORDER BY time_in DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['user_schoolId' => $text, 'log_date' => $date]);

        if ($stmt->rowCount() > 0) {
            // If a time-in exists but no time-out, update it with time_out
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $updateSql = "UPDATE lib_logs SET time_out = :time_out, STATUS = '1' WHERE id = :id";
            $updateStmt = $conn->prepare($updateSql);
            if ($updateStmt->execute(['time_out' => $time, 'id' => $row['id']])) {
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
            // If no active time-in found, insert a new time-in
            $insertSql = "INSERT INTO lib_logs (user_schoolId, time_in, log_date, STATUS) 
                          VALUES (:user_schoolId, :time_in, :log_date, '0')";
            $insertStmt = $conn->prepare($insertSql);
            if ($insertStmt->execute(['user_schoolId' => $text, 'time_in' => $time, 'log_date' => $date])) {
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

?>