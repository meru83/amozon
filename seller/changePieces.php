<?php
include "../db_config.php";

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

$error_message = false;

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $color_size_id = $_POST['color_size_id'];
    $pieces = $_POST['pieces'];
}else{
    header("Location:seller_products.php");
    exit();
}

try{
    $sql = "UPDATE color_size SET pieces = ? WHERE color_size_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii",$pieces,$color_size_id);
    if($stmt->execute()){
        $error_message = true;
    }
}catch (Exception $e) {
    error_log("error : ".$e->getMessage());
}

$response[] = array(
    'error_message' => $error_message
);

echo json_encode($response); // JSON 形式のデータを出力
?>