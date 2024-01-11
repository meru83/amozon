<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

// if(isset($_SESSION['seller_id'])){
//     $foo = <<<END
//     <form action="seller_out.php" method="post">
//         <input type="submit" name="logout" value="ログアウト">
//     </form>
//     <a href="seller_products.php">登録済み商品一覧</a><br>
//     <a href="../chat_rooms.php">チャットルーム一覧</a>
//     END;
// }else{
//     $foo = <<<END
//     <a href="seller.php">新規登録</a>
//     <a href="seller_log.php">ログイン</a><br>
//     ユーザー登録またはログインを完了させてください。
//     <br>
//     <br>
//     END;
// }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>販売者のトップ</title>
    <link rel="stylesheet" href="../css/Amozon_insta.css">
</head>
<body>
    <div class="Amozon-container">
        <!-- Left Side Menu -->
        <div class="left-menu">
            <div>
                <ul class="menu-list">
                    <li class="menu-item-logo"><a href=""><img src="../img/cart_dake.svg" class="logo"><span class="menu-item-text-logo">Re.ReaD</span></a></li>
                    <li class="menu-item"><a href=""><img src="../img/home.png" class="logo"><span class="menu-item-text">ホーム</span></a></li>
                    <li class="menu-item"><a href="../search.php"><img src="../img/musimegane.png" class="logo"><span class="menu-item-text">検索</span></a></li>
                    <li class="menu-item"> <a href="p2_insert.php"><img src="../img/cart.png" class="logo"><span class="menu-item-text">商品情報登録</span></a></li>
                    <li class="menu-item"> <a href="seller_products.php"><img src="../img/cart.png" class="logo"><span class="menu-item-text">登録商品一覧</span></a></li>
                    <?php
                    if(isset($_SESSION['seller_id'])){
                        echo '<li class="menu-item"><a href="../chat_rooms.php"><img src="../img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>';
                    }else{
                        echo '<li class="menu-item"><a href="seller.php"><img src="../img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>';
                    }
                    ?>
                    <li class="menu-item"><a href=""><span class="menu-item-icon">❤️</span><span class="menu-item-text">お知らせ</span></a></li>
                    <li class="menu-item"><a href=""><img src="../img/hito.png" class="logo"><span class="menu-item-text">プロフィール</span></a></li>
                    <!--log--->
                </ul>
            </div>
            <div>
                <ul class="menu-list-bottom">
                    <li class="menu-item"><a href=""><img src="../img/haguruma.svg" class="logo"></span><span class="menu-item-text">その他</span></a></li>
                </ul>
            </div>
        </div>
        
        <div class="right-content">
            <h1>販売者側</h1>
            <?php
            if(isset($_GET['error_message'])){
                $error_message = $_GET['error_message'];
                echo "<p>".$error_message."</p>";
            }
            if(isset($_SESSION['seller_id'])){
                echo <<<END
                <form action="seller_out.php" method="post">
                    <input type="submit" name="logout" value="ログアウト">
                </form>
                <a href="seller_products.php">登録済み商品一覧</a><br>
                <a href="../chat_rooms.php">チャットルーム一覧</a>
                END;
            }else{
                echo <<<END
                <a href="seller.php">新規登録</a>
                <a href="seller_log.php">ログイン</a><br>
                ユーザー登録またはログインを完了させてください。
                <br>
                <br>
                END;
            }
            ?>
        </div>
    </div>

 
