<?php

ini_set("session.cookie_httponly", 1);

session_start();
require 'database.php';

// Set content type to JSON
header("Content-Type: application/json");

$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);


/*if(!isset($json_obj['userID']) || !isset($json_obj['password'])){
    echo json_encode(array(
        "success" => false,
        "message" => "fields not set"
    ));
    exit;
}*/

$user_id = $json_obj['userID'];
$passwordGuess = $json_obj['password'];


//$_SESSION['csrfToken'] = bin2hex(random_bytes(32));


// Prepare SQL query for users table with only 'id' and 'password'
$stmt = $mysqli->prepare("SELECT password FROM users WHERE id = ?");
//$stmt = $mysqli->prepare("SELECT password FROM users WHERE id=?");
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
    // Login succeeded; set session variables
    $_SESSION['user_id'] = $user_id;

    // Generate CSRF token
    $_SESSION['token'] = bin2hex(random_bytes(32));

    echo json_encode(array(
        "success" => true,
        "user_id" => $user_id,
        "token" => $_SESSION['token']
    ));
    exit;

}
echo json_encode(array(
    "success" => false,
    "message" => "Invalid user ID or password."
));
exit;
?>