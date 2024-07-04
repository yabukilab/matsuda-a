<?php

$servername = "127.0.0.1";
$username = "testuser";
$password = "";
$dbname = "mydb";

// データベース接続を作成
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続をチェック
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<?php
include '_database_conf.php';


// 商品登録処理
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $name = $_POST['product-name'];
    $price = $_POST['product-price'];
    $stock = $_POST['product-stock'];
    $number = $_POST['product-number'];

    $sql = "INSERT INTO mst_product (name, price, stock, number) VALUES ('$name', $price, $stock, $number)";
    if ($conn->query($sql) === TRUE) {
        echo "New product created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// 商品削除処理
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $code = $_POST['product-id'];

    $sql = "DELETE FROM mst_product WHERE code = $code";
    if ($conn->query($sql) === TRUE) {
        echo "Product deleted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
   
}

// 商品一覧取得
$sql = "SELECT * FROM mst_product";
$result = $conn->query($sql);
?>



<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品管理</title>
    <link rel="stylesheet" href="css">
</head>
<body>
    <div class="header"></div>
    <div class="container">
        <h1>商品管理</h1>

        <div class="form-container">
            <h2>商品登録</h2>
            <form method="POST" action="">
                <label for="product-name">商品名:</label>
                <input type="text" id="product-name" name="product-name" required>

                <label for="product-price">価格:</label>
                <input type="number" id="product-price" name="product-price" required>
        <div>
                <label for="product-stock">在庫:</label>
                <input type="number" id="product-stock" name="product-stock" required>

                <label for="product-number">番号:</label>
                <input type="number" id="product-number" name="product-number" required>

                <button type="submit" name="register">登録</button>
</div>
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
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["code"] . "</td>";
                            echo "<td>" . $row["name"] . "</td>";
                            echo "<td>" . $row["price"] . "</td>";
                            echo "<td>" . $row["stock"] . "</td>";
                            echo "<td>" . $row["number"] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No products found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="button-container">
            <button onclick="history.back()">戻る</button>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
