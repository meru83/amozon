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

// セッションからユーザーIDを取得します
session_regenerate_id(TRUE);
$seller_id = $_SESSION['seller_id'];
$sellerName = $_SESSION['sellerName'];

$productname = $_POST['productname'];
$quality = $_POST['quality'];
$view = null;
if(isset($_POST['view']) && !($_POST['view'] === "")){
    $view = $_POST['view'];
}
$big_category = null;
$category = null;
$small_category = null;
if(isset($_POST['big_category']) && !($_POST['big_category'] === "")){
    $big_category = $_POST['big_category'];
}
if(isset($_POST['category']) && !($_POST['category'] === "")){
    $category = $_POST['category'];
}
if(isset($_POST['small_category']) && !($_POST['small_category'] === "")){
    $small_category = $_POST['small_category'];
}
$colorArray = $_POST['colorArray'];
$insertId = [];

$product_insert = "INSERT INTO products(productname, view, quality, seller_id, big_category_id, category_id, small_category)
                    VALUE (?,?,?,?,?,?,?)";
$stmtProduct = $conn->prepare($product_insert);
$stmtProduct->bind_param("ssssiii", $productname, $view, $quality, $seller_id, $big_category, $category, $small_category);
try{
    if($stmtProduct->execute()){
        $lastInsertId = $conn->insert_id;
        foreach($colorArray as $key => $values){
            $size = $key;
            foreach($values as $value){
                $color = $value['color'];
                $pieces = mb_convert_kana($value['pieces'], 'n', 'UTF-8');
                $price = mb_convert_kana($value['price'], 'n', 'UTF-8');
                $insertSql = "INSERT INTO color_size(product_id, color_code, size, pieces, price) 
                            VALUE (?,?,?,?,?)";
                $insertStmt = $conn->prepare($insertSql);
                $insertStmt->bind_param("issii", $lastInsertId, $color, $size, $pieces, $price);
                if($insertStmt->execute()){
                    $insertId[] = $conn->insert_id;
                }else{
                    $error_message = "商品の登録に失敗しました。<br>";
                    header("Location:p2_insert.php?error_message=".$error_message);
                    exit();
                }
            }
        }
    }
}catch(Exception $e){
    $error_message = "商品の登録に失敗しました。<br>";
    header("Location:p2_insert.php?error_message=".$error_message);
    exit();
}
header("Location:img_insert.php?insertId=".urlencode(json_encode($insertId)));
exit();
?>