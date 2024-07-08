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

                // 在庫数を減らす処理を追加
                $quantity = 1; // 減らしたい在庫数
                $currentStock = $rec['stock'];
        
                if ($currentStock >= $quantity) {
                    $newStock = $currentStock - $quantity;
                    $updateSql = 'UPDATE mst_product SET stock = :stock WHERE code = :code';
                    $updateStmt = $db->prepare($updateSql);
                    $updateStmt->bindValue(':stock', $newStock, PDO::PARAM_INT);
                    $updateStmt->bindValue(':code', $pro_code, PDO::PARAM_INT);
                    $updateStmt->execute();
        
                    echo '在庫数が更新されました。';
                    $rec['stock'] = $newStock; // 更新後の在庫数を設定
                } else {
                    echo '在庫が不足しています。';
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
    <p>在庫数: <?php print h($rec['stock']); ?></p>

    <a href="index.php">TOPに戻る</a>

    </div>
</body>
</html>