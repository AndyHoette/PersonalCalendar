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



echo json_encode(array(
    "success" => false,
    "message" => "Here not work"
));
exit;

if (!isset($json_obj['csrfToken']) || empty(trim($json_obj['csrfToken'])) || empty(trim($json_obj['title'])) || empty(trim($json_obj['year'])) || empty(trim($json_obj['month'])) || empty(trim($json_obj['day'])) || empty(trim($json_obj['hour'])) || empty(trim($json_obj['minute']))) {
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

$owner = $_SESSION['user_id'];
$title = $json_obj['title'];
$year = $json_obj['year'];
$month = $json_obj['month'];
$day = $json_obj['day'];
$hour = $json_obj['hour'];
$minute = $json_obj['minute'];

//not sure about recurring
$recurring = isset($json_obj['recurring']) ? 1 : 0;



echo json_encode(array(
    "success" => false,
    "id" => $owner,
    "title" => $title,
    "year" => $year,
    "month" => $month,
    "day" => $day,
    "hour" => $hour,
    "minute" => $minute,
    "recurringBoolean" => $recurring
));
exit;

$stmt = $mysqli->prepare("INSERT INTO events (id, title, year, month, day, hour, minute, recurringBoolean) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
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