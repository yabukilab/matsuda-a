<?php
// このページのガイド画像を設定
$guide_image = "introduction_thread.png";
require_once 'includes/config.php';
require_once 'includes/functions.php';

// ログイン確認
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: home.php");
    exit;
}

$stmt = $pdo->prepare("SELECT t.*, u.name AS author_name, u.is_admin 
                      FROM threads t
                      JOIN users u ON t.created_by = u.student_id
                      WHERE t.thread_id = ?");
$stmt->execute([$_GET['id']]);
$thread = $stmt->fetch();

if (!$thread) {
    header("Location: home.php");
    exit;
}

$stmt = $pdo->prepare("SELECT c.*, u.name AS author_name, u.is_admin 
                      FROM comments c
                      JOIN users u ON c.student_id = u.student_id
                      WHERE c.thread_id = ?
                      ORDER BY c.created_at ASC");
$stmt->execute([$_GET['id']]);
$comments = $stmt->fetchAll();

foreach ($comments as &$comment) {
    // ファイル情報取得
    $stmt = $pdo->prepare("SELECT * FROM uploaded_files WHERE comment_id = ?");
    $stmt->execute([$comment['comment_id']]);
    $comment['files'] = $stmt->fetchAll();
}
unset($comment);
?>

<?php include 'includes/header.php'; ?>
<div class="container">
    <header>
        <h1><?= h($thread['title']) ?></h1>

        <!-- ガイドボタン追加 -->
        <div class="help-btn-container">
            <a href="introduction/<?= $guide_image ?>" target="_blank" class="btn help-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                    <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z" />
                </svg>
                使い方ガイド
            </a>
        </div>

        <div class="user-info">
            ユーザー名: <?php echo htmlspecialchars($_SESSION['name']); ?>
            <?php if ($_SESSION['student_id'] === '9877389'): ?>
                <span class="admin-badge">管理者</span>
            <?php endif; ?>

            <a href="home.php" class="btn">ホームに戻る</a>
            <a href="logout.php" class="logout-btn">ログアウト</a>
        </div>
    </header>

    <div class="thread-meta">
        <span>カテゴリ: <?= h($thread['category']) ?></span>
        <span>作成者: <?= h($thread['author_name']) ?></span>
        <span>作成日時: <?= h($thread['created_at']) ?></span>
    </div>

    <div class="comment-section">
        <h2>コメント</h2>

        <div class="comment-list">
            <?php if (empty($comments)): ?>
                <p>コメントがありません</p>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment-item">
                        <div class="comment-header">
                            <span class="comment-author">
                                <?= h($comment['author_name']) ?>
                                <?php if ($comment['is_admin']): ?>
                                    <span class="admin-badge">管理者</span>
                                <?php endif; ?>
                            </span>
                            <span class="comment-date"><?= h($comment['created_at']) ?></span>

                            <?php if ($_SESSION['student_id'] === $comment['student_id'] || $_SESSION['student_id'] === '9877389'): ?>
                                <form method="post" action="delete.php" class="inline-form">
                                    <input type="hidden" name="comment_id" value="<?= $comment['comment_id'] ?>">
                                    <button type="submit" class="delete-btn" onclick="return confirm('本当に削除しますか？')">削除</button>
                                </form>
                            <?php endif; ?>
                        </div>

                        <div class="comment-content"><?= nl2br(htmlspecialchars_decode(trim(h($comment['content'])))) ?></div>

                        <?php if (!empty($comment['files'])): ?>
                            <div class="comment-files">
                                <strong>添付ファイル:</strong>
                                <?php foreach ($comment['files'] as $file): ?>
                                    <div class="file-item">
                                        <?php
                                        $ext = strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION));
                                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        $isPdf = ($ext === 'pdf');
                                        ?>

                                        <!-- スマホ向け画像プレビュー調整 -->
                                        <?php if ($isImage): ?>
                                            <div class="image-preview-container">
                                                <a href="download.php?file_id=<?= $file['file_id'] ?>&preview=1" target="_blank">
                                                    <img src="data:<?= $file['file_type'] ?>;base64,<?= $file['file_data'] ?>"
                                                        class="preview-image"
                                                        alt="画像プレビュー">
                                                </a>
                                                <div class="download-link">
                                                    <a href="download.php?file_id=<?= $file['file_id'] ?>" download="<?= h($file['file_name']) ?>">
                                                        <?= h($file['file_name']) ?> (ダウンロード)
                                                    </a>
                                                </div>
                                            </div>
                                        <?php elseif ($isPdf): ?>
                                            <!-- PDFプレビュー -->
                                            <div>
                                                <a href="download.php?file_id=<?= $file['file_id'] ?>&preview=1" target="_blank">
                                                    PDFプレビュー
                                                </a>
                                            </div>
                                            <div>
                                                <a href="download.php?file_id=<?= $file['file_id'] ?>" download="<?= h($file['file_name']) ?>">
                                                    <?= h($file['file_name']) ?> (ダウンロード)
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <!-- その他のファイル -->
                                            <a href="download.php?file_id=<?= $file['file_id'] ?>" download="<?= h($file['file_name']) ?>">
                                                <?= h($file['file_name']) ?>
                                            </a>
                                        <?php endif; ?>

                                        <span class="file-size">(<?= format_size($file['file_size']) ?>)</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="comment-form">
            <h3>コメントを投稿</h3>
            <form method="post" action="post_comment.php">
                <input type="hidden" name="thread_id" value="<?= h($_GET['id']) ?>">

                <div class="form-group">
                    <textarea name="content" rows="5" required placeholder="コメントを入力"></textarea>
                </div>

                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <button type="submit" class="btn">投稿</button>
                    <a href="upload_file.php?thread_id=<?= h($_GET['id']) ?>" class="btn">
                        ファイルアップロード
                    </a>
                    <a href="table_editor.php?thread_id=<?= h($_GET['id']) ?>" class="btn">
                        表を作成する
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- スクロールボタン -->
<div class="scroll-to-bottom" onclick="scrollToCommentForm()">↓</div>

<script>
    function scrollToCommentForm() {
        const commentForm = document.querySelector('.comment-form');
        commentForm.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    // ページ読み込み時のスクロール制御
    window.addEventListener('load', function() {
        // リファラをチェックしてhome.phpからの遷移か判定
        if (document.referrer.includes('home.php')) {
            window.scrollTo(0, 0); // ページ先頭にスクロール
        } else {
            // コメントフォームまでスクロール
            const commentForm = document.querySelector('.comment-form');
            if (commentForm) {
                commentForm.scrollIntoView({
                    behavior: 'auto',
                    block: 'start'
                });
            }
        }
    });
</script>

</body>

</html>