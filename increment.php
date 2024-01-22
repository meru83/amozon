<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

// データベース接続情報を読み込む
include 'db_config.php';

$error_message = false;

if(isset($_SESSION['user_id'])){
    try{
        $user_id = $_SESSION['user_id'];
        $piecesValue = $_POST['piecesValue'];
        $product_id = $_POST['product_id'];
        $color_size_id = $_POST['color_size_id'];

        $sql = "UPDATE cart SET pieces = ? WHERE user_id = ? && product_id = ? && color_size_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isii", $piecesValue, $user_id, $product_id, $color_size_id);
        if($stmt->execute()){
            $error_message = true;
        }
    }catch(Exception $e){
        error_log("Error in create.php: " . $e->getMessage() . PHP_EOL);
    }
}else{
    //セッションの時の処理
    try{
        $piecesValue = $_POST['piecesValue'];
        $i = $_POST['i'];
        $_SESSION['cart']['pieces'][$i] = $piecesValue;
        //error_log($_SESSION['cart']['product_id'][$i]);
        //error_log($_SESSION['cart']['pieces'][$i]);
        $error_message = true;
    }catch(Exception $e){
        error_log("Error in create.php: " . $e->getMessage() . PHP_EOL);
    }
}

$response[] = array(
    'error_message' => $error_message
);

echo json_encode($response); // JSON 形式のデータを出力
?>