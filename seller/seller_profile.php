<?php
include "../db_config.php";

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

// セッションを開始します
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(isset($_SESSION['user_id']) || isset($_SESSION['seller_id'])){
    $foo2 = <<<END
    <div style="width:100%; text-align: right; height: fit-content;">
    <form action="logout.php" method="post">
        <input type="submit" name="logout" class="log_out" value="ログアウト">
    </form>
    </div>
END;
} else {
    $foo2 = <<<END
    <div class="New_log">
        <a href="register.php"><div class="log_style">新規登録</div></a>
        <a href="login.php"><div class="log_style rightM">ログイン</div></a>
    </div>
END;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <link rel="stylesheet" href="../css/Amazon_profile.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール</title>
</head>
<body>
    <div id="header" class="header">
        <div class="back"><div class="backBtn" onclick="history.back()"><img src="../img/return_left.png" style="width:100%;"></div></div>
        <h1 class="h1_White">プロフィール</h1>
        <?=$foo2?>
    </div>
    <div class="Amozon-container">
        <!-- Left Side Menu -->
        <div class="left-menu">
            <div>
                <ul class="menu-list">
                    <li class="menu-item-logo"><a href=""><img src="../img/cart_dake.svg" class="logo"><span class="menu-item-text-logo">Re.ReaD</span></a></li>
                    <li class="menu-item"><a href="seller_top.php"><img src="../img/home.png" class="logo"><span class="menu-item-text">ホーム</span></a></li>
                    <li class="menu-item"> <a href="p2_insert.php"><img src="../img/hensyu.png" class="logo"><span class="menu-item-text">商品情報登録</span></a></li>
                    <li class="menu-item"> <a href="seller_products.php"><img src="../img/meisi.png" class="logo"><span class="menu-item-text">登録商品一覧</span></a></li>
                    <?php
                    if(isset($_SESSION['seller_id'])){
                        echo '<li class="menu-item"><a href="../chat_rooms.php"><img src="../img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>';
                    }else{
                        echo '<li class="menu-item"><a href="seller.php"><img src="../img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>';
                    }
                    ?>
                    <li class="menu-item"><a href="seller_home.php"><img src="../img/hito.png" class="logo"><span class="menu-item-text">プロフィール</span></a></li>
                    <!--log--->
                </ul>
            </div>
            <div>
                <ul class="menu-list-bottom">
                <li class="menu-item"><a href="../py/rireki.php"><img src="../img/gurafu.png" class="logo"><span class="menu-item-text">売上管理</span></a></li>
                </ul>
            </div>
        </div>

        <div class="amozon_profile">
        <img src="../img/cart_dake.svg" class="amozon_usericon">
        <?php
             // ログイン中のユーザーIDを取得
        if (isset($_SESSION['seller_id'])) {
            $seller_id = $_SESSION['seller_id'];
            $sql_seller = "SELECT sellerName FROM seller WHERE seller_id = '$seller_id'";
            $result_seller = $conn->query($sql_seller);

        // クエリの実行にエラーがある場合
        if (!$result_seller) {
            die("クエリの実行にエラーがあります: " . $conn->error);
        }

        // ユーザー名が取得できた場合は表示
        if ($result_seller->num_rows > 0) {
            $row_seller = $result_seller->fetch_assoc();
            echo "<h1>{$row_seller['sellerName']}</h1>";
        }           
        // データベース接続を閉じる
            $conn->close();
        } else {
            // ログインしていない場合
            echo "<p>ログインしていません</p>";
        }
        ?>
        <a href='chargePay.php'>
            <div class="sub-content">
                <div class="sub-content-item1"></div>
                <div class="sub-content-item1"></div>
                <div class="sub-content-item1"></div>
                    </div>>
                </a>
            </div>
        </div>  
    </body>
</html>