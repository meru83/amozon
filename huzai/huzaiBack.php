<?php
// セッションを開始します
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ログインセッションが存在しない場合、ログインページにリダイレクトします
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php"); // ログインページのURLにリダイレクト
    exit(); // リダイレクト後、スクリプトの実行を終了
}

session_regenerate_id(TRUE);
$user_id = $_SESSION['user_id'];

include "../db_config.php";

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

$error_message = false;
$flag = false;
$flag2 = false;
$flag3 = false;


if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $order_id = $_POST['order_id'];
    $product_id = $_POST['product_id'];
    $color_size_id = $_POST['color_size_id'];
    $detail_total = $_POST['detail_total'];
}else{
    header("Location:seller_products.php");
    exit();
}

try{
    //sql
    $sql = "UPDATE pay_history2 SET pay_pay = pay_pay - $detail_total WHERE order_id = $order_id";
    if($conn->query($sql)){
        $flag = true;
    }
    if($flag){
        $sql2 = "UPDATE pay SET total_pay = total_pay + $detail_total WHERE user_id = ?";
        $stmt = $conn->prepare($sql2);
        $stmt->bind_param("s",$user_id);
        if($stmt->execute()){
            $flag2 = true;
        }
    }
    if($flag2){
        //updateにする
        $sql3 = "UPDATE orders_detail SET detail_total = 0 WHERE order_id = $order_id && product_id = $product_id && color_size_id = $color_size_id";
        if($conn->query($sql3)){
            $flag3 = true;
        }
    }
    if($flag3){
        $sql4 = "UPDATE orders SET total = total - $detail_total WHERE order_id = $order_id";
        if($conn->query($sql4)){
            $error_message = true;
        }
    }
}catch (Exception $e) {
    error_log("error : ".$e->getMessage());
}

$response[] = array(
    'error_message' => $error_message
);

echo json_encode($response); // JSON 形式のデータを出力
?>