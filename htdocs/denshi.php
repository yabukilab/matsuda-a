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
            width: 45%;
            height: 300px;
            background-color: #d3d3d3;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .vending-machine img {
            max-width: 100%;
            max-height: 100%;
        }
        .vending-machine-number {
            position: absolute;
            bottom: 10px;
            background-color: rgba(0, 0, 0, 0.5);
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
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
                    <img src="https://raw.githubusercontent.com/yabukilab/matsuda-a/main/htdocs/S__33120280.jpg" alt="自動販売機3">
                    <div class="vending-machine-number">自販機番号: 3</div>
                </div>
                <div class="vending-machine">
                    <img src="https://raw.githubusercontent.com/yabukilab/matsuda-a/main/htdocs/S__33120283


