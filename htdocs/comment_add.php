<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>コメント登録</title>
</head>
<body>
    <?php
    require_once '_database_conf.php';
    require_once '_h.php';

    $pro_code = $_POST['pro_code'];
    $comment_text = $_POST['text'];

    try {
        $db = new PDO($dsn, $dbUser, $dbPass);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = 'INSERT INTO comments (pro_code, text) VALUES (:pro_code, :text)';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':pro_code', $pro_code, PDO::PARAM_INT);
        $stmt->bindValue(':text', $comment_text, PDO::PARAM_STR);
        $stmt->execute();

        $db = null;

        print 'コメントが追加されました。<br />';
    } catch (Exception $e) {
        echo 'エラーが発生しました。内容: ' . h($e->getMessage());
        exit();
    }
    ?>

<a href="index.php">戻る</a>
</body>
</html>
