<?php
include "db_config.php";

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

session_regenerate_id(TRUE);
if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
}else{
    header("Location:login.php");
    exit();
}

if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
    $foo2 = <<<END
    <div style="width:100%; text-align: right; height: fit-content;">
    <form action="logout.php" method="post">
        <input type="submit" name="logout" class="log_out" value="ログアウト">
    </form>
    </div>
    END;
}else{
    $user_id = "A";
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
    <link rel="stylesheet" href="css/Amozon_insta.css">
    <link rel="stylesheet" href="css/favorite.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <style>
        .swiper {
            width: 500px;
            max-width: 100%; 
            height: 300px; 
        }
        .swiper-slide img {
            width: 500px;
            height: 300px;
        }
    </style>
</head>
<body>
    <div id="header" class="header">
        <div class="back"><div class="backBtn" onclick="history.back()"><img src="img/return_left.png" style="width:100%;"></div></div>
        <h1 class="h1_White">お気に入り</h1>
        <?=$foo2?>
    </div>

        <div class="Amozon-container">
        <!-- Left Side Menu -->
            <div class="left-menu">
                <div>
                    <ul class="menu-list">
                        <li class="menu-item-logo"><a href=""><img src="img/cart_dake.svg" class="logo"><span class="menu-item-text-logo">Re.ReaD</span></a></li>
                        <li class="menu-item"><a href="user_top.php"><img src="img/home.png" class="logo"><span class="menu-item-text">ホーム</span></a></li>
                        <li class="menu-item"><a href="search.php"><img src="img/musimegane.png" class="logo"><span class="menu-item-text">検索</span></a></li>
                        <li class="menu-item"><a href="cartContents.php"><img src="img/cart.png" class="logo"><span class="menu-item-text">カート</span></a></li>
                        <li class="menu-item"><a href="chat_rooms.php"><img src="img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>
                        <li class="menu-item"><a href="favoriteProduct.php"><img src="img/heartBlack.png" class="logo"><span class="menu-item-text">お気に入り</span></a></li>
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

<?php
$sql = "SELECT f.user_id AS favorite_product, f.product_id, f.color_size_id, p.productname, s.color_code, s.size, s.service_status, s.pieces, s.price, i.img_url
        FROM favorite f
        LEFT JOIN products p ON (f.product_id = p.product_id)
        LEFT JOIN color_size s ON (f.color_size_id = s.color_size_id)
        LEFT JOIN products_img i ON (s.color_size_id = i.color_size_id)
        WHERE f.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s",$user_id);
$stmt->execute();
$result = $stmt->get_result();
$htmlText = "";
$count = 0;
if($result && $result->num_rows > 0){
    $lastImg = array();
    echo '<div class="htmlAll none">';
    echo '<div class="imgAll swiper">';
    echo '<div class="swiper-wrapper">';
    while($row = $result->fetch_assoc()){
        $favorite_product = !is_null($row['favorite_product'])?$row['favorite_product']:null;
        $product_id = $row['product_id'];
        $color_size_id = $row['color_size_id'];
        $productname = $row['productname'];
        $color_code = $row['color_code'];
        $colorName = getColor($conn,$color_code);
        $size = $row['size'];
        $pieces = $row['pieces'];
        $price = $row['price'];
        $commaPrice = number_format($price);
        $service_status = $row['service_status'];
        $img_url = is_null($row['img_url'])?null:$row['img_url'];

        if(!is_null($img_url)){
            $imgText = "
            <div class='swiper-slide'>
            <img src='seller/p_img/$img_url'>
            </div>";
        }else{
            //ここで商品の画像が一枚もないときに表示する写真を表示するタブを作る。
            $imgText = "
            <div class='swiper-slide'>
            <img src='img/noImg.jpg'>
            </div>";
        }
        if(!in_array($color_size_id, $lastImg)){
            $lastImg[] = $color_size_id;
            echo <<<HTML
            </div>
            <!-- If we need pagination -->
            <div class="swiper-pagination"></div>
            
            <!-- If we need navigation buttons -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
            
            </div>
            HTML;
            echo '<div class="textStyle">';
            echo $htmlText;                   
            echo '</div>';
            echo '</div>';
            if (!($count === 0)) {
                echo '<div class="hr">';
                echo '<hr>';
                echo '</div>';
            }
            echo '<div class="htmlAll">';
            echo '<div class="imgAll swiper float">';
            echo '<div class="swiper-wrapper">';
            echo $imgText;
            $htmlText = <<<END
            $productname<br>
            $colorName<br>
            $size<br>
            ￥$commaPrice<br>
            END;
            //$favorite_product null か $user_id
            if(!is_null($favorite_product)){
                $htmlText .= <<<END
                <div class="sonota">
                <label class="checkHeart" for="favorite$count">
                    <input type="checkbox" id="favorite$count" checked>
                    <span class="spanHeart"></span>
                </label>
                END;
            }else if(isset($_SESSION['user_id'])){
                $htmlText .= <<<END
                <div class="sonota">
                <label class="checkHeart" for="favorite$count">
                    <input type="checkbox" id="favorite$count">
                    <span class="spanHeart"></span>
                </label>
                END;
            }else{
                $htmlText .= <<<END
                <div class="sonota">
                <button type="button" class="heartBtn" onclick="heartButton()"><img src="img/heart2.png" style="height: 100%;"></button>
                END;
            }
            if($pieces > 0){
                $htmlText .= <<<END
                <form action="innerCart.php" method="post">
                    <input type="hidden" id="product_id$count" name="product_id" value="$product_id">
                    <input type="hidden" id="color_size_id$count" name="color_size_id" value="$color_size_id">
                    <button type="submit" name="submit" class="cart_btn">カートに入れる</button>
                </form>
                END;
            }else{
                $htmlText .= "<p class='sen'>カートに入れる</p>";//商品がないときは灰色のただの文字列にしてカートにする<<<<<<<<<  CSS  >>>>>>>>>>
            }          
            $count++;
        }else{
            echo $imgText;
        }
    }
    echo <<<HTML
    </div>
    <!-- If we need pagination -->
    <div class="swiper-pagination"></div>

    <!-- If we need navigation buttons -->
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>

    </div>
    HTML;
    echo '<div class="textStyle">';
    echo $htmlText;
    echo '</div>';
    echo '</div>';
    echo '<div class="hr">';
    echo '<hr>';
    echo '</div>';
}else{
    //お気に入り商品がないとき
}

echo '</div>';//<div class="right-content">
echo '</div>';//<div class="Amozon-container">
echo '</body>';
echo '</html>';

function getColor($conn, $color_code){
    $colorSql = "SELECT * FROM color_name
                WHERE color_code = ?";
    $colorStmt = $conn->prepare($colorSql);
    $colorStmt->bind_param("s",$color_code);
    $colorStmt->execute();
    $colorResult = $colorStmt->get_result();
    if ($row = $colorResult->fetch_assoc()) {
        $colorName = $row['colorName']; // ここで正しいカラム名を使用
        return $colorName;
    } 
}

// データベース接続を閉じる
$conn->close();

echo <<<END
<script>
var countMax = $count;
for(let i = 0; i < countMax; i++){
    var favorite_product = document.getElementById('favorite'+i);
    if(favorite_product !== null){
        favorite_product.addEventListener('change', function(){
            var checkState = this.checked;
            var product_id = document.getElementById('product_id'+i).value;
            var color_size_id = document.getElementById('color_size_id'+i).value;
            var favoriteChecked = checkState ? 1 : 0;
            const formData = new FormData();
            formData.append('product_id', product_id);
            formData.append('color_size_id', color_size_id);
            formData.append('favoriteChecked', favoriteChecked);
            fetch('changeFavorite.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if(!response.ok){
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.error_message === 1) {
                    if (favoriteChecked === 1) {
                        this.checked = true;
                    } else {
                        this.checked = false;
                    }
                } else {
                    if (favoriteChecked === 1) {
                        alert("お気に入り登録に失敗しました。");
                        this.checked = false;
                    } else {
                        alert("お気に入り商品の削除に失敗しました。");
                        this.checked = true;
                    }
                }
            })
            .catch(error => {
                console.error("Fetch error:", error);
                alert("リクエストが失敗しました。");
            });
        });
    }
}
</script>
END;
echo <<<HTML
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    const swiper = new Swiper('.swiper', {
        // Optional parameters
        direction: 'horizontal',
        loop: true,
        speed: 1000,
        effect: 'coverflow',

        // // If we need pagination
        // pagination: {
        //     el: '.swiper-pagination',
        //     type: 'progressbar',
        // },

        // Navigation arrows
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },

        // // And if we need scrollbar
        // scrollbar: {
        //     el: '.swiper-scrollbar',
        //     hide:true,
        // },
    });
</script>
HTML;
?>