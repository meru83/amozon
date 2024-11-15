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


$seller_id = isset($_SESSION['seller_id'])?$_SESSION['seller_id']:null;
$user_id = isset($_SESSION['user_id'])?$_SESSION['user_id']:null;

if(isset($_SESSION['user_id']) || isset($_SESSION['seller_id'])){
    $foo2 = <<<END
    <div style="width:100%; text-align: right; height: fit-content;">
    <form action="../logout.php" method="post">
        <input type="submit" name="logout" class="log_out" value="ログアウト">
    </form>
    </div>
END;
} else {
//     $foo2 = <<<END
//     <div class="New_log">
//         <a href="../register.php"><div class="log_style">新規登録</div></a>
//         <a href="login.php"><div class="log_style rightM">ログイン</div></a>
//     </div>
// END;
    header("Location:../login.php");
    exit();
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
                    <?php
                    if(isset($_SESSION['seller_id'])){
                        echo <<<HTML
                        <li class="menu-item-logo"><a href=""><img src="../img/cart_dake.svg" class="logo"><span class="menu-item-text-logo">Re.ReaD</span></a></li>
                        <li class="menu-item"><a href="seller_top.php"><img src="../img/home.png" class="logo"><span class="menu-item-text">ホーム</span></a></li>
                        <li class="menu-item"> <a href="p2_insert.php"><img src="../img/hensyu.png" class="logo"><span class="menu-item-text">商品情報登録</span></a></li>
                        <li class="menu-item"> <a href="seller_products.php"><img src="../img/meisi.png" class="logo"><span class="menu-item-text">登録商品一覧</span></a></li>
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
                            <li class="menu-item"> <a href="notYetDeli.php"><img src="../img/kuruma.png" class="logo"><span class="menu-item-text">未発送商品</span><span class="tuuti">$notYetDeli</span></a></li>
                            HTML;
                        }else{
                            echo <<<HTML
                            <li class="menu-item"> <a href="notYetDeli.php"><img src="../img/kuruma.png" class="logo"><span class="menu-item-text">未発送商品</span></a></li>
                            HTML;
                        }
                        // <?php
                        if(isset($_SESSION['seller_id'])){
                            echo '<li class="menu-item"><a href="../chat_rooms.php"><img src="../img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>';
                        }else{
                            echo '<li class="menu-item"><a href="seller.php"><img src="../img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>';
                        }
                        // 
                        // <?php
                        if(isset($_SESSION['seller_id'])){
                            $flagSellerId = $_SESSION['seller_id'];
                            echo <<<HTML
                            <li class="menu-item"><a href="seller_profile.php?seller_id=$flagSellerId"><img src="../img/hito.png" class="logo"><span class="menu-item-text">プロフィール</span></a></li>
                            HTML;
                        }
                    }else{
                        echo <<<HTML
                        <li class="menu-item-logo"><a href="" class="a_link"><img src="../img/cart_dake.svg" class="logo"><span class="menu-item-text-logo">Re.ReaD</span></a></li>
                        <li class="menu-item"><a href="../user_top.php" class="a_link"><img src="../img/home.png" class="logo"><span class="menu-item-text">ホーム</span></a></li>
                        <li class="menu-item"><a href="../search.php" class="a_link"><img src="../img/musimegane.png" class="logo"><span class="menu-item-text">検索</span></a></li>
                        <li class="menu-item"><a href="../cartContents.php" class="a_link"><img src="../img/cart.png" class="logo"><span class="menu-item-text">カート</span></a></li>
                        <li class="menu-item"><a href="../chat_rooms.php" class="a_link"><img src="../img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>
                        <li class="menu-item"><a href="../favoriteProduct.php" class="a_link"><img src="../img/heartBlack.png" class="logo"><span class="menu-item-text">お気に入り</span></a></li>
                        <li class="menu-item"><a href="../buyHistory.php" class="a_link"><img src="../img/meisi.png" class="logo"><span class="menu-item-text">購入履歴</span></a></li>
                        HTML;
                        if(isset($_SESSION['user_id'])){
                            $flagUserId = $_SESSION['user_id'];
                            echo <<<HTML
                            <li class="menu-item"><a href="../user_profile.php?user_id=$flagUserId" class="a_link"><img src="../img/hito.png" class="logo"><span class="menu-item-text">プロフィール</span></a></li>
                            HTML;
                        }
                    }
                    ?>
                    <!--log--->
                </ul>
            </div>
            <div>
                <ul class="menu-list-bottom">
                <li class="menu-item"><a href="../py/rireki.php"><img src="../img/gurafu.png" class="logo"><span class="menu-item-text">売上管理</span></a></li>
                </ul>
            </div>
        </div>

        <div class="right-content">
        <div class="amozon_profile">
        <!-- <img src="../img/cart_dake.svg" class="amozon_usericon"> -->
<?php
// ログイン中のユーザーIDを取得
$user_id = isset($_SESSION['user_id'])?$_SESSION['user_id']:null;//判定に使うときにnullが使えない
$seller_id = isset($_SESSION['seller_id'])?$_SESSION['seller_id']:"A";
$other_id = isset($_GET['other_id'])?$_GET['other_id']:null;
if(isset($_GET['seller_id'])){
    $postSellerId = $_GET['seller_id'];
}else if(isset($_POST['seller_id'])){
    $postSellerId = $_POST['seller_id'];
}else{
    $postSellerId = "B";
}

