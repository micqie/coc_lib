<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents("php://input"));

if (!empty($data->user_schoolId)) {
    try {
        // First check if user exists
        $check_query = "SELECT * FROM lib_users WHERE user_schoolId = :user_schoolId";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(":user_schoolId", $data->user_schoolId);
        $check_stmt->execute();

        if ($check_stmt->rowCount() > 0) {
            // User exists, proceed with time-in
            $query = "INSERT INTO lib_logs (user_schoolId, time_in) VALUES (:user_schoolId, CURRENT_TIME())";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":user_schoolId", $data->user_schoolId);
            
            if ($stmt->execute()) {
                // Get user details
                $user_query = "SELECT u.*, d.department_name, c.course_name 
                             FROM lib_users u 
                             LEFT JOIN lib_departments d ON u.user_departmentId = d.department_id 
                             LEFT JOIN lib_courses c ON u.user_courseId = c.course_id 
                             WHERE u.user_schoolId = :user_schoolId";
                $user_stmt = $db->prepare($user_query);
                $user_stmt->bindParam(":user_schoolId", $data->user_schoolId);
                $user_stmt->execute();
                $user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);

                http_response_code(200);
                echo json_encode(array(
                    "message" => "Time-in successful.",
                    "user_data" => $user_data
                ));
            } else {
                throw new Exception("Failed to record time-in.");
            }
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "User not found."));
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array("message" => "Error: " . $e->getMessage()));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Incomplete data."));
}
?> 