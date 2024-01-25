<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

include "../db_config.php";

if(isset($_SESSION['seller_id'])){
    $seller_id = $_SESSION['seller_id'];
    $foo = <<<END
    <div style="width:100%; text-align: right; height: fit-content;">
    <form action="seller_out.php" method="post">
        <input type="submit" name="logout" class="log_out" value="ログアウト">
    </form>
    </div>
    END;
}else{
    // $foo = <<<END
    // <div class="New_log">
    //     <a href="seller.php"><div class="log_style">新規登録</div></a>
    //     <a href="seller_log.php"><div class="log_style rightM">ログイン</div></a>
    // </div>
    // END;
    header("Location:seller_log.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>販売者のトップ</title>
    <link rel="stylesheet" href="../css/Amozon_insta.css">
    <link rel="stylesheet" href="../css/test.css">
</head>
<body>
    <div id="header" class="header">
        <div class="back"><div class="backBtn" onclick="history.back()"><img src="../img/return_left.png" style="width:100%;"></div></div>
        <h1 class="h1_White">販売者側トップページ</h1>
        <?=$foo?>
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
                    <!-- <li class="menu-item"> <a href=""><img src="../img/meisi.png" class="logo"><span class="menu-item-text">未発送商品</span></a></li> -->
                    <?php
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

                    if(isset($_SESSION['seller_id'])){
                        echo '<li class="menu-item"><a href="../chat_rooms.php"><img src="../img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>';
                    }else{
                        echo '<li class="menu-item"><a href="seller_log.php"><img src="../img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>';
                    }
                    ?>
                    <?php
                    if(isset($_SESSION['seller_id'])){
                        $flagSellerId = $_SESSION['seller_id'];
                        echo <<<HTML
                        <li class="menu-item"><a href="seller_profile.php?seller_id=$flagSellerId"><img src="../img/hito.png" class="logo"><span class="menu-item-text">プロフィール</span></a></li>
                        HTML;
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
        <p id="current-time"></p>
        <?php
            if(isset($_GET['error_message'])){
                $error_message = $_GET['error_message'];
                echo "<p class='error_red'>".$error_message."</p>";
            }

            if(!isset($_SESSION['seller_id'])){
                echo '<div class="error_red">※ユーザー登録またはログインを完了させてください。</div>';
            }
            ?>
        <div class="cp_box">
        <input id="cp03" type="checkbox">
        <label for="cp03"></label>
        <div class="cp_container">
            <h1>メニュー説明</h1>
            <p><h2>1.ホーム</h2></p>
            <p><h3>・・・FAQ一覧</h3></p>
            <p><h2>2.検索</h2></p>
            <p><h3>・・・ここから商品を見つけます。商品のカテゴリーで絞ることも可能です。</h3></p>
            <p><h2>3.カート</h2></p>
            <p><h3>・・・カートに入れた商品一覧が見れます、また購入はカート内から行えます。</h3></p>
            <p><h2>4.メッセージ</h2></p>
            <p><h3>・・・販売者と直接話すことができます、商品を検索していただき、お好みの商品を選んでいただいて商品詳細ページからチャットルームを作成します。また購入して頂くと通知が送信されます。（※チャットの有無に関わらず定型文がチャットで自動で送信されます）</h3></p>
            <p><h2>5.お気に入り</h2></p>
            <p><h3>・・・商品のハートマークを押すと、お気に入りページに別途で表示されます。</h3></p>
            <p><h2>6.購入履歴</h2></p>
            <p><h3>・・・明細と配達状況が確認できます。</h3></p>
            <p><h2>7.プロフィール</h2></p>
            <p><h3>・・・変更/設定、残高の確認はここで行えます。残高のチャージは残高を押して頂くとチャージページに行きます。</h3></p>
        </div>
        </div>
        <div class="cp_box">
        <input id="cp04" type="checkbox">
        <label for="cp04"></label>
        <div class="cp_container">
            <h1>当サイトの使用上の注意事項</h1>
            <p><h2>1.メールアドレスについて</h2></p>
            <p><h3>・・・正しくメールアドレスが登録されていない場合、二段階認証によってログインできなくなる可能性があります。</h3></p>
            <p><h2>2.チャットについて</h2></p>
            <p><h3>・・・不適切な発言や、違法行為は禁止です。</h3></p>
            <p><h2>3.配達状況について</h2></p>
            <p><h3>・・・配達経過はリアルタイムで確認できます。また状況によってはキャンセルできなかったりするのでご了承ください。トラブルの場合は販売者とチャットで相談お願いいたします。</h3></p>
        </div>
        </div>


        </div>
        </div>
        </div>
    </div>

 
