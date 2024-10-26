<?php

$mysqli = new mysqli('localhost', 'viewer', 'easypassword', 'leeHoetteCal');

if ($mysqli->connect_errno) {
    printf("Connection Failed: %s\n", $mysqli->connect_error);
    exit;
}
?>