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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // コンテンツの取得（空文字を許可）
    $content = trim($_POST['content'] ?? '');

    // ファイルエラーチェック
    $uploadErrors = [];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    if (!empty($_FILES['files'])) {
        foreach ($_FILES['files']['error'] as $key => $errorCode) {
            $tmpName = $_FILES['files']['tmp_name'][$key];
            $fileName = $_FILES['files']['name'][$key];
            $fileType = $_FILES['files']['type'][$key];

            // 画像ファイルチェック
            if ($errorCode === UPLOAD_ERR_OK) {
                // MIMEタイプ検証
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $detectedType = finfo_file($finfo, $tmpName);
                finfo_close($finfo);

                if (!in_array($detectedType, $allowedTypes)) {
                    $uploadErrors[] = "不正なファイル形式: $fileName (許可形式: JPEG, PNG, GIF, WebP)";
                    continue;
                }
            } else if ($errorCode !== UPLOAD_ERR_NO_FILE) {
                $uploadErrors[] = getUploadErrorMessage($errorCode);
            }
        }
    }

    if (!empty($uploadErrors)) {
        $error .= implode("<br>", $uploadErrors);
    }

    // コメントとファイルの両方が空の場合のみエラー
    if (empty($content) && (empty($_FILES['files']) || array_sum($_FILES['files']['size']) === 0)) {
        $error = "コメントか画像ファイルのいずれかは必須です";
    }

    if (empty($error)) {
        try {
            // トランザクション開始
            $pdo->beginTransaction();

            // コメント作成（空文字を許可）
            $stmt = $pdo->prepare("INSERT INTO comments (thread_id, student_id, content) VALUES (?, ?, ?)");
            $stmt->execute([$thread_id, $_SESSION['student_id'], $content]);
            $comment_id = $pdo->lastInsertId();

            // ファイルアップロード処理
            if (!empty($_FILES['files'])) {
                foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['files']['error'][$key] !== UPLOAD_ERR_OK) continue;

                    $file = [
                        'name' => $_FILES['files']['name'][$key],
                        'tmp_name' => $tmp_name,
                        'type' => $_FILES['files']['type'][$key],
                        'size' => $_FILES['files']['size'][$key]
                    ];

                    save_uploaded_file($file, $comment_id);
                }
            }

            $pdo->commit();
            header("Location: thread.php?id=" . $thread_id);
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log("Upload error: " . $e->getMessage());
            $error = "ファイルのアップロードに失敗しました";
        }
    }
}

// エラーメッセージ表示関数
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
            <textarea id="content" name="content" rows="3" placeholder="コメントを入力"></textarea>
        </div>

        <div class="form-group">
            <label>画像選択（複数可・最大3GB）:</label>
            <input type="file" name="files[]" multiple accept="image/*">
            <p class="hint">
                対応形式: JPEG, PNG, GIF, WebP<br>
                画像ファイルのみアップロード可能です
            </p>
        </div>

        <button type="submit" class="btn">アップロード</button>
    </form>
</div>