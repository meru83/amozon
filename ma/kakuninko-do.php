<!DOCTYPE html>
<html lang="ja">
<head>
    <!-- 文書の基本設定 -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>確認コード表示</title>

    <!-- スタイルの設定 -->
    <style>
        /* 全体のスタイル設定 */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        /* 上部および下部広告コンテナのスタイル */
        .ad-container {
            width: 100%;
            text-align: center;
            background-color: #e0e0e0; /* 仮の広告の背景色 */
            padding: 10px;
            box-sizing: border-box;
            margin-bottom: 20px;
        }

        /* 確認コード表示領域のスタイル */
        #confirmation-code {
            font-size: 24px;
            font-weight: bold;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            width: 80%; /* コンテンツの幅を調整 */
        }
    </style>
</head>
<body>

    <!-- 上部広告コンテナ -->
    <div class="ad-container">
        <!-- 上部広告 -->
        <div style="background-color: #ffd700; /* 仮の広告の背景色 */ padding: 10px;">
            ここに仮の広告のコンテンツが入ります
        </div>
    </div>

    <!-- 確認コード表示領域 -->
    <div id="confirmation-code">確認コード表示：2779</div>

    <!-- 下部広告コンテナ -->
    <div class="ad-container">
        <!-- 下部広告 -->
        <div style="background-color: #87ceeb; /* 仮の広告の背景色 */ padding: 10px;">
            ここに仮の広告のコンテンツが入ります
        </div>
    </div>
</body>
</html>
