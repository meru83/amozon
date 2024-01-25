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
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/test.css">
    <link rel="stylesheet" href="css/Amozon_insta.css">
    <title>ユーザートップ</title>
</head>
<body>
    <div id="header" class="header">
        <div class="back"><div class="backBtn" onclick="history.back()"><img src="img/return_left.png" style="width:100%;"></div></div>
        <h1 class="h1_White">トップページ</h1>
        <?=$foo2?>
    </div>
    <div class="Amozon-container">
        <!-- Left Side Menu -->
        <div class="left-menu">
            <div>
                <ul class="menu-list">
                    <li class="menu-item-logo"><a href="" class="a_link"><img src="img/cart_dake.svg" class="logo"><span class="menu-item-text-logo">Re.ReaD</span></a></li>
                    <li class="menu-item"><a href="user_top.php" class="a_link"><img src="img/home.png" class="logo"><span class="menu-item-text">ホーム</span></a></li>
                    <li class="menu-item"><a href="search.php" class="a_link"><img src="img/musimegane.png" class="logo"><span class="menu-item-text">検索</span></a></li>
                    <li class="menu-item"><a href="cartContents.php" class="a_link"><img src="img/cart.png" class="logo"><span class="menu-item-text">カート</span></a></li>
                    <li class="menu-item"><a href="chat_rooms.php" class="a_link"><img src="img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>
                    <li class="menu-item"><a href="favoriteProduct.php" class="a_link"><img src="img/heartBlack.png" class="logo"><span class="menu-item-text">お気に入り</span></a></li>
                    <li class="menu-item"><a href="buyHistory.php" class="a_link"><img src="img/meisi.png" class="logo"><span class="menu-item-text">購入履歴</span></a></li>
                    <?php
                    if(isset($_SESSION['user_id'])){
                        $flagUserId = $_SESSION['user_id'];
                        echo <<<HTML
                        <li class="menu-item"><a href="user_profile.php?user_id=$flagUserId" class="a_link"><img src="img/hito.png" class="logo"><span class="menu-item-text">プロフィール</span></a></li>
                        HTML;
                    }
                    ?>
                    <!-- <li class="menu-item"><a href="user_profile.php?"><img src="img/hito.png" class="logo"><span class="menu-item-text">プロフィール</span></a></li> -->
                </ul>
            </div>
            <div>
                <ul class="menu-list-bottom">
                </ul>
            </div>
        </div>
        
        <div class="right-content">
        <?php
        if(isset($_GET['error_message'])){
        $error_message = $_GET['error_message'];
        echo "<p class='error_red'>".$error_message."</p>";
        }

        if(!isset($_SESSION['user_id'])){
        echo '<div class="error_red">※ユーザー登録またはログインを完了させてください。</div>';
        }
        ?>
        <p id="current-time"></p>

        <div class="cp_box">
        <input id="cp01" type="checkbox">
        <label for="cp01"></label>
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
        <input id="cp02" type="checkbox">
        <label for="cp02"></label>
        <div class="cp_container">
            <h1>当サイトの使用上の注意事項</h1>
            <p><h2>1.メールアドレスについて</h2></p>
            <p><h3>・・・正しくメールアドレスが登録されていない場合、二段階認証によってログインできなくなる可能性があります。</h3></p>
            <p><h2>2.チャットについて</h2></p>
            <p><h3>・・・不適切な発言や、違法行為は禁止です。</h3></p>
            <p><h2>3.</h2></p>
            <p><h3>・・・</h3></p>
        </div>
        </div>


        </div>
        </div>
<script>
    function updateClock() {
    var currentTime = new Date();
    var hours = currentTime.getHours();
    var minutes = currentTime.getMinutes();
    var seconds = currentTime.getSeconds();

    // ゼロ埋め
    minutes = (minutes < 10 ? "0" : "") + minutes;
    seconds = (seconds < 10 ? "0" : "") + seconds;

    // フォーマット（24時間制）
    var formattedTime = hours + ":" + minutes + ":" + seconds;

    // 現在の時間を表示する要素にセット
    document.getElementById('current-time').innerHTML = formattedTime;

    // 1秒ごとに更新
    setTimeout(updateClock, 1000);
}

// ページ読み込み時に初回実行
document.addEventListener("DOMContentLoaded", function() {
    updateClock();
});
</script>

</body>
</html>