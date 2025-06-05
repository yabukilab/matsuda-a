<?php
function h($var) {
    if (is_array($var)) {
        return array_map('h', $var);
    } else {
        return htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
    }
}

// SQLで作成したデータベース設定を使用
$dbServer = 'localhost';  // 常にローカルホスト
$dbUser   = 'testuser';   // SQLで作成したユーザー名
$dbPass   = 'pass';       // SQLで設定したパスワード
$dbName   = 'mydb';       // SQLで作成したデータベース名

// 環境変数で上書き可能な設定（必要に応じて）
if (isset($_ENV['MYSQL_SERVER']))    $dbServer = $_ENV['MYSQL_SERVER'];
if (isset($_SERVER['MYSQL_USER']))   $dbUser   = $_SERVER['MYSQL_USER'];
if (isset($_SERVER['MYSQL_PASSWORD'])) $dbPass = $_SERVER['MYSQL_PASSWORD'];
if (isset($_SERVER['MYSQL_DB']))     $dbName   = $_SERVER['MYSQL_DB'];

// 定数設定（SQLの設定に合わせる）
define('DB_HOST', $dbServer);
define('DB_USER', $dbUser);
define('DB_PASS', $dbPass);
define('DB_NAME', $dbName);
define('MAX_FILE_SIZE', 3221225472); // 3GB
define('UPLOAD_DIR', 'uploads/');

ini_set('memory_limit', '3G');
set_time_limit(300);

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die("データベース接続に失敗しました: " . $e->getMessage());
}

session_start();