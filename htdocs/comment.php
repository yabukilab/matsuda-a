<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>コメント入力</title>
    <link rel="stylesheet" href="a.css">
</head>
<body>
<div class="header"></div>
<div class="container">
    <?php
    require_once '_database_conf.php';
    require_once '_h.php';

    $pro_code = $_GET['procode'];

    try {
        $db = new PDO($dsn, $dbUser, $dbPass);
        $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = 'SELECT * FROM mst_product WHERE code = :code';
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':code', $pro_code, PDO::PARAM_INT);
        $stmt->execute();

        $rec = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($rec == false) {
            print '商品コードが正しくありません。';
            print '<a href="index.php">戻る</a>';
            exit();
        }

        $db = null;
    } catch (Exception $e) {
        echo 'エラーが発生しました。内容: ' . h($e->getMessage());
        exit();
    }
    ?>

    <h2>商品表示</h2>
    <p>商品コード: <?php print h($rec['code']); ?></p>
    <p>商品名: <?php print h($rec['name']); ?></p>
    <p>価格: <?php print h($rec['price']).'円'; ?></p>

    <form action="comment_add.php" method="post">
        <textarea name="text" rows="4" cols="40" placeholder="コメントを入力してください"></textarea><br />
        <input type="hidden" name="pro_code" value="<?php print h($rec['code']); ?>">
        <input type="submit" value="コメントを投稿">
    </form>

    <form>
        <input type="button" onclick="history.back()" value="戻る">
    </form>
    <?php
require_once '_database_conf.php';
require_once '_h.php';

$pro_code = $_GET['procode'];

try {
    $db = new PDO($dsn, $dbUser, $dbPass);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = 'SELECT * FROM comments WHERE pro_code = :pro_code';
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':pro_code', $pro_code, PDO::PARAM_INT);
    $stmt->execute();

    $recs = $stmt->fetchAll(PDO::FETCH_ASSOC);


} catch (Exception $e) {
    echo 'エラーが発生しました。内容: ' . h($e->getMessage());
    exit();
}				
print '<br />';
print '<form method="get" action="delete.php">';
print 'コメント削除：コメントID ';
print '<input type="text" name="id" style="width:20px">';
print '<input type="submit" value="決定">';
print '</form>';



?>

<?php foreach ($recs as $rec): ?>
    <p>コメントID: <?php print h($rec['id']); ?></p>
    <p>コメント: <?php print h($rec['text']); ?></p>
    <p>投稿日: <?php print h($rec['created_at']); ?></p>
    <hr>
<?php endforeach; ?>

</div>
</body>
</html>
