<?php
include "db_config.php";

// セッションを開始します
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$product_id = $_POST['product_id'];
$color_size_id = $_POST['color_size_id'];
$pieces = isset($_POST['pieces'])?$_POST['pieces']:1;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $checkSql = "SELECT user_id, product_id, color_size_id FROM cart
                WHERE cart.user_id = '$user_id' && cart.product_id = $product_id  && cart.color_size_id = $color_size_id";
    $checkResult = $conn->query($checkSql);
    if($checkResult && $checkResult->num_rows === 0){
        $insertSql = "INSERT INTO cart(user_id, product_id, color_size_id, pieces)
                        VALUE(?,?,?,?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("siii",$user_id,$product_id,$color_size_id,$pieces);
        if($insertStmt->execute()){
            header("Location:cartContents.php");
            exit();
        }
    }else{
        $error_message = "カートに商品を登録するのに失敗しました。";
        header("Location:cartContents.php?error_message=$error_message");
    }
}else{
    //すでに同じ商品が登録されているか確認する処理が必要
    $_SESSION['cart']['product_id'][] = $product_id;
    $_SESSION['cart']['color_size_id'][] = $color_size_id;
    $_SESSION['cart']['pieces'][] = $pieces;

    header("Location:cartContents.php");
    exit();
}
?>