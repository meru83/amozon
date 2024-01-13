<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <div class="space"></div>
            <h1 class="h1_White">カート</h1>
            <div class="space"></div>
        </div>

        <div class="Amozon-container">

        <!-- Left Side Menu -->
            <div class="left-menu">
                <div>
                    <ul class="menu-list">
                        <li class="menu-item-logo"><a href=""><img src="img/cart_dake.svg" class="logo"><span class="menu-item-text-logo">Re.ReaD</span></a></li>
                        <li class="menu-item"><a href=""><img src="img/home.png" class="logo"><span class="menu-item-text">ホーム</span></a></li>
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

<?php
include "db_config.php";

// セッションを開始します
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
}

//エラーメッセージががある場合
if(isset($_GET['error_message'])){
    $error_message = $_GET['error_message'];
    echo $error_message;
}

$count = 0;
$countMax = 0;
$htmlText = "";
$lastImg = array();
//セッションで管理されている場合

if(isset($user_id)){
    //ログイン済みの時の処理を追加
    //データベースで管理
    $countMax++;
    $logSql = "SELECT c.product_id, c.color_size_id, c.pieces AS cartPieces, p.productname, p.quality, s.service_status, s.color_code, s.size, s.pieces AS maxPieces, s.price, i.img_url FROM cart c
                LEFT JOIN products p ON (c.product_id = p.product_id)
                LEFT JOIN color_size s ON (c.color_size_id = s.color_size_id)
                LEFT JOIN products_img i ON (c.color_size_id = i.color_size_id)
                WHERE c.user_id = ?";
    $logStmt = $conn->prepare($logSql);
    $logStmt->bind_param("s",$user_id);
    $logStmt->execute();
    $logResult = $logStmt->get_result();
    if($logResult && $logResult->num_rows > 1){
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
                $productname = $row['productname'];
                $quality = $row['quality'];
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
                if(!in_array($color_size_id, $lastImg)){
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
                    echo '<div class="imgAll swiper">';
                    echo '<div class="swiper-wrapper">';
                    echo $imgText;
                    $lastImg[] = $color_size_id;
                    $htmlText = <<<END
                    <br>
                    <a href='productsDetail.php?product_id=$product_id&color_size_id=$color_size_id'>
                    色: $colorName
                    サイズ: $size<br>
                    商品名　　: $productname<br>
                    価格　　　: $price<br>
                    </a>
                    <br>
                    END;
                    if($maxPieces >= $cartPieces){
                        $htmlText .= <<<END
                        <input type="hidden" id="product_id$count" value="$product_id">
                        <input type="hidden" id="color_size_id$count" value="$color_size_id">
                        <input type="number" id="$count" value="$cartPieces" min="1" max="$maxPieces">
                        <button type="button" id="delete$count" onclick="deleteProducts($count)">削除</button>
                        <br>
                        <hr>
                        END;
                        $count++;
                    }else{
                        $htmlText .= <<<END
                        在庫不足<br>
                        商品はカートから削除されます<br>
                        <br>
                        <hr>
                        END;
                        $deleteSql = "DELETE FROM cart WHERE user_id = ? && product_id = ? && color_size_id = ?";
                        $deleteStmt = $conn->prepare($deleteSql);
                        $deleteStmt->bind_param("sii", $user_id, $product_id, $color_size_id);
                        $deleteStmt->execute();
                    }
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
        echo $htmlText;
        echo '</div>';
        $htmlText = "";
    }

    if($count !== 0) {
        echo $count . "件";
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
        if($selectResult && $selectResult->num_rows > 1){
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
                    $htmlText = <<<END
                    <br>
                    <a href='productsDetail.php?product_id=$product_id&color_size_id=$color_size_id'>
                    色: $colorName
                    サイズ: $size<br>
                    商品名　　: $productname<br>
                    価格　　　: $price<br>
                    </a>
                    <br>
                    END;
                    if($maxPieces >= $pieces){
                        $htmlText .= <<<END
                        <input type="number" id="$i" value="$pieces" min="1" max="$maxPieces">
                        <button type="button" id="delete$i" onclick="deleteProducts($i)">削除</button>
                        <br>
                        END;
                        $count++;
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
            echo '</div>';
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

    if($count !== 0) {
        echo '<div class="hr">';
        echo '<hr>';
        echo $count . "件";
        echo '</div>';
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
    for(let i = 0; i < countMax; i++){
        var iId = document.getElementById(i);
        var productElement = document.getElementById('product_id'+i);
        var colorSizeElement = document.getElementById('color_size_id'+i);
        if(iId !== null){
            iId.addEventListener('change',function(){
                piecesValue = iId.value;
                if(productElement !== null && colorSizeElement !== null){
                    product_id = productElement.value;
                    color_size_id = colorSizeElement.value;
                }else{
                    product_id = null;
                    color_size_id = null;
                }

                const formData = new FormData();
                formData.append('piecesValue', piecesValue);
                formData.append('i', i);
                formData.append('product_id', product_id);
                formData.append('color_size_id', color_size_id);

                const xhr = new XMLHttpRequest();

                xhr.onreadystatechange = function(){
                    if(xhr.readyState === 4 && xhr.status === 200){
                        //console.log(i);
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
    const formData = new FormData();
    formData.append('i', defdeleteI);

    const xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function(){
        if(xhr.readyState === 4 && xhr.status === 200){
            window.location.reload();
        }
    }

    xhr.open('POST','cartDelete.php',true);
    xhr.send(formData);
}
</script>


</body>
</html>
