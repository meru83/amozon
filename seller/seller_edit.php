<?php
include '../db_config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

$seller_id = isset($_SESSION['seller_id'])?$_SESSION['seller_id']:null;
if(is_null($seller_id)){
    header("Location:seller_log.php");
    exit();
}

