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
$title = htmlentities($json_obj['title']);
$month = $json_obj['month'];
$day = $json_obj['day'];
$minute = $json_obj['minute'];


// Prepare SQL query to fetch events
$stmt = $mysqli->prepare("SELECT id, title, month, day, minute FROM events WHERE owner = ?");
if (!$stmt) {
    echo json_encode(array(
        "success" => false,
        "message" => "Query preparation failed."
    ));
    exit;
}

$stmt->bind_param('isii', $id, $title, $month, $day, $minute);
$stmt->execute();
$stmt->bind_result($id, $title, $month, $day, $minute);

$result = array();
while ($stmt->fetch()) {
    $event = array(
        "id" => $id,
        "title" => htmlentities($title),
        "month" => $month,
        "day" => $day,
        "minute" => $minute
    );
    array_push($result, $event);
}


$stmt->close();
echo json_encode(array(
    "success" => true,
    "events" => $result
));
?>