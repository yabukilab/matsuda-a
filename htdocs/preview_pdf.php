<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_GET['file_id'])) {
    die('ファイルIDが指定されていません');
}

$file_id = (int)$_GET['file_id'];

$stmt = $pdo->prepare("SELECT * FROM uploaded_files WHERE file_id = ?");
$stmt->execute([$file_id]);
$file = $stmt->fetch();

if (!$file) {
    die('ファイルが見つかりません');
}

// PDFファイルであることを確認
$ext = strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION));
if ($ext !== 'pdf') {
    die('PDFファイルではありません');
}

// PDFを表示
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="'.basename($file['file_name']).'"');
echo base64_decode($file['file_data']);
exit;