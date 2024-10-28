<?php
session_start();
require 'database.php';

// Set content type to JSON
header("Content-Type: application/json");
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    echo json_encode(array(
        "success" => false,
        "message" => "User not logged in."
    ));
    exit;
}

// Validate CSRF token
if (!isset($json_obj['csrfToken']) || !hash_equals($json_obj['csrfToken'], $json_obj['csrfToken'])) {
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid CSRF token."
    ));
    exit;
}


$id = $_SESSION["user_id"];
$title = htmlentities($json_obj['title']);
$day = $json_obj['day'];
$hour = $json_obj['hour'];
$minute = $json_obj['minute'];


// Prepare SQL query to fetch events
$stmt = $mysqli->prepare("SELECT id, title, day, hour, minute FROM events WHERE owner = ?");
if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => "Query preparation failed."
    ));
    exit;
}

$stmt->bind_param('isiii', $id, $title, $day, $hour, $minute);
$stmt->execute();
$stmt->bind_result($id, $title, $day, $hour, $minute);

$result = array();
while ($stmt->fetch()) {
    $event = array(
        "id" => $id,
        "title" => htmlentities($title),
        "day" => $day,
        "hour" => $hour,
        "minute" => $minute
    );
    $result[] = $event;
}

$stmt->close();
echo json_encode(array(
    "success" => true,
    "events" => $result
));
?>