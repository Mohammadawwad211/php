<?php
session_start();

// Check authentication
if (!isset($_SESSION["authenticated"]) || !$_SESSION["authenticated"]) {
    header("Location: login.php");
    exit;
}

// Generate and validate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = uniqid("",true); // Generate a random token
}

include "mysql_conn.php";
$mysql_obj = new mysql_conn();
$mysql = $mysql_obj->GetConn();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF Token Validation Failed");
    }

    // Use prepared statements to prevent SQL injection
    $name = $_POST["name"];
    $phone = $_POST["phone"];
    $mailbox = $_POST["mailbox"];

    $stmt = $mysql->prepare("INSERT INTO users (name, phone, mailbox) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $phone, $mailbox);

    if ($stmt->execute()) {
        header("Location: list.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

$mysql->close();
?>
<!DOCTYPE html>
<html>
<body>
<h2>Add a New Lecturer</h2>
<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <label>Name: <input type="text" name="name" required></label><br>
    <label>Phone: <input type="text" name="phone" required></label><br>
    <label>Mailbox: <input type="number" name="mailbox" required></label><br><br>
    <input type="submit" value="Add Lecturer">
</form>
</body>
</html>
