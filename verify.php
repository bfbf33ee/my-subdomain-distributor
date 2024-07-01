<?php
require 'db.php';
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: register.php");
    exit;
}

$email = $_SESSION['email'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = $conn->real_escape_string($_POST["code"]);

    $sql = "SELECT verification_code FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row["verification_code"] == $code) {
            $sql = "UPDATE users SET is_verified=1 WHERE email='$email'";
            if ($conn->query($sql) === TRUE) {
                header("Location: login.php");
                exit;
            } else {
                $error = "Error updating record: " . $conn->error;
            }
        } else {
            $error = "Invalid verification code.";
        }
    } else {
        $error = "No user found with that email.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
</head>
<body>
    <h1>Email Verification</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="verify.php" method="POST">
        <label for="code">Verification Code:</label>
        <input type="text" id="code" name="code" required>
        <br>
        <button type="submit">Verify</button>
    </form>
</body>
</html>
