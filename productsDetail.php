<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/search_style.css">
    <title>検索</title>
</head>
<body>
<h1>商品詳細</h1>
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
        $color_code = $row['color_code'];
        $colorName = getColor($conn, $color_code);
        $img_url = !is_null($row['img_url'])?$row['img_url']:null;
        if(!is_null($img_url)){
            echo "<img src='seller/p_img/$img_url' alt='$colorName 色,".$row['size']."サイズ'>";
        }//else{
            //ここで商品の画像が一枚もないときに表示する写真を表示するタブを作る。
        //}
        //画像にサイズと色の説明が出るようにする。
    }
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
<div>価格　　　　￥<b class="b_price">$price</b></div>
<div>商品名　　　$productName</div>
<div>カテゴリ　　$big_category_name - $category_name - $small_category_name</div>
<div>概要　　　　$view</div>
<div>品質　　　　$quality</div>
<div>サイズ　　　$size</div>
<div>色　　　　　$colorName</div>
<div>出品日　　　$create_at</div>
<div>出品者　　　$seller_id</div>
<hr>

<form name="sizeChangeForm" id="sizeChangeForm" method="post">
<input type="hidden" name="product_id" value="$product_id" required>
<input type="hidden" name="color_code" value="$color_code" required>
<label id="pSizeChange" for="sizeChange">ほかのサイズ：</label><select name="sizeChange" id="sizeChange">
    <option hidden selected>$size</option>
END;
$sizeSql = "SELECT s.size FROM products p
            LEFT JOIN color_size s ON(p.product_id = s.product_id)
            WHERE p.product_id = ? && s.color_code = ?";
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

if($pieces > 0){    
    $htmlText .= <<<END
    <form action="innerCart.php" method="post">
        <input type="hidden" name="product_id" value="$product_id">
        <input type="hidden" name="color_size_id" value="$color_size_id">
        <label for="pieces">数量：</label>
        <select name="pieces" id="pieces" required>
            <option hidden value="">選択してください</option>
    END;

    for($i = 1; $i <= $pieces; $i++){
        $htmlText .= "<option value='$i'>$i</option>";
    }
    $htmlText .= <<<END
        </select>
        <button type="submit" name="submit">カートに入れる</button>
    </form>
    <br>
    <hr>
    <br><br><br><br><br>
    END;
}else{
    $htmlText .= <<<END
    在庫なし
    <p class="">カートに入れる</p>
    <br>
    <hr>
    <br><br><br><br><br>
    END;
}

if(!($htmlText === "")){
    echo $htmlText;
    echo "<h2>この商品の別のカラー：</h2><br>";
}

$selectSql = "SELECT p.productname, s.color_size_id, s.color_code, s.size, s.price, i.img_url FROM products p
            LEFT JOIN color_size s ON (p.product_id = s.product_id)
            LEFT JOIN products_img i ON (s.color_size_id = i.color_size_id)
            WHERE p.product_id = ? && s.color_code != ?";
$selectStmt = $conn->prepare($selectSql);
$selectStmt->bind_param("is", $product_id, $color_code);
$selectStmt->execute();
$selectResult = $selectStmt->get_result();
$lastColorSize = array();
$tentative = "";
while($row = $selectResult->fetch_assoc()){
    $sImg_url = isset($row['img_url'])?$row['img_url']:null;
    $colorCode = $row['color_code'];
    $sColor_code = getColor($conn, $colorCode);
    $sSize = $row['size'];
    $sPrice = $row['price'];
    $sColor_size_id = $row['color_size_id'];
    if(!is_null($sImg_url)){
        $sImgText = "<a href='productsDetail.php?product_id=$product_id&color_size_id=$sColor_size_id'><img src='seller/p_img/$sImg_url' alt='服の写真'></a>";
    }//else{
    //     // ここで商品の画像が一枚もないときに表示する写真を表示するタブを作る。
    //     $sImgText = "<a><img><</a>";
    // }
    if(!in_array($sColor_size_id, $lastColorSize)){
        echo $tentative;
        echo $sImgText;
        $lastColorSize[] = $sColor_size_id;
        $tentative = <<<END
        <br>
        <a href='productsDetail.php?product_id=$product_id&color_size_id=$sColor_size_id'>
        色　　: $sColor_code
        サイズ: $sSize<br>
        価格　: $sPrice<br>
        </a>
        <form action="innerCart.php" method="post">
            <input type="hidden" name="product_id" value="$product_id">
            <input type="hidden" name="color_size_id" value="$sColor_size_id">
            <button type="submit" name="submit">カートに入れる</button>
        </form>
        <hr>
        END;
    }else{
        echo $sImgText;
    }
}
echo $tentative;
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