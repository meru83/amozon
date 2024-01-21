<?php
include "db_config.php";

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

echo <<<HTML
<!DOCTYPE html>
<html lang="ja">
<head>
<link rel="stylesheet" href="css/buyproducts.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>注文内容の確認</title>
</head>
<body>
<div id="header" class="header">
<div class="space"></div>
<h1 class="h1_White">注文内容の確認</h1>
<div class="space"></div>
</div>
HTML;

//ヘッダーは「注文内容の確認」

if(isset($_POST['buyProductId']) && isset($_POST['buyColorSize']) && isset($_POST['maxPrice'])){
    $addressSql = "SELECT * FROM address WHERE user_id = ? && default_status = 1";
    $addressStmt = $conn->prepare($addressSql);
    $addressStmt->bind_param("s", $user_id);
    $addressStmt->execute();
    $addressResult = $addressStmt->get_result();
    if($addressResult && $addressResult->num_rows > 0){
        $addressRow = $addressResult->fetch_assoc();
        $post_code = $addressRow['post_code'];
        $postBefore = substr($post_code, 0,3);//前3桁
        $postAfter = substr($post_code, -4);//後ろ4桁
        $post_code2 = $postBefore." - ".$postAfter;
        $prefectures = $addressRow['prefectures'];
        $city = $addressRow['city'];
        // $city_kana = $addressRow['city_kana'];
        $tyou = $addressRow['tyou'];//登録されたままの表示　例：１丁目１番１号、2-21-4 どちらもそのまま出力
        $room_number = isset($addressRow['room_number'])?$addressRow['room_number']:null;
        $addressname = $addressRow['addressname'];
        $means = $addressRow['means'];
        if($means === true){
            $means_result = "対面で受け渡し";
        }else{
            $means_result = "置き配";
        }
        $maxPrice = $_POST['maxPrice'];
        echo <<<END
        <div class="ALL">
        <div class="box">
        <div class="title top"><p>お届け先</p></div>
        <div class="parent">
        <div><p>$addressname 様</p></div>
        <div><p>〒$post_code</p></div>
        <div><p>$prefectures $city $tyou $room_number</p></div>
        </div>
        <div class="title"><p>受取り方法</p></div>
        <div><p>$means_result</p></div>
        <div class="title"><p>支払い方法</p></div><p>readPAY <a href="chargePay.php">チャージ</a></p><br>
        <h3><p>合計 (税込) ￥ $maxPrice</p></h3><br>
        <button class="BTN" type="button" onclick="orderButton()">注文を確定する</button><br>
        </div>
        </div>
        END;
    }else{
        echo "住所の登録を済ませてください。";
        echo "<a href='address_insert.php'>住所登録へ</a>";
        exit();
    }
    echo "<form action='orderConfirm.php' method='post' id='form'>";
    for($i = 0; $i < count($_POST['buyProductId']); $i++){
        $product_id = $_POST['buyProductId'][$i];
        $color_size_id = $_POST['buyColorSize'][$i];
        // echo $product_id;
        // echo $color_size_id;
        echo <<<END
        <input type="hidden" name="buyProductId[]" value="$product_id">
        <input type="hidden" name="buyColorSize[]" value="$color_size_id">
        END;
    }
    echo "</form>";
}

echo "</body>";
echo "</html>";
?>
<script>
function orderButton(){
    var form = document.getElementById('form');
    form.submit();
}
</script>