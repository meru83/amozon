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

if(!isset($_SESSION['seller_id'])){
    header("Location:seller_log.php");
    exit();
}

// セッションからユーザーIDを取得します
session_regenerate_id(TRUE);
$seller_id = $_SESSION['seller_id'];

if(isset($_SESSION['seller_id'])){
    $foo = <<<END
    <div style="width:100%; text-align: right; height: fit-content;">
    <form action="seller_out.php" method="post">
        <input type="submit" name="logout" class="log_out" value="ログアウト">
    </form>
    </div>
    END;
}else{
    // $foo = <<<END
    // <div class="New_log">
    //     <a href="seller.php"><div class="log_style">新規登録</div></a>
    //     <a href="seller_log.php"><div class="log_style rightM">ログイン</div></a>
    // </div>
    // END;
    header("Location:seller_log.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/notYet.css">
</head>
<body>
<div id="header" class="header">
        <div class="back"><div class="backBtn" onclick="history.back()"><img src="../img/return_left.png" style="width:100%;"></div></div>
        <h1 class="h1_White">未発送商品</h1>
        <?=$foo?>
    </div>

        <div class="Amozon-container">
        <!-- Left Side Menu -->
            <div class="left-menu">
                <div>
                    <ul class="menu-list">
                    <li class="menu-item-logo"><a href="" class="a_link"><img src="../img/cart_dake.svg" class="logo"><span class="menu-item-text-logo">Re.ReaD</span></a></li>
                    <li class="menu-item"><a href="seller_top.php" class="a_link"><img src="../img/home.png" class="logo"><span class="menu-item-text">ホーム</span></a></li>
                    <li class="menu-item"> <a href="p2_insert.php" class="a_link"><img src="../img/hensyu.png" class="logo"><span class="menu-item-text">商品情報登録</span></a></li>
                    <li class="menu-item"> <a href="seller_products.php" class="a_link"><img src="../img/meisi.png" class="logo"><span class="menu-item-text">登録商品一覧</span></a></li>
                    <!-- <li class="menu-item"> <a href=""><img src="../img/meisi.png" class="logo"><span class="menu-item-text">未発送商品</span></a></li> -->
                    <?php
                    $notYetSql = "SELECT COUNT(DISTINCT o.order_id) AS notYetDeli FROM orders o
                                LEFT JOIN orders_detail d ON (o.order_id = d.order_id)
                                LEFT JOIN products p ON (d.product_id = p.product_id)
                                WHERE o.order_status = '出荷準備中' && p.seller_id = ?";
                    $notYetStmt = $conn->prepare($notYetSql);
                    $notYetStmt->bind_param("s",$seller_id);
                    $notYetStmt->execute();
                    $notYetResult = $notYetStmt->get_result();
                    if($notYetResult && $notYetResult->num_rows > 0){
                        $notYetRow = $notYetResult->fetch_assoc();
                        $notYetDeli = $notYetRow['notYetDeli'];
                        echo <<<HTML
                        <li class="menu-item"> <a href="notYetDeli.php" class="a_link"><img src="../img/meisi.png" class="logo"><span class="menu-item-text">未発送商品</span><span class="tuuti">$notYetDeli</span></a></li>
                        HTML;
                    }else{
                        echo <<<HTML
                        <li class="menu-item"> <a href="notYetDeli.php" class="a_link"><img src="../img/meisi.png" class="logo"><span class="menu-item-text">未発送商品</span></a></li>
                        HTML;
                    }

                    if(isset($_SESSION['seller_id'])){
                        echo '<li class="menu-item"><a href="../chat_rooms.php" class="a_link"><img src="../img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>';
                    }else{
                        echo '<li class="menu-item"><a href="seller.php" class="a_link"><img src="../img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>';
                    }
                    ?>
                    <li class="menu-item"><a href="seller_home.php" class="a_link"><img src="../img/hito.png" class="logo"><span class="menu-item-text">プロフィール</span></a></li>
                    <!--log--->
                </ul>
            </div>
            <div>
                <ul class="menu-list-bottom">
                <li class="menu-item"><a href="../py/rireki.php" class="a_link"><img src="../img/gurafu.png" class="logo"><span class="menu-item-text">売上管理</span></a></li>
                    </ul>
                </div>
                <div>
                    <ul class="menu-list-bottom">
                    </ul>
                </div>
            </div>
            <div class="right-content">

<?php

$arrayOrderId = array();
$htmlText = "";
$create_atText = "";

$notYetDeliSql = "SELECT o.order_id, o.user_id, o.create_at, 
                d.product_id, d.color_size_id, d.order_pieces, d.detail_total,
                p.productname, s.color_code, s.size, s.price,
                a.address_id
                FROM orders o
                LEFT JOIN orders_detail d ON (o.order_id = d.order_id)
                LEFT JOIN products p ON (d.product_id = p.product_id)
                LEFT JOIN color_size s ON (d.color_size_id = s.color_size_id)
                LEFT JOIN address a ON (o.user_id = a.user_id)
                WHERE p.seller_id = ? && d.deli_status = false && a.default_status = true
                ORDER BY o.create_at";
$notYetDeliStmt = $conn->prepare($notYetDeliSql);
$notYetDeliStmt->bind_param("s",$seller_id);
$notYetDeliStmt->execute();
$notYetDeliResult = $notYetDeliStmt->get_result();
if($notYetDeliResult && $notYetDeliResult->num_rows > 0){
    echo "※登録商品一覧より商品IDを確認し商品の間違いがないように出荷の準備をしてください。<hr>";
    while($notYetDeliRow = $notYetDeliResult->fetch_assoc()){
        $order_id = $notYetDeliRow['order_id'];
        $user_id = $notYetDeliRow['user_id'];
        $create_at = $notYetDeliRow['create_at'];
        $product_id = $notYetDeliRow['product_id'];
        $color_size_id = $notYetDeliRow['color_size_id'];
        $order_pieces = $notYetDeliRow['order_pieces'];
        $detail_total =$notYetDeliRow['detail_total'];
        $productname = $notYetDeliRow['productname'];
        $color_code = $notYetDeliRow['color_code'];
        $colorName = getColor($conn, $color_code);
        $size = $notYetDeliRow['size'];
        $price = $notYetDeliRow['price'];
        $address_id = $notYetDeliRow['address_id'];

        if(!($detail_total === 0)){
            $htmlText = <<<HTML
            <div class='backG'><b>商品ID</b>　$product_id$color_size_id<br>
            <b>商品名</b>　$productname $colorName $size<div><b>単価</b>　　$price  ＊ $order_pieces  </div><div><b>計</b>　　　$detail_total</div>
            HTML;
        }else{
            $htmlText = <<<HTML
            以前、$user_id 様より購入されていた商品はキャンセルされました。
            HTML;
        }

        if(!in_array($order_id,$arrayOrderId)){
            $arrayOrderId[] = $order_id;
            echo $create_atText;
            echo $htmlText;

            $create_atText = <<<END
            <br>
            <b>購入日時</b>：$create_at 
            <a href="../user_profile.php?user_id=$user_id">$user_id</a> 様
            <button id="$product_id$color_size_id" class="hasso" type="button" style="display:block" onclick="deliComplete($product_id$color_size_id, $order_id, '$user_id')">発送完了</button>
            <div id="2$product_id$color_size_id" style="display:none; color:red; margin-top:24px;">発送が完了しました</div>
            </div>
            <span style="display:block; height:10px; background-color:white;"></span>
            END;
        }else{
            echo $htmlText;
        }
        // echo $htmlText."<br>";
    }
    echo $create_atText;
}else{
    //未発送の商品がないとき
}

echo '</div>';//<div class="right-content">
echo '</div>';//<div class="Amozon-container">
echo '</body>';
echo '</html>';

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
<script>
function deliComplete(productId, order_id, user_id){
    const product_idColor_size_id = document.getElementById(productId);
    const product_idColor_size_id2 = document.getElementById('2'+productId);
    // console.log(product_idColor_size_id);
    // console.log(product_idColor_size_id2);
    const formData = new FormData();
    formData.append('order_id', order_id);
    formData.append('user_id', user_id);

    const xhr = new XMLHttpRequest();
    xhr.open('POST','notYetDeliBack.php',true);
    xhr.send(formData);

    xhr.onreadystatechange = function(){
        if(xhr.readyState === 4 && xhr.status === 200){
            try {
                const response = JSON.parse(xhr.responseText);
                response.forEach(function(row) {
                    if(row.error_message === true){
                        product_idColor_size_id.style.display = "none";
                        product_idColor_size_id2.style.display = "block";
                    }else{
                        alert("正常に動作されませんでした。");
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