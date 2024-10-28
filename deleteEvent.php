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


$eventId = $json_obj['eventId'];
$owner = $json_obj['userID'];

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