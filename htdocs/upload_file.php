<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// ログイン確認
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit;
}

$thread_id = $_GET['thread_id'] ?? 0;

// スレッド存在チェック
$stmt = $pdo->prepare("SELECT 1 FROM threads WHERE thread_id = ?");
$stmt->execute([$thread_id]);
if (!$stmt->fetch()) {
    header("Location: home.php");
    exit;
}

$error = '';
$content = '';

function save_uploaded_file_chunked($file, $comment_id)
{
    global $pdo;

    $file_name = $file['name'];
    $file_type = $file['type'];
    $file_size = $file['size'];

    $file_data = file_get_contents(
        $file['tmp_name'],
        false,
        null,
        0,
        min(filesize($file['tmp_name']), MAX_FILE_SIZE)
    );
    $base64_data = base64_encode($file_data);

    $stmt = $pdo->prepare("INSERT INTO uploaded_files (comment_id, file_name, file_type, file_size, file_data, is_zip) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $comment_id,
        $file_name,
        $file_type,
        $file_size,
        $base64_data,
        0
    ]);

    return $pdo->lastInsertId();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content'] ?? '');

    $allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/zip',
        'text/plain',
        'application/octet-stream'
    ];
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'txt'];
    $dangerousExtensions = ['php', 'phtml', 'html', 'htm', 'js', 'exe', 'bat', 'sh'];

    $filesToProcess = [];
    $uploadErrors = [];

    if (!empty($_FILES['files'])) {
        foreach ($_FILES['files']['error'] as $key => $errorCode) {
            if ($errorCode === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if ($errorCode !== UPLOAD_ERR_OK) {
                $uploadErrors[] = getUploadErrorMessage($errorCode);
                continue;
            }

            $tmpName = $_FILES['files']['tmp_name'][$key];
            $fileName = $_FILES['files']['name'][$key];
            $fileType = $_FILES['files']['type'][$key];
            $fileSize = $_FILES['files']['size'][$key];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $detectedType = finfo_file($finfo, $tmpName);
            finfo_close($finfo);

            // 危険な拡張子のブロック
            if (in_array($fileExtension, $dangerousExtensions)) {
                $uploadErrors[] = "セキュリティ上、このファイルタイプはアップロードできません: $fileName";
                continue;
            }

            // MIMEタイプ・拡張子の検証
            if (!in_array($detectedType, $allowedTypes) || !in_array($fileExtension, $allowedExtensions)) {
                $uploadErrors[] = "不正なファイル形式: $fileName (許可形式: " . implode(', ', $allowedExtensions) . ")";
                continue;
            }

            if ($fileSize > MAX_FILE_SIZE) {
                $uploadErrors[] = "ファイルサイズが大きすぎます: $fileName (最大" . format_size(MAX_FILE_SIZE) . ")";
                continue;
            }

            $filesToProcess[] = [
                'name' => $fileName,
                'tmp_name' => $tmpName,
                'type' => $fileType,
                'size' => $fileSize
            ];
        }
    }

    if (!empty($uploadErrors)) {
        $error .= implode("<br>", $uploadErrors);
    }

    if (empty($content) && empty($filesToProcess)) {
        $error = "コメントかファイルのいずれかは必須です";
    }

    if (empty($error)) {
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO comments (thread_id, student_id, content) VALUES (?, ?, ?)");
            $stmt->execute([$thread_id, $_SESSION['student_id'], $content]);
            $comment_id = $pdo->lastInsertId();

            foreach ($filesToProcess as $file) {
                save_uploaded_file_chunked($file, $comment_id);
            }

            $pdo->commit();
            header("Location: thread.php?id=" . $thread_id);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Upload error: " . $e->getMessage());
            $error = "ファイルのアップロードに失敗しました: " . $e->getMessage();
        }
    }
}

function getUploadErrorMessage($code)
{
    $errors = [
        UPLOAD_ERR_INI_SIZE => "ファイルサイズが大きすぎます（最大3GB）",
        UPLOAD_ERR_FORM_SIZE => "ファイルサイズがフォームの制限を超えています",
        UPLOAD_ERR_PARTIAL => "ファイルが一部しかアップロードされていません",
        UPLOAD_ERR_NO_FILE => "ファイルが選択されていません",
        UPLOAD_ERR_NO_TMP_DIR => "一時フォルダが存在しません",
        UPLOAD_ERR_CANT_WRITE => "ディスクへの書き込みに失敗しました",
        UPLOAD_ERR_EXTENSION => "拡張モジュールによってアップロードが中止されました",
    ];
    return $errors[$code] ?? "不明なエラーが発生しました (コード: $code)";
}

include 'includes/header.php';
?>
<div class="container">
    <header>
        <h1>ファイルアップロード</h1>
        <div class="user-info">
            ユーザー名: <?= htmlspecialchars($_SESSION['name']); ?>
            <a href="thread.php?id=<?= htmlspecialchars($thread_id); ?>" class="btn">スレッドに戻る</a>
            <a href="logout.php" class="logout-btn">ログアウト</a>
        </div>
    </header>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="thread_id" value="<?= htmlspecialchars($thread_id); ?>">

        <div class="form-group">
            <label for="content">コメント:</label>
            <textarea id="content" name="content" rows="3" placeholder="コメントを入力"><?= htmlspecialchars($content); ?></textarea>
        </div>

        <div class="form-group">
            <label>ファイル選択:</label>
            <input type="file" name="files[]" multiple accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.zip,.txt">
            <p class="hint">
                対応形式: JPG, PNG, GIF, WebP, PDF, Word, Excel, ZIP, TXT（危険な形式はブロックされます）<br>
                最大ファイルサイズ: <?= format_size(MAX_FILE_SIZE) ?>
            </p>
        </div>

        <button type="submit" class="btn">アップロード</button>
    </form>
</div>