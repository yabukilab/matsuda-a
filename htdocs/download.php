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

// プレビュー表示
if (isset($_GET['preview'])) {
    $ext = strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION));

    // 画像プレビュー
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        $image_data = base64_decode($file['file_data']);
        header('Content-Type: ' . $file['file_type']);
        echo $image_data;
        exit;
    }
    // PDFプレビュー
    elseif ($ext === 'pdf') {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $file['file_name'] . '"');
        echo base64_decode($file['file_data']);
        exit;
    }
}

// 通常のダウンロード
$file_data = base64_decode($file['file_data']);
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $file['file_name'] . '"');
header('Content-Length: ' . $file['file_size']);
echo $file_data;
exit;
