<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

// データベース接続情報を読み込む
include 'db_config.php';

$piecesValue = $_POST['piecesValue'];

if(isset($_SESSION['user_id'])){
    try{
        $user_id = $_SESSION['user_id'];
        $product_id = $_POST['product_id'];
        $color_size_id = $_POST['color_size_id'];

        $sql = "UPDATE cart SET pieces = ? WHERE user_id = ? && product_id = ? && color_size_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isii", $piecesValue, $user_id, $product_id, $color_size_id);
        $stmt->execute();
    }catch(Exception $e){
        error_log("Error in create.php: " . $e->getMessage() . PHP_EOL);
    }
}else{
    //セッションの時の処理
    $i = $_POST['i'];

    $_SESSION['cart']['pieces'][$i] = $piecesValue;
    //error_log($_SESSION['cart']['product_id'][$i]);
    //error_log($_SESSION['cart']['pieces'][$i]);
}
?>