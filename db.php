<?php
$servername = "mysql.sqlpub.com";
$username = "j2jyunming";
$password = "j2jyunming";
$dbname = "be5ypTUezplhVoVV";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
