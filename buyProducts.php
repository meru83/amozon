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

// if(isset($_POST['buyProductId']) && isset($_POST['buyColorSize']) && isset($_POST['maxPrice']) && isset($_POST['arrayPieces']) && isset($_POST['arrayPrice'])){
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

    $cartSql = "SELECT c.pieces AS cartPieces, s.price FROM cart c
                LEFT JOIN color_size s ON (c.color_size_id = s.color_size_id)
                WHERE user_id = ?";
    $cartStmt = $conn->prepare($cartSql);
    $cartStmt->bind_param("s",$user_id);
    $cartStmt->execute();
    $cartResult = $cartStmt->get_result();
    $maxPrice = 0;
    while($cartRow = $cartResult->fetch_assoc()){
        $maxPrice += $cartRow['price'] * $cartRow['cartPieces'];
    }
    $commaPriceMax = number_format($maxPrice);
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
    <h3><p>合計 (税込) ￥ $commaPriceMax</p></h3><br>
    END;
    $totalPaySql = "SELECT total_pay FROM pay WHERE user_id = ?";
    $totalPayStmt = $conn->prepare($totalPaySql);
    $totalPayStmt->bind_param("s",$user_id);
    $totalPayStmt->execute();
    $totalPayResult = $totalPayStmt->get_result();
    $totalPayRow = $totalPayResult->fetch_assoc();
    $totalPay = $totalPayRow['total_pay'];
    if($totalPay >= $maxPrice){
        echo<<<END
        <button class="BTN" type="button" onclick="location.href='orderConfirm.php?maxPrice=$maxPrice'">注文を確定する</button><br>
        END;
    }else{
        echo<<<END
        <button class="BTN" type="button" onclick="orderAlert()">注文を確定する</button><br>
        END;
    }
    echo<<<END
    </div>
    </div>
    END;
}else{
    echo '<div class="addBox">';
    echo "<div class='touroku'>住所の登録を済ませてください。</div>";
    echo "<a href='address_insert.php'><div class='addBtn'>住所登録へ</div></a>";
    echo '</div>';
    exit();
}
// echo "<form action='orderConfirm.php' method='post' id='form'>";
// for($i = 0; $i < count($_POST['buyProductId']); $i++){
//     $product_id = $_POST['buyProductId'][$i];
//     $color_size_id = $_POST['buyColorSize'][$i];
//     $pieces = $_POST['arrayPieces'][$i];
//     $price = $_POST['arrayPrice'][$i];
//     // echo $product_id;
//     // echo $color_size_id;
//     // echo $pieces;
//     echo <<<END
//     <input type="hidden" name="arrayPieces[]" value="$pieces">
//     <input type="hidden" name="arrayPrice[]" value="$price">
//     <input type="hidden" name="buyProductId[]" value="$product_id">
//     <input type="hidden" name="buyColorSize[]" value="$color_size_id">
//     END;
// }
// echo <<<END
// <input type="hidden" name="maxPrice" value="$maxPrice">
// </form>
// END;
// }

echo "</body>";

echo<<<END
<script>
function orderAlert(){
    if(!alert("残高不足です。チャージしてください。")){
        window.location.href = "chargePay.php";
    }
}
</script>
END;

echo "</html>";
?>
