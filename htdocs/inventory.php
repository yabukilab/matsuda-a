<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>在庫状況確認</title>
    <link rel="stylesheet" href="css">
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
        label {
            font-size: 20px;
            color: #555;
            display: block;
            margin-bottom: 10px;
        }
        select {
            font-size: 18px;
            padding: 10px;
            margin-bottom: 20px;
            width: 100%;
            max-width: 400px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            font-size: 20px;
            color: white;
            background-color: #007bff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>在庫状況確認システム</h1>
        <form action="check_stock.php" method="post">
            <label for="product">商品名を選択してください：</label>
            <select name="product" id="product">
                <?php
                require_once '_database_conf.php';

                try {
                    $db = new PDO($dsn, $dbUser, $dbPass);
                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    $product_query = "SELECT code, name FROM mst_product";
                    $stmt = $db->prepare($product_query);
                    $stmt->execute();

                    while ($product_row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo '<option value="' . htmlspecialchars($product_row['code'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($product_row['name'], ENT_QUOTES, 'UTF-8') . '</option>';
                    }

                    $db = null;
                } catch (Exception $e) {
                    echo 'エラーが発生しました。内容: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
                    exit();
                }
                ?>
            </select>

            <label for="vending_machine">自動販売機番号を選択してください：</label>
            <select name="vending_machine" id="vending_machine">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
            </select>
            <button type="submit">在庫状況確認</button>
        </form>
    </div>
</body>
</html>
