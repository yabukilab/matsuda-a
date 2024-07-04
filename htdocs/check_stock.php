<?php
require_once '_database_conf.php';
require_once '_h.php';

try {
    // データベース接続を作成
    $db = new PDO($dsn, $dbUser, $dbPass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("SET NAMES utf8");

    // POSTリクエストの処理
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product'])) {
        $product_code = $_POST['product'];

        // 商品の在庫情報を取得するクエリ
        $query = "SELECT name, price, stock FROM mst_product WHERE code = :code";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':code', $product_code, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $product_name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
            $product_price = number_format($row['price']);
            $product_stock = htmlspecialchars($row['stock'], ENT_QUOTES, 'UTF-8');
        } else {
            $error_message = '商品情報が見つかりませんでした。';
        }
    } else {
        $error_message = '正しい方法でアクセスしてください。';
    }

    // データベース接続を閉じる
    $db = null;
} catch (Exception $e) {
    $error_message = 'エラーが発生しました。内容: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>在庫状況確認結果</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: white;
            padding: 20px 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #333;
        }
        p {
            font-size: 18px;
            color: #555;
            margin: 10px 0;
        }
        a {
            font-size: 18px;
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s;
        }
        a:hover {
            color: #0056b3;
        }
        .result-label {
            font-size: 24px;
            color: #555;
            font-weight: bold;
        }
        .result-value {
            font-size: 24px;
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>在庫状況確認結果</h1>
        <?php
        if (isset($product_name)) {
            echo '<p class="result-label">商品名: <span class="result-value">' . $product_name . '</span></p>';
            echo '<p class="result-label">単価: <span class="result-value">¥' . $product_price . '</span></p>';
            echo '<p class="result-label">在庫数: <span class="result-value">' . $product_stock . '</span></p>';
        } elseif (isset($error_message)) {
            echo '<p class="result-label">' . $error_message . '</p>';
        }
        ?>
        <br>
        <a href="index.php">TOPに戻る</a>
    </div>
</body>
</html>
