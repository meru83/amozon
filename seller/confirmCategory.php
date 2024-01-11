<?php
header('Content-Type: application/json'); // JSONレスポンスであることを指定
include "../db_config.php";

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $b_id = isset($_POST['big_category'])?$_POST['big_category']:null;
    $c_id = isset($_POST['category'])?$_POST['category']:null;
    $s_id = isset($_POST['small_category'])?$_POST['small_category']:null;

    $cateArray = array(
        'message' => false;
    );

    $sql = "UPDATE products SET big_category_id = $b_id, category = $c_id, small_category =$s_id
            WHERE product_id = $product_id";
    if($conn->query($sql)){
        $cateArray[] = array(
            'message' => true;
        );
    }
    echo json_encode($cateArray);
}
$conn->close();
?>