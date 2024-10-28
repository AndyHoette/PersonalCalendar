<?php
session_start();
require 'database.php'; // Ensure this connects to your database

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

// Validate CSRF token
if (!isset($_POST['token']) || !hash_equals($_POST['token'], $_SESSION['token'])) {
    echo json_encode(array(
        "success" => false,
        "message" => "Invalid CSRF token."
    ));
    exit;
}

// Sanitize input
$eventID = htmlentities($_POST['eventID']);
$newTitle = htmlentities($_POST['newTitle']);
$newDatetime = htmlentities($_POST['newDatetime']);
$userID = $json_obj['user_id']; // Assumes you have stored user ID in the session upon login

// Check if the event belongs to the logged-in user
$stmt = $mysqli->prepare("SELECT owner FROM events WHERE id = ?");
$stmt->bind_param("i", $eventID);
$stmt->execute();
$stmt->bind_result($ownerID);
$stmt->fetch();
$stmt->close();

// Verify ownership
if ($ownerID == $userID) {
    // Update the event
    $stmt = $mysqli->prepare("UPDATE events SET title = ?, eventDatetime = ? WHERE id = ?");
    $stmt->bind_param("ssi", $newTitle, $newDatetime, $eventID);
    $stmt->execute();
    $stmt->close();
} else {
    header("Location: unauthorized.php");
    exit;
}

exit;
?>