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
    <link rel="stylesheet" href="css/style_chat_rooms.css">
    <title>チャットルーム一覧</title>
</head>
<body>
    <div id="header" class="header">
        <div class="space"></div>
        <h1>チャットルーム一覧</h1>
        <?php        
        //ユーザーでログインしてた時のログアウトボタン
        if(!is_null($user_id)){
            echo '<a href="logout.php"><div class="log_out">ログアウト</div></a>';
        }
        //売り手側でログインしてた時のログイン
        else if(!is_null($seller_id)){
            echo '<a href="seller/seller_out.php">ログアウト</a>';
        }
        ?>
    </div>
    <?php        
    if(!is_null($user_id)){
        //user
        echo <<< HTML
        <div class="Amozon-container">
        <!-- Left Side Menu -->
        <div class="left-menu">
            <div>
                <ul class="menu-list">
                    <li class="menu-item-logo"><a href=""><img src="img/cart_dake.svg" class="logo"><span class="menu-item-text-logo">Re.ReaD</span></a></li>
                    <li class="menu-item"><a href=""><img src="img/home.png" class="logo"><span class="menu-item-text">ホーム</span></a></li>
                    <li class="menu-item"><a href="search.php"><img src="img/musimegane.png" class="logo"><span class="menu-item-text">検索</span></a></li>
                    <li class="menu-item"><a href="cartContents.php"><img src="img/cart.png" class="logo"><span class="menu-item-text">カート</span></a></li>
                    <li class="menu-item"><a href=""><span class="menu-item-icon">🎥</span><span class="menu-item-text">リール動画</span></li>
                    <li class="menu-item"><a href="chat_rooms.php"><img src="img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>
                    <li class="menu-item"><a href=""><span class="menu-item-icon">❤️</span><span class="menu-item-text">お知らせ</span></a></li>
                    <li class="menu-item"><a href=""><span class="menu-item-icon">➕</span><span class="menu-item-text">#</span></a></li>
                    <li class="menu-item"><a href=""><img src="img/hito.png" class="logo"><span class="menu-item-text">プロフィール</span></a></li>
                </ul>
            </div>
            <div>
                <ul class="menu-list-bottom">
                    <li class="menu-item"><a href=""><span class="menu-item-icon">💬</span><span class="menu-item-text">Threads</span></a></li>
                    <li class="menu-item"><a href=""><img src="img/haguruma.svg" class="logo"></span><span class="menu-item-text">その他</span></a></li>
                </ul>
            </div>
        </div>
        HTML;
    }else if(!is_null($seller_id)){
        //seller
        echo <<< HTML
        <div class="Amozon-container">
        <!-- Left Side Menu -->
        <div class="left-menu">
            <div>
                <ul class="menu-list">
                    <li class="menu-item-logo"><a href=""><img src="../img/cart_dake.svg" class="logo"><span class="menu-item-text-logo">Re.ReaD</span></a></li>
                    <li class="menu-item"><a href=""><img src="../img/home.png" class="logo"><span class="menu-item-text">ホーム</span></a></li>
                    <li class="menu-item"><a href="../search.php"><img src="../img/musimegane.png" class="logo"><span class="menu-item-text">検索</span></a></li>
                    <li class="menu-item"> <a href="p2_insert.php"><img src="../img/cart.png" class="logo"><span class="menu-item-text">商品情報登録</span></a></li>
                    <li class="menu-item"><a href="../chat_rooms.php"><img src="../img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>
                    <li class="menu-item"><a href=""><span class="menu-item-icon">❤️</span><span class="menu-item-text">お知らせ</span></a></li>
                    <li class="menu-item"><a href=""><span class="menu-item-icon">➕</span><span class="menu-item-text">#</span></a></li>
                    <li class="menu-item"><a href=""><img src="../img/hito.png" class="logo"><span class="menu-item-text">プロフィール</span></a></li>
                </ul>
            </div>
            <div>
                <ul class="menu-list-bottom">
                    <li class="menu-item"><a href=""><span class="menu-item-icon">💬</span><span class="menu-item-text">Threads</span></a></li>
                    <li class="menu-item"><a href=""><img src="../img/haguruma.svg" class="logo"></span><span class="menu-item-text">その他</span></a></li>
                </ul>
            </div>
        </div>
        HTML;
    }
         
    echo '<div class="right-content">';
    echo '<p><a href="create.php">新しいチャットルームを作成</a></p>';
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
                    //user
                    echo "<a href='chat_room.php?room_id=$room_id&sellerName=$sellerName'>$sellerName とのチャット</a><br>";
                }else if(isset($_SESSION['seller_id'])){
                    //seller
                    echo "<a href='chat_room.php?room_id=$room_id&username=$username'>$username とのチャット</a><br>";
                }
                
            }
        }
    } else {
        echo "チャットルームがありません。";
    }
                    
            
    echo "</div>";
    echo "</div>";

    // データベース接続を閉じます
    $conn->close();
    ?>
</body>
</html>