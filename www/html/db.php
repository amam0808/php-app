<?php
// DB接続用
$servername = "db";
$username = "appuser";
$password = "apppass";
$dbname = "appdb";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("DB接続失敗: " . $conn->connect_error);
}
