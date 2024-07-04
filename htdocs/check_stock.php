<?php
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "product_reviews";

// データベース接続を作成
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続をチェック
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 文字セットをUTF-8に設定
$conn->set_charset("utf8");
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
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product'])) {
            $product_code = $_POST['product'];

            // 商品の在庫情報を取得するクエリ
            $query = "SELECT name, price, stock FROM mst_product WHERE code = '$product_code'";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                echo '<p class="result-label">商品名: <span class="result-value">' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '</span></p>';
                echo '<p class="result-label">単価: <span class="result-value">¥' . number_format($row['price']) . '</span></p>';
                echo '<p class="result-label">在庫数: <span class="result-value">' . $row['stock'] . '</span></p>';
            } else {
                echo '<p class="result-label">商品情報が見つかりませんでした。</p>';
            }

            mysqli_close($conn);
        } else {
            echo '<p class="result-label">正しい方法でアクセスしてください。</p>';
        }
        ?>
        <br>
        <a href="index.php">TOPに戻る</a>
    </div>
</body>
</html>
