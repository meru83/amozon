<?php
// セッションを開始します
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include "db_config.php";

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

$error_message = false;

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $verification_code = intval($_POST['verification_code']);
    $verificationCode = intval($_POST['verificationCode']);

    try{
        if($verificationCode === $verification_code){
            // 認証成功
            //ユーザー情報をセッションに保存
            //今はチャットルームの一覧に飛ぶけどトップに飛ばすようにする
            $_SESSION['user_id'] = $_POST['user_id'];
            $_SESSION['username'] = $_POST['username'];
            if(isset($_SESSION['cart'])){
                for($i = 0; $i < count($_SESSION['cart']['product_id']); $i++){
                    if($_SESSION['cart']['product_id'][$i] !== null){
                        $product_id = $_SESSION['cart']['product_id'][$i];
                        $color_size_id = $_SESSION['cart']['color_size_id'][$i];
                        $pieces = $_SESSION['cart']['pieces'][$i];
                        
                        $insertSql = "INSERT INTO cart(user_id, product_id, color_size_id, pieces) VALUES(?,?,?,?)";
                        $insertStmt = $conn->prepare($insertSql);
                        $insertStmt->bind_param("siii",$_SESSION['user_id'], $product_id, $color_size_id, $pieces);
                        $insertStmt->execute();
                    }
                }
                $_SESSION['cart'] = null;
            }
            $error_message = true;
        }
    }catch (Exception $e) {
        error_log("error : ".$e->getMessage());
    }
}

    
$response[] = array(
    'error_message' => $error_message
);

echo json_encode($response); // JSON 形式のデータを出力
?>