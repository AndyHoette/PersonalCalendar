<?php
require 'database.php';
session_start();

// Set content type to JSON
header("Content-Type: application/json");
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


$id = $_SESSION["user_id"];
$month = $json_obj['monthIndex'];
$year = $json_obj['yearIndex'];


// Prepare SQL query to fetch events
$stmt = $mysqli->prepare("SELECT day FROM events WHERE owner = ? and WHERE year = ? and WHERE month = ?");
if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => "Query preparation failed."
    ));
    exit;
}

$stmt->bind_param('iii', $id, $year, $month);
$stmt->execute();
$stmt->bind_result($day);

$result = array();
while ($stmt->fetch()) {
    if(!in_array($day, $result)) {
        array_push($result, $day);
    }
}


$stmt->close();
echo json_encode(array(
    "success" => true,
    "events" => $result
));
?>