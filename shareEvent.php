<?php
session_start();
require 'database.php';
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
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
if (!isset($json_obj['eventID'], $_SESSION['user_id']) || !is_numeric($json_obj['eventID']) || empty(trim($_SESSION['user_id']))) {
    exit;
}

// newOwner needed??
$eventID = $json_obj['eventID'];
$newOwner = $json_obj['newOwner'];

// Fetch event details

$stmt = $mysqli->prepare("SELECT title, year, month, day, hour, minute FROM events WHERE id = ?");
if (!$stmt) {
    exit;
}

$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result( $title, $year, $month, $day, $hour, $minute);
$stmt->fetch();
$stmt->close();



// Insert duplicated event for the new owner
$stmt2 = $mysqli->prepare("INSERT INTO events (owner, title, year, month, day, hour, minute, recurring) VALUES (?, ?, ?, ?, ?, ?, ?, false)");
if (!$stmt2) {
    echo json_encode(array(
        "success" => false,
        "message" => "Incorrect Username"
    ));
    exit;
}

$stmt2->bind_param('isiiiii', $newOwner, $title, $year, $month, $day, $hour, $minute);
$stmt2->execute();
$stmt2->close();
echo json_encode(array(
    "success" => true,
));

?>