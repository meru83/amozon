<?php
include "db_config.php";

// セッションを開始します
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$product_id = $_POST['product_id'];
$color_size_id = $_POST['color_size_id'];
$pieces = isset($_POST['pieces'])?$_POST['pieces']:1;


//在庫確認
$piecesSql = "SELECT pieces FROM color_size WHERE product_id = $product_id && color_size_id = $color_size_id && service_status = true";
$piecesResult = $conn->query($piecesSql);
if($piecesResult && $piecesResult->num_rows > 0){
    while($row = $piecesResult->fetch_assoc()){
        $confirmation = $row['pieces'];
        if($confirmation < $pieces){
            $error_message = "在庫が不足しています。カートに商品を登録できませんでした。";
            header("Location:cartContents.php?error_message=$error_message");
            exit();
        }
    }
}else{
    $error_message = "カートに商品を登録できませんでした。";
    header("Location:cartContents.php?error_message=$error_message");
    exit();
}

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $checkSql = "SELECT user_id FROM cart c
                LEFT JOIN color_size s ON (c.color_size_id = s.color_size_id)
                WHERE c.user_id = ? && c.product_id = ?  && c.color_size_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("sii", $user_id, $product_id, $color_size_id);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
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
        $error_message = "カートに商品を登録できませんでした。";
        header("Location:cartContents.php?error_message=$error_message");
        exit();
    }
}else{
    //セッションでカートを管理している場合
    //すでに同じ商品が登録されているか確認する処理が必要
    $flag = true;
    if(isset($_SESSION['cart'])){
        for($i = 0; $i < count($_SESSION['cart']['product_id']); $i++){
            if($_SESSION['cart']['product_id'][$i] === $product_id && $_SESSION['cart']['color_size_id'][$i] === $color_size_id){
                $flag = $i;
            }
        }
    }

    //同じ商品が登録されているか否かで処理を分ける
    if($flag === true){
        $_SESSION['cart']['product_id'][] = $product_id;
        $_SESSION['cart']['color_size_id'][] = $color_size_id;
        $_SESSION['cart']['pieces'][] = $pieces;
        header("Location:cartContents.php");
        exit();
    }else{
        $_SESSION['cart']['pieces'][$flag] += $pieces;
        header("Location:cartContents.php");
        exit();
    }
    
}
?>