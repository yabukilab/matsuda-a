<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品管理</title>
    <link rel="stylesheet" href="a.css">
</head>
<body>
    <div class="header"></div>
    <div class="container">
        <?php
        require_once '_database_conf.php';
        require_once '_h.php'; // サニタイズ関数などが含まれていると仮定

        try {
            $db = new PDO($dsn, $dbUser, $dbPass);
            $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST['register'])) {
                    $name = $_POST['product-name'];
                    $price = $_POST['product-price'];
                    $stock = $_POST['product-stock'];
                    $number = $_POST['product-number'];

                    $sql = "INSERT INTO mst_product (name, price, stock, number) VALUES (:name, :price, :stock, :number)";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':price', $price);
                    $stmt->bindParam(':stock', $stock);
                    $stmt->bindParam(':number', $number);
                    $stmt->execute();

                    echo "New product created successfully";
                }

                if (isset($_POST['delete'])) {
                    $code = $_POST['product-id'];

                    $sql = "DELETE FROM mst_product WHERE code = :code";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':code', $code);
                    $stmt->execute();

                    echo "Product deleted successfully";
                }
            }

            $sql = 'SELECT * FROM mst_product';
            $stmt = $db->prepare($sql);
            $stmt->execute();

            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $db = null;
        } catch (Exception $e) {
            echo 'エラーが発生しました。内容: ' . h($e->getMessage());
            exit();
        }
        ?>

        <h1>商品管理</h1>

        <div class="form-container">
            <h2>商品登録</h2>
            <form method="POST" action="">
                <label for="product-name">商品名:</label>
                <input type="text" id="product-name" name="product-name" required>

                <label for="product-price">価格:</label>
                <input type="number" id="product-price" name="product-price" required>
                
                <label for="product-stock">在庫:</label>
                <input type="number" id="product-stock" name="product-stock" required>

                <label for="product-number">番号:</label>
                <input type="number" id="product-number" name="product-number" required>

                <button type="submit" name="register">登録</button>
            </form>
        </div>

        <div class="form-container">
            <h2>商品削除</h2>
            <form method="POST" action="">
                <label for="product-id">商品ID:</label>
                <input type="number" id="product-id" name="product-id" required>

                <button type="submit" name="delete">削除</button>
            </form>
        </div>

        <div class="product-list">
            <h2>商品一覧</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>商品名</th>
                        <th>価格</th>
                        <th>在庫</th>
                        <th>番号</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= h($product['code']) ?></td>
                                <td><?= h($product['name']) ?></td>
                                <td><?= h($product['price']) ?></td>
                                <td><?= h($product['stock']) ?></td>
                                <td><?= h($product['number']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5">No products found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="button-container">
            <button onclick="history.back()">戻る</button>
        </div>
    </div>
</body>
</html>
