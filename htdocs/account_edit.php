<?php
$guide_image = "introduction_account_edit.png";
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['student_id'];
$errors = [];

// ユーザー情報取得
$stmt = $pdo->prepare("SELECT * FROM users WHERE student_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: logout.php");
    exit;
}

$name = $user['name'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // バリデーション
    if (empty($name)) {
        $errors[] = "名前を入力してください";
    } elseif (strlen($name) > 100) {
        $errors[] = "名前は100文字以内で入力してください";
    }

    // 現在のパスワード検証
    if (!password_verify($current_password, $user['password_hash'])) {
        $errors[] = "現在のパスワードが正しくありません";
    }

    // 新しいパスワード処理
    $password_updated = false;
    if (!empty($new_password)) {
        if (strlen($new_password) < 8) {
            $errors[] = "新しいパスワードは8文字以上で入力してください";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "新しいパスワードが一致しません";
        } else {
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $password_updated = true;
        }
    }

    // 更新処理
    if (empty($errors)) {
        if ($password_updated) {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, password_hash = ? WHERE student_id = ?");
            $stmt->execute([$name, $password_hash, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE student_id = ?");
            $stmt->execute([$name, $user_id]);
        }

        $_SESSION['success_message'] = "アカウント情報を更新しました";
        header("Location: account_edit.php");
        exit;
    }
}
?>

<?php include 'includes/header.php'; ?>
<div class="container">
    <header>
        <h1>アカウント情報変更</h1>

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
            ユーザー名: <?php echo htmlspecialchars($name); ?>
            <?php if ($user['is_admin']): ?>
                <span class="admin-badge">管理者</span>
            <?php endif; ?>
            <a href="home.php" class="btn">ホームに戻る</a>
            <a href="logout.php" class="logout-btn">ログアウト</a>
        </div>
    </header>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="success" style="background-color: #dff0d8; color: #3c763d; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
            <?php echo htmlspecialchars($_SESSION['success_message']); ?>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="name">名前:</label>
            <input type="text" id="name" name="name" required
                maxlength="100" value="<?php echo htmlspecialchars($name); ?>">
        </div>

        <div class="form-group">
            <label for="current_password">現在のパスワード:</label>
            <input type="password" id="current_password" name="current_password" required>
        </div>

        <div class="form-group">
            <label for="new_password">新しいパスワード (変更しない場合は空欄):</label>
            <input type="password" id="new_password" name="new_password" minlength="8">
        </div>

        <div class="form-group">
            <label for="confirm_password">新しいパスワード確認:</label>
            <input type="password" id="confirm_password" name="confirm_password">
        </div>

        <button type="submit" class="btn">更新</button>
    </form>
</div>
</body>

</html>