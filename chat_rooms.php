<?php
// セッションを開始します
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// データベースへの接続情報を設定します
include 'db_config.php'; // データベース接続情報を読み込む

// セッションからユーザーIDを取得します
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$seller_id = isset($_SESSION['seller_id']) ? $_SESSION['seller_id'] : null;

// ログインセッションが存在しない場合、ログインページにリダイレクトします
if (is_null($user_id) && is_null($seller_id)) {
    header("Location: login.php"); // ログインページのURLにリダイレクト
    exit(); // リダイレクト後、スクリプトの実行を終了
}

//ユーザーでログインしてた時のログアウトボタン
if(!is_null($user_id)){
    echo '<a href="logout.php">ログアウト</a>';
}
//売り手側でログインしてた時のログイン
else if(!is_null($seller_id)){
    echo '<a href="seller/seller_out.php">ログアウト</a>';
}



// ユーザーのチャットルームを取得します
//DISTINCTで重複レコードを一つにまとめる
$sql = "SELECT DISTINCT chatrooms.room_id, chatrooms.user_id, chatrooms.seller_id, users.username, seller.sellerName
        FROM chatrooms
        INNER JOIN users ON (chatrooms.user_id = users.user_id)
        INNER JOIN seller ON (chatrooms.seller_id = seller.seller_id)
        WHERE (chatrooms.user_id = ?) OR (chatrooms.seller_id = ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $user_id, $seller_id);
$stmt->execute();
$result = $stmt->get_result();
//自分のidが入っているレコードを取得

// クエリの実行エラーに対するエラーハンドリング
if ($result === false) {
    $errorMessage = "クエリの実行に失敗しました: " . $conn->error;
    
    // エラーメッセージをログファイルに記録
    error_log($errorMessage, 3, 'error.log');
    
    die($errorMessage);
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>チャットルーム一覧</title>
</head>
<body>
    <div id="header">
        <h1>チャットルーム一覧</h1>
    </div>

    <div id="main">
        <p><a href="create.php">新しいチャットルームを作成</a></p>
        <?php
        // すでに表示したチャットルームのIDを格納する配列
        $displayedRooms = array();

        if ($result->num_rows > 0) {
            // チャットルームが存在する場合、それらを表示します
            while ($row = $result->fetch_assoc()) {
                $room_id = $row['room_id'];

                // すでに表示したチャットルームかどうかをチェック
                if (!in_array($room_id, $displayedRooms)) {
                    $displayedRooms[] = $room_id;

                    $user_id = $row['user_id'];
                    $username = $row['username'];
                    $seller_id = $row['seller_id'];
                    $sellerName = $row['sellerName'];

                    if (!isset($sellerName)) {
                        $sellerName = "不明なユーザー";
                    }
                    if (!isset($username)){
                        $username = "不明なユーザー";
                    }

                    if(isset($_SESSION['user_id'])){
                        echo "<a href='chat_room.php?room_id=$room_id&sellerName=$sellerName'>$sellerName とのチャット</a><br>";
                    }else if(isset($_SESSION['seller_id'])){
                        echo "<a href='chat_room.php?room_id=$room_id&username=$username'>$username とのチャット</a><br>";
                    }
                    
                }
            }
        } else {
            echo "チャットルームがありません。";
        }
                
        // データベース接続を閉じます
        $conn->close();
        ?>
    </div>
    
</body>
</html>