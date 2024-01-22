<?php
include "../db_config.php";

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

// セッションを開始します
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
}else{
    header("Location:login.php");
    exit();
}

$sql = "SELECT create_at FROM orders WHERE user_id = ? && create_at < CURRENT_TIMESTAMP - INTERVAL 2 MINUTE";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$user_id);
$stmt->execute();
$result = $stmt->get_result();
if($result && $result->num_rows > 0){
    // 現在の日時を取得
    $currentDateTime = new DateTime();
    while($row = $result->fetch_assoc()){
        $create_at = $row['create_at'];
        $createDateTime = new DateTime($create_at);
        $timeGap = $currentDateTime->diff($createDateTime);
        if($timeGap->days > 0){
            echo "過去の時間です。";
        }
    }
}else{
    error_log("5");
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style-ready.css"> <!-- 出荷準備中まで -->
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
        <!-- <p>詳しい配達状況を確認するには以下のボタンをクリックしてください。</p>
        <a href="" class="button">配達状況を確認する</a> -->
        <p id="status1">現在、出荷準備中です。</p>
        <p id="status2">現在、発送済みです。</p>
        <p id="status3">現在、配達中です。</p>
        <p id="status4">配達済みです。商品が届かない場合は不在票が投函されていないかご確認ください。</p>
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
