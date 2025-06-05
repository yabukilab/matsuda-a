<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: home.php");
    exit;
}

$stmt = $pdo->prepare("SELECT t.*, u.is_admin 
                      FROM threads t
                      JOIN users u ON t.created_by = u.student_id
                      WHERE t.thread_id = ?");
$stmt->execute([$_GET['id']]);
$thread = $stmt->fetch();

if (!$thread) {
    header("Location: home.php");
    exit;
}

$stmt = $pdo->prepare("SELECT c.*, u.is_admin 
                      FROM comments c
                      JOIN users u ON c.student_id = u.student_id
                      WHERE c.thread_id = ?
                      ORDER BY c.created_at ASC");
$stmt->execute([$_GET['id']]);
$comments = $stmt->fetchAll();

foreach ($comments as &$comment) {
    $stmt = $pdo->prepare("SELECT folder_path FROM uploaded_folders WHERE comment_id = ?");
    $stmt->execute([$comment['comment_id']]);
    $comment['folders'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $comment['files'] = [];
    if (!empty($comment['file_path'])) {
        $comment['files'] = explode("\n", trim($comment['file_path']));
    }
}
unset($comment);
?>

<?php include 'includes/header.php'; ?>
<div class="container">
    <header>
        <h1><?= h($thread['title']) ?></h1>
        <div class="user-info">
            学籍番号: <?= h($_SESSION['student_id']) ?>
            <?php if ($_SESSION['student_id'] === '9877389'): ?>
                <span class="admin-badge">管理者</span>
            <?php endif; ?>
            <a href="home.php" class="btn">ホームに戻る</a>
            <a href="logout.php" class="logout-btn">ログアウト</a>
        </div>
    </header>

    <div class="thread-meta">
        <span>カテゴリ: <?= h($thread['category']) ?></span>
        <span>作成者: <?= h($thread['created_by']) ?></span>
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
                                <?= h($comment['student_id']) ?>
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

                        <div class="comment-content"><?= nl2br(htmlspecialchars_decode(trim(h($comment['content'])))) ?>
                        </div>

                        <?php if (!empty($comment['files']) || !empty($comment['folders'])): ?>
                            <div class="comment-files">
                                <strong>添付ファイル:</strong>
                                <?php foreach ($comment['files'] as $file): ?>
                                    <?php if (!empty($file)): ?>
                                        <?php
                                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);
                                        ?>
                                        <div class="file-item">
                                            <?php if ($isImage): ?>
                                                <a href="<?= h($file) ?>" target="_blank">
                                                    <img src="<?= h($file) ?>" class="preview-image" alt="画像プレビュー">
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= h($file) ?>" download>
                                                    <?= basename(h($file)) ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                                <?php foreach ($comment['folders'] as $folder): ?>
                                    <?php $files = glob(rtrim($folder, '/') . '/*'); ?>
                                    <?php foreach ($files as $file): ?>
                                        <?php if (is_file($file)): ?>
                                            <?php
                                            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);
                                            ?>
                                            <div class="file-item">
                                                <?php if ($isImage): ?>
                                                    <a href="<?= h($file) ?>" target="_blank">
                                                        <img src="<?= h($file) ?>" class="preview-image" alt="フォルダ内画像">
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= h($file) ?>" download>
                                                        <?= basename(h($file)) ?> (フォルダ内)
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
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
                        ファイルを添付する
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
</script>

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