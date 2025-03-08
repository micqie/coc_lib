<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json");

include 'connect_pdo.php';

try {
    // Fetch logs from database
    $sql = "SELECT user_schoolId, log_date, time_in, COALESCE(time_out, time_in) AS time_out 
            FROM lib_logs 
            ORDER BY log_date DESC, time_in DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON response
    echo json_encode(["status" => "success", "data" => $logs]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Error fetching logs: " . $e->getMessage()]);
}

$conn = null;
?>