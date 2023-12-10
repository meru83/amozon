<?php
// セッションを開始します
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ログインセッションが存在しない場合、ログインページにリダイレクトします
if (!isset($_SESSION['seller_id'])) {
    header("Location: seller_log.php"); // ログインページのURLにリダイレクト
    exit(); // リダイレクト後、スクリプトの実行を終了
}

include "../db_config.php";

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log'); // エラーログをerror.logファイルに記録
error_reporting(E_ALL);

// セッションからユーザーIDを取得します
session_regenerate_id(TRUE);
$seller_id = $_SESSION['seller_id'];
$seller_name = $_SESSION['sellerName'];

echo "<h1>登録済み商品一覧</h1>";
echo "<h2>$seller_name 様</h2>"

$selectSql = "SELECT p.product_id, p.productname, p.view, p.create_at, p.seller_id, p.quality, s.color_code, s.size, s.pieces, s.price, s.color_size_id, b.big_category_name, c.category_name, small_category_name, i.img_url FROM products p
                LEFT JOIN color_size s ON (p.product_id = s.product_id)
                LEFT JOIN big_category b ON (p.big_category_id = b.big_category_id)
                LEFT JOIN category c ON (p.category_id = c.category_id)
                LEFT JOIN small_category sc ON (p.small_category = sc.small_category)
                LEFT JOIN products_img i ON (s.color_size_id = i.color_size_id)
                WHERE p.seller_id = ?";
$selectStmt = $conn->prepare($selectSql);
$selectStmt->bind_param("s",$seller_id);
$selectStmt->execute();
$selectResult = $selectStmt->get_result();

$htmlText = "";
$count = 0;
$countMax = 0;
if($selectResult && $selectResult->num_rows > 0){
    $lastImg = array();
    while ($row = $selectResult->fetch_assoc()) {
        $imgText = null;
        $productname = $row['productname'];
        $view = $row['view'];
        $create_at = $row['create_at'];
        $colorCode = $row['color_code'];
        $colorName = getColor($conn, $colorCode);
        $product_id = $row['product_id'];
        $size = $row['size'];
        $pieces = $row['pieces'];
        $category_name = !is_null($row['category_name'])?$row['category_name']:"";
        $price  = $row['price'];
        $color_size_id = $row['color_size_id'];
        $img_url = is_null($row['img_url'])?null:$row['img_url'];
        if(!is_null($img_url)){
            $imgText = "
            <!---<a href='productsDetail.php?product_id=$product_id&color_size_id=$color_size_id'>--->
            <img src='seller/p_img/$img_url'>
            <!---</a>----->";
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
            <br>
            <!-----<a href='productsDetail.php?product_id=$product_id&color_size_id=$color_size_id'>---->
            色: $colorName
            サイズ: $size<br>
            商品名　　: $productname<br>
            カテゴリ名: $category_name<br>
            価格　　　: $price<br>
            在庫数　　: $<br>
            <!-----</a>---->
            <hr>
            END;
            // 他の情報も必要に応じて表示
            $count++;
        }else{
            echo $imgText;
        }
    }
    echo "登録商品は".$count."件です。";
}else{
    echo "登録されている商品がありません。";
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
?>