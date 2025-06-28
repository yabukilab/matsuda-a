<?php
$guide_image = "introduction_create_thread.png";
require_once 'includes/config.php';
require_once 'includes/functions.php';

// ログイン確認
if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit;
}

// カテゴリ定義
$categories = [
    '教科' => ['数学', '英語', 'プログラミング', 'ソフトウェア', 'IT関連'],
    '研究関連' => ['卒論', '演習', '研究内容相談', '課題研究'],
    'その他' => ['趣味', '雑談', '気軽な相談']
];

// スレッド作成処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $category = $_POST['category'];

    if (empty($title)) {
        $error = "スレッド名を入力してください";
    } elseif (strlen($title) > 100) {
        $error = "スレッド名が長すぎます（全角34文字or半角100文字までです）";
    } else {
        // スレッド作成
        $stmt = $pdo->prepare("INSERT INTO threads (title, category, created_by) VALUES (?, ?, ?)");
        $stmt->execute([$title, $category, $_SESSION['student_id']]);

        header("Location: home.php");
        exit;
    }
}
?>

<?php include 'includes/header.php'; ?>
<div class="container">
    <header>
        <h1>スレッド作成</h1>

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
            <a href="home.php" class="btn">ホームに戻る</a>
            <a href="logout.php" class="logout-btn">ログアウト</a>
        </div>
    </header>

    <?php if (isset($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="title">スレッド名:</label>
            <input type="text" id="title" name="title" required maxlength="100">
        </div>

        <div class="form-group">
            <label>カテゴリ:</label>
            <?php foreach ($categories as $group => $items): ?>
                <fieldset>
                    <legend><?php echo htmlspecialchars($group); ?></legend>
                    <?php foreach ($items as $item): ?>
                        <label>
                            <input type="radio" name="category" value="<?php echo htmlspecialchars($item); ?>" required>
                            <?php echo htmlspecialchars($item); ?>
                        </label><br>
                    <?php endforeach; ?>
                </fieldset>
            <?php endforeach; ?>
        </div>

        <button type="submit" class="btn">作成</button>
    </form>
</div>
</body>

</html>