<?php
session_start();
require 'database.php';

// Set content type to JSON
header("Content-Type: application/json");

// Sanitize inputs
$user_id = $_POST['user'];
$passwordGuess = $_POST['pass_guess'];

// Prepare SQL query for users table with only 'id' and 'password'
//$stmt = $mysqli->prepare("SELECT COUNT(*), id, password FROM users WHERE id = ?");
$stmt = $mysqli->prepare("SELECT password FROM users WHERE id=?");
if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => "Query preparation failed."
    ));
    exit;
}

// Bind parameter and execute query
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($pwd_hash);
$stmt->fetch();

// Verify password and login if successful
if (password_verify($passwordGuess, $pwd_hash)) {
    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);

    // Login succeeded; set session variables
    $_SESSION['user_id'] = $user_id;

    // Generate CSRF token
    $_SESSION['token'] = bin2hex(random_bytes(32));

    header('Location: unauthorized.php');


}
echo json_encode(array(
    "success" => false,
    "message" => "Invalid user ID or password."
));
exit;


?>