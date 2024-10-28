<?php
session_start();
require 'database.php';

// Set content type to JSON
header("Content-Type: application/json");

// Check if user is logged in
if (empty($json_obj["userID"])) {
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


$id = $json_obj['userID'];
$title = htmlentities($title);
$day = $json_obj['day'];
$minute = $json_obj['minute'];


// Prepare SQL query to fetch events
$stmt = $mysqli->prepare("SELECT id, title, day, minute FROM events WHERE owner = ?");
if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => "Query preparation failed."
    ));
    exit;
}

$stmt->bind_param('isii', $id, $title, $day, $minute);
$stmt->execute();
$stmt->bind_result($id, $title, $day, $minute);

$result = array();
while ($stmt->fetch()) {
    $event = array(
        "id" => $id,
        "title" => htmlentities($title),
        "day" => $day,
        "minute" => $minute
    );
    array_push($result, $event);
}

$stmt->close();
echo json_encode(array(
    "success" => true,
    "events" => $result
));
?>