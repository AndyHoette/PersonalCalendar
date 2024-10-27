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
    if (!isset($_POST['token']) || !hash_equals($_POST['token'], $_SESSION['token'])) {
        echo json_encode(array(
            "success" => false,
            "message" => "Invalid CSRF token."
        ));
        exit;
    }

    // Validate that required fields are set and sanitize inputs
    if (!isset($_POST['username'], $_POST['password']) || empty(trim($_POST['username'])) || empty(trim($_POST['password']))) {
        echo json_encode(array(
            "success" => false,
            "message" => "Invalid input."
        ));
        exit;
    }

    // Sanitize inputs
    $usernameAttempt = htmlentities($_POST["username"]);
    $passwordAttempt = htmlentities($_POST["password"]);

    // Check if username is available
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM users WHERE username=?");
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
            "user_id" => $_SESSION['user_id'],
            "token" => $_SESSION['token']
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