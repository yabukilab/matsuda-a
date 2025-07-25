<?php
$guide_image = "introduction_register.png";
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (isset($_SESSION['student_id'])) {
    header("Location: home.php");
    exit;
}

$errors = [];
$student_id = '';
$name = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id']);
    $name = trim($_POST['name']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // バリデーション
    if (!preg_match('/^\d{7}$/', $student_id)) {
        $errors[] = "学籍番号は7桁の数字で入力してください";
    }

    if (empty($name)) {
        $errors[] = "名前を入力してください";
    } elseif (strlen($name) > 100) {
        $errors[] = "名前は100文字以内で入力してください";
    }

    if (strlen($password) < 8) {
        $errors[] = "パスワードは8文字以上で入力してください";
    } elseif ($password !== $confirm_password) {
        $errors[] = "パスワードが一致しません";
    }

    // 学籍番号重複チェック
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE student_id = ?");
        $stmt->execute([$student_id]);
        if ($stmt->fetch()) {
            $errors[] = "この学籍番号は既に登録されています";
        }
    }

    // 登録処理
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (student_id, name, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$student_id, $name, $password_hash]);

        $_SESSION['success_message'] = "アカウント登録が完了しました。ログインしてください。";
        header("Location: index.php");
        exit;
    }
}
?>

<?php include 'includes/header.php'; ?>
<div class="container">
    <header>
        <h1>アカウント作成</h1>

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

    </header>

    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="student_id">学籍番号 (7桁):</label>
            <input type="text" id="student_id" name="student_id"
                pattern="\d{7}" required
                value="<?php echo htmlspecialchars($student_id); ?>">
            <p class="hint">※7桁以下の場合は前に0を追加</p>
        </div>

        <div class="form-group">
            <label for="name">名前:</label>
            <input type="text" id="name" name="name" required
                maxlength="100" value="<?php echo htmlspecialchars($name); ?>">
        </div>

        <div class="form-group">
            <label for="password">パスワード (8文字以上):</label>
            <input type="password" id="password" name="password" required minlength="8">
        </div>

        <div class="form-group">
            <label for="confirm_password">パスワード確認:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>

        <button type="submit" class="btn">登録</button>
        <a href="index.php" class="btn">ログイン画面へ戻る</a>
    </form>
</div>
</body>

</html>