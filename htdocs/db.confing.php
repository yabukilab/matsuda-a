<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "study";

// データベース接続を作成
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続をチェック
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
