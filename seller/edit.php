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

echo '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>';
echo '<link rel="stylesheet" href="../css/edit.css">';
echo"<style>
        .swiper {
            width: 500px;
            max-width: 100%; 
            height: 300px; 
        }
        .swiper-slide img {
            width: 500px;
            height: 300px;
        }
    </style>";

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $product_id = $_POST['product_id'];
    $colorSize = $_POST['colorSize'];
    $colorSizeArray = preg_split('/[|]+/u',$colorSize);
    $color_code = $colorSizeArray[0];
    $colorName = getColor($conn, $color_code);
    $size = $colorSizeArray[1];
}else{
    header("Location:seller_products.php");
    exit();
}

$selectSql = "SELECT p.productname, p.create_at, s.pieces, s.price, s.color_size_id, i.img_url 
                FROM products p
                LEFT JOIN color_size s ON (p.product_id = s.product_id)
                LEFT JOIN products_img i ON (s.color_size_id = i.color_size_id)
                WHERE p.product_id = ? && s.color_code = ? && s.size = ?";
$selectStmt = $conn->prepare($selectSql);
$selectStmt->bind_param("iss",$product_id,$color_code,$size);
$selectStmt->execute();
$selectResult = $selectStmt->get_result();
echo '<htmlAll>';
echo '<div class="imgAll swiper">';
echo '<div class="swiper-wrapper">';
if($selectResult && $selectResult->num_rows > 0){
    while($row = $selectResult->fetch_assoc()){
        $productname = $row['productname'];
        $create_at = $row['create_at'];
        //$colorName
        $pieces = $row['pieces'];
        $price = $row['price'];
        $commaPrice = number_format($price);
        $color_size_id = $row['color_size_id'];
        $img_url = $row['img_url'];
        if(!is_null($img_url)){
            echo <<<END
            <div class='swiper-slide'>
                <img src='p_img/$img_url' alt='$colorName 色,".$size."サイズ'>
            </div>
            END;
        }else{
            //ここで商品の画像が一枚もないときに表示する写真を表示するタブを作る。
            echo <<<END
            <div class='swiper-slide'>
            <img src='../img/noImg.jpg'>
            </div>
            END;
        }
        //画像にサイズと色の説明が出るようにする。
    }
    echo <<<END
    </div>
    <!-- If we need pagination -->
    <div class="swiper-pagination"></div>
  
    <!-- If we need navigation buttons -->
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
  
    </div>

    <div class="setumei">
    <button type="button" id="addImg" class="btnStyle" onclick="addImg($color_size_id)" style="display:block">写真を追加</button>
    <form id="imgInsertForm" enctype="multipart/form-data" style="display:none">
        <input type="text" id="color_sizeInput" name="color_sizeInput" value="$color_size_id" hidden>
        <input type="file" id="image-file" name="img[]" multiple accept="image/*">
        <input type="submit" name="submit" class="kakutei" value="確定">
    </form>
    カラー:$colorName サイズ:$size <br>
    　　  商品名　: $productname <br>
    <button type="button" class="btnStyle" onclick="changePrice($color_size_id)">変更</button>
    <p id="priceText">価格　　: $commaPrice </p><br>
    <button type="button" class="btnStyle" onclick="changePieces($color_size_id)">変更</button>
    <p id="piecesText">在庫数　: $pieces </p><br>
    出品日　: $create_at <br>
    </div>
    </div>
    END;
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
<script>
function changePrice(id){
    newPrice = window.prompt("価格を入力してください","");
    if(newPrice != "" && newPrice != null){
        const formData = new FormData();
        formData.append('color_size_id', id);
        formData.append('newPrice', newPrice);

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'changePrice.php', true);
        xhr.send(formData);

        xhr.onreadystatechange = function(){
            if(xhr.readyState === 4 && xhr.status === 200){
                try {
                    const response = JSON.parse(xhr.responseText);
                    response.forEach(function(row) {
                        if(row.error_message === true){
                            var priceElement = document.getElementById('priceText');
                            priceElement.innerHTML = "価格　　: "+newPrice;
                            alert("価格を変更しました。");
                        }else{
                                alert("価格の変更に失敗しました。");
                        }
                    });
                } catch (error) {
                    console.error("Error parsing JSON response:", error);
                    alert("リクエストが失敗しました。");
                }
            }
        }
    }
}

function changePieces(id){
    newPieces = window.prompt("在庫数を入力してください","");
    if(newPieces != "" && newPieces != null){
        const formData = new FormData();
        formData.append('color_size_id', id);
        formData.append('pieces', newPieces);

        const xhr = new XMLHttpRequest();
        xhr.open('POST','changePieces.php',true);
        xhr.send(formData);
        xhr.onreadystatechange = function(){
            if(xhr.readyState === 4 && xhr.status === 200){
                try {
                    const response = JSON.parse(xhr.responseText);
                    response.forEach(function(row) {
                        if(row.error_message === true){
                            var piecesElement = document.getElementById('piecesText');
                            piecesElement.innerHTML = "在庫数　: "+newPieces;
                            alert("在庫数を変更しました。");
                        }else{
                                alert("在庫数の変更に失敗しました。");
                        }
                    });
                } catch (error) {
                    console.error("Error parsing JSON response:", error);
                    alert("リクエストが失敗しました。");
                }
            }
        }
    }
}

function addImg(id){
    var addImg = document.getElementById('addImg');
    addImg.style.display = "none";
    var imgInsertForm = document.getElementById('imgInsertForm');
    imgInsertForm.style.display = "block";
    imgInsertForm.addEventListener('submit',function(e){
        e.preventDefault();
        var color_sizeElement = document.getElementById('color_sizeInput');
        var color_size_id = color_sizeElement.value;
        var imageElement = document.getElementById('image-file');
        // var imageFile = imageElement.files;
        var imageFileLength = imageElement.files.length;
        // console.log(imageFileLength);

        const formData = new FormData();
        formData.append('color_size_id', color_size_id);
        for(let i = 0; i < imageFileLength; i++){
            formData.append('imgFile['+i+']', imageElement.files[i]);
        }
        const xhr = new XMLHttpRequest();

        xhr.open('POST','addImg.php',true);
        xhr.send(formData);

        xhr.onreadystatechange = function(){
            if(xhr.readyState === 4 && xhr.status === 200){
                try {
                    const response = JSON.parse(xhr.responseText);
                    response.forEach(function(row) {
                        if(row.error_message){
                            alert("画像が登録されました。");
                            window.location.reload();
                        }else{
                            alert("画像の登録に失敗しました。");
                        }
                    });
                } catch (error) {
                    console.error("Error parsing JSON response:", error);
                    alert("リクエストが失敗しました。");
                }
            }
        }
    });
}
</script>