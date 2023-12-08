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

//セッションの時の処理
$piecesValue = $_POST['piecesValue'];
$i = $_POST['i'];
$_SESSION['cart']['pieces'][$i] = $piecesValue;
//error_log($_SESSION['cart']['product_id'][$i], 3);
//error_log($_SESSION['cart']['pieces'][$i], 3);
?>