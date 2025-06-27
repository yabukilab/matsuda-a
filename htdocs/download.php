<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// file_id がセットされていない、または無効な場合
if (!isset($_GET['file_id']) || (int)$_GET['file_id'] <= 0) {
    header("HTTP/1.1 400 Bad Request");
    exit;
}

$file_id = (int)$_GET['file_id'];

// データベースからファイル情報を取得
$stmt = $pdo->prepare("SELECT * FROM uploaded_files WHERE file_id = ?");
$stmt->execute([$file_id]);
$file = $stmt->fetch();

if (!$file) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

// プレビュー表示処理
if (isset($_GET['preview'])) {
    $ext = strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION));

    // 画像ファイルの場合
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        header('Content-Type: ' . $file['file_type']);
        echo base64_decode($file['file_data']);
        exit;
    }
    // PDFファイルの場合
    elseif ($ext === 'pdf') {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $file['file_name'] . '"');
        echo base64_decode($file['file_data']);
        exit;
    }
}

// 通常のダウンロード処理
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $file['file_name'] . '"');
header('Content-Length: ' . $file['file_size']);
echo base64_decode($file['file_data']);
exit;
