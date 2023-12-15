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

$error_message = false;

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $color_size_id = $_POST['color_size_id'];
    $imgFile = $_FILES['imgFile']['tmp_name'];
}else{
    header("Location:seller_products.php");
    exit();
}
// $imgFileLength = count($imgFile);
// error_log($imgFileLength);

// try{
//     $sql = "INSERT INTO products_img(color_size_id, img_url) VALUES(?, ?)";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("is",$color_size_id, );
//     if($stmt->execute()){
//         $error_message = true;
//     }
// }catch (Exception $e) {
//     error_log("error : ".$e->getMessage());
// }

// $response[] = array(
//     'error_message' => $error_message
// );

// echo json_encode($response); // JSON 形式のデータを出力
?>