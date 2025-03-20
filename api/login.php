<?php
include '../config/database.php'; // Adjust this based on your project structure

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$email = $data['user_email'];
$password = md5($data['user_password']); // Ensure this matches how passwords are stored

// Query to check user credentials
$query = "SELECT user_id, user_email, usertype_id FROM lib_users WHERE user_email = ? AND user_password = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $email, $password);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    session_start();
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['user_email'] = $user['user_email'];
    $_SESSION['usertype_id'] = $user['usertype_id'];

    if ($user['usertype_id'] == 4) { // Adjust this ID to match your Admin type
        echo json_encode(["status" => "success", "redirect" => "admin_dashboard.html"]);
    } else {
        echo json_encode(["status" => "success", "redirect" => "dashboard.html"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid email or password!"]);
}

$stmt->close();
$conn->close();
