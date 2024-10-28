<?php
session_start();
require 'database.php'; // Ensure this connects to your database
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str, true);
// Set content type to JSON
header("Content-Type: application/json");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
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

// The new stuff
$id = $_SESSION['user_id'];
$title = htmlentities($json_obj['title']);
$year = $json_obj['year'];
$month = $json_obj['month'];
$day = $json_obj['day'];
$hour = $json_obj['hour'];
$minute = $json_obj['minute'];
$owner = $json_obj['owner'];


// Check if the event belongs to the logged-in user
$stmt = $mysqli->prepare("SELECT owner FROM events WHERE id = ?");

$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($ownerID);
$stmt->fetch();
$stmt->close();

// Verify ownership
if ($ownerID == $id) {
    // Update the event
    $stmt = $mysqli->prepare("UPDATE events SET title = ?, year = ?, month = ?, day = ?, hour = ?, minute = ? WHERE id = ?");
    $stmt->bind_param("siiiii", $title, $year, $month, $day, $hour, $minute);
    $stmt->execute();
    $stmt->close();
}
echo json_encode(array(
    "success" => true,
));
exit;
?>