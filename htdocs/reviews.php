<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>商品一覧</title>
    <link rel="stylesheet" href="a.css">
</head>
<body>
    <div class="header"></div>
    <div class="container">
        <?php
        require_once '_database_conf.php';
        require_once '_h.php';

        try {
            $db = new PDO($dsn, $dbUser, $dbPass);
            $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = 'SELECT * FROM mst_product';
            $stmt = $db->prepare($sql);
            $stmt->execute();

            $db = null;

            echo '<h1>商品批評・閲覧</h1>';

            echo '<form method="get" action="comment.php">';
            echo 'コメント追加：ID';
            echo '<input type="text" name="procode" style="width:20px">';
            echo '<input type="submit" value="決定">';
            echo '</form>';

            echo '<form method="get" action="stock.php">';
            echo '1つ在庫数を減らす：ID';
            echo '<input type="text" name="procode" style="width:20px">';
            echo '<input type="submit" value="決定">';
            echo '</form>';

            // データのグループ化
            $results = [];
            while ($rec = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $results[$rec['number']][] = $rec;
            }

            // グループごとの表示
            foreach ($results as $number => $group) {
                // グループのヘッダーを表示
                echo '<div class="group-box">';
                echo '<h2>自販機番号: ' . h($number) . '</h2>';

                // グループ内の各商品を表示
                foreach ($group as $rec) {
                    echo '<div class="product-box">';
                    echo '<p>ID: ' . h($rec['code']) . '</p>';
                    echo '<p>商品名: ' . h($rec['name']) . '</p>';
                    echo '<p>価格: ' . h($rec['price']) . '円</p>';
                    echo '</div>';
                }

                echo '</div>'; // .group-box
            }
        } catch (Exception $e) {
            echo 'エラーが発生しました。内容: ' . h($e->getMessage());
            exit();
        }
        ?>
    </div>
</body>
</html>
