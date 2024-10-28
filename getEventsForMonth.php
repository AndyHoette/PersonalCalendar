<?php
session_start();
require 'database.php';

// Set content type to JSON
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
if (!isset($_POST['monthIndex'], $_POST['yearIndex'])) {
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid input."
    ));
    exit;
}

$monthIndex = (int) $_POST['monthIndex'];
$yearIndex = (int) $_POST['yearIndex'];
$user_id = $_SESSION['user_id'];

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