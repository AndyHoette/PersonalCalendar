<?php
session_start();
require 'database.php';

header("Content-Type: application/json");

// Check if user is logged in
if (empty($json_obj["user_id"])) {
    echo json_encode(array(
        "success" => false,
        "message" => "User not logged in."
    ));
    exit;
}

// Validate CSRF token
if (!isset($json_obj['csrfToken']) || !hash_equals($json_obj['csrfToken'], $json_obj['csrfToken'])) {
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid CSRF token."
    ));
    exit;
}

// Validate and sanitize inputs
if (!isset($json_obj['id'], $json_obj['password']) || !is_numeric($json_obj['id']) || empty(trim($json_obj['password']))) {
    exit; // Invalid input, terminate script
}

$user_id = $json_obj['user_id'];
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
?>