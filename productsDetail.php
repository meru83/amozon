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
    <link rel="stylesheet" href="css/productsDetail.css">
    <link rel="stylesheet" href="css/Amozon_insta.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <title>検索</title>
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
    <div class="back"><div class="backBtn" onclick="history.back()" style="width:48px; height: 100%; background:#fff;"><img src=""></div></div>
    <h1 class="h1_White">詳細</h1>
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
                    <li class="menu-item"><a href=""><span class="menu-item-icon">❤️</span><span class="menu-item-text">お知らせ</span></a></li>
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
        <form action="search_results.php" method="GET">
            <div class="flexBox">
                <label for="search">商品を検索</label>
                <input type="text" id="search" name="search">
                <button type="submit" class="btn-img"></button>
            </div>
        </form>
<?php
include "db_config.php";

$htmlText = "";
$sImgText = null;

if(isset($_GET['product_id']) && isset($_GET['color_size_id'])){
    $product_id = $_GET['product_id'];
    $color_size_id = $_GET['color_size_id'];
}

if(isset($_POST['sizeChange'])){
    $sizeChange = $_POST['sizeChange'];
    $product_id = $_POST['product_id'];
    $color_code = $_POST['color_code'];
    $detailSql = "SELECT p.product_id, p.productname, p.view, p.create_at, p.seller_id, p.quality, s.color_code, s.size, s.pieces, s.price, s.color_size_id, b.big_category_name, c.category_name, small_category_name, i.img_url FROM products p
                    LEFT JOIN color_size s ON (p.product_id = s.product_id)
                    LEFT JOIN big_category b ON (p.big_category_id = b.big_category_id)
                    LEFT JOIN category c ON (p.category_id = c.category_id)
                    LEFT JOIN small_category sc ON (p.small_category = sc.small_category)
                    LEFT JOIN products_img i ON (s.color_size_id = i.color_size_id)
                    WHERE p.product_id = ? && s.size = ? && color_code = ? && s.service_status = true";
    $detailStmt = $conn->prepare($detailSql);
    $detailStmt->bind_param("iss",$product_id,$sizeChange,$color_code);
}else{
    $detailSql = "SELECT p.product_id, p.productname, p.view, p.create_at, p.seller_id, p.quality, s.color_code, s.size, s.pieces, s.price, s.color_size_id, b.big_category_name, c.category_name, small_category_name, i.img_url FROM products p
                LEFT JOIN color_size s ON (p.product_id = s.product_id)
                LEFT JOIN big_category b ON (p.big_category_id = b.big_category_id)
                LEFT JOIN category c ON (p.category_id = c.category_id)
                LEFT JOIN small_category sc ON (p.small_category = sc.small_category)
                LEFT JOIN products_img i ON (s.color_size_id = i.color_size_id)
                WHERE p.product_id = ? && s.color_size_id = ? && s.service_status = true";
    $detailStmt = $conn->prepare($detailSql);
    $detailStmt->bind_param("ii",$product_id,$color_size_id);
}
$detailStmt->execute();
$detailResult = $detailStmt->get_result();
$lastImg = array();
if($detailResult && $detailResult->num_rows > 0){
    echo '<div class="targetTextAll">';//商品ごと
    echo '<div class="targetImgAll swiper float">';//画像
    echo '<div class="swiper-wrapper">';
    while($row = $detailResult->fetch_assoc()){
        $productName = $row['productname'];
        $view = !is_null($row['view'])?$row['view']:"";
        $create_at = $row['create_at'];
        $seller_id = $row['seller_id'];
        $big_category_name = !is_null($row['big_category_name'])?$row['big_category_name']:"";
        $category_name = !is_null($row['category_name'])?$row['category_name']:"";
        $small_category_name = !is_null($row['small_category_name'])?$row['small_category_name']:"";
        $quality = $row['quality'];
        $size = $row['size'];
        $pieces = $row['pieces'];
        $price = $row['price'];
        $commaPrice = number_format($price);
        $color_code = $row['color_code'];
        $colorName = getColor($conn, $color_code);
        $img_url = !is_null($row['img_url'])?$row['img_url']:null;
        if(!is_null($img_url)){
            echo '<div class="swiper-slide">';
            echo "<img src='seller/p_img/$img_url' alt='$colorName 色,".$row['size']."サイズ'>";
            echo "</div>";
        }else{
            echo '<div class="swiper-slide">';
            echo '<img src="img/noImg.jpg">';
            echo "</div>";
        }
        //画像にサイズと色の説明が出るようにする。
    }
    echo '</div>';
    echo <<<HTML
    <!-- If we need pagination -->
    <div class="swiper-pagination"></div>

    <!-- If we need navigation buttons -->
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>

    </div>

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
}else{
    echo <<< END
    <script>
        if(!alert("該当商品が見つかりませんでした。")){
            history.back();
        }
    </script>
    END;
}

