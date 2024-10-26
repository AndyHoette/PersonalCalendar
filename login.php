<?php
session_start();
require 'database.php';

// Set content type to JSON
header("Content-Type: application/json");

// Validate that required fields are set and sanitize inputs
if (!isset($_POST['user'], $_POST['pass_guess']) || empty(trim($_POST['user'])) || empty(trim($_POST['pass_guess']))) {
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid input."
    ));
    exit;
}

// Sanitize inputs
$user_id = (int) $_POST['user']; // Assuming 'user' field is meant to be the user ID
$pwd_guess = (string) $_POST['pass_guess'];

// Prepare SQL query for users table with only 'id' and 'password'
$stmt = $mysqli->prepare("SELECT COUNT(*), id, password FROM users WHERE id = ?");
if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => "Query preparation failed."
    ));
    exit;
}

// Bind parameter and execute query
$stmt->bind_param('i', $user_id);  // 'i' for integer
$stmt->execute();
$stmt->bind_result($cnt, $db_user_id, $pwd_hash);
$stmt->fetch();

// Verify password and login if successful
if ($cnt == 1 && password_verify($pwd_guess, $pwd_hash)) {
    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);

    // Login succeeded; set session variables
    $_SESSION['user_id'] = $db_user_id;

    // Generate CSRF token
    $_SESSION['token'] = bin2hex(random_bytes(32));

    echo json_encode(array(
        "success" => true,
        "token" => $_SESSION['token']
    ));
    exit;
} else {
    // Login failed
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid user ID or password."
    ));
    exit;
}

// Close statement
$stmt->close();
?>