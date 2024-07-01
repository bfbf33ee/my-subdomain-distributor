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

    // 获取 Cloudflare 配置
    $sql = "SELECT root_domain, server_ip, api_token, email, zone_id, account_id FROM cloudflare_config WHERE id=1";
    $result = $conn->query($sql);
    $config = $result->fetch_assoc();

    if (!$config) {
        die("Cloudflare configuration not found in the database.");
    }

    $root_domain = $config["root_domain"];
    $server_ip = $config["server_ip"];
    $api_token = $config["api_token"];
    $email = $config["email"];
    $zone_id = $config["zone_id"];
    $account_id = $config["account_id"];

    // 验证子域名格式
    if (!preg_match('/^[a-zA-Z0-9-]+$/', $subdomain)) {
        $error = "子域名格式无效。";
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
                             "Authorization: Bearer $api_token\r\n",
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
                $success = "子域名 $subdomain.$root_domain 已成功注册！";
            } else {
                $error = "错误: " . $sql . "<br>" . $conn->error;
            }
        } else {
            $error_message = $response["errors"][0]["message"];
            $error = "子域名注册失败: $error_message";
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注册子域名</title>
</head>
<body>
    <h1>注册子域名</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if (isset($success)): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>
    <form action="register_subdomain.php" method="POST">
        <label for="subdomain">子域名:</label>
        <input type="text" id="subdomain" name="subdomain" required>
        <br>
        <button type="submit">注册</button>
    </form>
    <a href="logout.php">登出</a>
</body>
</html>
