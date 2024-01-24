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
if(isset($_GET['order_id'])){
    $order_id = $_GET['order_id'];
}else{
    header("Location:buyHistory.php");
    exit();
}
$orderStatusText1 = "出荷準備中";
$orderStatusText2 = "発送済み";
$orderStatusText3 = "配達中";
$orderStatusText4 = "配達完了";
$sql = "SELECT order_status FROM orders WHERE order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i",$order_id);
$stmt->execute();
$result = $stmt->get_result();
if($result && $result->num_rows > 0){
    $updateSql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    $updateRow = $result->fetch_assoc();
    $order_status = $updateRow['order_status'];
    $updateStmt = $conn->prepare($updateSql);
    if($order_status === $orderStatusText1){
        $cssLink = '<link rel="stylesheet" href="css/style-ready.css">';//出荷準備中まで
        // $updateStmt->bind_param("si",$orderStatusText2,$order_id);
        // $updateStmt->execute();
        $notYetDeliText = "";
        $notYetDeliSql = "SELECT o.create_at, d.order_pieces, d.deli_status,
        p.productname, p.seller_id, s.color_code, s.size, s.price
                        FROM orders o
                        LEFT JOIN orders_detail d ON (o.order_id = d.order_id)
                        LEFT JOIN products p ON (d.product_id = p.product_id)
                        LEFT JOIN color_size s ON (d.color_size_id = s.color_size_id)
                        WHERE o.user_id = ? && o.order_status = '出荷準備中' && o.order_id = ?
                        ORDER BY o.create_at";
        $notYetDeliStmt = $conn->prepare($notYetDeliSql);
        $notYetDeliStmt->bind_param("si",$user_id,$order_id);
        $notYetDeliStmt->execute();
        $notYetDeliResult = $notYetDeliStmt->get_result();
        if($notYetDeliResult && $notYetDeliResult->num_rows > 0){
            while($notYetDeliRow = $notYetDeliResult->fetch_assoc()){
                $create_at = $notYetDeliRow['create_at'];
                $order_pieces = $notYetDeliRow['order_pieces'];
                $deli_status = $notYetDeliRow['deli_status'];
                $productname = $notYetDeliRow['productname'];
                $seller_id = $notYetDeliRow['seller_id'];
                $color_code = $notYetDeliRow['color_code'];
                $colorName = getColor($conn, $color_code);
                $size = $notYetDeliRow['size'];
                $price = $notYetDeliRow['price'];
            }
        }
    }else if($order_status === $orderStatusText2){
        $cssLink = '<link rel="stylesheet" href="css/style-set.css">';//発送済みまで
        $updateStmt->bind_param("si",$orderStatusText3,$order_id);
        $updateStmt->execute();
    }else if($order_status === $orderStatusText3){
        $cssLink = '<link rel="stylesheet" href="css/style-go.css">';//配達中まで
        $updateStmt->bind_param("si",$orderStatusText4,$order_id);
        $updateStmt->execute();
    }else{
        $cssLink = '<link rel="stylesheet" href="css/style-goru.css">';//配達済みまで
    }
}else{
    //ここに入ることが基本あり得ない必要あればエラー処理追加
    // error_log("a");
    // echo <<<HTML
    // <script>
    // if(!alert("")){}
    // </script>
    // HTML;
}

function getColor($conn, $color_code){
    $colorSql = "SELECT * FROM color_name
                WHERE color_code = ?";
    $colorStmt = $conn->prepare($colorSql);
    $colorStmt->bind_param("s",$color_code);
    $colorStmt->execute();
    $colorResult = $colorStmt->get_result();
    if ($row = $colorResult->fetch_assoc()) {
        $colorName = $row['colorName']; // ここで正しいカラム名を使用
        return $colorName;
    } 
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?= $cssLink ?>
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
                    <p>配達完了</p>
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
