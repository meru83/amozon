<?php
header('Content-Type: application/json'); // JSONレスポンスであることを指定

include "../db_config.php";

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

$product_id = $_POST['product_id'];
$size = $_POST['size'];
$color = $_POST['color'];
$pieces = $_POST['pieces'];
$price = $_POST['price'];

$error_message = "";

try{
    //selectで同じ構成があったらinsertしないようにする
    //errorをresponseで返す
    $insertSql = "INSERT INTO color_size_id(product_id, color_code, size, pieces, price) VALUES(?,?,?,?,?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("issii",$product_id,$color,$size,$pieces,$price);
    $insertStmt->execute();
}catch(Exception $e){
    error_log("error : " . $e->getMessage() , 3, 'error.log');

    $error_message = "カラー・サイズの追加に失敗しました。";
    $response[] = array(
        'error_message' => $error_message;
    );
}

echo json_encode($response); // JSON 形式のデータを出力
?>