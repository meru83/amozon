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

// セッションからユーザーIDを取得します
session_regenerate_id(TRUE);
$seller_id = $_SESSION['seller_id'];

include "../db_config.php";

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

$error_message = true;
// $error_message = false;

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $order_id = $_POST['order_id'];
    $user_id = $_POST['user_id'];
    // error_log($user_id);
    // error_log($order_id);
}

try{
    $sql = "UPDATE orders_detail SET deli_status = true WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i",$order_id);
    if($stmt->execute()){
        $error_message = true;
    }
    $selectSql = "SELECT room_id FROM chatrooms WHERE user_id = ? && seller_id = ?";
    $selectStmt = $conn->prepare($selectSql);
    $selectStmt->bind_param("ss",$user_id,$seller_id);
    $selectStmt->execute();
    $selectResult = $selectStmt->get_result();
    if($selectResult && $selectResult->num_rows > 0){
        $selectRow = $selectResult->fetch_assoc();
        $room_id = $selectRow['room_id'];
        $message_text = "商品の発送が完了しました！";
        $insertSql = "INSERT INTO messages (room_id, seller_id, message_text) VALUES(?,?,?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("iss",$room_id,$seller_id,$message_text);
        $insertStmt->execute();
    }
}catch (Exception $e) {
    error_log("error : ".$e->getMessage());
}

$response[] = array(
    'error_message' => $error_message
);

echo json_encode($response); // JSON 形式のデータを出力
?>