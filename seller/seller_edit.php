<?php
include '../db_config.php';

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// セッションからユーザーIDを取得します
session_regenerate_id(TRUE);


$seller_id = isset($_SESSION['seller_id'])?$_SESSION['seller_id']:null;
if(is_null($seller_id)){
    header("Location:seller_log.php");
    exit();
}

$seller_name = $_SESSION['sellerName'];

$selectSql = "SELECT s.sellerName, 
                FROM seller s";

                
                "SELECT p.product_id, p.productname, p.view, p.create_at, p.quality, s.color_code, s.size, b.big_category_name, c.category_name, sc.small_category_name, i.img_url 
                FROM products p
                LEFT JOIN color_size s ON (p.product_id = s.product_id)
                LEFT JOIN big_category b ON (p.big_category_id = b.big_category_id)
                LEFT JOIN category c ON (p.category_id = c.category_id)
                LEFT JOIN small_category sc ON (p.small_category = sc.small_category)
                LEFT JOIN products_img i ON (s.color_size_id = i.color_size_id)
                WHERE p.seller_id = ? && s.service_status = true";

