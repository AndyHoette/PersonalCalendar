<?php

$mysqli = new mysqli('localhost', 'test', 'password', 'leeHoetteCal');

if ($mysqli->connect_errno) {
    printf("Connection Failed: %s\n", $mysqli->connect_error);
    exit;
}
?>