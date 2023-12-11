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

if(isset($_POST['product_id'])){
    $product_id = intval($_POST['product_id']);
}else{
    header("Location:seller_products.php");
    exit();
}

try{
    //名前とって消した商品をわかりやすくしてもいい。
    // $getNameSql = "SELECT productname FROM products WHERE product_id = $product_id";
    // $getNameResult = $conn->query($getNameSql);

    //orders_detailとの外部キー制約の兼ね合いでstatusで管理するようにするべき。
    //上記の場合、商品の検索時などの時に検索にかからないようにする修正が必要である。
    $getCSIdSql = "SELECT color_size_id FROM color_size WHERE product_id = $product_id";
    $getCSIdResult = $conn->query($getCSIdSql);
    if($getCSIdResult){
        while($row = $getCSIdResult->fetch_assoc()){
            $color_size_id = $row['color_size_id'];
            try{
                $pImgDelete = "DELETE FROM products_img WHERE color_size_id = $color_size_id RETURNING *";
                $deleteResult = $conn->query($pImgDelete);
                while($row = $deleteResult->fetch_assoc()){
                    $img_url = $row['img_url'];
                    $relativePath = "p_img/$img_url";
                    if(file_exists($relativePath)){
                        unlink($relativePath);
                    }
                }
            }catch(Exception $e) {
                error_log('error : '. $e->getMessage(), 3, 'error.log');
            }
        }
        $deleteColorSize = "DELETE FROM color_size WHERE product_id = $product_id";
        $conn->query($deleteColorSize);
        $deleteProduct = "DELETE FROM products WHERE product_id = $product_id";
        $conn->query($deleteProduct);   
    }else {
        // $getCSIdResultがNULLの場合のエラー処理
        error_log('Error in query: ' . $conn->error, 3, 'error.log');
    }
}catch(Exception $e){
    error_log('error : '. $e->getMessage(), 3, 'error.log');
}
?>