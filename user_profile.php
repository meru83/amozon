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
$user_id = isset($_SESSION['user_id'])?$_SESSION['user_id']:"B";//判定に使うときにnullが使えない
$seller_id = isset($_SESSION['seller_id'])?$_SESSION['seller_id']:null;
$other_id = isset($_GET['other_id'])?$_GET['other_id']:null;
if(isset($_GET['user_id'])){
    $postUserId = $_GET['user_id'];
}else if(isset($_POST['user_id'])){
    $postUserId = $_POST['user_id'];
}else{
    $postUserId = "A";
}
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
            <?php
            if(isset($_SESSION['user_id'])){
                echo <<<HTML
                <li class="menu-item-logo"><a href="" class="a_link"><img src="img/cart_dake.svg" class="logo"><span class="menu-item-text-logo">Re.ReaD</span></a></li>
                <li class="menu-item"><a href="user_top.php" class="a_link"><img src="img/home.png" class="logo"><span class="menu-item-text">ホーム</span></a></li>
                <li class="menu-item"><a href="search.php" class="a_link"><img src="img/musimegane.png" class="logo"><span class="menu-item-text">検索</span></a></li>
                <li class="menu-item"><a href="cartContents.php" class="a_link"><img src="img/cart.png" class="logo"><span class="menu-item-text">カート</span></a></li>
                <li class="menu-item"><a href="chat_rooms.php" class="a_link"><img src="img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>
                <li class="menu-item"><a href="favoriteProduct.php" class="a_link"><img src="img/heartBlack.png" class="logo"><span class="menu-item-text">お気に入り</span></a></li>
                <li class="menu-item"><a href="buyHistory.php" class="a_link"><img src="img/meisi.png" class="logo"><span class="menu-item-text">購入履歴</span></a></li>
                HTML;
                if(isset($_SESSION['user_id'])){
                    $flagUserId = $_SESSION['user_id'];
                    echo <<<HTML
                    <li class="menu-item"><a href="user_profile.php?user_id=$flagUserId" class="a_link"><img src="img/hito.png" class="logo"><span class="menu-item-text">プロフィール</span></a></li>
                    HTML;
                }
            }else{
                echo <<<HTML
                <li class="menu-item-logo"><a href=""><img src="img/cart_dake.svg" class="logo"><span class="menu-item-text-logo">Re.ReaD</span></a></li>
                <li class="menu-item"><a href="seller/seller_top.php"><img src="img/home.png" class="logo"><span class="menu-item-text">ホーム</span></a></li>
                <li class="menu-item"> <a href="seller/p2_insert.php"><img src="img/hensyu.png" class="logo"><span class="menu-item-text">商品情報登録</span></a></li>
                <li class="menu-item"> <a href="seller/seller_products.php"><img src="img/meisi.png" class="logo"><span class="menu-item-text">登録商品一覧</span></a></li>
                HTML;
                $notYetSql = "SELECT COUNT(DISTINCT o.order_id) AS notYetDeli FROM orders o
                LEFT JOIN orders_detail d ON (o.order_id = d.order_id)
                LEFT JOIN products p ON (d.product_id = p.product_id)
                WHERE o.order_status = '出荷準備中' && p.seller_id = ?";
                $notYetStmt = $conn->prepare($notYetSql);
                $notYetStmt->bind_param("s",$seller_id);
                $notYetStmt->execute();
                $notYetResult = $notYetStmt->get_result();
                if($notYetResult && $notYetResult->num_rows > 0){
                    $notYetRow = $notYetResult->fetch_assoc();
                    $notYetDeli = $notYetRow['notYetDeli'];
                    echo <<<HTML
                    <li class="menu-item"> <a href="seller/notYetDeli.php"><img src="img/kuruma.png" class="logo"><span class="menu-item-text">未発送商品</span><span class="tuuti">$notYetDeli</span></a></li>
                    HTML;
                }else{
                    echo <<<HTML
                    <li class="menu-item"> <a href="seller/notYetDeli.php"><img src="img/kuruma.png" class="logo"><span class="menu-item-text">未発送商品</span></a></li>
                    HTML;
                }
                // <?php
                if(isset($_SESSION['seller_id'])){
                    echo '<li class="menu-item"><a href="chat_rooms.php"><img src="img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>';
                }else{
                    echo '<li class="menu-item"><a href="seller/seller.php"><img src="img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>';
                }
                // 
                // <?php
                if(isset($_SESSION['seller_id'])){
                    $flagSellerId = $_SESSION['seller_id'];
                    echo <<<HTML
                    <li class="menu-item"><a href="seller/seller_profile.php?seller_id=$flagSellerId"><img src="img/hito.png" class="logo"><span class="menu-item-text">プロフィール</span></a></li>
                    HTML;
                }
            }
            ?>
            </ul>
        </div>
        <div>
            <ul class="menu-list-bottom">
            </ul>
        </div>
    </div>
    <div class="right-content">
            <div class="amozon_profile">
