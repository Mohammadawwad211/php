<?php
session_start();
$max_attempts = 3;
$block_duration = 15;

// Function to check if the user is blocked
function isUserBlocked() {
    if (isset($_SESSION['block_start_time']) && time() - $_SESSION['block_start_time'] < $GLOBALS['block_duration']) {
        return true;
    }
    return false;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is blocked
    if (isUserBlocked()) {
        $remaining_time = $GLOBALS['block_duration'] - (time() - $_SESSION['block_start_time']);
        $error_message = "Too many login attempts. Please try again in " . $remaining_time . " seconds.";
    } else {
        $password = $_POST["password"];
        $correct_password = "AAA";

        if ($password === $correct_password) {
            unset($_SESSION['login_attempts']);
            unset($_SESSION['block_start_time']);
            $_SESSION["authenticated"] = true;
            header("Location: list.php");
            exit;
        } else {
            if (!isset($_SESSION['login_attempts'])) {
                $_SESSION['login_attempts'] = 1;
            } else {
                $_SESSION['login_attempts']++;
                if ($_SESSION['login_attempts'] >= $max_attempts) {
                    $_SESSION['block_start_time'] = time();
                    $error_message = "Too many login attempts. Please try again in $block_duration seconds.";
                }
            }
            $error_message = "Incorrect password";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>
<center>
    <!-- XSS secure -->
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="password">Password:
            <input type="password" name="password" required></label>
        <br>
        <input type="submit" value="Login">

    </form>
    <?php
    if (isset($error_message)) {
        echo "<p class='error-message'>$error_message</p>";
    }
    ?>
</center>
</body>
</html>
