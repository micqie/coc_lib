<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json");

include 'connect_pdo.php';

try {
    // Fetch logs from database
    $sql = "SELECT user_schoolId, time_in, time_out, DATE_FORMAT(log_date, '%Y-%m-%d') AS log_date FROM lib_logs ORDER BY log_date DESC, time_in DESC";


    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Kung wala pay value ang time_out
    foreach ($logs as &$log) {
        if (!isset($log['time_out']) || is_null($log['time_out']) || $log['time_out'] === "") {
            $log['time_out'] = "N/A";
        }
    }

    // Return JSON response
    echo json_encode(["status" => "success", "data" => $logs]);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Error fetching logs: " . $e->getMessage()]);
}

$conn = null;
