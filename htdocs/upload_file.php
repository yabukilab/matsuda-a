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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content'] ?? '');

    $uploadErrors = [];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    $filesToProcess = [];

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

            // MIMEタイプ検証
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $detectedType = finfo_file($finfo, $tmpName);
            finfo_close($finfo);

            if (!in_array($detectedType, $allowedTypes)) {
                $uploadErrors[] = "不正なファイル形式: $fileName (許可形式: JPEG, PNG, GIF, WebP)";
                continue;
            }

            // ファイルサイズ検証
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
        $error = "コメントか画像ファイルのいずれかは必須です";
    }

    if (empty($error)) {
        try {
            $pdo->beginTransaction();

            // コメント作成
            $stmt = $pdo->prepare("INSERT INTO comments (thread_id, student_id, content) VALUES (?, ?, ?)");
            $stmt->execute([$thread_id, $_SESSION['student_id'], $content]);
            $comment_id = $pdo->lastInsertId();

            // ファイルアップロード処理
            if (!empty($filesToProcess)) {
                foreach ($filesToProcess as $file) {
                    save_uploaded_file_chunked($file, $comment_id);
                }
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
        <h1>画像アップロード</h1>
        <div class="user-info">
            ユーザー名: <?php echo htmlspecialchars($_SESSION['name']); ?>
            <a href="thread.php?id=<?php echo htmlspecialchars($thread_id); ?>" class="btn">スレッドに戻る</a>
            <a href="logout.php" class="logout-btn">ログアウト</a>
        </div>
    </header>

    <?php if (isset($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="thread_id" value="<?= h($thread_id) ?>">

        <div class="form-group">
            <label for="content">コメント:</label>
            <textarea id="content" name="content" rows="3" placeholder="コメントを入力"><?= h($content) ?></textarea>
        </div>

        <div class="form-group">
            <label>画像選択:</label>
            <input type="file" name="files[]" multiple accept="image/*">
            <p class="hint">
                対応形式: JPEG, PNG, GIF, WebP<br>
                画像ファイルのみアップロード可能です
            </p>
        </div>

        <button type="submit" class="btn">アップロード</button>
    </form>
</div>