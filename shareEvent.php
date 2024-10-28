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
if (!isset($_POST['eventID'], $_POST['newOwner']) || !is_numeric($_POST['eventID']) || empty(trim($_POST['newOwner']))) {
    exit;
}

$eventID = (int) $_POST['eventID'];
$newOwner = htmlentities($_POST['newOwner']);

// Fetch event details
$stmt = $mysqli->prepare("SELECT title, eventDateTime FROM events WHERE id = ?");
if (!$stmt) {
    exit;
}

$stmt->bind_param('i', $eventID);
$stmt->execute();
$stmt->bind_result($title, $eventDateTime);
$stmt->fetch();
$stmt->close();

if (empty($title) || empty($eventDateTime)) {
    exit;
}

// Insert duplicated event for the new owner
$stmt2 = $mysqli->prepare("INSERT INTO events (owner, title, eventDateTime) VALUES (?, ?, ?)");
if (!$stmt2) {
    exit;
}

$stmt2->bind_param('sss', $newOwner, $title, $eventDateTime);
$stmt2->execute();
$stmt2->close();
?>