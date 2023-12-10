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
echo "<h2>$seller_name 様</h2>";
echo "<div id='errorMessage'></div>";

if(isset($_GET['errorMessage'])){
    $errorMessage = $_GET['errorMessage'];
    echo $errorMessage."<br>";
}

$selectSql = "SELECT p.product_id, p.productname, p.view, p.create_at, p.quality, s.color_code, s.size, b.big_category_name, c.category_name, sc.small_category_name, i.img_url 
                FROM products p
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
$productText = "";
$formFlag = false;
$checkFlag = true;
$count = 0;
if($selectResult && $selectResult->num_rows > 0){
    $lastImg = array();
    $colorArray = array();
    $sizeArray = array();
    $productArray = array();
    echo "<div id='div$count'>";
    while ($row = $selectResult->fetch_assoc()) {
        $imgText = null;
        $product_id = $row['product_id'];
        $productname = $row['productname'];
        $view = isset($row['view'])?$row['view']:"未登録";
        $quality = $row['quality'];
        $create_at = $row['create_at'];
        $color_code = $row['color_code'];
        $colorName = getColor($conn, $color_code);
        $size = $row['size'];
        $big_category_name = !is_null($row['big_category_name'])?$row['big_category_name']:"未登録";
        $category_name = !is_null($row['category_name'])?$row['category_name']:"未登録";
        $small_category_name = !is_null($row['small_category_name'])?$row['small_category_name']:"未登録";
        $img_url = is_null($row['img_url'])?null:$row['img_url'];
        $countId = $count - 1;
        if(!is_null($img_url)){
            $imgText = "
            <!---<a href='edit.php?product_id=$product_id>--->
            <img src='p_img/$img_url'>
            <!---</a>----->";
        }//else{
            //ここで商品の画像が一枚もないときに表示する写真を表示するタブを作る。
        //}
        
        if(in_array($product_id,$lastImg)){
            for($i = 0; $i < count($colorArray); $i++){
                if($colorArray[$i] === $colorName && $sizeArray[$i] === $size){
                    $checkFlag = false;
                }
            }
            if($checkFlag === true){
                $colorArray[] = $colorName;
                $sizeArray[] = $size;
            }else{
                $checkFlag = true;
            }
        }
        if($formFlag === true){
            $productText = <<<END
            <form action="edit.php" method="post">
                <select id="select$countId" required>
                    <option value="" hidden>選択してください</option>
            END;
            for($i = 0; $i < count($colorArray); $i++){
                $productText .= "<option>$colorArray[$i] - $sizeArray[$i]</option>";
            }
            $productText .= <<<END
                </select>
                <input type="hidden" value="$product_id">
                <input type="submit" value="登録内容変更">
            </form>
            <input type="hidden" id="$countId" value="$lastImg[$countId]">
            <button type="button" onclick="addColorSize($countId)">カラー・サイズの追加</button>
            <button type="button" onclick="deleteProducts($countId)">商品の削除</button>
            <hr>
            </div>
            <div id= "div$count">
            END;
        }else{
            $formFlag = true;
        }

        //違う商品になったタイミング
        if(!in_array($product_id, $lastImg)){
            echo $htmlText;

            //form
            echo $productText;
            $colorArray = array();
            $sizeArray = array();
            $colorArray[] = $colorName;
            $sizeArray[] = $size;

            echo $imgText;
            $lastImg[] = $product_id;
            $htmlText = <<<END
            <br>
            <br>
            商品名　　: $productname<br>
            カテゴリ名: $big_category_name - $category_name - $small_category_name<br>
            概要　　　: $view<br>
            品質　　　: $quality<br>
            出品日　　: $create_at<br>
            <br>
            END;
            // 他の情報も必要に応じて表示
            $count++;
        }else{
            echo $imgText;
        }
    }
    echo $htmlText;
    echo $productText;
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
<script>
const errorMessageDiv = document.getElementById('errorMessage');
function deleteProducts(deleteCount){
    var inputId = document.getElementById(deleteCount);
    var product_id = inputId.value;
    const formData = new FormData();
    formData.append('product_id',product_id);

    const xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function(){
        if(xhr.readyState === 4 && xhr.status === 200){
            var divCount = document.getElementById('div'+deleteCount);
            divCount.remove();

            errorMessageDiv.innerHTML = "商品の削除に成功しました。";
        }
        if(xhr.status < 200 && xhr.status >= 300){
            errorMessageDiv.innerHTML = "登録商品の削除に失敗しました。";
        }
    }
    xhr.open('POST','deleteProducts.php',true);
    xhr.send(formData);
}
</script>