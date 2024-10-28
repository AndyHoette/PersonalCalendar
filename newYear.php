<?php
session_start();
require 'database.php';

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

// Validate and sanitize inputs
if (!isset($json_obj['newYear']) || !is_numeric($json_obj['newYear'])) {
    exit; // Invalid input, terminate script
}

$newYear = (int) $json_obj['newYear'];
$user_id = $json_obj['userID'];

// Get all recurring events for the logged-in user
$stmt = $mysqli->prepare("SELECT id, title, month, day, hour, minute FROM events WHERE owner = ? AND recurring = 1");
if (!$stmt) {
    exit;
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($id, $title, $month, $day, $hour, $minute);

$events = array();
while ($stmt->fetch()) {
    $events[] = array(
        "id" => $id,
        "title" => htmlentities($title),
        "month" => $month,
        "day" => $day,
        "hour" => $hour,
        "minute" => $minute
    );
}
$stmt->close();

// Generate new events for the specified year
foreach ($events as $event) {
    $stmt2 = $mysqli->prepare("INSERT INTO events (owner, title, year, month, day, hour, minute, recurring) VALUES (?, ?, ?, ?, ? ,?, ?, 0)");
    if (!$stmt2) {
        continue;
    }

    $stmt2->bind_param('isiiiii', $user_id, $event['title'], $newYear, $event['month'], $event['day'], $event['hour'], $event['minute']);
    $stmt2->execute();
    $stmt2->close();
}

?>