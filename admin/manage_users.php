<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}
require '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_user_id"])) {
    $user_id = intval($_POST["delete_user_id"]);
    $sql = "DELETE FROM users WHERE id=$user_id";
    if ($conn->query($sql) === TRUE) {
        echo "User deleted successfully.";
    } else {
        echo "Error deleting user: " . $conn->error;
    }
}

$sql = "SELECT id, username, email, is_verified FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
</head>
<body>
    <h1>Manage Users</h1>
    <a href="index.php">Back to Dashboard</a>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Verified</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row["id"]; ?></td>
            <td><?php echo $row["username"]; ?></td>
            <td><?php echo $row["email"]; ?></td>
            <td><?php echo $row["is_verified"] ? "Yes" : "No"; ?></td>
            <td>
                <form method="POST" action="manage_users.php">
                    <input type="hidden" name="delete_user_id" value="<?php echo $row["id"]; ?>">
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
