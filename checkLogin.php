<?php
session_start();
require 'database.php';

header("Content-Type: application/json");

// Check if the user is logged in by verifying if `user_id` is set in the session
if (isset($_SESSION['user_id'])) {
    echo json_encode(array(
        "login" => true,
        "token" => $_SESSION['token']
    ));
    exit;
} else {
    echo json_encode(array(
        "login" => false
    ));
    exit;
}
?>