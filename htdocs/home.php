<?php
$guide_image = "introduction_home.png";
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit;
}

$category_filter = $_GET['category'] ?? null;
$order = (isset($_GET['order']) && $_GET['order'] === 'asc') ? 'ASC' : 'DESC';

$sql = "SELECT t.*, u.name AS author_name, u.is_admin 
        FROM threads t
        JOIN users u ON t.created_by = u.student_id
        WHERE ? IS NULL OR t.category = ?
        ORDER BY t.created_at $order";
$stmt = $pdo->prepare($sql);
$stmt->execute([$category_filter, $category_filter]);
$threads = $stmt->fetchAll();

$categories = $pdo->query("SELECT DISTINCT category FROM threads ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
?>

<?php include 'includes/header.php'; ?>
<div class="container">
    <header>
        <h1>スレッド一覧</h1>

        <!-- ヘッダー部分に追加 -->
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
            <?php if (is_admin()): ?>
                <span class="admin-badge">管理者</span>
            <?php endif; ?>
            <a href="account_edit.php" class="btn">アカウント情報変更</a>
            <a href="logout.php" class="logout-btn">ログアウト</a>
        </div>
    </header>

    <div class="controls">
        <a href="create_thread.php" class="btn">スレッド作成</a>

        <div class="sort-filter">
            <form method="get" class="inline-form">
                <label for="order">並び順:</label>
                <select name="order" id="order" onchange="this.form.submit()">
                    <option value="desc" <?= (!isset($_GET['order']) || $_GET['order'] === 'desc') ? 'selected' : '' ?>>新しい順</option>
                    <option value="asc" <?= (isset($_GET['order']) && $_GET['order'] === 'asc') ? 'selected' : '' ?>>古い順</option>
                </select>
                <?php if (isset($_GET['category'])): ?>
                    <input type="hidden" name="category" value="<?php echo h($_GET['category']); ?>">
                <?php endif; ?>
            </form>

            <form method="get" class="inline-form">
                <label for="category">カテゴリ:</label>
                <select name="category" id="category" onchange="this.form.submit()">
                    <option value="">すべて</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo h($cat); ?>"
                            <?php echo isset($_GET['category']) && $_GET['category'] === $cat ? 'selected' : ''; ?>>
                            <?php echo h($cat); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($_GET['order'])): ?>
                    <input type="hidden" name="order" value="<?php echo h($_GET['order']); ?>">
                <?php endif; ?>
            </form>

            <?php if (isset($_GET['category']) || isset($_GET['order'])): ?>
                <a href="home.php" class="btn">すべて表示</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="thread-list">
        <?php if (empty($threads)): ?>
            <p>スレッドがありません</p>
        <?php else: ?>
            <?php foreach ($threads as $thread): ?>
                <div class="thread-item">
                    <div class="thread-header">
                        <h3><a href="thread.php?id=<?php echo $thread['thread_id']; ?>">
                                <?php echo h($thread['title']); ?>
                            </a></h3>
                        <span class="category"><?php echo h($thread['category']); ?></span>
                    </div>

                    <div class="thread-meta">
                        <span>投稿者: <?php echo h($thread['author_name']); ?></span>
                        <span>投稿日時: <?php echo h($thread['created_at']); ?></span>

                        <?php if ($_SESSION['student_id'] === $thread['created_by'] || $_SESSION['student_id'] === '9877389'): ?>
                            <form method="post" action="delete.php" class="inline-form">
                                <input type="hidden" name="thread_id" value="<?php echo $thread['thread_id']; ?>">
                                <button type="submit" class="delete-btn" onclick="return confirm('本当に削除しますか？')">削除</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>

</html>