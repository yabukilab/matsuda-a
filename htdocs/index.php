<?php
// このページのガイド画像を設定
$guide_image = "introduction_index.png";
require_once 'includes/config.php';
require_once 'includes/functions.php';

// ログイン処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = preg_replace('/[^0-9]/', '', $_POST['student_id']);
    $password = $_POST['password'];

    if (strlen($student_id) !== 7) {
        $error = "学籍番号は7桁の数字で入力してください";
    } else {
        // ユーザー検索
        $stmt = $pdo->prepare("SELECT * FROM users WHERE student_id = ?");
        $stmt->execute([$student_id]);
        $user = $stmt->fetch();

        if ($user && verify_password($password, $user['password_hash'])) {
            $_SESSION['student_id'] = $student_id;
            $_SESSION['name'] = $user['name'];
            header("Location: home.php");
            exit;
        } else {
            $error = "学籍番号またはパスワードが正しくありません";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>
<div class="login-container">
    <header>
        <h1>研究室共有<br>システム</h1>

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
    </header>

    <?php if (isset($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="success">
            <?php echo htmlspecialchars($_SESSION['success_message']); ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="student_id">学籍番号 (7桁):</label>
            <input type="text" id="student_id" name="student_id" pattern="\d{7}" required
                placeholder="例: 0123456" class="login-input">
            <p class="hint">※7桁以下の場合は前に0を追加</p>
        </div>

        <div class="form-group">
            <label for="password">パスワード:</label>
            <input type="password" id="password" name="password" required
                placeholder="パスワードを入力" class="login-input">
        </div>

        <button type="submit" class="btn login-btn">ログイン</button>
    </form>
    <div class="register-link">
        <a href="register.php">アカウント作成はこちら</a>
    </div>
</div>
</body>

</html>