<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style-ready.css"> 出荷準備中まで
    <!-- <link rel="stylesheet" href="css/style-set.css"> 発送済みまで -->
    <!-- <link rel="stylesheet" href="css/style-go.css">  配達中まで -->
    <!-- <link rel="stylesheet" href="css/style-goru.css"> 配達済みまで -->
    <title>荷物の配達状況確認</title>
</head>
<body>

    <header >
        <h1>荷物の配達状況確認</h1>
    </header>

    <section>
        <h2>荷物の詳細</h2>
        <p>配送業者: サンプルエクスプレス</p>
        <p>配送予定日: 2023年04月28日</p>
        <div class="status-box">
            <p>配達状況:</p>
            <div class="status-container">
                <label class="status">
                    <div class="circle1"></div>
                    <p>出荷準備中</p>
                    <div class="line1"></div>
                </label>
                <label class="status">
                    <div class="circle2"></div>
                    <p>発送済み</p>
                    <div class="line2"></div>
                </label>
                <label class="status3">
                    <div class="circle3"></div>
                    <p>配達中</p>
                </label>
                <label class="status4">
                    <div class="circle4"></div>
                    <p>配達済み</p>
                </label>
            </div>
            <p>荷物番号: 1234567890</p>
        </div>
        <p>詳しい配達状況を確認するには以下のボタンをクリックしてください。</p>
        <a href="" class="button">配達状況を確認する</a>
    </section>

    <script>
function checked() {
    // ボタンを無効化
    document.getElementById("checked").disabled = true;

    // ここにチャージの処理を追加

    // 例: 5秒後にボタンを再度有効化
    setTimeout(function() {
        document.getElementById("checked").disabled = false;
    }, 5000);
}
    </script>
</body>
</html>