$htmlText = <<<END
<br>
<div class="setumei">
<div class="flex">価格　　　　<div>￥<b class="b_price">$commaPrice</b></div></div>
<div class="flex">商品名　　　<div>$productName</div></div>
<div class="flex">カテゴリ　　<div>$big_category_name - $category_name - $small_category_name</div></div>
<div class="flex">概要　　　　<div>$view</div></div>
<div class="flex">品質　　　　<div>$quality</div></div>
<div class="flex">サイズ　　　<div>$size</div></div>
<div class="flex">色　　　　　<div>$colorName</div></div>
<div class="flex">出品日　　　<div>$create_at</div></div>
<div class="flex">出品者　　　<div>$seller_id</div></div>


<div>
<form name="sizeChangeForm" id="sizeChangeForm" method="post">
<input type="hidden" name="product_id" value="$product_id" required>
<input type="hidden" name="color_code" value="$color_code" required>
<label id="pSizeChange" for="sizeChange">ほかのサイズ　</label><select name="sizeChange" id="sizeChange" class="sizeChangeStyle">
    <option hidden selected>$size</option>
END;
$sizeSql = "SELECT s.size FROM products p
            LEFT JOIN color_size s ON(p.product_id = s.product_id)
            WHERE p.product_id = ? && s.color_code = ? && s.service_status = true";
$sizeStmt = $conn->prepare($sizeSql);
$sizeStmt->bind_param("is",$product_id, $color_code);
$sizeStmt->execute();
$sizeResult = $sizeStmt->get_result();
$otherSize = false;
while($row = $sizeResult->fetch_assoc()){
    $sizeChange = $row['size'];
    if(!($size === $sizeChange)){
        $htmlText .= "<option value='$sizeChange'>$sizeChange</option>";
        if($otherSize === false){
            //ほかのサイズがあればtrueなければfalseでscript実行
            $otherSize = true;
        }
    }
}  
$htmlText .= "</select></form>";

if($pieces > 0){    
    $htmlText .= <<<END
    <form action="innerCart.php" method="post">
        <input type="hidden" name="product_id" value="$product_id">
        <input type="hidden" name="color_size_id" value="$color_size_id">
        <label for="pieces">数量　</label>
        <select name="pieces" id="pieces" class="piecesStyle" required>
            <option hidden value="">選択してください</option>
    END;

    for($i = 1; $i <= $pieces; $i++){
        $htmlText .= "<option value='$i'>$i</option>";
    }
    $htmlText .= <<<END
        </select>
        <br>
        <button type="submit" name="submit" class="cart_btn">カートに入れる</button>
    </form>
    </div>
    <br>
    <br><br><br><br><br>
    END;
}else{
    $htmlText .= <<<END
    在庫なし
    <p class="sen">カートに入れる</p>
    <br>
    <br><br><br><br><br>
    END;
}

echo '</div>';

if(!($htmlText === "")){
    echo $htmlText;
    echo '</div>';
    echo '<hr>';
    echo '<br><br><br><br><br><br>';
    echo "<h2 class='noCart'>この商品の別のカラー</h2><br>";
}


