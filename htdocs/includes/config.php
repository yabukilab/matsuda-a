<?php
# HTMLでのエスケープ処理をする関数（データベースとは無関係だが，ついでにここで定義しておく．）
// この関数定義はfunctions.phpに移動しました

// SQLで作成したデータベース設定を使用
// config.php の修正部分
$dbServer = isset($_ENV['MYSQL_SERVER'])    ? $_ENV['MYSQL_SERVER']      : '127.0.0.1';
$dbUser = isset($_ENV['MYSQL_USER'])     ? $_ENV['MYSQL_USER']     : 'testuser';
$dbPass = isset($_ENV['MYSQL_PASSWORD']) ? $_ENV['MYSQL_PASSWORD'] : 'pass';
$dbName = isset($_ENV['MYSQL_DB'])       ? $_ENV['MYSQL_DB']       : 'mydb';

$dsn = "mysql:host={$dbServer};dbname={$dbName};charset=utf8";

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
  $db = new PDO($dsn, $dbUser, $dbPass);
  # プリペアドステートメントのエミュレーションを無効にする．
  $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  # エラー→例外
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo "Can't connect to the database: " . h($e->getMessage());
}

session_start();