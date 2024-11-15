<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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
    <link rel="stylesheet" href="css/search_style.css">
    <link rel="stylesheet" href="css/Amozon_insta.css">
    <title>商品検索</title>
</head>

<body>
<div id="header" class="header">
    <div class="back"><div class="backBtn" onclick="history.back()"><img src="img/return_left.png" style="width:100%;"></div></div>
    <h1 class="h1_White">検索</h1>
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
                </ul>
            </div>
            <div>
                <ul class="menu-list-bottom">
                </ul>
            </div>
        </div>
        
        <div class="right-content">
            <form id="form" action="search_results.php" method="GET">
            <div class="flexBox">
                <label for="search">商品を検索</label>
                <input type="text" id="search" name="search">
                <button type="submit" id="submit" class="btn-img"></button>
            </div>
            </form>
        </div>
    </div>
</body>
</html>
<script>
const search = document.getElementById('search');
const submit = document.getElementById('submit');
const form = document.getElementById('form');
form.addEventListener('submit',(e) => {
    let searchValue = search.value;
    let str = searchValue.replace(/\s+/g, "");
    console.log(str);
    if(str === null || str === ""){
        e.preventDefault();
        return false;
    }else{
        return true;
    }
});
</script>