if($otherSize === false){
    echo "
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const sizeChange = document.getElementById('sizeChange');
        const pSizeChange = document.getElementById('pSizeChange');
        if (sizeChange) {
            sizeChange.style.display = 'none';
            pSizeChange.style.display = 'none';
        }
    });    
    </script>";
}

$selectSql = "SELECT p.productname, s.color_size_id, s.color_code, s.size, s.pieces, s.price, i.img_url FROM products p
            LEFT JOIN color_size s ON (p.product_id = s.product_id)
            LEFT JOIN products_img i ON (s.color_size_id = i.color_size_id)
            WHERE p.product_id = ? && s.color_code != ? && s.service_status = true";
$selectStmt = $conn->prepare($selectSql);
$selectStmt->bind_param("is", $product_id, $color_code);
$selectStmt->execute();
$selectResult = $selectStmt->get_result();
$lastColorSize = array();
$tentative = "";
$sImgText = null;
echo '<div class="textAll none">';
echo '<div class="imgAll swiper">';
echo '<div class="swiper-wrapper">';
while($row = $selectResult->fetch_assoc()){
    $sImg_url = isset($row['img_url'])?$row['img_url']:null;
    $colorCode = $row['color_code'];
    $sColor_code = getColor($conn, $colorCode);
    $sSize = $row['size'];
    $sPieces = $row['pieces'];
    $sPrice = $row['price'];
    $sColor_size_id = $row['color_size_id'];
    if(!is_null($sImg_url)){
        $sImgText = <<<END
        <div class='swiper-slide'>
            <a href='productsDetail.php?product_id=$product_id&color_size_id=$sColor_size_id'>
                <img src='seller/p_img/$sImg_url' alt='服の写真'>
            </a>
        </div>
        END;
    }else{
         // ここで商品の画像が一枚もないときに表示する写真を表示するタブを作る。
        $sImgText = <<<END
        <div class="swiper-slide">
            <img src="img/noImg.jpg">
        </div>
        END;
    }
    if(!in_array($sColor_size_id, $lastColorSize)){
        echo <<<HTML
        </div>
        <!-- If we need pagination -->
        <div class="swiper-pagination"></div>
      
        <!-- If we need navigation buttons -->
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
      
        </div>
        HTML;
        echo $tentative;
        echo '</div>';
        echo '<div class="textAll">';
        echo '<div class="imgAll swiper">';
        echo '<div class="swiper-wrapper">';
        echo $sImgText;

        $lastColorSize[] = $sColor_size_id;
        $tentative = <<<END
        <br>
        <div class="sonota">
        <div class="sonota2">
        <a href='productsDetail.php?product_id=$product_id&color_size_id=$sColor_size_id'>
        色　　　$sColor_code<br>
        サイズ　$sSize<br>
        価格　　$sPrice<br>
        </a>
        END;
        if($pieces > 0){
            $tentative .= <<<END
            <form action="innerCart.php" method="post">
                <input type="hidden" name="product_id" value="$product_id">
                <input type="hidden" name="color_size_id" value="$color_size_id">
                <button type="submit" name="submit" class="cart_btn2">カートに入れる</button>
            </form>
            </div>
            </div>
            END;
        }else{
            $tentative .= "<p class='sen noCart'>カートに入れる</p><hr></div></div>";//商品がないときは灰色のただの文字列にしてカートにする<<<<<<<<<  CSS  >>>>>>>>>>
        }
    }else{
        echo $sImgText;
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
echo $tentative;
echo '</div>';
echo '</div>';
// 他の情報も必要に応じて表示

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

echo <<<HTML
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    const swiper2 = new Swiper('.swiper', {
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
<script>
document.addEventListener('DOMContentLoaded',function(){
    const sizeChangeForm = document.getElementById('sizeChangeForm');
    const sizeChange = document.getElementById('sizeChange');
    sizeChange.addEventListener('change',function(){
        sizeChangeForm.submit();
    });
});
</script>