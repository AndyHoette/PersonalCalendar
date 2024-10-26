<?php
session_start();

if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

function verifyCSRFToken($token)
{
    return isset($token) && hash_equals($token, $_SESSION['token']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    if (!verifyCSRFToken($_POST['token'])) {
        header("Location: unauthorized.php");
        exit();
    }

    $username = htmlspecialchars($_POST['user']);  // Prevent XSS by sanitizing input
    $password = $_POST['password'];


    $_SESSION['loggedin'] = true;
    header("Location: index.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addEvent'])) {
    if (!verifyCSRFToken($_POST['token'])) {
        header("Location: unauthorized.php");
        exit();
    }

    $title = htmlspecialchars($_POST['title']);
    $eventDate = $_POST['EventDatetime'];

    echo "Event added successfully: $title on $eventDate";
}
?>