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
    <link rel="stylesheet" href="css/cartContentsStyle.css">
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
            <h1 class="h1_White">カート</h1>
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

<?php
//エラーメッセージががある場合
if(isset($_GET['error_message'])){
    $error_message = $_GET['error_message'];
    echo $error_message;
}

$count = 0;
$countMax = 0;
$piecesCount = 0;
$htmlText = "";
$lastImg = array();
$arrayProductId = array();
$arrayPieces = array();
$arrayPrice = array();
$priceMax = 0;
//セッションで管理されている場合

if(!($user_id === "A")){
    //ログイン済みの時の処理を追加
    //データベースで管理
    $logSql = "SELECT c.product_id, c.color_size_id, c.pieces AS cartPieces, p.productname, p.quality, s.service_status, s.color_code, s.size, s.pieces AS maxPieces, s.price, i.img_url, f.user_id AS favorite_product FROM cart c
                LEFT JOIN products p ON (c.product_id = p.product_id)
                LEFT JOIN color_size s ON (c.color_size_id = s.color_size_id)
                LEFT JOIN products_img i ON (c.color_size_id = i.color_size_id)
                LEFT JOIN favorite f ON (p.product_id = f.product_id) && (s.color_size_id = f.color_size_id) && (f.user_id = ?)
                WHERE c.user_id = ?";
    $logStmt = $conn->prepare($logSql);
    $logStmt->bind_param("ss", $user_id ,$user_id);
    $logStmt->execute();
    $logResult = $logStmt->get_result();
    if($logResult && $logResult->num_rows > 0){
        echo '<div class="htmlAll none">';
        echo '<div class="imgAll swiper">';
        echo '<div class="swiper-wrapper">';
        while($row = $logResult->fetch_assoc()){
            $service_status = $row['service_status'];
            if($service_status == true){
                $imgText = null;
                $product_id = $row['product_id'];
                $color_size_id = $row['color_size_id'];
                $colorCode = $row['color_code'];
                $colorName = getColor($conn, $colorCode);
                $size = $row['size'];
                $cartPieces = $row['cartPieces'];
                $maxPieces = $row['maxPieces'];
                $price  = $row['price'];
                $commaPrice = number_format($price);
                $productname = $row['productname'];
                $quality = $row['quality'];
                $img_url = is_null($row['img_url'])?null:$row['img_url'];
                $favorite_product = ($row['favorite_product'] === null)?null:$row['favorite_product'];
                if(!is_null($img_url)){
                    $imgText = <<<END
                    <div class="swiper-slide">
                        <a href='productsDetail.php?product_id=$product_id&color_size_id=$color_size_id'><img src='seller/p_img/$img_url' alt=''></a>
                    </div>
                    END;
                }else{
                    //ここで商品の画像が一枚もないときに表示する写真を表示するタブを作る。
                    $imgText = <<<END
                    <div class="swiper-slide">
                        <a href='productsDetail.php?product_id=$product_id&color_size_id=$color_size_id'><img src='img/noImg.jpg' alt=''></a>
                    </div>
                    END;
                }
                if(!in_array($color_size_id, $lastImg)){
                    $piecesCount += $cartPieces;
                    $priceMax += $price * $cartPieces;
                    echo '</div>';
                    echo <<<HTML
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

                    $lastImg[] = $color_size_id;
                    $arrayProductId[] = $product_id;
                    $arrayPieces[] = $cartPieces;
                    $arrayPrice[] = $price;
                    $htmlText = <<<END
                    <br>
                    <a href='productsDetail.php?product_id=$product_id&color_size_id=$color_size_id'>
                    <b>色</b>: $colorName
                    <b>サイズ</b>: $size<br>
                    <b>商品名</b>　　: $productname<br>
                    <b>価格</b>　　　: ￥ $commaPrice<br>
                    </a>
                    <br>
                    END;
                    //$favorite_product null か $user_id
                    if(!($favorite_product === null) && isset($_SESSION['user_id'])){
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
                    if($maxPieces >= $cartPieces){
                        $htmlText .= <<<END
                        <input type="hidden" id="product_id$count" value="$product_id">
                        <input type="hidden" id="color_size_id$count" value="$color_size_id">
                        <input type="hidden" id="price$count" value="$price">
                        <input type="number" id="$count" class="selectStyle" value="$cartPieces" min="1" max="$maxPieces">
                        <button type="button" id="delete$count" class="btnStyle"onclick="deleteProducts($count)">削除</button>
                        <br>
                        END;
                    }else{
                        $htmlText .= <<<END
                        在庫不足<br>
                        商品はカートから削除されます<br>
                        <br>
                        END;
                        $deleteSql = "DELETE FROM cart WHERE user_id = ? && product_id = ? && color_size_id = ?";
                        $deleteStmt = $conn->prepare($deleteSql);
                        $deleteStmt->bind_param("sii", $user_id, $product_id, $color_size_id);
                        $deleteStmt->execute();
                    }
                    $count++;
                    $countMax++;
                    // 他の情報も必要に応じて表示
                }else{
                    echo $imgText;
                }
            }else{
                echo <<<END
                <br>
                以前登録されていた商品は販売者の都合により削除されました
                END;

                $deleteSql = "DELETE FROM cart WHERE user_id = ? && product_id = ? && color_size_id = ?";
                $deleteStmt = $conn->prepare($deleteSql);
                $deleteStmt->bind_param("sii", $user_id, $product_id, $color_size_id);
                $deleteStmt->execute();
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
        $htmlText = "";
    }
    $commaPriceMax = number_format($priceMax);
    if($count !== 0) {
        //ヒアドキュメントで書けよ
        echo '<div class="subtotal">';
        echo '<div class="subtotalChild">';
        echo "小計 (<b id='piecesCountElement'>$piecesCount</b> 個の商品) (税込)";
        echo "<input type='hidden' name='piecesCount' id='piecesCount' value='$piecesCount'>";
        echo '</div>';
        echo "<b>￥</b><b id='commaPriceMax'>$commaPriceMax</b>";
        echo "<input type='hidden' name='maxPrice' id='maxPrice' value='$priceMax'>";
        // echo "<form action='buyProducts.php' method='post'>";
        // echo "<input type='hidden' name='maxPrice' id='maxPrice' value='$priceMax'>";
        // for($i = 0; $i < count($lastImg); $i++){
        //     echo <<<END
        //     <input type="hidden" name="arrayPieces[]" value="$arrayPieces[$i]">
        //     <input type="hidden" name="arrayPrice[]" value="$arrayPrice[$i]">
        //     <input type="hidden" name="buyProductId[]" value="$arrayProductId[$i]">
        //     <input type="hidden" name="buyColorSize[]" value="$lastImg[$i]">
        //     END;
        // }
        // echo <<<END
        // <input type="submit" class="btnStyle" value="レジに進む">
        // </form>
        // END;
        echo <<<END
        <br>
        <button type="button" class="btnStyle" onclick="location.href='buyProducts.php'">レジに進む</button>
        </div>
        END;
    }else{
        //0件
        //ここ！！！！！！！！と一緒のデザイン
        echo "<div class='cart_no'>カートに商品は登録されていません";
        echo '<a href="user_top.php"><div class="home">ホームに戻る</div></a>';
        echo '</div>';
    }
}else if(isset($_SESSION['cart'])){
    //未ログの時(カートのsessionがある時)
    for($i = 0; $i < count($_SESSION['cart']['product_id']); $i++){
        $countMax++;
        $product_id = isset($_SESSION['cart']['product_id'][$i])?$_SESSION['cart']['product_id'][$i]:null;
        $color_size_id = isset($_SESSION['cart']['color_size_id'][$i])?$_SESSION['cart']['color_size_id'][$i]:null;
        $pieces = isset($_SESSION['cart']['pieces'][$i])?$_SESSION['cart']['pieces'][$i]:null;
        $selectSql = "SELECT p.productname, p.view, p.create_at, p.seller_id, p.quality, s.color_size_id, s.color_code, s.size, s.pieces, s.price, i.img_url, b.big_category_name, c.category_name, sc.small_category_name FROM products p
                    LEFT JOIN color_size s ON (p.product_id = s.product_id)
                    LEFT JOIN big_category b ON (p.big_category_id = b.big_category_id)
                    LEFT JOIN category c ON (p.category_id = c.category_id)
                    LEFT JOIN small_category sc ON (p.small_category = sc.small_category)
                    LEFT JOIN products_img i ON (s.color_size_id = i.color_size_id)
                    WHERE p.product_id = ? && s.color_size_id = ? && s.service_status = true";
        $selectStmt = $conn->prepare($selectSql);
        $selectStmt->bind_param("ii",$product_id,$color_size_id);
        $selectStmt->execute();
        $selectResult = $selectStmt->get_result();
        if($selectResult && $selectResult->num_rows > 0){
            echo '<div class="htmlAll none">';
            echo '<div class="imgAll swiper">';
            echo '<div class="swiper-wrapper">';
            while ($row = $selectResult->fetch_assoc()) {
                $imgText = null;
                $colorCode = $row['color_code'];
                $colorName = getColor($conn, $colorCode);
                $size = $row['size'];
                $maxPieces = $row['pieces'];
                $productname = $row['productname'];
                $category_name = !is_null($row['category_name'])?$row['category_name']:"";
                $price  = $row['price'];
                $commaPrice = number_format($price);
                $color_size_id = $row['color_size_id'];
                $img_url = is_null($row['img_url'])?null:$row['img_url'];
                if(!is_null($img_url)){
                    $imgText = <<<END
                    <div class="swiper-slide">
                        <a href='productsDetail.php?product_id=$product_id&color_size_id=$color_size_id'><img src='seller/p_img/$img_url' alt=''></a>
                    </div>
                    END;
                }else{
                    //ここで商品の画像が一枚もないときに表示する写真を表示するタブを作る。
                    $imgText = <<<END
                    <div class="swiper-slide">
                        <a href='productsDetail.php?product_id=$product_id&color_size_id=$color_size_id'><img src='img/noImg.jpg' alt=''></a>
                    </div>
                    END;
                }
                //画像にサイズと色の説明が出るようにする。
                if(!in_array($color_size_id, $lastImg)){
                    $priceMax += $price * $pieces;
                    $piecesCount += $pieces;
                    echo '</div>';
                    echo <<<HTML
                    <!-- If we need pagination -->
                    <div class="swiper-pagination"></div>
                  
                    <!-- If we need navigation buttons -->
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>

                    </div>
                    HTML;
                    echo $htmlText;
                    echo '</div>';
                    echo '<div class="htmlAll">';
                    echo '<div class="imgAll swiper float">';
                    echo '<div class="swiper-wrapper">';
                    echo $imgText;

                    $lastImg[] = $color_size_id;
                    $arrayProductId[] = $product_id;
                    $htmlText = <<<END
                    <br>
                    <a href='productsDetail.php?product_id=$product_id&color_size_id=$color_size_id'>
                    <b>色</b>: $colorName
                    <b>サイズ</b>: $size<br>
                    <b>商品名</b>　　: $productname<br>
                    <b>価格</b>　　　: ￥ $commaPrice<br>
                    </a>
                    <br>
                    <div class="sonota">
                    <button type="button" class="heartBtn" onclick="heartButton()"><img src="img/heart2.png" style="height: 100%;"></button>
                    END;

                    if($maxPieces >= $pieces){
                        $htmlText .= <<<END
                        <input type="hidden" id="product_id$i" value="$product_id">
                        <input type="hidden" id="color_size_id$i" value="$color_size_id">
                        <input type="hidden" id="price$i" value="$price">
                        <input type="number" id="$i" class="selectStyle" value="$pieces" min="1" max="$maxPieces">
                        <button type="button" id="delete$i" class="btnStyle" onclick="deleteProducts($i)">削除</button>
                        <br>
                        END;
                    }else{
                        $htmlText .= <<<END
                        在庫なし<br>
                        商品はカートから削除されます<br>
                        <br>
                        END;
                        $_SESSION['cart']['product_id'][$i] = null;
                        $_SESSION['cart']['color_size_id'][$i] = null;
                        $_SESSION['cart']['pieces'][$i] = null;
                    }
                    $count++;
                    // 他の情報も必要に応じて表示
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
            echo '</div>';//sonota
            echo '</div>';
            echo '</div>';
            echo '<div class="hr">';
            echo '<hr>';
            echo '</div>';
            $htmlText = "";
        }else if(!($_SESSION['cart']['product_id'][$i] === null) && !($_SESSION['cart']['color_size_id'][$i] === null) && !($_SESSION['cart']['pieces'][$i] = null)){
            echo <<<END
            <br>
            以前登録されていた商品は販売者の都合により削除されました
            END;
            
            $_SESSION['cart']['product_id'][$i] = null;
            $_SESSION['cart']['color_size_id'][$i] = null;
            $_SESSION['cart']['pieces'][$i] = null;
        }
    }
    $commaPriceMax = number_format($priceMax);
    if($count !== 0) {
        echo '<div class="subtotal">';
        echo '<div class="subtotalChild">';
        echo "小計 (<b id='piecesCountElement'>$piecesCount</b> 個の商品) (税込)";
        echo "<input type='hidden' name='piecesCount' id='piecesCount' value='$piecesCount'>";
        echo '</div>';
        echo "<b>￥</b><b id='commaPriceMax'>$commaPriceMax</b>";
        echo "<input type='hidden' name='maxPrice' id='maxPrice' value='$priceMax'>";
        echo <<<HTML
        <br>
        <button type="button" class="btnStyle" onclick="buttonClick()">レジに進む</button>
        </div>
        HTML;
    }else{
        //0件
        //ここ！！！！！！！！と一緒のデザイン
        echo "<div class='cart_no'>カートに商品は登録されていません";
        echo '<a href="user_top.php"><div class="home">ホームに戻る</div></a>';
        echo '</div>';
    }
}else{
    //カートのsessionもないとき
    //ここ！！！！！！！！と一緒のデザイン
    echo "<div class='cart_no'>カートに商品は登録されていません";
    echo '<a href="user_top.php"><div class="home">ホームに戻る</div></a>';
    echo '</div>';
}

echo '</div>';//<div class="right-content">
echo '</div>';//<div class="Amozon-container">
echo '</body>';
echo '</html>';

echo <<<HTML
<script>
document.addEventListener('DOMContentLoaded',function(){
    var countMax = $countMax;
    var maxPrice = Number(document.getElementById('maxPrice').value);
    var piecesCount = Number(document.getElementById('piecesCount').value);
    var iIdValue = [];
    for(let i = 0; i < countMax; i++){
        var iId = document.getElementById(i);//在庫数
        if(iId !== null){
            iIdValue[i] = iId.value;//元の在庫数を格納する配列
            iId.addEventListener('change',function(){
                var productElement = document.getElementById('product_id'+i);
                var colorSizeElement = document.getElementById('color_size_id'+i);
                var priceElement = document.getElementById('price'+i);
                // console.log(priceElement);
                piecesValue = this.value;//変更後の値
                // console.log(piecesValue);
                // console.log(iIdValue[i]);
                if(productElement !== null && colorSizeElement !== null && priceElement !== null){
                    var product_id = productElement.value;
                    var color_size_id = colorSizeElement.value;
                    var price = Number(priceElement.value);
                }else{
                    product_id = null;
                    color_size_id = null;
                    price = null
                }
                // console.log(price);

                const formData = new FormData();
                formData.append('piecesValue', piecesValue);
                formData.append('i', i);
                formData.append('product_id', product_id);
                formData.append('color_size_id', color_size_id);

                const xhr = new XMLHttpRequest();

                xhr.onreadystatechange = function(){
                    if(xhr.readyState === 4 && xhr.status === 200){
                        try {
                            const response = JSON.parse(xhr.responseText);
                            response.forEach(function(row) {
                                if(row.error_message){
                                    var commaPriceMaxElement = document.getElementById('commaPriceMax');
                                    var piecesCountElement = document.getElementById('piecesCountElement');
                                    // console.log(piecesCountElement);
                                    // console.log(piecesCount);
                                    // console.log(maxPrice);
                                    // console.log(piecesValue);
                                    // console.log(iIdValue[i]);
                                    maxPrice +=  price * (piecesValue - iIdValue[i]);
                                    piecesCount += piecesValue - iIdValue[i];
                                    iIdValue[i] = piecesValue;
                                    piecesCountElement.textContent = piecesCount;
                                    // console.log(maxPrice);
                                    commaPriceMax = maxPrice.toLocaleString();//コンマ区切り
                                    commaPriceMaxElement.textContent = commaPriceMax;
                                    console.log(maxPrice);
                                    console.log(piecesCount);
                                    console.log(piecesValue);
                                    console.log(iIdValue[i]);
                                }
                            });
                        } catch (error) {
                            console.error("Error parsing JSON response:", error);
                            alert("リクエストが失敗しました。");
                        }
                    }
                }

                xhr.open('POST','increment.php',true);
                xhr.send(formData);
            });
        }
    }
});
</script>
HTML;

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
function deleteProducts(defdeleteI){
    var productElement = document.getElementById('product_id'+defdeleteI);
    var colorSizeElement = document.getElementById('color_size_id'+defdeleteI);
    const formData = new FormData();
    if(productElement !== null && colorSizeElement !== null){
        var product_id = productElement.value;
        var color_size_id = colorSizeElement.value;
    }else{
        var product_id = null;
        var color_size_id = null;
    }
    formData.append('i', defdeleteI);
    formData.append('product_id', product_id);
    formData.append('color_size_id', color_size_id);

    const xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function(){
        if(xhr.readyState === 4 && xhr.status === 200){
            window.location.reload();
        }
    }

    xhr.open('POST','cartDelete.php',true);
    xhr.send(formData);
}

function heartButton(){
    alert("お気に入り登録にはログインを完了させてください。");
}

function buttonClick(){
    window.location.href = "user_top.php";
}
</script>
</body>
</html>
