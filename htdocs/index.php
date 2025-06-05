<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// ログイン処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = preg_replace('/[^0-9]/', '', $_POST['student_id']);
    $password = $_POST['password'];
    
    if (strlen($student_id) !== 7) {
        $error = "学籍番号は7桁の数字で入力してください";
    } elseif ($password != calculate_password($student_id)) {
        $error = "パスワードが正しくありません";
    } else {
        // ユーザー登録確認 (初回ログイン時に自動登録)
        $stmt = $pdo->prepare("INSERT IGNORE INTO users (student_id) VALUES (?)");
        $stmt->execute([$student_id]);
        
        $_SESSION['student_id'] = $student_id;
        header("Location: home.php");
        exit;
    }
}
?>

<?php include 'includes/header.php'; ?>
    <div class="login-container">
        <h1>研究室共有システム</h1>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error, ENT_QUOTES); ?></div>
        <?php endif; ?>
        
        <form method="post">
            <div class="form-group">
                <label for="student_id">学籍番号 (7桁):</label>
                <input type="text" id="student_id" name="student_id" pattern="\d{7}" required>
            </div>
            
            <div class="form-group">
                <label for="password">パスワード:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">ログイン</button>
        </form>
    </div>
</body>
</html>