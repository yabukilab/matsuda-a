<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_GET['file_id'])) {
    header("HTTP/1.1 400 Bad Request");
    exit;
}

$file_id = (int)$_GET['file_id'];
$stmt = $pdo->prepare("SELECT * FROM uploaded_files WHERE file_id = ?");
$stmt->execute([$file_id]);
$file = $stmt->fetch();

if (!$file) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

// プレビュー表示（画像のみ）
if (isset($_GET['preview'])) {
    $ext = strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION));

    if (in_array($ext, ['jpg', 'jpeg'])) {
        header('Content-Type: image/jpeg');
    } elseif ($ext === 'png') {
        header('Content-Type: image/png');
    } elseif ($ext === 'gif') {
        header('Content-Type: image/gif');
    } else {
        // 画像以外のプレビュー要求はダウンロードにフォールバック
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $file['file_name'] . '"');
    }

    echo $file['file_data'];
    exit;
}

// 通常のダウンロード
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $file['file_name'] . '"');
header('Content-Length: ' . strlen($file['file_data']));
echo $file['file_data'];
exit;
