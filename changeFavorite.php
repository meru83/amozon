<?php
include "db_config.php";

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ログインセッションが存在しない場合、ログインページにリダイレクトします
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

$error_message = false;

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $product_id = $_POST['product_id'];
    $color_size_id = $_POST['color_size_id'];
    $favoriteChecked = $_POST['favoriteChecked'];
}else{
    history.back();
    exit();
}

// try{
    if($favoriteChecked === true){
        $DeleteSql = "DELETE FROM favorite
                    WHERE user_id = ? && product_id = ? && color_size_id = ?";
        $DeleteStmt = $conn->prepare($DeleteSql);
        $DeleteStmt->bind_param("sii", $user_id, $product_id, $color_size_id);
        if($DeleteStmt->execute()){
            $error_message = true;
        }
    }else{
        $insertSql = "INSERT INTO favorite VALUES(?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("sii", $user_id, $product_id, $color_size_id);
        if($insertStmt->execute()){
            $error_message = true;
        }
    }
// }catch (Exception $e) {
//     error_log("error : ".$e->getMessage());
// }

$response[] = array(
    'error_message' => $error_message
);

echo json_encode($response); // JSON 形式のデータを出力
?>