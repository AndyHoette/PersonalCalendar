<?php
    session_start();
    require 'database.php';

    // Set content type to JSON
    header("Content-Type: application/json");

    // Validate CSRF token

    // Validate that required fields are set and sanitize inputs
    if (!isset($json_obj['password']) || empty(trim($json_obj['password']))) {
        echo json_encode(array(
            "success" => false,
            "message" => "Invalid input."
        ));
        exit;
    }

    // Sanitize inputs
    $passwordAttempt = htmlentities($json_obj["password"]);

    // Check if username is available

    // If username is available, create new user
        $passwordHash = password_hash($passwordAttempt, PASSWORD_BCRYPT);

        $stmt2 = $mysqli->prepare("INSERT INTO users (password) VALUES (?)");
        if (!$stmt2) {
            echo json_encode(array(
                "success" => false,
                "message" => "Query preparation failed."
            ));
            exit;
        }

        $stmt2->bind_param('s', $passwordHash);
        $stmt2->execute();
        $stmt2->close();

        // Login the user after successful creation
        $_SESSION['user_id'] = $mysqli->insert_id;
        $_SESSION['token'] = bin2hex(random_bytes(32));

        echo json_encode(array(
            "success" => true,
            "message" => "Account created successfully.",
            "user_id" => $_SESSION['user_id'],
            "token" => $_SESSION['token']
        ));
        exit;
    ?>