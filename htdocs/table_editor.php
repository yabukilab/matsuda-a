<?php
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
        <div class="user-info">
            学籍番号: <?php echo h($_SESSION['student_id']); ?>
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