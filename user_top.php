<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/Amozon_insta.css">
    <title>ユーザーのトップ</title>
</head>
<body>
    

    
    <div class="Amozon-container">
        <!-- Left Side Menu -->
        <div class="left-menu">
            <div>
                <ul class="menu-list">
                    <li class="menu-item"><a href=""><img src="img/cart_dake.svg" class="logo"><span class="menu-item-text-logo">Re.ReaD</span></a></li>
                    <li class="menu-item"><a href=""><img src="img/home.png" class="logo"></span><span class="menu-item-text">ホーム</span></a></li>
                    <li class="menu-item"><a href="search.php"><span class="menu-item-icon">🔍</span><span class="menu-item-text">検索</span></a></li>
                    <li class="menu-item"><a href="cartContents.php"><span class="menu-item-icon">📸</span><span class="menu-item-text">カート</span></a></li>
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
        
        <div class="right-content">
            <h1>ユーザー側</h1>
            <?php
            if(isset($_GET['error_message'])){
                $error_message = $_GET['error_message'];
                echo "<p>".$error_message."</p>";
            }
            if(isset($_SESSION['user_id'])){
                echo <<<END
                <form action="logout.php" method="post">
                    <input type="submit" name="logout" value="ログアウト">
                </form>
                END;
            }else{
                echo <<<END
                <a href="register.php">新規登録</a>
                <a href="login.php">ログイン</a>
                <br>
                <br>
                END;
            }
            ?>
            <a href="cartContents.php">カート</a><br>
            <a href="chat_rooms.php">チャットルーム一覧</a>
        </div>
    </div>

</body>
</html>