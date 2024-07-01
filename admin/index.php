<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <a href="logout.php">Logout</a>
    <h2>Manage</h2>
    <ul>
        <li><a href="manage_users.php">Manage Users</a></li>
        <li><a href="manage_subdomains.php">Manage Subdomains</a></li>
        <li><a href="email_settings.php">Email Settings</a></li>
    </ul>
</body>
</html>
