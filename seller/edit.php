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

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $product_id = $_POST['product_id'];
    $colorSize = $_POST['colorSize'];
    $colorSizeArray = preg_split('/[|]+/u',$colorSize);
    $color_code = $colorSizeArray[0];
    $colorName = getColor($conn, $color_code);
    $size = $colorSizeArray[1];
}else{
    header("Location:seller_products.php");
    exit();
}

$selectSql = "SELECT p.productname, p.create_at, s.pieces, s.price, s.color_size_id, i.img_url 
                FROM products p
                LEFT JOIN color_size s ON (p.product_id = s.product_id)
                LEFT JOIN products_img i ON (s.color_size_id = i.color_size_id)
                WHERE p.product_id = ? && s.color_code = ? && s.size = ?";
$selectStmt = $conn->prepare($selectSql);
$selectStmt->bind_param("iss",$product_id,$color_code,$size);
$selectStmt->execute();
$selectResult = $selectStmt->get_result();

if($selectResult && $selectResult->num_rows > 0){
    while($row = $selectResult->fetch_assoc()){
        $productname = $row['productname'];
        $create_at = $row['create_at'];
        //$colorName
        $pieces = $row['pieces'];
        $price = $row['price'];
        $color_size_id = $row['color_size_id'];
        $img_url = $row['img_url'];
    }
}

function getColor($conn, $color_code){
    $colorSql = "SELECT * FROM color_name
                WHERE color_code = ?";
    $colorStmt = $conn->prepare($colorSql);
    $colorStmt->bind_param("s",$color_code);
    $colorStmt->execute();
    $colorResult = $colorStmt->get_result();
    if ($row = $colorResult->fetch_assoc()) {
        $colorName = $row['colorName']; // ここで正しいカラム名を使用
        return $colorName;
    } 
}
?>