<?php
//自分のプロフィールか否か判定
//このページに飛んでくるときにpostかgetでuser_idを持たせてそれが自分のidか否か
if($user_id === $postUserId){
    // $sessionFlag = true;
    //ここに自分から見た時のデザイン
    $selectSql = "SELECT u.username, u.icon, p.total_pay
                -- a.post_code, a.prefectures, a.city, a.tyou, a.room_number, a.addressname
                FROM users u
                LEFT JOIN pay p ON (u.user_id = p.user_id)
                -- LEFT JOIN address a ON (u.user_id = a.user_id)
                WHERE u.user_id = ?";
    $selectStmt = $conn->prepare($selectSql);
    $selectStmt->bind_param("s",$user_id);
    $selectStmt->execute();
    $selectResult = $selectStmt->get_result();
    if($selectResult && $selectResult->num_rows > 0){
        $selectRow = $selectResult->fetch_assoc();
        $username = $selectRow['username'];
        $icon = isset($selectRow['icon'])?$selectRow['icon']:null;
        $total_pay = isset($selectRow['total_pay'])?$selectRow['total_pay']:0;
        $comma_total_pay = number_format($total_pay);
    }
    if(isset($icon)){
        echo <<<END
        <img src="img/$icon" class="amozon_usericon">
        END;
    }else{
        echo <<<HTML
        <img src="img/cart_dake.svg" class="amozon_usericon">
        HTML;
    }
    echo "<h1>$username</h1>";
    $addressSql = "SELECT post_code, prefectures, city, tyou, room_number, addressname 
                FROM address
                WHERE user_id = ? && default_status = 1";
    $addressStmt = $conn->prepare($addressSql);
    $addressStmt->bind_param("s",$user_id);
    $addressStmt->execute();
    $addressResult = $addressStmt->get_result();
    if($addressResult && $addressResult->num_rows > 0){
        $addressRow = $addressResult->fetch_assoc();
        $post_code = $addressRow['post_code'];
        $prefectures = $addressRow['prefectures'];
        $city = $addressRow['city'];
        $tyou = $addressRow['tyou'];
        $room_number = isset($addressRow['room_number'])?$addressRow['room_number']:"";
        $addressname = $addressRow['addressname'];

        echo '<div class="sub-content">';
        echo <<<HTML
        <div class='sub-content-item1'>
            <div class="flexBox001">
                <h2>お届け先</h2>
                <div class="box001">
                <b>〒 $post_code</b><br>
                <b>$prefectures</b>
                <b>$city $tyou $room_number</b><br>
                <b>$addressname</b>
                </div>
        </div>
        </div>
        HTML;
    }else{
        echo <<<HTML
        <div class='sub-content-item1'>
        <div class="flexBox001">
            <b><h2>お届け先<h2></b>
                <b>未登録</b>
        </div>
        </div>
        HTML;
    }
    echo "</div>";
    if(isset($comma_total_pay)){
        echo <<<HTML
        <a href='chargePay.php'>
        <div class='sub-content-item'>
            <div class="flexBox">
                <h2>残高<h2>
                <p>$comma_total_pay 円</p>
            </div>
        </div>
        </a>
        HTML;
    }else{
        echo <<<HTML
        <a href='chargePay.php'>
        <div class='sub-content-item'>
            <div class="flexBox">
                <h2>残高<h2>
                <p>0 円</p>
            </div>
        </div>
        </a>
        HTML;
    }
}else{
    // $sessionFlag = false;
    //ここに相手から見た時のデザイン
    $selectSql = "SELECT u.username, u.icon
                FROM users u
                WHERE u.user_id = ?";
    $selectStmt = $conn->prepare($selectSql);
    $selectStmt->bind_param("s",$other_id);
    $selectStmt->execute();
    $selectResult = $selectStmt->get_result();
    $selectRow = $selectResult->fetch_assoc();
    $username = $selectRow['username'];
    $icon = isset($selectRow['icon'])?$selectRow['icon']:null;
    if(isset($icon)){
        echo <<<END
        <img src="img/$icon" class="amozon_usericon">
        END;
    }else{
        echo <<<HTML
        <img src="img/cart_dake.svg" class="amozon_usericon">
        HTML;
    }
    echo "<h1>$username</h1>";
    $chatSql = "SELECT c.room_id, username FROM chatrooms c
            LEFT JOIN users u ON (c.user_id = u.user_id)
            WHERE c.user_id = ? && c.seller_id = ?";
    $chatStmt = $conn->prepare($chatSql);
    $chatStmt->bind_param("ss",$other_id,$seller_id);
    $chatStmt->execute();
    $chatResult = $chatStmt->get_result();
    if($chatResult && $chatResult->num_rows > 0){
        $chatRow = $chatResult->fetch_assoc();
        $room_id = $chatRow['room_id'];
        $username = $chatRow['username'];
        //こいつとのチャットにとばす
        // echo "<a href='chat_room.php?room_id=$room_id&sellerName=$user_id'><div class='sellerChat'>$username とのチャット</div></a><br>";
        echo <<<HTML
        <a href='chat_room.php?room_id=$room_id&username=$username'>
        <div class='sub-content-item'>
            <div class="flexBox2">
                <!-- ここにチャットマーク -->
                <img src="img/chat2.svg" class="logo" style="width:60px;">
            </div>
        </div>
        </a>
        HTML;
    }else{
        //チャットルームが存在しない
    }
}
?>


            </div>
        </div>  
    </body>
