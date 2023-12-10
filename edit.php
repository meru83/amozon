<?php
// セッションを開始します
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ログインセッションが存在しない場合、ログインページにリダイレクトします
if (!isset($_SESSION['seller_id'])) {
    header("Location: seller_log.php"); // ログインページのURLにリダイレクト
    exit(); // リダイレクト後、スクリプトの実行を終了
}

include "../db_config.php";

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

// セッションからユーザーIDを取得します
session_regenerate_id(TRUE);
$seller_id = $_SESSION['seller_id'];
$seller_name = $_SESSION['sellerName'];

if(isset($_POST['$product_id'])){
    $product_id = $_POST['product_id'];
}else{
    header("Location:seller_products.php");
    exit();
}

$selectSql = "SELECT p.productname, p.view, p.create_at, p.seller_id, p.quality, s.color_code, s.size, s.pieces, s.price, s.color_size_id, b.big_category_name, c.category_name, small_category_name, i.img_url 
                FROM products p
                LEFT JOIN color_size s ON (p.product_id = s.product_id)
                LEFT JOIN big_category b ON (p.big_category_id = b.big_category_id)
                LEFT JOIN category c ON (p.category_id = c.category_id)
                LEFT JOIN small_category sc ON (p.small_category = sc.small_category)
                LEFT JOIN products_img i ON (s.color_size_id = i.color_size_id)
                WHERE p.product_id = ?";
$selectStmt = $conn->prepare($selectSql);
$selectStmt->bind_param("i",$product_id);
$selectStmt->execute()
$selectResult = $selectStmt->get_result();

if(){}
?>