<!DOCTYPE html>
<html lang="en">

<head>
    <title>Processing Account Creation</title>
</head>

<body>
    <?php
    session_start();
    require 'database.php';

    // Set content type to JSON
    header("Content-Type: application/json");

    // Validate CSRF token
    if (!isset($json_obj['csrfToken']) || !hash_equals($json_obj['csrfToken'], $json_obj['csrfToken'])) {
        echo json_encode(array(
            "success" => false,
            "message" => "Invalid CSRF token."
        ));
        exit;
    }

    // Validate that required fields are set and sanitize inputs
    if (!isset($json_obj['user_id'], $json_obj['password']) || empty(trim($json_obj['user_id'])) || empty(trim($json_obj['password']))) {
        echo json_encode(array(
            "success" => false,
            "message" => "Invalid input."
        ));
        exit;
    }

    // Sanitize inputs
    $usernameAttempt = htmlentities($json_obj["username"]);
    $passwordAttempt = htmlentities($json_obj["password"]);

    // Check if username is available
    $stmt = $mysqli->prepare("SELECT users WHERE username=?");
    if (!$stmt) {
        echo json_encode(array(
            "success" => false,
            "message" => "Query preparation failed."
        ));
        exit;
    }

    // Bind parameter and execute query
    $stmt->bind_param('s', $usernameAttempt);
    $stmt->execute();
    $stmt->bind_result($cnt);
    $stmt->fetch();
    $stmt->close();

    // If username is available, create new user
    if ($cnt == 0) {
        $passwordHash = password_hash($passwordAttempt, PASSWORD_BCRYPT);

        $stmt2 = $mysqli->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        if (!$stmt2) {
            echo json_encode(array(
                "success" => false,
                "message" => "Query preparation failed."
            ));
            exit;
        }

        $stmt2->bind_param('ss', $usernameAttempt, $passwordHash);
        $stmt2->execute();
        $stmt2->close();

        // Login the user after successful creation
        $_SESSION['username'] = $usernameAttempt;
        $_SESSION['user_id'] = $mysqli->insert_id;
        $_SESSION['token'] = bin2hex(random_bytes(32));

        echo json_encode(array(
            "success" => true,
            "message" => "Account created successfully.",
            "user_id" => 'user_id',
            "token" => 'token'
        ));
    } else {
        // Username is already taken
        echo json_encode(array(
            "success" => false,
            "message" => "Username already taken."
        ));
    }
    ?>
</body>

</html>