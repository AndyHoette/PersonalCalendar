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


if (!isset($json_obj['token'], $json_obj['title'], $json_obj['eventDateTime']) || empty(trim($json_obj['token'])) || empty(trim($json_obj['title'])) || empty(trim($json_obj['eventDateTime']))) {
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid input."
    ));
    exit;
}

// Sanitize inputs
$token = (string) $json_obj['token'];
$title = (string) trim($json_obj['title']);
$eventDateTime = (string) trim($json_obj['eventDateTime']);
$recurring = isset($json_obj['recurring']) ? 1 : 0;

// Validate CSRF token
if ($token !== $_SESSION['token']) {
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid CSRF token."
    ));
    exit;
}

$stmt = $mysqli->prepare("INSERT INTO events (owner, title, eventDateTime, recurring) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => "Query preparation failed."
    ));
    exit;
}

// Bind parameters and execute query
$owner = $json_obj['user_id'];
$stmt->bind_param('sss', $owner, $title, $eventDateTime);
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