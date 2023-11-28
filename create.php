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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームから送信されたユーザーIDを取得
    $seller_id = $_POST['seller_id'];

    // 自分のユーザーIDを取得
    $user_id = $_SESSION['user_id'];

    if (!isValidUserID($conn, $seller_id)) {
        echo "無効なユーザーIDです。";
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
            echo "指定した販売者とのチャットルームは既に存在します。";
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
            }
        }
    }catch (Exception $e) {
        // エラーハンドリング: エラーメッセージをログに記録
        $logFile = 'error.log';
        error_log("Error in create.php: " . $e->getMessage() . PHP_EOL, 3, $logFile);

        // エラーメッセージを表示
        echo "エラーが発生しました。申し訳ありませんが、後でもう一度試してください。";
    }

    // データベース接続を閉じます
    $conn->close();
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
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新しいチャットルーム作成</title>
</head>
<body>
    <h1>新しいチャットルームを作成</h1>
    <!---チャットルーム作成フォーム--->
    <form action="" method="POST">
        <label for="seller_id">ユーザーIDを入力:</label>
        <input type="text" name="seller_id" id="seller_id" required>
        <button type="submit">チャットルームを作成</button>
    </form>
    <a href="chat_rooms.php">戻る</a>
</body>
</html>
