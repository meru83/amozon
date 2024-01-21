<?php
include "db_config.php";

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

// セッションを開始します
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ログインセッションが存在しない場合、ログインページにリダイレクトします
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // ログインページのURLにリダイレクト
    exit(); // リダイレクト後、スクリプトの実行を終了
}

$user_id = $_SESSION['user_id'];

$error_message = false;

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $chargePrice = $_POST['chargePrice'];
    $bank = $_POST['bank'];
}else{
    header("Location:chargePay.php");
    exit();
}

try{
    $selectSql = "SELECT user_id FROM pay WHERE user_id = ?";
    $selectStmt = $conn->prepare($selectSql);
    $selectStmt->bind_param("s",$user_id);
    $selectStmt->execute();
    $selectResult = $selectStmt->get_result();
    if($selectResult && $selectResult->num_rows > 0){
        $sql = "UPDATE pay SET total_pay = total_pay + ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si",$chargePrice,$user_id);
    }else{
        $sql = "INSERT INTO pay VALUES(?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $user_id, $chargePrice);
    }
    if($stmt->execute()){
        $insertSql = "INSERT INTO pay_history (user_id, charge_pay, bank) VALUES(?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("sis", $user_id, $chargePrice, $bank);
        if($insertStmt->execute()){
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