<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}
require '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_subdomain_id"])) {
    $subdomain_id = intval($_POST["delete_subdomain_id"]);
    $sql = "DELETE FROM subdomains WHERE id=$subdomain_id";
    if ($conn->query($sql) === TRUE) {
        echo "Subdomain deleted successfully.";
    } else {
        echo "Error deleting subdomain: " . $conn->error;
    }
}

$sql = "SELECT s.id, s.subdomain, u.username FROM subdomains s JOIN users u ON s.user_id = u.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subdomains</title>
</head>
<body>
    <h1>Manage Subdomains</h1>
    <a href="index.php">Back to Dashboard</a>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Subdomain</th>
            <th>User</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row["id"]; ?></td>
            <td><?php echo $row["subdomain"]; ?></td>
            <td><?php echo $row["username"]; ?></td>
            <td>
                <form method="POST" action="manage_subdomains.php">
                    <input type="hidden" name="delete_subdomain_id" value="<?php echo $row["id"]; ?>">
                    <button type="submit">Delete</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
