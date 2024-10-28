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

// Sanitize inputs
$eventId = (int) $_POST['eventId'];
$owner = $_SESSION['user_id'];

// Check if the event belongs to the user
$stmt = $mysqli->prepare("SELECT owner FROM events WHERE id = ?");
if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => "Query preparation failed."
    ));
    exit;
}

$stmt->bind_param("i", $eventId);
$stmt->execute();
$stmt->bind_result($eventOwner);
$stmt->fetch();
$stmt->close();

// If the event owner matches the logged-in user, delete the event
if ($eventOwner === $owner) {
    $stmt = $mysqli->prepare("DELETE FROM events WHERE id = ?");
    if (!$stmt) {
        echo json_encode(array(
            "success" => false,
            "message" => "Query preparation failed."
        ));
        exit;
    }

    $stmt->bind_param("i", $eventId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo json_encode(array(
            "success" => true,
            "message" => "Event deleted successfully."
        ));
    } else {
        echo json_encode(array(
            "success" => false,
            "message" => "Failed to delete event or event not found."
        ));
    }

    // Close statement
    $stmt->close();
} else {
    // User is not authorized to delete this event
    echo json_encode(array(
        "success" => false,
        "message" => "Unauthorized to delete this event."
    ));
}
?>