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

// トランザクションを開始
$conn->begin_transaction();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['room_id'])) {
    $room_id = $_POST['room_id'];
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $seller_id = isset($_SESSION['seller_id']) ? $_SESSION['seller_id'] : null;
    $message_text = null;
    if(isset($_POST['message_text']) && !($_POST['message_text'] === "")){
        $message_text = $_POST['message_text'];
    }
    $image_data = isset($_FILES['image_file']) ? $_FILES['image_file']['tmp_name'] : null;

    // メッセージの長さを検証
    if (strlen($message_text) > 1000) {
        error_log("メッセージが長すぎます。",3);
        exit();
    }

    // メッセージをデータベースに挿入
    $sql = "INSERT INTO messages (room_id, user_id, seller_id) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("iss", $room_id, $user_id, $seller_id);
        try {
            if ($stmt->execute()) {
                $lastInsertId = $conn->insert_id;
                $conn->commit();
                if(!is_null($image_data)){
                    $img1 = $_FILES["image_file"]["name"];

                    $img_url = add_filename($img1, $lastInsertId);

                    $update_sql = "UPDATE messages SET img_url = ? WHERE message_id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("si", $img_url, $lastInsertId);
                    if($update_stmt->execute()){
                        // メッセージの送信に成功した場合、何も返さない
                        move_uploaded_file($image_data, "img/" . $img_url);
                        $conn->commit(); // トランザクションをコミット
                    }else{
                        error_log("Message send failed:".$update_stmt->error, 3);
                    }
                }

                if(!is_null($message_text)){
                    $uptext_sql = "UPDATE messages SET message_text = ? WHERE message_id = ?";
                    $uptext_stmt = $conn->prepare($uptext_sql);
                    $uptext_stmt->bind_param("si", $message_text, $lastInsertId);
                    if($uptext_stmt->execute()){
                        $conn->commit();
                    }else{
                        error_log("Message send text:".$uptext_stmt->error, 3);
                    }
                }
            } else {
                // エラーハンドリング
                error_log("Message send failed: " . $stmt->error, 3); // エラーログにエラーメッセージを記録
            }
        } catch (Exception $e) {
            // エラーハンドリング: エラーメッセージをログに記録
            error_log("Error in send_message.php: " . $e->getMessage() . PHP_EOL, 3);
            $conn->rollback(); // エラーが発生した場合、トランザクションをロールバック
        }
    } else {
        // ステートメントの作成に失敗した場合のエラーハンドリング
        error_log("Statement preparation failed: " . $conn->error, 3); // エラーログにエラーメッセージを記録
        $conn->rollback(); // エラーが発生した場合、トランザクションをロールバック
    }
} else {
    // エラーメッセージを返すか、適切なエラーハンドリングを実装
    $conn->rollback(); // エラーが発生した場合、トランザクションをロールバック
}


//関数
function add_filename($filename,$addtext){
    //拡張子の前に文字列を追加
    $pos  = strrpos($filename, '.'); // .が最後に現れる位置
    if ($pos){
        return(substr($filename, 0, $pos).$addtext.substr($filename, $pos));
    }else{
        return($filename.$addtext);
    }
}

// データベース接続を閉じる
$conn->close();
?>
