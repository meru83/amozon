<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/cartContentsStyle.css">
</head>
<body>
    
<?php
include "db_config.php";

// セッションを開始します
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if(isset($_GET['error_message'])){
    $error_message = $_GET['error_message'];
    echo $error_message;
}

$count = 0;
$countMax = 0;
$htmlText = "";
//セッションで管理されている場合
if(isset($_SESSION['cart'])){
    $lastImg = array();
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
                    WHERE p.product_id = ? && s.color_size_id = ?";
        $selectStmt = $conn->prepare($selectSql);
        $selectStmt->bind_param("ii",$product_id,$color_size_id);
        $selectStmt->execute();
        $selectResult = $selectStmt->get_result();
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
                $imgText = "
                <div id='divImg$i'>
                <a href='productsDetail.php?product_id=$product_id&color_size_id=$color_size_id'><img src='seller/p_img/$img_url' alt='$colorName 色,".$row['size']."サイズ'>
                </a>
                </div>";
            }//else{
                //ここで商品の画像が一枚もないときに表示する写真を表示するタブを作る。
            //}
            //画像にサイズと色の説明が出るようにする。
            if(!in_array($color_size_id, $lastImg)){
                echo $htmlText;
                echo $imgText;
                $lastImg[] = $color_size_id;
                $htmlText = <<<END
                <br>
                <div id="divText$i">
                <a href='productsDetail.php?product_id=$product_id&color_size_id=$color_size_id'>
                色: $colorName
                サイズ: $size<br>
                商品名　　: $productname<br>
                カテゴリ名: $category_name<br>
                価格　　　: $price<br>
                </a>
                <br>
                <input type="number" id="$i" value="$pieces" min="1" max="$maxPieces">
                <button type="button" id="delete$i" onclick="deleteProducts($i)">削除</button>
                <br>
                <hr>
                </div>
                END;
                // 他の情報も必要に応じて表示
                $count++;
            }else{
                echo $imgText;
            }
        }
        echo $htmlText;
        $htmlText = "";
    }
    $countJS = $count;
    //if($count !== 0){
        //echo $count . "件";
    //}else{
        //0件の場合のデザイン
    //}

    //関数化の場合。
    //countInCart();
if($count !== 0) {
    echo $count . "件";
} else {
    echo <<< HTML
    <div class="Amozon-container">
        <!-- Left Side Menu -->
        <div class="left-menu">
            <div>
                <ul class="menu-list">
                    <li class="menu-item"><a href=""><img src="img/cart_dake.svg" class="logo"><span class="menu-item-text-logo">Re.ReaD</span></a></li>
                    <li class="menu-item"><a href=""><img src="img/home.png" class="logo"></span><span class="menu-item-text">ホーム</span></a></li>
                    <li class="menu-item"><a href="search.php"><span class="menu-item-icon">🔍</span><span class="menu-item-text">検索</span></a></li>
                    <li class="menu-item"><a href=""><span class="menu-item-icon">📸</span><span class="menu-item-text">発見</span></a></li>
                    <li class="menu-item"><a href=""><span class="menu-item-icon">🎥</span><span class="menu-item-text">リール動画</span></li>
                    <li class="menu-item"><a href="chat_rooms.php"><img src="img/chat2.svg" class="logo"></span><span class="menu-item-text-chat">メッセージ</span></a></li>
                    <li class="menu-item"><a href=""><span class="menu-item-icon">❤️</span><span class="menu-item-text">お知らせ</span></a></li>
                    <li class="menu-item"><a href=""><span class="menu-item-icon">➕</span><span class="menu-item-text">#</span></a></li>
                    <li class="menu-item"><a href=""><img src="img/hito.png" class="logo"><span class="menu-item-text">プロフィール</span></a></li>
                </ul>
            </div>
          
            <div>
                <ul class="menu-list-bottom">
                    <li class="menu-item"><a href=""><span class="menu-item-icon">💬</span><span class="menu-item-text">Threads</span></a></li>
                    <li class="menu-item"><a href=""><img src="img/haguruma.svg" class="logo"></span><span class="menu-item-text">その他</span></a></li>
                </ul>
            </div>
        </div>
        
        <div class="right-content">
            <h1 class="rigt-content-center">カート</h1>
            <div class="rigt-content-center rigt-content-top">商品がありません</div>
            <div class="homeBack">ホームに戻る</div>
        </div>
    </div>
    HTML;
}

    echo <<<END
    <script>
    document.addEventListener('DOMContentLoaded',function(){
        var countMax = $countMax;
        for(let i = 0; i < countMax; i ++){
            var iId = document.getElementById(i);
            if(iId !== null){
                iId.addEventListener('change',function(){
                    piecesValue = iId.value;
                    console.log(piecesValue);
    
                    const formData = new FormData();
                    formData.append('piecesValue',piecesValue);
                    formData.append('i', i);
    
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
    END;
}else if(isset($_SESSION['user_id'])){
    //ログイン済みの時の処理を追加
    //データベースで管理
}

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

//関数化したログインしていないときの処理
// function countInCart(){
//     if($count !== 0) {
//         echo $count . "件";
//     } else {
//         echo "商品がありません";
//     }
// }
?>
<script>
    function deleteProducts(defdeleteI){
        const formData = new FormData();
        formData.append('i', defdeleteI);

        const xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function(){
            if(xhr.readyState === 4 && xhr.status === 200){
                var divImgI = document.getElementById('divImg'+defdeleteI);
                var divTextI = document.getElementById('divText'+defdeleteI);

                if(divImgI != null){
                    divImgI.remove();
                }
                divTextI.remove();
            }
        }

        xhr.open('POST','cartDelete.php',true);
        xhr.send(formData);
    }
</script>
</body>
</html>
