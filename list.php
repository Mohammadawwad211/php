<?php
session_start();

// Redirect to login page if not authenticated
if (!isset($_SESSION["authenticated"]) || !$_SESSION["authenticated"]) {
    header("Location: login.php");
    exit;
}

include "mysql_conn.php";
$mysql_obj = new mysql_conn();
$mysql = $mysql_obj->GetConn();
$sql = "SELECT id, name, phone, mailbox FROM users";
$result = $mysql->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lecturer Management</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        td {
            border: 2px solid #000000;
            text-align: center;
            font-weight: bold;
            font-size: 20px;
        }

        th {
            border: 2px solid #000000;
            text-align: center;
            padding: 15px;
            font-weight: bold;
            font-size: 25px;
        }

    </style>
</head>
<body>
<center>
    <a  href="add.php">Add a New Lecturer</a>
</center>
<table>
    <tr>
        <th>Name</th>
        <th>phone</th>
        <th>mailbox</th>
        <th>Actions</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            // Use htmlspecialchars to prevent XSS
            echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["phone"]) . "</td>";
            echo "<td>" . htmlspecialchars($row["mailbox"]) . "</td>";
            echo "<td><a href='edit.php?id=" . $row["id"] . "'>Edit</a>    <a href='delete.php?id=" . $row["id"] . " '>Delete</a></td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5'>No lecturers to display</td></tr>";
    }
    ?>
</table>
</body>
</html>
