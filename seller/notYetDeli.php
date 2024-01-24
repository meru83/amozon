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

        $htmlText = <<<HTML
        <b>商品ID</b>$product_id$color_size_id
        $productname $colorName $size<div>単価$price  ＊ $order_pieces  </div><div>計　$detail_total</div>
        HTML;

        if(!in_array($order_id,$arrayOrderId)){
            $arrayOrderId[] = $order_id;
            echo $create_atText;
            echo $htmlText;

            $create_atText = <<<END
            <br>
            <b>購入日時</b>：$create_at 
            <a href="../user_profile.php">$user_id</a> 様
            <button id="$product_id$color_size_id" type="button" style="display:block" onclick="deliComplete($product_id$color_size_id, $order_id, '$user_id')">発送完了</button>
            <div id="2$product_id$color_size_id" style="display:none">発送が完了しました</div>
            <hr>
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