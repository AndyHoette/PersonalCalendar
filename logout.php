<?php
header("Content-Type: application/json");
session_start(); //just makes sure a session is open, unsets everything, and destroys it
session_unset();
session_destroy();
echo json_encode(array(
    "success" => true,
));
?>