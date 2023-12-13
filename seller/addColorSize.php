<?php
// header('Content-Type: application/json'); // JSONレスポンスであることを指定

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
// error_log("1:".$product_id." 2:".$size." 3:".$color." 4:".$pieces." 5:".$price);
$error_message = true;

try{
    //selectで同じ構成があったらinsertしないようにする
    //errorはresponseで返す
    $selectSql = "SELECT product_id FROM color_size WHERE product_id = ? && size = ? && color_code = ?";
    $selectStmt = $conn->prepare($selectSql);
    $selectStmt->bind_param("iss", $product_id, $size, $color);
    $selectStmt->execute();
    $selectResult = $selectStmt->get_result();
    if($selectResult->num_rows == 0){
        $insertSql = "INSERT INTO color_size(product_id, color_code, size, pieces, price) VALUES(?,?,?,?,?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("issii",$product_id,$color,$size,$pieces,$price);
        $insertStmt->execute();
    }else{
        $colorName = getColor($conn, $color);
        $error_message = $colorName . "-" . $size . "は既に存在します。";
    }
}catch(Exception $e){
    error_log("error : " . $e->getMessage());

    $error_message = "カラー・サイズの追加に失敗しました。";
}
$response[] = array(
    'error_message' => $error_message
);

echo json_encode($response); // JSON 形式のデータを出力

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