<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

if(isset($_SESSION['user_id'])){
    $foo2 = <<<END
    <form action="logout.php" method="post">
        <input type="submit" name="logout" class="log_out" value="ログアウト">
    </form>
    END;
}else{
    $foo2 = <<<END
    <div class="New_log">
        <a href="register.php"><div class="log_style">新規登録</div></a>
        <a href="login.php"><div class="log_style">ログイン</div></a>
    </div>
    END;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/Amozon_insta.css">
    <title>ユーザートップ</title>
</head>
<body>
    <div id="header" class="header">
        <div class="space"></div>
        <h1 class="h1_White">トップページ</h1>
        <?=$foo2?>
    </div>
    <div class="Amozon-container">
        <!-- Left Side Menu -->
        <div class="left-menu">
            <div>
                <ul class="menu-list">
                    <li class="menu-item-logo"><a href=""><img src="img/cart_dake.svg" class="logo"><span class="menu-item-text-logo">Re.ReaD</span></a></li>
                    <li class="menu-item"><a href=""><img src="img/home.png" class="logo"><span class="menu-item-text">ホーム</span></a></li>
                    <li class="menu-item"><a href="search.php"><img src="img/musimegane.png" class="logo"><span class="menu-item-text">検索</span></a></li>
                    <li class="menu-item"><a href="cartContents.php"><img src="img/cart.png" class="logo"><span class="menu-item-text">カート</span></a></li>
                    <li class="menu-item"><a href="chat_rooms.php"><img src="img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>
                    <li class="menu-item"><a href=""><span class="menu-item-icon">❤️</span><span class="menu-item-text">お知らせ</span></a></li>
                    <li class="menu-item"><a href=""><img src="img/hito.png" class="logo"><span class="menu-item-text">プロフィール</span></a></li>
                </ul>
            </div>
            <div>
                <ul class="menu-list-bottom">
                    <li class="menu-item"><a href=""><img src="img/haguruma.svg" class="logo"></span><span class="menu-item-text">その他</span></a></li>
                </ul>
            </div>
        </div>
        
        <div class="right-content">
            <h1>ユーザー側</h1>
            <?php
            if(isset($_GET['error_message'])){
                $error_message = $_GET['error_message'];
                echo "<p class='error_red'>".$error_message."</p>";
            }

            if(!isset($_SESSION['user_id'])){
                echo '<div class="error_red">※ユーザー登録またはログインを完了させてください。</div>';
            }
            ?>
        </div>
    </div>

</body>
</html>