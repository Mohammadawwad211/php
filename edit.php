<?php
session_start();

// Check authentication
if (!isset($_SESSION["authenticated"]) || !$_SESSION["authenticated"]) {
    header("Location: login.php");
    exit;
}

include "mysql_conn.php";
$mysql_obj = new mysql_conn();
$mysql = $mysql_obj->GetConn();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Use prepared statements to prevent SQL injection
    $id = $_POST["id"];
    $name = $_POST["name"];
    $phone = $_POST["phone"];
    $mailbox = $_POST["mailbox"];

    $stmt = $mysql->prepare("UPDATE users SET name = ?, phone = ?, mailbox = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $phone, $mailbox, $id);

    if ($stmt->execute()) {
        header("Location: list.php");
        exit;
    } else {
        echo "Error updating record: " . $stmt->error;
    }
    $stmt->close();
}

$id = $_GET["id"];

// Use prepared statement to prevent SQL injection
$stmt = $mysql->prepare("SELECT id, name, phone, mailbox FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
} else {
    echo "Lecturer not found";
    exit;
}

$stmt->close();
$mysql->close();
?>

<!DOCTYPE html>
<html>
<body>
<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">
    <label>Edit Name: <input type="text" name="name" value="<?php echo htmlspecialchars($row["name"]); ?>" required></label><br>
    <label>Edit Phone: <input type="number" name="phone" value="<?php echo htmlspecialchars($row["phone"]); ?>" required></label><br>
    <label>Edit Mailbox: <input type="text" name="mailbox" value="<?php echo htmlspecialchars($row["mailbox"]); ?>"></label><br><br>
    <input type="submit" value="Edit Lecturer">
</form>
</body>
</html>
