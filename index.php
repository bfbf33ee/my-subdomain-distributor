<?php
session_start();
require 'db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subdomain = $conn->real_escape_string($_POST["subdomain"]);
    $user_id = $_SESSION["user_id"];

    $root_domain = "yourdomain.com";
    $server_ip = "your_server_ip";
    $api_key = "your_cloudflare_api_key";
    $email = "your_email@example.com";
    $zone_id = "your_zone_id";

    // Check if subdomain is valid
    if (!preg_match('/^[a-zA-Z0-9-]+$/', $subdomain)) {
        $error = "Invalid subdomain format.";
    } else {
        $url = "https://api.cloudflare.com/client/v4/zones/$zone_id/dns_records";

        $data = [
            "type" => "A",
            "name" => "$subdomain.$root_domain",
            "content" => $server_ip,
            "ttl" => 3600
        ];

        $options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n" .
                             "X-Auth-Email: $email\r\n" .
                             "X-Auth-Key: $api_key\r\n",
                'method'  => 'POST',
                'content' => json_encode($data)
            ]
        ];

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $response = json_decode($result, true);

        if ($response["success"]) {
            $sql = "INSERT INTO subdomains (user_id, subdomain) VALUES ($user_id, '$subdomain')";
            if ($conn->query($sql) === TRUE) {
                $success = "Subdomain $subdomain.$root_domain has been registered successfully!";
            } else {
                $error = "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            $error_message = $response["errors"][0]["message"];
            $error = "Failed to register subdomain: $error_message";
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
    <title>Register Subdomain</title>
</head>
<body>
    <h1>Register Subdomain</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>
    <form action="index.php" method="POST">
        <label for="subdomain">Subdomain:</label>
        <input type="text" id="subdomain" name="subdomain" required>
        <br>
        <button type="submit">Register</button>
    </form>
    <a href="logout.php">Logout</a>
</body>
</html>
