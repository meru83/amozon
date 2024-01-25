<?php
include "db_config.php";

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

// セッションを開始します
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

//ヘッダーログインボタンORログアウトボタン
if(isset($_SESSION['user_id']) || isset($_SESSION['seller_id'])){
    $foo2 = <<<END
    <div style="width:100%; text-align: right; height: fit-content;">
    <form action="logout.php" method="post">
        <input type="submit" name="logout" class="log_out" value="ログアウト">
    </form>
    </div>
END;
}else{
    $foo2 = <<<END
    <div class="New_log">
        <a href="register.php"><div class="log_style">新規登録</div></a>
        <a href="login.php"><div class="log_style rightM">ログイン</div></a>
    </div>
END;
}


$user_id = isset($_SESSION['user_id'])?$_SESSION['user_id']:"A";//判定に使うときにnullが使えない
$seller_id = isset($_SESSION['seller_id'])?$_SESSION['seller_id']:null;
if(isset($_GET['user_id'])){
    $postUserId = $_GET['user_id'];
}else if(isset($_POST['user_id'])){
    $postUserId = $_POST['user_id'];
}else{
    $postUserId = "B";
}

//自分のプロフィールか否か判定
//このページに飛んでくるときにpostかgetでuser_idを持たせてそれが自分のidか否か
// if($user_id === $postUserId){
//     // $sessionFlag = true;
//     //ここに自分から見た時のデザイン
//     $selectSql = "SELECT u.user_id, u.username, u.user_status, u.icon 
//                 FROM users u
//                 LEFT JOIN address a ON (u.user_id = a.user_id)
//                 WHERE user_id = ?";
//     $selectStmt = ;
// }else{
//     // $sessionFlag = false;
//     //ここに相手から見た時のデザイン
// }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <link rel="stylesheet" href="css/Amazon_profile.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール</title>
</head>
<body>
    <div id="header" class="header">
        <div class="back"><div class="backBtn" onclick="history.back()"><img src="img/return_left.png" style="width:100%;"></div></div>
        <h1 class="h1_White">プロフィール</h1>
        <?=$foo2?>
    </div>
<div class="profile_container">    
    <div class="left-menu">
        <div>
            <ul class="menu-list">
                <li class="menu-item-logo"><a href=""><img src="img/cart_dake.svg" class="logo"><span class="menu-item-text-logo">Re.ReaD</span></a></li>
                <li class="menu-item"><a href="user_top.php"><img src="img/home.png" class="logo"><span class="menu-item-text">ホーム</span></a></li>
                <li class="menu-item"><a href="search.php"><img src="img/musimegane.png" class="logo"><span class="menu-item-text">検索</span></a></li>
                <li class="menu-item"><a href="cartContents.php"><img src="img/cart.png" class="logo"><span class="menu-item-text">カート</span></a></li>
                <li class="menu-item"><a href="chat_rooms.php"><img src="img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>
                <li class="menu-item"><a href="favoriteProduct.php"><img src="img/heartBlack.png" class="logo"><span class="menu-item-text">お気に入り</span></a></li>
                <li class="menu-item"><a href="user_profile.php"><img src="img/hito.png" class="logo"><span class="menu-item-text">プロフィール</span></a></li>
            </ul>
        </div>
        <div>
            <ul class="menu-list-bottom">
            </ul>
        </div>
    </div>
    <div class="right-content">
            <div class="amozon_profile">
                <img src="img/cart_dake.svg" class="amozon_usericon">
                <h1>名前</h1>
                <a href='chargePay.php'>
                <div class="sub-content">
                    <div class="sub-content-item1"></div>
                    <div class="sub-content-item1"></div>
                    <div class="sub-content-item1"></div>
                </div>
                </a>
                
                <a href='chargePay.php'>
                <div class='sub-content-item'>
                    <div class="flexBox">
                        <h2>残高<h2>
                        <p>￥0</p>
                    </div>
                </div>
                </a>
            </div>
        </div>  
    </body>
</html>