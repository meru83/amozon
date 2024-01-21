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

if(isset($_SESSION['user_id']) || $_SESSION['seller_id']){
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
// ログイン中のユーザーの情報を取得（例としてユーザーIDを仮定）
if(isset($_SESSION['user_id'])){
    $user = $_SESSION['user_id'];
    $sql = "SELECT total_pay FROM pay WHERE user_id = '$user'";
    $result = $conn->query($sql);

    // クエリの実行にエラーがある場合
    if (!$result) {
        die("クエリの実行にエラーがあります: " . $conn->error);
    }

    // データが存在する場合、データを1行だけ取得して表示
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // "total_pay" キーが存在するか確認してから表示
        if (isset($row["total_pay"])) {
            // チャージ情報をHTMLに表示
            $zandaka =<<<END
            <a href='chargePay.php'>
                <div class='sub-content-item'>
                    <h2>残高<h2>
                    <p>{$row["total_pay"]}</p>
                </div>
            </a>
            END;
        } else {
            //0円の時
            $zandaka =<<<END
            <a href='chargePay.php'>
                <div class='sub-content-item'>
                    <h2>残高<h2>
                    <p>0</p>
                </div>
            </a>
            END;
        }
    } else {
        // チャージ情報が存在しない場合
        echo "<a href='chargePay.php'>
                <div class='sub-content-item'>
                    <h2>チャージする</h2>
                </div>
            </a>";
    }
} else {
    // ログインしていない場合
    echo "<p>ログインしていません</p>";
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
            <h1>ユーザー名</h1>
            <p>こんにちは、私はユーザー名です。プロフィールの説明文がここに入ります。</p>
            <div class="sub-content">
                <a href="address_insert.php">
                    <div class="sub-content-item">
                        <h2>住所登録</h2>
                        <p>ここにサブコンテンツ1の説明が入ります。</p>
                    </div>
                </a>
                <a href="chargePay.php">
                <div class="sub-content-item">
                    <h2>チャージする</h2>
                    <p>a<p>
                </div>
                </a>
                <div class="sub-content-item">
                    <h2>サブコンテンツ3</h2>
                    <p>ここにサブコンテンツ3の説明が入ります。</p>
                </div>
            </div>
            <?= $zandaka ?>
        </div>
    </div>
</div>
</body>
</html>