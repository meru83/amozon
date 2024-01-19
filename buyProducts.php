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

isset($_SESSION['user_id']){
    $user_id = $_SESSION['user_id'];
}

if(isset($_POST['buyProductId']) && isset($_POST['lastImg'])){
    $buyProductId = $_POST['buyProductId'];
    $lastImg = $_POST['lastImg'];
}
?>