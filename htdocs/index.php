<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>自動販売機在庫システム</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .header {
            width: 100%;
            background-color: #007BFF;
            padding: 20px 0;
        }
        .header h1 {
            color: white;
            margin: 0;
        }
        .main {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }
        .box {
            border: 2px solid black;
            padding: 20px;
            width: 150px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }
        .box:hover {
            background-color: #f0f0f0;
            transform: scale(1.05);
        }
        a {
            text-decoration: none;
            color: black;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>自動販売機在庫システム</h1>
        </div>
        <div class="main">
            <div class="box"><a href="reviews.php">批評・閲覧・在庫削除</a></div>
            <div class="box"><a href="touroku.php">商品登録・削除</a></div>
            <div class="box"><a href="inventory.php">在庫状況</a></div>
            <div class="box"><a href="denshi.php">電子マネー有無</a></div>
        </div>
    </div>
</body>
</html>
