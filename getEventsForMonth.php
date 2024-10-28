<?php
session_start();
require 'database.php';

// Set content type to JSON
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
if (!isset($json_obj['token']) || !hash_equals($json_obj['token'], $json_obj['token'])) {
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid CSRF token."
    ));
    exit;
}

// Validate and sanitize inputs
if (!isset($json_obj['monthIndex'], $json_obj['yearIndex'])) {
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid input."
    ));
    exit;
}

$monthIndex = $json_obj['monthIndex'];
$yearIndex = $json_obj['yearIndex'];
$user_id = $json_obj['user_id'];

// Prepare SQL query to fetch events
$stmt = $mysqli->prepare("SELECT id, title, eventDateTime FROM events WHERE owner = ? AND MONTH(eventDateTime) = ? AND YEAR(eventDateTime) = ?");
if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => "Query preparation failed."
    ));
    exit;
}

$stmt->bind_param('iii', $user_id, $monthIndex, $yearIndex);
$stmt->execute();
$stmt->bind_result($id, $title, $eventDateTime);

$result = array();
while ($stmt->fetch()) {
    $event = array(
        "id" => htmlentities($id),
        "title" => htmlentities($title),
        "eventDateTime" => htmlentities($eventDateTime)
    );
    array_push($result, $event);
}

$stmt->close();
echo json_encode(array(
    "success" => true,
    "events" => $result
));
?>