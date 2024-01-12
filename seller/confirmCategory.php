<?php
include "../db_config.php";

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

$error_message = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $b_id = isset($_POST['big_category'])?$_POST['big_category']:null;
    $c_id = isset($_POST['category'])?$_POST['category']:null;
    $s_id = isset($_POST['small_category'])?$_POST['small_category']:null;
}else{
    header("Location:seller_products.php");
    exit();
}

try {    
    $sql = "UPDATE products SET big_category_id = ?, category_id = ?, small_category = ?
    WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiii",$b_id, $c_id, $s_id, $product_id);
    if($stmt->execute()){
        $error_message = true;
    }
} catch (Exception $e) {
    error_log("error : ".$e->getMessage());
}

$response[] = array(
    'error_message' => $error_message
);

echo json_encode($response);
?>