<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}
require '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $smtp_host = $conn->real_escape_string($_POST["smtp_host"]);
    $smtp_port = intval($_POST["smtp_port"]);
    $smtp_user = $conn->real_escape_string($_POST["smtp_user"]);
    $smtp_password = $conn->real_escape_string($_POST["smtp_password"]);
    $smtp_from_email = $conn->real_escape_string($_POST["smtp_from_email"]);
    $smtp_from_name = $conn->real_escape_string($_POST["smtp_from_name"]);

    $sql = "UPDATE email_config SET smtp_host='$smtp_host', smtp_port=$smtp_port, smtp_user='$smtp_user', smtp_password='$smtp_password', smtp_from_email='$smtp_from_email', smtp_from_name='$smtp_from_name' WHERE id=1";

    if ($conn->query($sql) === TRUE) {
        echo "Email settings updated successfully.";
    } else {
        echo "Error updating email settings: " . $conn->error;
    }
}

// Fetch current email settings
$sql = "SELECT smtp_host, smtp_port, smtp_user, smtp_password, smtp_from_email, smtp_from_name FROM email_config WHERE id=1";
$result = $conn->query($sql);
$email_config = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Settings</title>
</head>
<body>
    <h1>Email Settings</h1>
    <a href="index.php">Back to Dashboard</a>
    <form action="email_settings.php" method="POST">
        <label for="smtp_host">SMTP Host:</label>
        <input type="text" id="smtp_host" name="smtp_host" value="<?php echo $email_config['smtp_host']; ?>" required>
        <br>
        <label for="smtp_port">SMTP Port:</label>
        <input type="number" id="smtp_port" name="smtp_port" value="<?php echo $email_config['smtp_port']; ?>" required>
        <br>
        <label for="smtp_user">SMTP User:</label>
        <input type="text" id="smtp_user" name="smtp_user" value="<?php echo $email_config['smtp_user']; ?>" required>
        <br>
        <label for="smtp_password">SMTP Password:</label>
        <input type="password" id="smtp_password" name="smtp_password" value="<?php echo $email_config['smtp_password']; ?>" required>
        <br>
        <label for="smtp_from_email">From Email:</label>
        <input type="email" id="smtp_from_email" name="smtp_from_email" value="<?php echo $email_config['smtp_from_email']; ?>" required>
        <br>
        <label for="smtp_from_name">From Name:</label>
        <input type="text" id="smtp_from_name" name="smtp_from_name" value="<?php echo $email_config['smtp_from_name']; ?>" required>
        <br>
        <button type="submit">Save Settings</button>
    </form>
</body>
</html>

