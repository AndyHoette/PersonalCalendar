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


if (!isset($_POST['token'], $_POST['title'], $_POST['eventDateTime']) || empty(trim($_POST['token'])) || empty(trim($_POST['title'])) || empty(trim($_POST['eventDateTime']))) {
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid input."
    ));
    exit;
}

// Sanitize inputs
$token = (string) $_POST['token'];
$title = (string) trim($_POST['title']);
$eventDateTime = (string) trim($_POST['eventDateTime']);
$recurring = isset($Post['recurring']) ? 1 : 0;

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
$owner = $_SESSION['user_id'];
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