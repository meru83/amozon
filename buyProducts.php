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

//ヘッダーは注文内容の確認

if(isset($_POST['buyProductId']) && isset($_POST['buyColorSize']) && isset($_POST['maxPrice'])){
    $maxPrice = $_POST['maxPrice'];
    echo "商品合計 ￥ $maxPrice";
    // print_r($_POST['buyProductId']);
    // print_r($_POST['buyColorSize']);
    // $buyProductId = $_POST['buyProductId'];
    // $lastImg = $_POST['lastImg'];

    // print_r($buyProductId);
    // print_r($lastImg);
}
?>