</html>



<!-- <!DOCTYPE html>
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
                <li class="menu-item"><a href="buyHistory.php"><img src="img/meisi.png" class="logo"><span class="menu-item-text">購入履歴</span></a></li>
                <?php
                // if(isset($_SESSION['user_id'])){
                //     $flagUserId = $_SESSION['user_id'];
                //     echo <<<HTML
                //     <li class="menu-item"><a href="user_profile.php?user_id=".$flagUserId><img src="img/hito.png" class="logo"><span class="menu-item-text">プロフィール</span></a></li>
                //     HTML;
                // }
                ?>
            </ul>
        </div>
        <div>
            <ul class="menu-list-bottom">
            </ul>
        </div>
    </div>
    <div class="right-content">
            <div class="amozon_profile"> -->

            <!-- ＊＊＊＊＊＊＊＊＊＊＊＊＊　　　ここまで一番上にした　　　　＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊＊ -->




                <!-- <img src="img/cart_dake.svg" class="amozon_usericon">
                <h1>名前</h1>
                <a href='chargePay.php'>
                <div class="sub-content">
                    <div class="sub-content-item1"></div>
                    <div class="sub-content-item1"></div>
                    <div class="sub-content-item1"></div>
                </div>
                </a> -->
                
                <!-- <a href='chargePay.php'>
                <div class='sub-content-item'>
                    <div class="flexBox">
                        <h2>残高<h2>
                        <p>￥0</p>
                    </div>
                </div>
                </a> -->




                <!-- ＊＊＊＊＊＊＊＊＊＊＊＊　　　ここからｐｈｐのした　　　＊＊＊＊＊＊＊＊＊＊＊＊＊ -->
            <!-- </div>
        </div>  
    </body>
</html> -->