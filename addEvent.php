<?php
session_start();
require 'database.php';

// Set content type to JSON
header("Content-Type: application/json");
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(array(
        "success" => false,
        "message" => "User not logged in."
    ));
    exit;
}


$owner = $_SESSION['user_id'];
$title = $json_obj['title'];
$year = $json_obj['year'];
$month = $json_obj['month'];
$day = $json_obj['day'];
$hour = $json_obj['hour'];
$minute = $json_obj['minute'];

//not sure about recurring
$recurring = $json_obj['recurring'] ? 1 : 0;




if (!isset($json_obj['csrfToken']) || empty(trim($json_obj['csrfToken'])) || !isset(($json_obj['title'])) || !isset(($json_obj['year'])) || !isset(($json_obj['month'])) || !isset(($json_obj['day'])) || !isset(($json_obj['hour'])) || !isset(($json_obj['minute']))) {
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid input."
    ));
    exit;
}




if (!isset($json_obj['csrfToken']) || !hash_equals($json_obj['csrfToken'], $json_obj['csrfToken'])) {
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid CSRF token."
    ));
    exit;
}


$stmt = $mysqli->prepare("INSERT INTO events (owner, title, year, month, day, hour, minute, recurringBoolean) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => "Query preparation failed."
    ));
    exit;
}

// Bind parameters and execute query
$stmt->bind_param('isiiiiii', $owner, $title, $year, $month, $day, $hour, $minute, $recurring);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(array(
        "success" => true,
        "message" => "Event added successfully."
    ));
} else {
    echo json_encode(array(
        "success" => false,
        "message" => "Failed to add event."
    ));
}

// Close statement
$stmt->close();
?>