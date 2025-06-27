<?php
$guide_image = "introduction_table_editor.png";
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit;
}

$thread_id = $_GET['thread_id'] ?? 0;

$stmt = $pdo->prepare("SELECT 1 FROM threads WHERE thread_id = ?");
$stmt->execute([$thread_id]);
if (!$stmt->fetch()) {
    header("Location: home.php");
    exit;
}
?>

<?php include 'includes/header.php'; ?>
<div class="container">
    <header>
        <h1>表作成エディタ</h1>

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
            <a href="thread.php?id=<?php echo h($thread_id); ?>" class="btn">スレッドに戻る</a>
            <a href="logout.php" class="logout-btn">ログアウト</a>
        </div>
    </header>

    <form id="table-form" class="table-editor">
        <input type="hidden" id="thread_id" name="thread_id" value="<?php echo h($thread_id); ?>">

        <div class="form-group">
            <label for="table-title">タイトル:</label>
            <input type="text" id="table-title" name="table-title" placeholder="タイトルを入力">
        </div>

        <div class="form-group">
            <label for="table-comment">コメント:</label>
            <textarea id="table-comment" name="table-comment" rows="3" placeholder="表に関するコメントを入力"></textarea>
        </div>

        <div class="form-group">
            <label>表のサイズ:</label>
            <div class="table-editor-controls">
                <div>
                    <label for="rows">行数:</label>
                    <input type="number" id="rows" name="rows" min="1" max="20" value="3">
                </div>
                <div>
                    <label for="cols">列数:</label>
                    <input type="number" id="cols" name="cols" min="1" max="10" value="3">
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>表のプレビュー:</label>
            <table id="table-preview" class="table-preview"></table>
        </div>

        <button type="submit" class="btn">表を挿入</button>
    </form>
</div>

<script src="js/table_editor.js"></script>
</body>

</html>