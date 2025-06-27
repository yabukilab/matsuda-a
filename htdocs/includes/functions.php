<?php
// 既存のcalculate_password関数を削除
// 新規関数追加
function verify_password($password, $hash)
{
  return password_verify($password, $hash);
}

function is_admin()
{
  if (!isset($_SESSION['student_id'])) return false;
  global $pdo;
  $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE student_id = ?");
  $stmt->execute([$_SESSION['student_id']]);
  $user = $stmt->fetch();
  return $user && $user['is_admin'];
}

// 型ヒントを追加して重複を防ぐ
function h(string|array $var): string|array
{
  if (is_array($var)) {
    return array_map('h', $var);
  } else {
    return htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
  }
}

function format_size($bytes)
{
  $units = ['B', 'KB', 'MB', 'GB'];
  $bytes = max($bytes, 0);
  $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
  $pow = min($pow, count($units) - 1);
  $bytes /= (1 << (10 * $pow));
  return round($bytes, 2) . ' ' . $units[$pow];
}

function save_uploaded_file_chunked($file, $comment_id)
{
  global $pdo; // 追加

  $file_name = $file['name'];
  $file_type = $file['type'];
  $file_size = $file['size'];

  // メモリ制限付きで読み込み
  $file_data = file_get_contents(
    $file['tmp_name'],
    false,
    null,
    0,
    min(filesize($file['tmp_name']), MAX_FILE_SIZE)
  );
  $base64_data = base64_encode($file_data);

  // 修正箇所: false → 0
  $stmt = $pdo->prepare("INSERT INTO uploaded_files (comment_id, file_name, file_type, file_size, file_data, is_zip) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->execute([
    $comment_id,
    $file_name,
    $file_type,
    $file_size,
    $base64_data,
    0 // ここを 0 に変更
  ]);

  return $pdo->lastInsertId();
}
