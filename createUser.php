<?php
session_start();
require 'database.php';

header("Content-Type: application/json");
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
// Check if user is logged in

// Validate CSRF token

// Validate and sanitize inputs

$user_id = $json_obj['userID'];
$password = htmlentities($json_obj['password']);

// Check if user ID is available
$stmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE id = ?");
if (!$stmt) {
    exit; // Query preparation failed, terminate script
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($cnt);
$stmt->fetch();
$stmt->close();

// If user ID is available, create new user
if ($cnt == 0) {
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    $stmt2 = $mysqli->prepare("INSERT INTO users (id, password) VALUES (?, ?)");
    if (!$stmt2) {
        exit;
    }

    $stmt2->bind_param('is', $user_id, $passwordHash);
    $stmt2->execute();
    $stmt2->close();
}
echo json_encode(array(
    "success" => true,
));
?>