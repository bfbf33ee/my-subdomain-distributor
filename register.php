<?php
require 'db.php';
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Assuming you have installed PHPMailer using Composer

function sendVerificationEmail($email, $code) {
    global $conn;

    $sql = "SELECT * FROM email_config WHERE id=1";
    $result = $conn->query($sql);
    $config = $result->fetch_assoc();

    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = $config['smtp_host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['smtp_user'];
        $mail->Password = $config['smtp_password'];
        $mail->SMTPSecure = 'tls';
        $mail->Port = $config['smtp_port'];

        // Recipients
        $mail->setFrom($config['smtp_from_email'], $config['smtp_from_name']);
        $mail->addAddress($email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification';
        $mail->Body = "Your verification code is: $code";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST["username"]);
    $email = $conn->real_escape_string($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Input validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Check if email or username already exists
        $sql = "SELECT id FROM users WHERE email='$email' OR username='$username'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $error = "Username or email already exists";
        } else {
            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $verification_code = rand(100000, 999999); // Generate a 6-digit verification code
            $sql = "INSERT INTO users (username, email, password, verification_code) VALUES ('$username', '$email', '$hashed_password', '$verification_code')";

            if ($conn->query($sql) === TRUE) {
                if (sendVerificationEmail($email, $verification_code)) {
                    $_SESSION['email'] = $email;
                    header("Location: verify.php");
                    exit;
                } else {
                    $error = "Failed to send verification email";
                }
            } else {
                $error = "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h1>Register</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="register.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <br>
        <button type="submit">Register</button>
    </form>
</body>
</html>
