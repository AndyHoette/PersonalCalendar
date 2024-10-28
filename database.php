<?php
header("Content-Type: application/json");
$mysqli = new mysqli('localhost', 'test', 'password', 'leeHoetteCal');

if ($mysqli->connect_errno) {
    printf("Connection Failed: %s\n", $mysqli->connect_error);
    echo json_encode(array(
        "success" => false,
        "message" => "Couldn't connect to Database"
    ));
    exit;
}
?>