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
if (!isset($json_obj['eventID'], $json_obj['userID']) || !is_numeric($json_obj['eventID']) || empty(trim($json_obj['userID']))) {
    exit;
}

// newOwner needed??
$eventID = $json_obj['eventID'];
$newOwner = $json_obj['newOwner'];

$title = $json_obj['title'];
$year = $json_obj['year'];
$month = $json_obj['month'];
$day = $json_obj['day'];
$hour = $json_obj['hour'];
$minute = $json_obj['minute'];

// Fetch event details

$stmt = $mysqli->prepare("SELECT title, year, month, day, hour, minute, recurring FROM events WHERE id = ?");
if (!$stmt) {
    exit;
}

$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->bind_result($id, $title, $year, $month, $day, $hour, $minute, $recurring);
$stmt->fetch();
$stmt->close();



// Insert duplicated event for the new owner
$stmt2 = $mysqli->prepare("INSERT INTO events (owner, title, year, month, day, hour, minute, recurring) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
if (!$stmt2) {
    echo json_encode(array(
        "success" => false,
        "message" => "Incorrect Username"
    ));
    exit;
}

$stmt2->bind_param('isiiiiii', $newOwner, $title, $year, $month, $day, $hour, $minute, $recurring);
$stmt2->execute();
$stmt2->close();
?>