<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>電子マネー確認画面</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            display: flex;
            justify-content: space-between;
        }
        .main {
            width: 75%;
            border: 1px solid #000;
        }
        .sidebar {
            width: 20%;
            border: 1px solid #000;
            padding: 10px;
        }
        .header {
            background-color: #0078d4;
            color: #fff;
            padding: 10px;
            text-align: center;
        }
        .vending-machines {
            display: flex;
            justify-content: space-around;
            padding: 20px;
        }
        .vending-machine {
            width: 30%;
            height: 150px;
            background-color: #d3d3d3;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .top-button {
            display: block;
            margin: 0 auto;
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #fff;
            border: 1px solid #000;
            text-align: center;
            cursor: pointer;
            text-decoration: none;
            color: #000;
        }
        .vending-machine img {
            max-width: 100%;
            max-height: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="main">
            <div class="header">
                電子マネー確認画面
            </div>
            <div class="vending-machines">
                <div class="vending-machine">
                    <img src="https://github.com/yabukilab/matsuda-a/blob/main/htdocs/S__33120280.jpg" alt="自動販売機1">
                </div>
                <div class="vending-machine">
                    <img src="https://github.com/yabukilab/matsuda-a/blob/main/htdocs/S__33120282.jpg" alt="自動販売機2">
                </div>
                <div class="vending-machine">
                    <img src="https://github.com/yabukilab/matsuda-a/blob/main/htdocs/S__33120283.jpg" alt="自動販売機3">
                </div>
            </div>
            <a href="index.php">TOPに戻る</a>
        </div>
        <div class="sidebar">
            <h3>実装にあたっての注意事項</h3>
            <ol>
                <li>対応している電子マネーの内容も記載する</li>
            </ol>
        </div>
    </div>
</body>
</html>
