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
        //出荷準備中の場合
        $cssLink = '<link rel="stylesheet" href="css/style-ready.css">';//出荷準備中まで
        // $updateStmt->bind_param("si",$orderStatusText2,$order_id);
        // $updateStmt->execute();
        $notYetDeliSql = "SELECT o.total, o.create_at, d.product_id, d.color_size_id, d.order_pieces, d.detail_total, d.deli_status,
        p.productname, p.seller_id, s.color_code, s.size, s.price
                        FROM orders o
                        LEFT JOIN orders_detail d ON (o.order_id = d.order_id)
                        LEFT JOIN products p ON (d.product_id = p.product_id)
                        LEFT JOIN color_size s ON (d.color_size_id = s.color_size_id)
                        WHERE o.user_id = ? && o.order_status = '出荷準備中' && o.order_id = ? && NOT d.detail_total = 0
                        ORDER BY o.create_at";
        $notYetDeliStmt = $conn->prepare($notYetDeliSql);
        $notYetDeliStmt->bind_param("si",$user_id,$order_id);
        $notYetDeliStmt->execute();
        $notYetDeliResult = $notYetDeliStmt->get_result();
        if($notYetDeliResult && $notYetDeliResult->num_rows > 0){
            $totalText = "";//合計金額
            $htmlText = "";//商品情報
            $count = 0;
            $flag = true;
            while($notYetDeliRow = $notYetDeliResult->fetch_assoc()){
                //order_idはgetで取得
                $total = $notYetDeliRow['total'];
                $create_at = $notYetDeliRow['create_at'];
                $order_pieces = $notYetDeliRow['order_pieces'];
                $product_id = $notYetDeliRow['product_id'];
                $color_size_id = $notYetDeliRow['color_size_id'];
                $deli_status = $notYetDeliRow['deli_status'];
                $detail_total = $notYetDeliRow['detail_total'];
                $productname = $notYetDeliRow['productname'];
                $seller_id = $notYetDeliRow['seller_id'];
                $color_code = $notYetDeliRow['color_code'];
                $colorName = getColor($conn, $color_code);
                $size = $notYetDeliRow['size'];
                $price = $notYetDeliRow['price'];
                $deli_status_flag = true;

                if(!$deli_status){
                    //未発送
                    $deli_status_flag = false;
                    $htmlText .= <<<END
                    <div id="$count">
                    <a href="productsDetail.php?product_id=$product_id&color_size_id=$color_size_id">$productname $colorName $size </a> <div>単価$price  ＊ $order_pieces  </div><div>計　$detail_total</div>
                    <button class="huzai_button" type="button" onclick="cancelButton($order_id,$product_id,$color_size_id,$detail_total,$count)">注文をキャンセル</button>
                    <br>
                    </div>
                    <br>
                    END;
                }else if($deli_status){
                    //発送済み
                    $htmlText .= <<<END
                    <div id="$count">
                    <a href="productsDetail.php?product_id=$product_id&color_size_id=$color_size_id">$productname $colorName $size </a> <div>単価$price  ＊ $order_pieces  </div><div>計　$detail_total</div>
                    <button class="huzai_button" type="button">発送済み</button>
                    <br>
                    </div>
                    <br>
                    END;
                }

                if($flag){
                    $totalText = <<<END
                    <br>
                    <p>購入日時 $create_at</p><br>
                    <p>合計金額 $total</p>
                    END;
                    $flag = false;
                }
            }
            if($deli_status_flag){
                $cssLink = '<link rel="stylesheet" href="css/style-set.css">';//発送済みまで
                $updateStmt->bind_param("si",$orderStatusText2,$order_id);
                $updateStmt->execute();
            }
        }else{
            $cssLink = '<link rel="stylesheet" href="css/style-set.css">';//発送済みまで
            $updateStmt->bind_param("si",$orderStatusText2,$order_id);
            $updateStmt->execute();
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
        // 配達済みのお互いが評価できるようにしたい
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
    <link rel="stylesheet" href=css/Amozon_huzai_insta.css>
    <?= $cssLink ?>
    <title>荷物の配達状況確認</title>
</head>
<body>

    <div id="header" class="header">
        <div class="back"><div class="backBtn" onclick="history.back()"><img src="../img/return_left.png" style="width:100%;"></div></div>
        <h1 class="h1_White">荷物の配達状況確認</h1>
        <div style="width: 100%;"></div>
    </div>
    
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
    <?php
    if(isset($htmlText) && isset($totalText)){
        echo <<<HTML
        <div class="textbox">
        <h3>購入明細</h3>
        ※発送済み商品は注文のキャンセルができません。<br><br>
        <!-- ＊＊＊＊＊＊＊＊注意＊＊＊＊＊＊＊＊＊ -->
        <!-- ※発送済み商品は注文のキャンセルができません。は赤色表示 -->
        $htmlText $totalText
        </div>
        HTML;
    }
    ?>

<script>
function cancelButton(order_id,product_id,color_size_id,detail_total,count){
    const formData = new FormData();
    formData.append('order_id',order_id);
    formData.append('color_size_id',color_size_id);
    formData.append('product_id',product_id);
    formData.append('detail_total',detail_total);

    const xhr = new XMLHttpRequest();
    xhr.open('POST','huzaiBack.php',true);
    xhr.send(formData);

    xhr.onreadystatechange = function(){
        if(xhr.readyState === 4 && xhr.status === 200){
            try {
                const response = JSON.parse(xhr.responseText);
                response.forEach(function(row) {
                    if(row.error_message){
                        var divElement = document.getElementById(count);
                        console.log(divElement);
                        divElement.innerHtml = "注文がキャンセルされました";
                    }else{
                        alert("注文のキャンセルができませんでした。もう一度お試しください。");
                    }
                });
            } catch (error) {
                console.error("Error parsing JSON response:", error);
                alert("リクエストが失敗しました。");
            }
        }
    }
}
</script>
</body>
</html>