if($seller_id === $postSellerId){
    //自分
    $sql_seller = "SELECT sellerName, icon FROM seller WHERE seller_id = ?";
    $sql_stmt = $conn->prepare($sql_seller);
    $sql_stmt->bind_param("s",$seller_id); 
    $sql_stmt->execute();
    $sql_result = $sql_stmt->get_result();
    if($sql_result && $sql_result->num_rows > 0){
        $sql_row = $sql_result->fetch_assoc();
        $sellerName = $sql_row['sellerName'];
        $icon = isset($selectRow['icon'])?$selectRow['icon']:null;
    }
    if(isset($icon)){
        echo <<<END
        <img src="../img/$icon" class="amozon_usericon">
        END;
    }else{
        echo <<<HTML
        <img src="../img/cart_dake.svg" class="amozon_usericon">
        HTML;
    }
    echo "<h1>$sellerName</h1>";
    $addressSql = "SELECT post_code, prefectures, city, tyou, room_number, addressname 
                FROM address
                WHERE seller_id = ? && default_status = 1";
    $addressStmt = $conn->prepare($addressSql);
    $addressStmt->bind_param("s",$seller_id);
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

        echo <<<HTML
        <div class='sub-content-item1'>
            <div class="flexBox001">
                <h2>発送元：</h2>
                <div class="box001">
                <b>〒 $post_code</b><br>
                <b>$prefectures</b>
                <b>$city $tyou $room_number</b> <br>
                <b>$addressname</b>
            </div>
        </div>
        </div>
        HTML;
    }else{
        echo <<<HTML
        <div class="sub-content">
        <div class='sub-content-item1'>
        <div class="flexBox001">
        <a href="../address_insert.php">
                <h2>お届け先：<h2>
                未登録
        </a>
        </div>
        </div>
        HTML;
    }
    echo '</div>';
    // echo '</div>';
    $soldSql = "SELECT d.detail_total FROM orders_detail d
                LEFT JOIN orders o ON (d.order_id = o.order_id)
                LEFT JOIN products p ON (d.product_id = p.product_id)
                WHERE p.seller_id = ? && o.order_status = '配達完了'";
    $soldStmt = $conn->prepare($soldSql);
    $soldStmt->bind_param("s",$seller_id);
    $soldStmt->execute();
    $soldResult = $soldStmt->get_result();
    if($soldResult && $soldResult->num_rows > 0){
        $total = 0;
        //トータル計算
        while($soldRow = $soldResult->fetch_assoc()){
            $total += $soldRow['detail_total'];
        }
        // error_log($total);
        $commaTotal = number_format($total);
        echo <<<HTML
        <div class='sub-content-item'>
            <div class="flexBox">
                <h2>総売り上げ：<h2>
                $commaTotal 円
            </div>
        </div>
        HTML;
    }else{
        error_log("a");
        echo <<<HTML
        <div class='sub-content-item'>
            <div class="flexBox">
                <h2>総売り上げ：<h2>
                0 円
            </div>
        </div>
        HTML;
    }
}else{
    //相手
    $otherSql = "SELECT sellerName, icon FROM seller WHERE seller_id = ?";
    $otherStmt = $conn->prepare($otherSql);
    $otherStmt->bind_param("s",$other_id);
    $otherStmt->execute();
    $otherResult = $otherStmt->get_result();
    if($otherResult && $otherResult->num_rows > 0){
        $otherRow = $otherResult->fetch_assoc();
        $sellerName = $otherRow['sellerName'];
        $icon = isset($selectRow['icon'])?$selectRow['icon']:null;
        if(isset($icon)){
            echo <<<END
            <img src="../img/$icon" class="amozon_usericon">
            END;
        }else{
            echo <<<HTML
            <img src="../img/cart_dake.svg" class="amozon_usericon">
            HTML;
        }
        echo "<h1>$sellerName</h1>";

        $chatSql = "SELECT c.room_id, s.sellerName FROM chatrooms c
                -- LEFT JOIN users u ON (c.user_id = u.user_id)
                LEFT JOIN seller s ON (c.seller_id = s.seller_id)
                WHERE c.user_id = ? && c.seller_id = ?";
        $chatStmt = $conn->prepare($chatSql);
        $chatStmt->bind_param("ss",$user_id,$other_id);
        $chatStmt->execute();
        $chatResult = $chatStmt->get_result();
        if($chatResult && $chatResult->num_rows > 0){
            $chatRow = $chatResult->fetch_assoc();
            $room_id = $chatRow['room_id'];
            $sellerName = $chatRow['sellerName'];
            //こいつとのチャットにとばす
            // echo "<a href='chat_room.php?room_id=$room_id&sellerName=$user_id'><div class='sellerChat'>$username とのチャット</div></a><br>";
            echo <<<HTML
            <a href='../chat_room.php?room_id=$room_id&sellerName=$sellerName'>
            <div class='sub-content-item'>
                <div class="flexBox">
                    <!-- ここにチャットマーク -->
                    <img src="../img/chat2.svg" class="logo" style="width:60px;">
                </div>
            </div>
            </a>
            HTML;
        }else{
            $insertSql = "INSERT INTO user_id, seller_id VALUES(?,?)";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param("ss",$user_id,$other_id);
            $insertStmt->execute();
            $last_id = $conn->$insert_id;
        }
    }
}
// データベース接続を閉じる
$conn->close();
?>  
    </body>
</html>