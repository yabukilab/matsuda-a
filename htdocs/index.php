<?php
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
    <h1>研究室共有システム</h1>
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
            <p class="hint">※7桁以下の場合は前に0を追加してください</p>
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