<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ログインセッションが存在しない場合、ログインページにリダイレクトします
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// データベースへの接続情報を設定します（db_config.phpを適切に設定してください）
include 'db_config.php';

$error_message = null;

if (isset($_GET['seller_id'])) {
    // フォームから送信されたユーザーIDを取得
    $seller_id = $_GET['seller_id'];

    // 自分のユーザーIDを取得
    $user_id = $_SESSION['user_id'];

    if (!isValidUserID($conn, $seller_id)) {
        $error_message = "無効なユーザーIDです。";
        header("Location: chat_rooms.php?error_message=$error_message");
        exit();
    }

    try{
        // チャットルームが既に存在するか確認
        $check_sql = "SELECT room_id FROM chatrooms WHERE (user_id = ? AND seller_id = ?)";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ss", $user_id, $seller_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // 既存のチャットルームが存在する場合
            $error_message = "指定した販売者とのチャットルームは既に存在します。";
            header("Location: chat_rooms.php?error_message=$error_message");
            exit();
        } else {
            // チャットルームを新しく作成
            $insert_sql = "INSERT INTO chatrooms (user_id, seller_id) VALUES (?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ss", $user_id, $seller_id);
            
            if ($insert_stmt->execute()) {
                // チャットルームの作成に成功
                header("Location: chat_rooms.php");
                exit();
            } else {
                // チャットルームの作成に失敗した場合のエラーハンドリング
                //echoに比べてエラーハンドリングが柔軟
                throw new Exception("チャットルームの作成に失敗しました。");
                $error_message = "チャットルームの作成に失敗しました。";
                header("Location: chat_rooms.php?error_message=$error_message");
                exit();
            }
        }
    }catch (Exception $e) {
        // エラーハンドリング: エラーメッセージをログに記録
        error_log("Error in create.php: " . $e->getMessage() . PHP_EOL);

        // エラーメッセージを表示
        $error_message = "エラーが発生しました。申し訳ありませんが、後でもう一度試してください。";
        header("Location: chat_rooms.php?error_message=$error_message");
        exit();
    }
}

// 有効なユーザーIDかどうかを確認する関数
function isValidUserID($conn, $seller_id) {
    $sql = "SELECT seller_id FROM seller WHERE seller_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $seller_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return ($result && $result->num_rows > 0);
}
?>
