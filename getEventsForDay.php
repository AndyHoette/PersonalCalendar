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
if (!isset($_POST['token']) || !hash_equals($_POST['token'], $_SESSION['token'])) {
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid CSRF token."
    ));
    exit;
}


$monthIndex = (int) $_POST['monthIndex'];
$dayIndex = (int) $_POST['dayIndex'];
$yearIndex = (int) $_POST['yearIndex'];
$user_id = $json_obj['user_id'];

// Prepare SQL query to fetch events
$stmt = $mysqli->prepare("SELECT id, title, eventDateTime FROM events WHERE owner = ? AND MONTH(eventDateTime) = ? AND DAY(eventDateTime) = ? AND YEAR(eventDateTime) = ?");
if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => "Query preparation failed."
    ));
    exit;
}

$stmt->bind_param('iiii', $user_id, $monthIndex, $dayIndex, $yearIndex);
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