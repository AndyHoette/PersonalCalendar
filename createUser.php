<?php
session_start();
require 'database.php';

header("Content-Type: application/json");

// Check if user is logged in
if (empty($_SESSION["user_id"])) {
    echo json_encode(array(
        "success" => false,
        "message" => "User not logged in."
    ));
    exit;
}

// Validate CSRF token
if (!isset($_POST['token']) || !hash_equals($_POST['token'], $_SESSION['token'])) {
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid CSRF token."
    ));
    exit;
}

// Validate and sanitize inputs
if (!isset($_POST['id'], $_POST['password']) || !is_numeric($_POST['id']) || empty(trim($_POST['password']))) {
    exit; // Invalid input, terminate script
}

$user_id = (int) $_POST['id'];
$password = htmlentities($_POST['password']);

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