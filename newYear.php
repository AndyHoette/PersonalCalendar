<?php
session_start();
require 'database.php';

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

// Validate and sanitize inputs
if (!isset($_POST['newYear']) || !is_numeric($_POST['newYear'])) {
    exit; // Invalid input, terminate script
}

$newYear = (int) $_POST['newYear'];
$user_id = $json_obj['user_id'];

// Get all recurring events for the logged-in user
$stmt = $mysqli->prepare("SELECT id, title, eventDateTime FROM events WHERE owner = ? AND recurring = 1");
if (!$stmt) {
    exit;
}

$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($id, $title, $eventDateTime);

$events = array();
while ($stmt->fetch()) {
    $events[] = array(
        "id" => $id,
        "title" => $title,
        "eventDateTime" => $eventDateTime
    );
}
$stmt->close();

// Generate new events for the specified year
foreach ($events as $event) {
    $eventDateTime = new DateTime($event['eventDateTime']);
    $eventDateTime->setDate($newYear, $eventDateTime->format('m'), $eventDateTime->format('d'));

    $newEventDateTime = $eventDateTime->format('Y-m-d H:i:s');
    $stmt2 = $mysqli->prepare("INSERT INTO events (owner, title, eventDateTime, recurring) VALUES (?, ?, ?, 0)");
    if (!$stmt2) {
        continue;
    }

    $stmt2->bind_param('iss', $user_id, $event['title'], $newEventDateTime);
    $stmt2->execute();
    $stmt2->close();
}

?>